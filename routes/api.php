<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AuthController;

// Auth routes (nyilvános)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Védett route-ok (autentikáció szükséges)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Product routes
Route::apiResource('products', ProductController::class);

// Review routes
Route::apiResource('reviews', ReviewController::class);

// Termékhez tartozó értékelések
Route::get('products/{id}/reviews', function ($id) {
    $product = \App\Models\Products::with('reviews.user')->findOrFail($id);
    return response()->json($product->reviews);
});
