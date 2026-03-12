<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BidTest extends TestCase
{
    use RefreshDatabase;

    public function test_bid_must_be_higher_than_current_price()
    {
        $item = Item::factory()->create([
            'starting_price' => 100,
            'current_price' => 100,
            'status' => 'active',
            'ends_at' => now()->addDays(1),
        ]);

        $user = User::find(1);

        $response = $this->actingAs($user)
            ->postJson("/api/items/{$item->id}/bids", [
                'amount' => 50,
            ]);

        $response->assertStatus(422);
    }

    public function test_seller_cannot_bid_on_own_item()
    {
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'starting_price' => 100,
            'current_price' => 100,
            'status' => 'active',
            'ends_at' => now()->addDays(1),
        ]);

        $response = $this->actingAs($seller)
            ->postJson("/api/items/{$item->id}/bids", [
                'amount' => 150,
            ]);

        $response->assertStatus(403);
    }

    public function test_bid_on_ended_auction_is_rejected()
    {
        $seller = User::factory()->create();
        $bidder = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'starting_price' => 100,
            'current_price' => 100,
            'status' => 'active',
            'ends_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($bidder)
            ->postJson("/api/items/{$item->id}/bids", [
                'amount' => 150,
            ]);

        $response->assertStatus(422);
    }
}
