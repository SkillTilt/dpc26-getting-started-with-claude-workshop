<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * List all categories with item counts.
     */
    public function index()
    {
        $categories = Category::withCount('items')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Get items for a specific category, filtered by status.
     */
    public function items(Category $category, Request $request): JsonResponse
    {
        $status = $request->query('status', 'active');

        $items = $category->items()->where('status', $status)->paginate(12);

        $items->through(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'image_url' => $item->image_url ? Storage::disk('public')->url($item->image_url) : null,
                'current_price' => $item->current_price,
                'bids_count' => $item->bids->count(),
                'seller_name' => $item->seller->name,
                'ends_at' => $item->ends_at,
            ];
        });

        return response()->json($items);
    }
}
