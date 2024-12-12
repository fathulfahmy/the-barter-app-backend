<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Requests\BarterReviewStoreRequest;
use App\Http\Requests\BarterReviewUpdateRequest;
use App\Models\BarterReview;
use App\Models\BarterTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BarterReviewController extends BaseController
{
    public function index(): JsonResponse
    {
        $barter_reviews = BarterReview::with('author', 'barter_service', 'barter_transaction.barter_invoice')
            ->where('author_id', auth()->id())
            ->paginate(config('app.default.pagination'));

        return ApiResponse::success(
            'Reviews fetched successfully',
            200,
            $barter_reviews,
        );
    }

    public function show($barter_review_id): JsonResponse
    {
        $barter_review = BarterReview::with('author', 'barter_service', 'barter_transaction.barter_invoice')->find($barter_review_id);

        if (! isset($barter_review)) {
            throw (new \Exception('Review does not exist'));
        }

        return ApiResponse::success(
            'Review detail fetched successfully',
            200,
            $barter_review,
        );
    }

    public function store(BarterReviewStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $validated['author_id'] = auth()->id();

            $barter_transaction = BarterTransaction::find($validated['barter_transaction_id']);

            if (! isset($barter_transaction)) {
                throw (new \Exception('Transaction does not exist'));
            }

            $validated['barter_service_id'] = $barter_transaction->barter_service_id;

            $barter_review = BarterReview::create($validated);

            DB::commit();

            return ApiResponse::success(
                'Review created successfully',
                201,
                $barter_review
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to create review',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function update(BarterReviewUpdateRequest $request, $barter_review_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_review = BarterReview::find($barter_review_id);
            if (! isset($barter_review)) {
                throw (new \Exception('Review does not exist'));
            }

            Gate::authorize('update', $barter_review);

            $validated = $request->validated();
            $barter_review->update($validated);

            DB::commit();

            return ApiResponse::success(
                'Review updated successfully',
                200,
                $barter_review
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to update review',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function destroy($barter_review_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_review = BarterReview::find($barter_review_id);
            if (! isset($barter_review)) {
                throw (new \Exception('Review does not exist'));
            }

            Gate::authorize('delete', $barter_review);

            $barter_review->delete();

            DB::commit();

            return ApiResponse::success('Review deleted successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to delete review',
                500,
                [$e->getMessage()],
            );
        }
    }
}
