<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    /**
     * Display a single item with its relationships.
     */
    public function show(Item $item): ItemResource
    {
        $item->load(['bids.user', 'seller', 'category']);

        return new ItemResource($item);
    }

    /**
     * Store a newly created item.
     */
    public function store(StoreItemRequest $request): JsonResponse
    {
        // Magic number: hardcoded max duration
        if ($request->duration > 7) {
            return response()->json([
                'errors' => [
                    'duration' => ['Maximum auction duration is 7 days'],
                ],
            ], 422);
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $request->file('image')->store('items', 'public');
        }

        $item = Item::create([
            'title' => $request->title,
            'description' => $request->description,
            'starting_price' => $request->starting_price,
            'current_price' => $request->starting_price,
            'seller_id' => auth()->id(),
            'category_id' => $request->category_id,
            'status' => 'active',
            'ends_at' => now()->addDays($request->duration),
            'image_url' => $imageUrl,
        ]);

        return response()->json(new ItemResource($item), 201);
    }
}
