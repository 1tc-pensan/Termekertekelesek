<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Product routes
Route::apiResource('products', ProductController::class);

// Review routes
Route::apiResource('reviews', ReviewController::class);

// Termékhez tartozó értékelések
Route::get('products/{id}/reviews', function ($id) {
    $product = \App\Models\Products::with('reviews.user')->findOrFail($id);
    return response()->json($product->reviews);
});
