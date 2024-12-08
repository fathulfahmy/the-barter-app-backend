<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarterCategoryController;
use App\Http\Controllers\BarterInvoiceController;
use App\Http\Controllers\BarterReviewController;
use App\Http\Controllers\BarterServiceController;
use App\Http\Controllers\BarterTransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');

    Route::get('/barter_services/acquire', [BarterServiceController::class, 'acquire'])->name('api.barter_services.acquire');
    Route::get('/barter_services/provide', [BarterServiceController::class, 'provide'])->name('api.barter_services.provide');
    Route::get('/barter_services/{barter_service_id}', [BarterServiceController::class, 'show'])->name('api.barter_services.show');
    Route::post('/barter_services', [BarterServiceController::class, 'store'])->name('api.barter_services.store');
    Route::put('/barter_services{/barter_service_id}', [BarterServiceController::class, 'update'])->name('api.barter_services.update');
    Route::delete('/barter_services/{barter_service_id}', [BarterServiceController::class, 'destroy'])->name('api.barter_services.destroy');

    Route::get('/barter_transactions/{barter_service_id}/requests', [BarterTransactionController::class, 'requests'])->name('api.barter_transactions.requests');
    Route::get('/barter_transactions/{barter_service_id}/records', [BarterTransactionController::class, 'records'])->name('api.barter_transactions.records');
    Route::get('/barter_transactions/{barter_transaction_id}', [BarterTransactionController::class, 'show'])->name('api.barter_transactions.show');
    Route::post('/barter_transactions', [BarterTransactionController::class, 'store'])->name('api.barter_transactions.store');
    Route::put('/barter_transactions/{barter_transaction_id}', [BarterTransactionController::class, 'update'])->name('api.barter_transactions.update');
    Route::delete('/barter_transactions/{barter_transaction_id}', [BarterTransactionController::class, 'destroy'])->name('api.barter_transactions.destroy');

    Route::get('/barter_invoices', [BarterInvoiceController::class, 'index'])->name('api.barter_invoices.index');
    Route::get('/barter_invoices/{barter_invoice_id}', [BarterInvoiceController::class, 'show'])->name('api.barter_invoices.show');
    Route::post('/barter_invoices', [BarterInvoiceController::class, 'store'])->name('api.barter_invoices.store');
    Route::put('/barter_invoices/{barter_invoice_id}', [BarterInvoiceController::class, 'update'])->name('api.barter_invoices.update');
    Route::delete('/barter_invoices/{barter_invoice_id}', [BarterInvoiceController::class, 'destroy'])->name('api.barter_invoices.destroy');

    Route::get('/barter_reviews', [BarterReviewController::class, 'index'])->name('api.barter_reviews.index');
    Route::get('/barter_reviews/{barter_review_id}', [BarterReviewController::class, 'show'])->name('api.barter_reviews.show');
    Route::post('/barter_reviews', [BarterReviewController::class, 'store'])->name('api.barter_reviews.store');
    Route::put('/barter_reviews/{barter_review_id}', [BarterReviewController::class, 'update'])->name('api.barter_reviews.update');
    Route::delete('/barter_reviews/{barter_review_id}', [BarterReviewController::class, 'destroy'])->name('api.barter_reviews.destroy');

    Route::get('/barter_categories', [BarterCategoryController::class, 'index'])->name('api.barter_categories.index');
    Route::get('/barter_categories/names', [BarterCategoryController::class, 'names'])->name('api.barter_categories.names');
});
