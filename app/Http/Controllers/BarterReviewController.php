<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarterReviewStoreRequest;
use App\Http\Requests\BarterReviewUpdateRequest;
use App\Models\BarterReview;
use App\Models\BarterTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Review
 */
class BarterReviewController extends BaseController
{
    /**
     * Get Reviews
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: Illuminate\Pagination\LengthAwarePaginator<BarterReview>,
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $barter_service_id = $request->input('barter_service_id');

            $query = BarterReview::query()
                ->with([
                    'reviewer',
                    'barter_transaction.barter_service',
                    'barter_transaction.barter_invoice',
                ])
                ->when($barter_service_id, function ($query) use ($barter_service_id) {
                    $query->where('barter_service_id', $barter_service_id);
                })
                ->when(! $barter_service_id, function ($query) {
                    $query->where('reviewer_id', auth()->id());
                });

            $barter_reviews = $query
                ->orderByDesc('updated_at')
                ->paginate(config('app.default.pagination'));

            return response()->apiSuccess('Reviews fetched successfully', $barter_reviews);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch reviews', $e->getMessage());
        }
    }

    /**
     * Get Review
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterReview,
     * }
     */
    public function show(string $barter_review_id): JsonResponse
    {
        try {
            $barter_review = BarterReview::with([
                'reviewer',
                'barter_transaction.barter_service',
                'barter_transaction.barter_invoice',
            ])
                ->findOrFail($barter_review_id);

            return response()->apiSuccess('Review detail fetched successfully', $barter_review);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch review detail', $e->getMessage());
        }
    }

    /**
     * Create Review
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterReview,
     * }
     */
    public function store(BarterReviewStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $barter_transaction = BarterTransaction::findOrFail($validated['barter_transaction_id']);

            $validated['reviewer_id'] = auth()->id();
            $validated['barter_transaction_id'] = $barter_transaction->id;

            $barter_review = BarterReview::create($validated);

            DB::commit();

            return response()->apiSuccess('Review created successfully', $barter_review, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to create review', $e->getMessage());
        }
    }

    /**
     * Update Review
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterReview,
     * }
     */
    public function update(BarterReviewUpdateRequest $request, string $barter_review_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_review = BarterReview::findOrFail($barter_review_id);

            Gate::authorize('update', $barter_review);

            $validated = $request->validated();

            $barter_review->update($validated);

            DB::commit();

            $barter_review->refresh();

            return response()->apiSuccess('Review updated successfully', $barter_review);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to update review', $e->getMessage());
        }
    }

    /**
     * Delete Review
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: [],
     * }
     */
    public function destroy(string $barter_review_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_review = BarterReview::findOrFail($barter_review_id);

            Gate::authorize('delete', $barter_review);

            $barter_review->delete();

            DB::commit();

            return response()->apiSuccess('Review deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to delete review', $e->getMessage());
        }
    }
}
