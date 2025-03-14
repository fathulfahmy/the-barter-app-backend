<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarterTransactionStoreRequest;
use App\Http\Requests\BarterTransactionUpdateRequest;
use App\Models\BarterInvoice;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Transaction
 */
class BarterTransactionController extends BaseController
{
    /**
     * Get Transactions
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: Illuminate\Pagination\LengthAwarePaginator<BarterTransaction>,
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $mode = $request->input('mode');
            $search = trim($request->input('search'));
            $barter_service_id = $request->input('barter_service_id');

            $query = BarterTransaction::query()
                ->with(['barter_acquirer', 'barter_provider', 'barter_service', 'barter_invoice', 'barter_remarks', 'barter_reviews'])
                ->when($search && $search !== '', function ($query) use ($search) {
                    $datetime = $this->parseSearchDate($search);
                    $query->where(function ($query) use ($search, $datetime) {
                        $query->orWhereHas('barter_service', function ($query) use ($search) {
                            $query->whereLike('title', "%{$search}%");
                        });

                        $query->orWhereHas('barter_invoice.barter_services', function ($query) use ($search) {
                            $query->whereLike('title', "%{$search}%");
                        });

                        $query->orWhereHas('barter_acquirer', function ($query) use ($search) {
                            $query->whereLike('name', "%{$search}%");
                        });

                        $query->orWhereHas('barter_provider', function ($query) use ($search) {
                            $query->whereLike('name', "%{$search}%");
                        });

                        $query->orWhereHas('barter_remarks', function ($query) use ($search) {
                            $query->whereLike('address', "%{$search}%")
                                ->orWhereLike('deliverables', "%{$search}%")
                                ->orWhereLike('note', "%{$search}%");
                        });

                        if (isset($datetime)) {
                            $query->orWhereHas('barter_remarks', function ($query) use ($datetime) {
                                $query->where('datetime', $datetime->toDateTimeString())
                                    ->orWhereDate('datetime', $datetime->toDateString())
                                    ->orWhereTime('datetime', $datetime->toTimeString());
                            });
                        }
                    });
                })
                ->when($barter_service_id, function ($query) use ($barter_service_id) {
                    $query->where('barter_service_id', $barter_service_id);
                })
                ->when($mode === 'incoming', function ($query) {
                    $query->where('barter_provider_id', auth()->id())
                        ->where('status', 'pending');
                })
                ->when($mode === 'outgoing', function ($query) {
                    $query->where('barter_acquirer_id', auth()->id())
                        ->where('status', 'pending');
                })
                ->when($mode === 'ongoing', function ($query) {
                    $query
                        ->where(function ($query) {
                            $query->where('barter_acquirer_id', auth()->id())
                                ->orWhere('barter_provider_id', auth()->id());
                        })
                        ->whereIn('status', ['accepted', 'awaiting_completed', 'completed'])
                        ->whereDoesntHave('barter_reviews', function ($query) {
                            $query->where('reviewer_id', auth()->id());
                        });
                })
                ->when($mode === 'history', function ($query) {
                    $query
                        ->where(function ($query) {
                            $query->where('barter_acquirer_id', auth()->id())
                                ->orWhere('barter_provider_id', auth()->id());
                        })
                        ->where(function ($query) {
                            $query->where('status', 'rejected')
                                ->orWhere(function ($query) {
                                    $query->where('status', 'completed')
                                        ->whereHas('barter_reviews', function ($query) {
                                            $query->where('reviewer_id', auth()->id());
                                        });
                                });
                        });
                });

            $barter_transactions = $query
                ->orderByDesc('updated_at')
                ->paginate(config('app.default.pagination'));

            return response()->apiSuccess('Transactions fetched successfully', $barter_transactions);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch transactions', $e->getMessage());
        }
    }

    /**
     * Get Transaction
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterTransaction
     * }
     */
    public function show(string $barter_transaction_id): JsonResponse
    {
        try {
            $barter_transaction = BarterTransaction::with([
                'barter_acquirer',
                'barter_provider',
                'barter_service',
                'barter_invoice',
            ])
                ->findOrFail($barter_transaction_id);

            return response()->apiSuccess('Transaction detail fetched successfully', $barter_transaction);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch transaction detail', $e->getMessage());
        }
    }

    /**
     * Create Transaction
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterTransaction
     * }
     */
    public function store(BarterTransactionStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $barter_acquirer_id = auth()->id();

            $barter_service = BarterService::findOrFail($validated['barter_service_id']);

            $barter_transaction = BarterTransaction::create([
                'barter_acquirer_id' => $barter_acquirer_id,
                'barter_provider_id' => $barter_service->barter_provider_id,
                'barter_service_id' => $validated['barter_service_id'],
            ]);

            $barter_invoice = BarterInvoice::create([
                'barter_acquirer_id' => $barter_acquirer_id,
                'barter_transaction_id' => $barter_transaction->id,
                'amount' => $validated['amount'] ?? 0,
            ]);

            if (! empty($validated['barter_service_ids'])) {
                $barter_invoice->barter_services()->attach($validated['barter_service_ids']);
            }

            DB::commit();

            return response()->apiSuccess('Transaction created successfully', $barter_transaction, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to create transaction', $e->getMessage());
        }
    }

    /**
     * Update Transaction
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterTransaction
     * }
     */
    public function update(BarterTransactionUpdateRequest $request, string $barter_transaction_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_transaction = BarterTransaction::findOrFail($barter_transaction_id);

            Gate::authorize('update', $barter_transaction);

            $validated = $request->validated();

            if ($validated['status'] && ($validated['status'] === 'completed' || $validated['status'] === 'awaiting_completed')) {
                if ($barter_transaction->status === 'awaiting_completed') {
                    $validated['status'] = 'completed';
                    $validated['awaiting_user_id'] = null;
                } else {
                    $validated['status'] = 'awaiting_completed';
                    $validated['awaiting_user_id'] = auth()->id();
                }
            }

            $barter_transaction->update($validated);

            DB::commit();

            $barter_transaction->refresh();

            return response()->apiSuccess('Transaction updated successfully', $barter_transaction);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to update transaction', $e->getMessage());
        }
    }

    /**
     * Delete Transaction
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: [],
     * }
     */
    public function destroy(string $barter_transaction_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_transaction = BarterTransaction::findOrFail($barter_transaction_id);

            Gate::authorize('delete', $barter_transaction);

            $barter_transaction->delete();

            DB::commit();

            return response()->apiSuccess('Transaction deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to delete transaction', $e->getMessage());
        }
    }
}
