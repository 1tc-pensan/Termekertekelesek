<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;

// Auth routes (nyilvános)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Product routes (nyilvános olvasás)
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// Review routes (nyilvános olvasás)
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

// Védett route-ok (autentikáció szükséges)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Reviews írás/módosítás/törlés (sima userek)
    Route::apiResource('reviews', ReviewController::class)->except(['index', 'show']);

    // Admin route-ok (csak admin jogosultság)
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('products', AdminProductController::class);
        Route::apiResource('reviews', AdminReviewController::class);
    });
});

// Products írás/módosítás/törlés (CSAK admin)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
});

// Termékhez tartozó értékelések
Route::get('products/{id}/reviews', function ($id) {
    $product = \App\Models\Products::with('reviews.user')->findOrFail($id);
    return response()->json($product->reviews);
});
