<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;

// ==========================================
// NYILVÁNOS VÉGPONTOK (Public - NO AUTH)
// ==========================================

// Auth routes (CSAK ezek nyilvánosak)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// ==========================================
// VÉDETT VÉGPONTOK (AUTH REQUIRED)
// ==========================================

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Products - olvasás (autentikált felhasználók)
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    
    // Termékhez tartozó értékelések (autentikált felhasználók)
    Route::get('products/{id}/reviews', function ($id) {
        $product = \App\Models\Products::with('reviews.user')->findOrFail($id);
        return response()->json($product->reviews);
    });

    // Reviews - olvasás (autentikált felhasználók)
    Route::get('reviews', [ReviewController::class, 'index']);
    Route::get('reviews/{id}', [ReviewController::class, 'show']);

    // Reviews - írás/módosítás/törlés (autentikált felhasználók)
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::put('reviews/{id}', [ReviewController::class, 'update']);
    Route::patch('reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);

    // ==========================================
    // ADMIN VÉGPONTOK (Admin Only)
    // ==========================================
    
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('products', AdminProductController::class);
        Route::apiResource('reviews', AdminReviewController::class);
    });

    // Products - írás/módosítás/törlés (CSAK admin)
    Route::middleware('admin')->group(function () {
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{id}', [ProductController::class, 'update']);
        Route::patch('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);
    });
});
