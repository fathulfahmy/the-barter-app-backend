<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarterCategoryController;
use App\Http\Controllers\BarterInvoiceController;
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
Route::post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

Route::middleware(['auth:sanctum', EnsureUserIsNotSuspended::class])->group(function () {
    // AUTH
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');

    // BARTER CATEGORIES
    Route::get('/barter_categories', [BarterCategoryController::class, 'index'])->name('api.barter_categories.index');

    // BARTER SERVICES
    Route::get('/barter_services', [BarterServiceController::class, 'index'])->name('api.barter_services.index');
    Route::get('/barter_services/{barter_service_id}', [BarterServiceController::class, 'show'])->name('api.barter_services.show');
    Route::post('/barter_services', [BarterServiceController::class, 'store'])->name('api.barter_services.store');
    Route::patch('/barter_services/{barter_service_id}', [BarterServiceController::class, 'update'])->name('api.barter_services.update');
    Route::delete('/barter_services/{barter_service_id}', [BarterServiceController::class, 'destroy'])->name('api.barter_services.destroy');

    // BARTER TRANSACTIONS
    Route::get('/barter_transactions', [BarterTransactionController::class, 'index'])->name('api.barter_transactions.index');
    Route::get('/barter_transactions/{barter_transaction_id}', [BarterTransactionController::class, 'show'])->name('api.barter_transactions.show');
    Route::post('/barter_transactions', [BarterTransactionController::class, 'store'])->name('api.barter_transactions.store');
    Route::patch('/barter_transactions/{barter_transaction_id}', [BarterTransactionController::class, 'update'])->name('api.barter_transactions.update');
    Route::delete('/barter_transactions/{barter_transaction_id}', [BarterTransactionController::class, 'destroy'])->name('api.barter_transactions.destroy');

    // BARTER INVOICES
    Route::get('/barter_invoices', [BarterInvoiceController::class, 'index'])->name('api.barter_invoices.index');
    Route::get('/barter_invoices/{barter_invoice_id}', [BarterInvoiceController::class, 'show'])->name('api.barter_invoices.show');
    Route::post('/barter_invoices', [BarterInvoiceController::class, 'store'])->name('api.barter_invoices.store');
    Route::patch('/barter_invoices/{barter_invoice_id}', [BarterInvoiceController::class, 'update'])->name('api.barter_invoices.update');
    Route::delete('/barter_invoices/{barter_invoice_id}', [BarterInvoiceController::class, 'destroy'])->name('api.barter_invoices.destroy');

    // BARTER REVIEWS
    Route::get('/barter_reviews', [BarterReviewController::class, 'index'])->name('api.barter_reviews.index');
    Route::get('/barter_reviews/{barter_review_id}', [BarterReviewController::class, 'show'])->name('api.barter_reviews.show');
    Route::post('/barter_reviews', [BarterReviewController::class, 'store'])->name('api.barter_reviews.store');
    Route::patch('/barter_reviews/{barter_review_id}', [BarterReviewController::class, 'update'])->name('api.barter_reviews.update');
    Route::delete('/barter_reviews/{barter_review_id}', [BarterReviewController::class, 'destroy'])->name('api.barter_reviews.destroy');

    // USER
    Route::patch('/users/{user_id}', [UserController::class, 'update'])->name('api.profile.update');

    // USER REPORT REASON
    Route::get('/user_report_reasons', [UserReportReasonController::class, 'index'])->name('api.user_report_reasons.index');

    // USER REPORT
    Route::get('/user_reports', [UserReportController::class, 'index'])->name('api.user_reports.index');
    Route::post('/user_reports', [UserReportController::class, 'store'])->name('api.user_reports.store');

    // STRIPE
    Route::post('/stripe/payment_sheet', [StripeController::class, 'payment_sheet'])->name('api.stripe.payment_sheet');

    // STATS
    Route::get('/stats/barter_transactions/monthly_group_by_status', [StatsController::class, 'barter_transactions_monthly_group_by_status'])->name('api.stats.barter_transactions.monthly_group_by_status');
    Route::get('/stats/barter_services/monthly_trending', [StatsController::class, 'barter_services_monthly_trending'])->name('api.stats.barter_services.monthly_trending');
});
