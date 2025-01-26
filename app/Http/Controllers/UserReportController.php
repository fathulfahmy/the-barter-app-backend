<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserReportStoreRequest;
use App\Models\UserReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Report
 */
class UserReportController extends BaseController
{
    /**
     * Get Reports
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: array<User Report>
     * }
     */
    public function index()
    {
        try {
            $user_reports = UserReport::all();

            return response()->apiSuccess('Reports fetched successfully', $user_reports);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch reports', $e->getMessage());
        }
    }

    /**
     * Create Report
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: UserReport
     * }
     */
    public function store(UserReportStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $validated['reporter_id'] = auth()->id();

            $user_report = UserReport::create($validated);

            DB::commit();

            return response()->apiSuccess('Report created successfully', $user_report, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to create report', $e->getMessage());
        }
    }
}
