<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarterCategoryController;
use App\Http\Controllers\BarterInvoiceController;
use App\Http\Controllers\BarterRemarkController;
use App\Http\Controllers\BarterReviewController;
use App\Http\Controllers\BarterServiceController;
use App\Http\Controllers\BarterTransactionController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\UserReportReasonController;
use App\Http\Middleware\EnsureUserIsNotSuspended;
use Illuminate\Support\Facades\Route;

// AUTH
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', EnsureUserIsNotSuspended::class])->group(function () {
    // AUTH
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // BARTER CATEGORIES
    Route::get('/barter_categories', [BarterCategoryController::class, 'index'])->name('api.barter_categories.index');

    // BARTER SERVICES
    Route::get('/barter_services', [BarterServiceController::class, 'index']);
    Route::get('/barter_services/{barter_service_id}', [BarterServiceController::class, 'show']);
    Route::post('/barter_services', [BarterServiceController::class, 'store']);
    Route::patch('/barter_services/{barter_service_id}', [BarterServiceController::class, 'update']);
    Route::delete('/barter_services/{barter_service_id}', [BarterServiceController::class, 'destroy']);

    // BARTER TRANSACTIONS
    Route::get('/barter_transactions', [BarterTransactionController::class, 'index']);
    Route::get('/barter_transactions/{barter_transaction_id}', [BarterTransactionController::class, 'show']);
    Route::post('/barter_transactions', [BarterTransactionController::class, 'store']);
    Route::patch('/barter_transactions/{barter_transaction_id}', [BarterTransactionController::class, 'update']);
    Route::delete('/barter_transactions/{barter_transaction_id}', [BarterTransactionController::class, 'destroy']);

    // BARTER INVOICES
    Route::get('/barter_invoices', [BarterInvoiceController::class, 'index']);
    Route::get('/barter_invoices/{barter_invoice_id}', [BarterInvoiceController::class, 'show']);
    Route::post('/barter_invoices', [BarterInvoiceController::class, 'store']);
    Route::patch('/barter_invoices/{barter_invoice_id}', [BarterInvoiceController::class, 'update']);
    Route::delete('/barter_invoices/{barter_invoice_id}', [BarterInvoiceController::class, 'destroy']);

    // BARTER REMARKS
    Route::get('/barter_remarks', [BarterRemarkController::class, 'index']);
    Route::get('/barter_remarks/{barter_remark_id}', [BarterRemarkController::class, 'show']);
    Route::post('/barter_remarks', [BarterRemarkController::class, 'store']);
    Route::patch('/barter_remarks/{barter_remark_id}', [BarterRemarkController::class, 'update']);
    Route::delete('/barter_remarks/{barter_remark_id}', [BarterRemarkController::class, 'destroy']);

    // BARTER REVIEWS
    Route::get('/barter_reviews', [BarterReviewController::class, 'index']);
    Route::get('/barter_reviews/{barter_review_id}', [BarterReviewController::class, 'show']);
    Route::post('/barter_reviews', [BarterReviewController::class, 'store']);
    Route::patch('/barter_reviews/{barter_review_id}', [BarterReviewController::class, 'update']);
    Route::delete('/barter_reviews/{barter_review_id}', [BarterReviewController::class, 'destroy']);

    // USER
    Route::patch('/users/{user_id}', [UserController::class, 'update']);

    // USER REPORT REASON
    Route::get('/user_report_reasons', [UserReportReasonController::class, 'index']);

    // USER REPORT
    Route::get('/user_reports', [UserReportController::class, 'index']);
    Route::post('/user_reports', [UserReportController::class, 'store']);

    // STRIPE
    Route::post('/stripe/payment_sheet', [StripeController::class, 'payment_sheet']);

    // STATS
    Route::get('/stats/barter_transactions/monthly_group_by_status', [StatsController::class, 'barter_transactions_monthly_group_by_status']);
    Route::get('/stats/barter_services/monthly_trending', [StatsController::class, 'barter_services_monthly_trending']);
    Route::get('/stats/tab_bar_badges', [StatsController::class, 'tab_bar_badges']);
});
