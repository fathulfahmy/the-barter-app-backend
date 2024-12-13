<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Requests\BarterTransactionStoreRequest;
use App\Http\Requests\BarterTransactionUpdateRequest;
use App\Models\BarterInvoice;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BarterTransactionController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        try {
            $mode = $request->input('mode');
            $barter_service_id = $request->input('barter_service_id');

            if (! in_array($mode, ['incoming', 'outgoing', 'ongoing', 'history'])) {
                return ApiResponse::error('Invalid transaction mode', 400);
            }

            $query = BarterTransaction::query()
                ->with(['barter_acquirer', 'barter_provider', 'barter_service', 'barter_invoice'])
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
                    $query->where(function ($q) {
                        $q->where('barter_acquirer_id', auth()->id())
                            ->orWhere('barter_provider_id', auth()->id());
                    })->where('status', 'accepted');
                })
                ->when($mode === 'history', function ($query) {
                    $query->where(function ($q) {
                        $q->where('barter_acquirer_id', auth()->id())
                            ->orWhere('barter_provider_id', auth()->id());
                    })
                        ->whereIn('status', ['rejected', 'cancelled', 'completed'])
                        ->with('barter_reviews');
                });

            $barter_transactions = $query
                ->orderBy('updated_at', 'desc')
                ->paginate(config('app.default.pagination'));

            return ApiResponse::success(
                'Transactions fetched successfully',
                200,
                $barter_transactions
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to fetch transactions',
                500,
                [$e->getMessage()]
            );
        }
    }

    public function show($barter_transaction_id): JsonResponse
    {
        $barter_transaction = BarterTransaction::with('barter_acquirer', 'barter_service', 'barter_invoice')->find($barter_transaction_id);

        if (! isset($barter_transaction)) {
            throw (new \Exception('Transaction does not exist'));
        }

        return ApiResponse::success(
            'Transaction detail fetched successfully',
            200,
            $barter_transaction,
        );
    }

    public function store(BarterTransactionStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $barter_acquirer_id = auth()->id();

            $barter_service = BarterService::find($validated['barter_service_id']);

            if (! isset($barter_service)) {
                throw (new \Exception('Service does not exist'));
            }

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

            return ApiResponse::success(
                'Transaction created successfully',
                201,
                $barter_transaction
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to create transaction',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function update(BarterTransactionUpdateRequest $request, $barter_transaction_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_transaction = BarterTransaction::find($barter_transaction_id);
            if (! isset($barter_transaction)) {
                throw (new \Exception('Transaction does not exist'));
            }

            Gate::authorize('update', $barter_transaction);

            $validated = $request->validated();
            $barter_transaction->update($validated);

            DB::commit();

            return ApiResponse::success(
                'Transaction updated successfully',
                200,
                $barter_transaction
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to update transaction',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function destroy($barter_transaction_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_transaction = BarterTransaction::find($barter_transaction_id);
            if (! isset($barter_transaction)) {
                throw (new \Exception('Transaction does not exist'));
            }

            Gate::authorize('delete', $barter_transaction);

            $barter_transaction->delete();

            DB::commit();

            return ApiResponse::success('Transaction deleted successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to delete transaction',
                500,
                [$e->getMessage()],
            );
        }
    }
}
