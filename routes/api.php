<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarterCategoryController;
use App\Http\Controllers\BarterInvoiceController;
use App\Http\Controllers\BarterReviewController;
use App\Http\Controllers\BarterServiceController;
use App\Http\Controllers\BarterTransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])
        ->name('api.auth.register');

    Route::post('login', [AuthController::class, 'login'])
        ->name('api.auth.login');

    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('api.auth.logout');

    Route::get('me', [AuthController::class, 'me'])
        ->middleware('auth:sanctum')
        ->name('api.auth.me');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('barter_services')->group(function () {
        Route::get('acquire', [BarterServiceController::class, 'acquire'])->name('api.barter_services.acquire');
        Route::get('provide', [BarterServiceController::class, 'provide'])->name('api.barter_services.provide');
        Route::get('{id}', [BarterServiceController::class, 'show'])->name('api.barter_services.show');
        Route::post('', [BarterServiceController::class, 'store'])->name('api.barter_services.store');
        Route::put('{id}', [BarterServiceController::class, 'update'])->name('api.barter_services.update');
        Route::delete('{id}', [BarterServiceController::class, 'destroy'])->name('api.barter_services.destroy');
    });

    Route::prefix('barter_transactions')->group(function () {
        Route::get('requests', [BarterTransactionController::class, 'requests'])->name('api.barter_transactions.requests');
        Route::get('records', [BarterTransactionController::class, 'records'])->name('api.barter_transactions.records');
        Route::get('{id}', [BarterTransactionController::class, 'show'])->name('api.barter_transactions.show');
        Route::post('', [BarterTransactionController::class, 'store'])->name('api.barter_transactions.store');
        Route::put('{id}', [BarterTransactionController::class, 'update'])->name('api.barter_transactions.update');
        Route::delete('{id}', [BarterTransactionController::class, 'destroy'])->name('api.barter_transactions.destroy');
    });

    Route::prefix('barter_invoices')->group(function () {
        Route::get('', [BarterInvoiceController::class, 'index'])->name('api.barter_invoices.index');
        Route::get('{id}', [BarterInvoiceController::class, 'show'])->name('api.barter_invoices.show');
        Route::post('', [BarterInvoiceController::class, 'store'])->name('api.barter_invoices.store');
        Route::put('{id}', [BarterInvoiceController::class, 'update'])->name('api.barter_invoices.update');
        Route::delete('{id}', [BarterInvoiceController::class, 'destroy'])->name('api.barter_invoices.destroy');
    });

    Route::prefix('barter_reviews')->group(function () {
        Route::get('', [BarterReviewController::class, 'index'])->name('api.barter_reviews.index');
        Route::get('{id}', [BarterReviewController::class, 'show'])->name('api.barter_reviews.show');
        Route::post('', [BarterReviewController::class, 'store'])->name('api.barter_reviews.store');
        Route::put('{id}', [BarterReviewController::class, 'update'])->name('api.barter_reviews.update');
        Route::delete('{id}', [BarterReviewController::class, 'destroy'])->name('api.barter_reviews.destroy');
    });

    Route::prefix('barter_categories')->group(function () {
        Route::get('', [BarterCategoryController::class, 'index'])->name('api.barter_categories.index');
        Route::get('names', [BarterCategoryController::class, 'names'])->name('api.barter_categories.names');
    });
});