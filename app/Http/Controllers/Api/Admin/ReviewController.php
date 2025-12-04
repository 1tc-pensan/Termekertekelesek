<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reviews;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Reviews::with(['user', 'product'])->paginate(20);
        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Reviews::create($validated);
        return response()->json($review->load(['user', 'product']), 201);
    }

    public function show(string $id)
    {
        $review = Reviews::with(['user', 'product'])->findOrFail($id);
        return response()->json($review);
    }

    public function update(Request $request, string $id)
    {
        $review = Reviews::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);
        return response()->json($review->load(['user', 'product']));
    }

    public function destroy(string $id)
    {
        $review = Reviews::findOrFail($id);
        $review->delete();
        return response()->json(null, 204);
    }
}
