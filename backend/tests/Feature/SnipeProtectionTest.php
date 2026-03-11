<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SnipeProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_bid_within_30_seconds_extends_auction_by_2_minutes()
    {
        $seller = User::factory()->create();
        $bidder = User::factory()->create();

        $endsAt = now()->addSeconds(20);

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'starting_price' => 100,
            'current_price' => 100,
            'status' => 'active',
            'ends_at' => $endsAt,
        ]);

        $response = $this->actingAs($bidder)
            ->postJson("/api/items/{$item->id}/bids", [
                'amount' => 150,
            ]);

        $response->assertStatus(201);

        $item->refresh();
        $this->assertTrue(
            $item->ends_at->greaterThan($endsAt),
            'Auction end time should have been extended'
        );
        $this->assertTrue(
            $item->ends_at->diffInSeconds(now(), false) < 0,
            'Extended auction should still be in the future'
        );
    }

    public function test_bid_with_plenty_of_time_does_not_extend_auction()
    {
        $seller = User::factory()->create();
        $bidder = User::factory()->create();

        $endsAt = now()->addHours(2);

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'starting_price' => 100,
            'current_price' => 100,
            'status' => 'active',
            'ends_at' => $endsAt,
        ]);

        $response = $this->actingAs($bidder)
            ->postJson("/api/items/{$item->id}/bids", [
                'amount' => 150,
            ]);

        $response->assertStatus(201);

        $item->refresh();
        $this->assertTrue(
            abs($item->ends_at->diffInSeconds($endsAt)) < 2,
            'Auction end time should not have been changed'
        );
    }
}
