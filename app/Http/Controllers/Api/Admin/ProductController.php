<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Products::with('reviews')->paginate(20);
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Products::create($validated);
        return response()->json($product, 201);
    }

    public function show(string $id)
    {
        $product = Products::with('reviews.user')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, string $id)
    {
        $product = Products::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy(string $id)
    {
        $product = Products::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product soft deleted successfully'], 200);
    }

    public function trashed()
    {
        $products = Products::onlyTrashed()->with('reviews')->paginate(20);
        return response()->json($products);
    }

    public function restore(string $id)
    {
        $product = Products::withTrashed()->findOrFail($id);
        $product->restore();
        return response()->json(['message' => 'Product restored successfully', 'product' => $product], 200);
    }

    public function forceDestroy(string $id)
    {
        $product = Products::withTrashed()->findOrFail($id);
        $product->forceDelete();
        return response()->json(['message' => 'Product permanently deleted'], 200);
    }
}
