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
}
