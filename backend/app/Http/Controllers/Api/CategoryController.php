<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryItemsRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ItemResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
     * Get active items for a specific category, paginated and sorted by ending soonest.
     */
    public function items(Category $category, CategoryItemsRequest $request): AnonymousResourceCollection
    {
        $items = $category->items()
            ->active()
            ->withCount('bids')
            ->with('seller')
            ->orderBy('ends_at')
            ->paginate(12);

        return ItemResource::collection($items);
    }
}
