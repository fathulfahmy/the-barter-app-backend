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

class BarterReviewController extends BaseController
{
    /**
     * Display a listing of the barter review.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $barter_service_id = $request->input('barter_service_id');

            $query = BarterReview::query()
                ->with(['author', 'barter_service', 'barter_transaction.barter_invoice'])
                ->when($barter_service_id, function ($query) use ($barter_service_id) {
                    $query->where('barter_service_id', $barter_service_id);
                })
                ->when(! $barter_service_id, function ($query) {
                    $query->where('author_id', auth()->id());
                });

            $barter_reviews = $query->paginate(config('app.default.pagination'));

            return response()->json([
                'success' => true,
                'message' => 'Reviews fetched successfully',
                'data' => $barter_reviews,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch reviews');
        }
    }

    /**
     * Display the specified barter review.
     */
    public function show(string $barter_review_id): JsonResponse
    {
        try {
            $barter_review = BarterReview::with('author', 'barter_service', 'barter_transaction.barter_invoice')
                ->findOrFail($barter_review_id);

            return response()->json([
                'success' => true,
                'message' => 'Review detail fetched successfully',
                'data' => $barter_review,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch review detail');
        }
    }

    /**
     * Store a newly created barter review in storage.
     */
    public function store(BarterReviewStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $validated['author_id'] = auth()->id();

            $barter_transaction = BarterTransaction::findOrFail($validated['barter_transaction_id']);

            $validated['barter_service_id'] = $barter_transaction->barter_service_id;

            $barter_review = BarterReview::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review created successfully',
                'data' => $barter_review,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to create review');
        }
    }

    /**
     * Update the specified barter review in storage.
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

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully',
                'data' => $barter_review,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to update review');
        }
    }

    /**
     * Remove the specified barter review from storage.
     */
    public function destroy(string $barter_review_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_review = BarterReview::findOrFail($barter_review_id);

            Gate::authorize('delete', $barter_review);

            $barter_review->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully',
                'data' => [],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to delete review');
        }
    }
}
