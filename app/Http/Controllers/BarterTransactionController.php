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

class BarterTransactionController extends BaseController
{
    /**
     * Display a listing of the barter transaction.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $mode = $request->input('mode');
            $barter_service_id = $request->input('barter_service_id');

            if (! in_array($mode, ['incoming', 'outgoing', 'ongoing', 'history'])) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Invalid transaction mode');
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

            return response()->json([
                'success' => true,
                'message' => 'Transactions fetched successfully',
                'data' => $barter_transactions,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch transactions');
        }
    }

    /**
     * Display the specified barter transaction.
     */
    public function show(string $barter_transaction_id): JsonResponse
    {
        try {
            $barter_transaction = BarterTransaction::with('barter_acquirer', 'barter_provider', 'barter_service', 'barter_invoice')
                ->findOrFail($barter_transaction_id);

            return response()->json([
                'success' => true,
                'message' => 'Transaction detail fetched successfully',
                'data' => $barter_transaction,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch transaction detail');
        }
    }

    /**
     * Store a newly created barter transaction in storage.
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

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $barter_transaction,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to create transaction');
        }
    }

    /**
     * Update the specified barter transaction in storage.
     */
    public function update(BarterTransactionUpdateRequest $request, string $barter_transaction_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_transaction = BarterTransaction::findOrFail($barter_transaction_id);

            Gate::authorize('update', $barter_transaction);

            $validated = $request->validated();
            $barter_transaction->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully',
                'data' => $barter_transaction,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to update transaction');
        }
    }

    /**
     * Remove the specified barter transaction from storage.
     */
    public function destroy(string $barter_transaction_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_transaction = BarterTransaction::findOrFail($barter_transaction_id);

            Gate::authorize('delete', $barter_transaction);

            $barter_transaction->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully',
                'data' => [],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to delete transaction');
        }
    }
}
