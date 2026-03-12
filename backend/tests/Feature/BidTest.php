<?php

namespace Tests\Feature;

use App\Events\BidPlaced;
use App\Models\Bid;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
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

    public function test_no_outbid_notification_when_auction_auto_closes()
    {
        Event::fake([BidPlaced::class]);

        $seller = User::factory()->create();
        $previousBidder = User::factory()->create();
        $winningBidder = User::factory()->create();

        // Auction ending in 45 seconds — outside snipe protection window (30s)
        // but inside auto-close window (1 min), so it will auto-close
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'starting_price' => 100,
            'current_price' => 150,
            'status' => 'active',
            'ends_at' => now()->addSeconds(45),
        ]);

        // Existing bid from previous bidder
        Bid::factory()->create([
            'item_id' => $item->id,
            'user_id' => $previousBidder->id,
            'amount' => 150,
        ]);

        // Winning bid that triggers auto-close
        $response = $this->actingAs($winningBidder)
            ->postJson("/api/items/{$item->id}/bids", [
                'amount' => 200,
            ]);

        $response->assertOk();
        $response->assertJson(['item_status' => 'sold']);

        // BidPlaced should NOT have been dispatched — auction closed
        Event::assertNotDispatched(BidPlaced::class);
    }

    public function test_outbid_notification_sent_when_auction_still_active()
    {
        Event::fake([BidPlaced::class]);

        $seller = User::factory()->create();
        $previousBidder = User::factory()->create();
        $newBidder = User::factory()->create();

        // Auction with plenty of time left — won't auto-close
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'starting_price' => 100,
            'current_price' => 150,
            'status' => 'active',
            'ends_at' => now()->addDays(1),
        ]);

        Bid::factory()->create([
            'item_id' => $item->id,
            'user_id' => $previousBidder->id,
            'amount' => 150,
        ]);

        $response = $this->actingAs($newBidder)
            ->postJson("/api/items/{$item->id}/bids", [
                'amount' => 200,
            ]);

        $response->assertStatus(201);

        // BidPlaced SHOULD be dispatched with the previous bidder
        Event::assertDispatched(BidPlaced::class, function (BidPlaced $event) use ($previousBidder) {
            return $event->previousBidder?->id === $previousBidder->id;
        });
    }
}
