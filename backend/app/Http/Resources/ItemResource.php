<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_url ? Storage::disk('public')->url($this->image_url) : null,
            'starting_price' => $this->starting_price,
            'current_price' => $this->current_price,
            'ends_at' => $this->ends_at?->toIso8601String(),
            'status' => $this->status,
            'seller' => $this->whenLoaded('seller', fn () => [
                'id' => $this->seller->id,
                'name' => $this->seller->name,
            ]),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'winner' => $this->when($this->winner_id, fn () => $this->whenLoaded('winner', fn () => [
                'id' => $this->winner->id,
                'name' => $this->winner->name,
            ])),
            'bids' => $this->whenLoaded('bids', fn () => BidResource::collection($this->bids)),
            'bids_count' => $this->bids_count ?? $this->bids->count(),
            'created_at' => $this->created_at,
        ];
    }
}
