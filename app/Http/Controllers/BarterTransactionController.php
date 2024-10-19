<?php

namespace App\Http\Controllers;
use App\ApiResponse;
use App\Http\Requests\BarterTransactionStoreRequest;
use App\Http\Requests\BarterTransactionUpdateRequest;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BarterTransactionController extends BaseController
{
    public function requests(): JsonResponse
    {
        $barter_transactions = BarterTransaction::with('barter_acquirer', 'barter_provider', 'barter_service')
            ->where('barter_provider_id', auth()->id())
            ->where('status', 'pending')
            ->paginate(10);

        return ApiResponse::success(
            'Requests fetched successfully',
            200,
            $barter_transactions,
        );
    }

    public function records(): JsonResponse
    {
        $barter_transactions = BarterTransaction::with('barter_acquirer', 'barter_provider', 'barter_service')
            ->where('barter_provider_id', auth()->id())
            ->whereNot('status', 'pending')
            ->paginate(10);

        return ApiResponse::success(
            'Records fetched successfully',
            200,
            $barter_transactions,
        );
    }

    public function show($id): JsonResponse
    {
        $barter_transaction = BarterTransaction::with('barter_acquirer', 'barter_provider', 'barter_service')->find($id);

        if (!isset($barter_transaction)) {
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
            $validated['barter_acquirer_id'] = auth()->id();

            $barter_service = BarterService::find($validated['barter_service_id']);

            if (!isset($barter_service)) {
                throw (new \Exception('Service does not exist'));
            }

            $validated['barter_provider_id'] = $barter_service->barter_provider_id;

            BarterTransaction::create($validated);

            DB::commit();

            return ApiResponse::success('Transaction created successfully', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                'Failed to create transaction',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function update(BarterTransactionUpdateRequest $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_transaction = BarterTransaction::find($id);
            if (!isset($barter_transaction)) {
                throw (new \Exception('Transaction does not exist'));
            }

            Gate::authorize('update', $barter_transaction);

            $validated = $request->validated();
            $barter_transaction->update($validated);

            DB::commit();

            return ApiResponse::success('Transaction updated successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                'Failed to update transaction',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_transaction = BarterTransaction::find($id);
            if (!isset($barter_transaction)) {
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
