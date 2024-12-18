<?php

namespace App\Http\Controllers;

use App\Models\BarterCategory;
use Symfony\Component\HttpFoundation\Response;

class BarterCategoryController extends BaseController
{
    /**
     * Display a listing of the barter categories.
     */
    public function index()
    {
        try {
            $barter_categories = BarterCategory::all();

            return response()->json([
                'success' => true,
                'message' => 'Categories fetched successfully',
                'data' => $barter_categories,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch categories');
        }
    }
}
