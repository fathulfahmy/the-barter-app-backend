<?php

namespace App\Http\Controllers;

use App\Models\BarterCategory;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Category
 */
class BarterCategoryController extends BaseController
{
    /**
     * Get Categories
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: array<BarterCategory>
     * }
     */
    public function index()
    {
        try {
            $barter_categories = BarterCategory::all();

            return response()->apiSuccess('Categories fetched successfully', $barter_categories);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch categories', $e->getMessage());
        }
    }
}
