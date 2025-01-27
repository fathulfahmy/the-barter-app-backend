<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarterRemarkStoreRequest;
use App\Http\Requests\BarterRemarkUpdateRequest;
use App\Models\BarterRemark;
use App\Models\BarterTransaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Remark
 */
class BarterRemarkController extends BaseController
{
    /**
     * Get Remarks
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: Illuminate\Pagination\LengthAwarePaginator<BarterRemark>,
     * }
     */
    public function index(): JsonResponse
    {
        try {
            $query = BarterRemark::with(['barter_transaction.barter_service', 'barter_transaction.barter_invoice.barter_services']);

            $barter_remarks = $query
                ->where('user_id', auth()->id())
                ->whereNotNull('datetime')
                ->whereHas('barter_transaction', function ($query) {
                    $query->whereIn('status', ['accepted', 'awaiting_completed']);
                })
                ->orderBy('datetime')
                ->paginate(config('app.default.pagination'));

            return response()->apiSuccess('Remarks fetched successfully', $barter_remarks);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch remarks', $e->getMessage());
        }
    }

    /**
     * Get Remark
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterRemark,
     * }
     */
    public function show(string $barter_remark_id): JsonResponse
    {
        try {
            $barter_remark = BarterRemark::with(['user'])->findOrFail($barter_remark_id);

            return response()->apiSuccess('Remark detail fetched successfully', $barter_remark);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch remark detail', $e->getMessage());
        }
    }

    /**
     * Create Remark
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterRemark,
     * }
     */
    public function store(BarterRemarkStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $barter_transaction = BarterTransaction::findOrFail($validated['barter_transaction_id']);

            $validated['user_id'] = auth()->id();
            $validated['barter_transaction_id'] = $barter_transaction->id;

            if (isset($validated['datetime'])) {
                $validated['datetime'] = Carbon::parse($validated['datetime'])
                    ->setTimezone('Asia/Kuala_Lumpur')
                    ->toDateTimeString();
            }

            if (isset($validated['deliverables']) && is_array($validated['deliverables'])) {
                $validated['deliverables'] = array_filter($validated['deliverables'], function ($value) {
                    return $value !== null && $value !== '';
                });
            }

            $barter_remark = BarterRemark::create($validated);

            DB::commit();

            return response()->apiSuccess('Remark created successfully', $barter_remark, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to create remark', $e->getMessage());
        }
    }

    /**
     * Update Remark
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterRemark,
     * }
     */
    public function update(BarterRemarkUpdateRequest $request, string $barter_remark_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_remark = BarterRemark::findOrFail($barter_remark_id);

            Gate::authorize('update', $barter_remark);

            $validated = $request->validated();

            if (isset($validated['datetime'])) {
                $validated['datetime'] = Carbon::parse($validated['datetime'])
                    ->setTimezone('Asia/Kuala_Lumpur')
                    ->toDateTimeString();
            }

            if (isset($validated['deliverables']) && is_array($validated['deliverables'])) {
                $validated['deliverables'] = array_filter($validated['deliverables'], function ($value) {
                    return $value !== null && $value !== '';
                });
            }

            $barter_remark->update($validated);

            DB::commit();

            $barter_remark->refresh();

            return response()->apiSuccess('Remark updated successfully', $barter_remark);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to update remark', $e->getMessage());
        }
    }

    /**
     * Delete Remark
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: [],
     * }
     */
    public function destroy(string $barter_remark_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_remark = BarterRemark::findOrFail($barter_remark_id);

            Gate::authorize('delete', $barter_remark);

            $barter_remark->delete();

            DB::commit();

            return response()->apiSuccess('Remark deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to delete remark', $e->getMessage());
        }
    }
}
