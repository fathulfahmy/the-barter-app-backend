<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Models\BarterCategory;

class BarterCategoryController extends BaseController
{
    public function index()
    {
        $barter_categories = BarterCategory::all();

        return ApiResponse::success(
            'Categories fetched successfully',
            200,
            $barter_categories,
        );
    }

    public function names()
    {
        $names = BarterCategory::pluck('name');

        return ApiResponse::success(
            'Category names fetched successfully',
            200,
            $names,
        );
    }
}
