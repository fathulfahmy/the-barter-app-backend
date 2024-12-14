<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarterCategoryController;
use App\Http\Controllers\BarterInvoiceController;
use App\Http\Controllers\BarterReviewController;
use App\Http\Controllers\BarterServiceController;
use App\Http\Controllers\BarterTransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// AUTH
Route::post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

Route::middleware('auth:sanctum')->group(function () {
    // AUTH
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');

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

    // BARTER CATEGORIES
    Route::get('/barter_categories', [BarterCategoryController::class, 'index'])->name('api.barter_categories.index');

    Route::patch('/profile/{user_id}', [UserController::class, 'update'])->name('api.profile.update');
});
