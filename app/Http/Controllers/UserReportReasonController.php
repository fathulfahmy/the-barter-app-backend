<?php

namespace App\Http\Controllers;

use App\Models\UserReportReason;

/**
 * @tags Report
 */
class UserReportReasonController extends BaseController
{
    /**
     * Get Reasons
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: array<UserReportReason>
     * }
     */
    public function index()
    {
        try {
            $user_report_reasons = UserReportReason::all();

            return response()->apiSuccess('Reasons fetched successfully', $user_report_reasons);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch reasons', $e->getMessage());
        }
    }
}
