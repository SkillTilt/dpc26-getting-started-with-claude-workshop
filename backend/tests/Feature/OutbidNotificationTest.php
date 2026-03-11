<?php

namespace Tests\Feature;

use App\Events\BidPlaced;
use App\Listeners\SendOutbidNotification;
use App\Models\Bid;
use App\Models\Item;
use App\Models\User;
use App\Notifications\OutbidNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OutbidNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_placing_a_bid_dispatches_bid_placed_event()
    {
        Event::fake([BidPlaced::class]);

        $item = Item::factory()->create([
            'starting_price' => 100,
            'current_price' => 100,
            'status' => 'active',
            'ends_at' => now()->addDays(1),
        ]);

        $bidder = User::factory()->create();

        $this->actingAs($bidder)
            ->postJson("/api/items/{$item->id}/bids", ['amount' => 150]);

        Event::assertDispatched(BidPlaced::class);
    }

    public function test_listener_sends_notification_to_previous_bidder()
    {
        Notification::fake();

        $item = Item::factory()->create([
            'starting_price' => 100,
            'current_price' => 200,
            'status' => 'active',
            'ends_at' => now()->addDays(1),
        ]);

        $previousBidder = User::factory()->create();
        $currentBidder = User::factory()->create();

        $bid = Bid::factory()->create([
            'item_id' => $item->id,
            'user_id' => $currentBidder->id,
            'amount' => 200,
        ]);

        $event = new BidPlaced($bid, $previousBidder);
        $listener = new SendOutbidNotification();
        $listener->handle($event);

        Notification::assertSentTo($previousBidder, OutbidNotification::class);
    }

    public function test_no_notification_if_bidder_outbids_themselves()
    {
        Notification::fake();

        $item = Item::factory()->create([
            'starting_price' => 100,
            'current_price' => 200,
            'status' => 'active',
            'ends_at' => now()->addDays(1),
        ]);

        $sameBidder = User::factory()->create();

        $bid = Bid::factory()->create([
            'item_id' => $item->id,
            'user_id' => $sameBidder->id,
            'amount' => 200,
        ]);

        $event = new BidPlaced($bid, $sameBidder);
        $listener = new SendOutbidNotification();
        $listener->handle($event);

        Notification::assertNothingSent();
    }

    public function test_no_notification_on_first_bid()
    {
        Notification::fake();

        $item = Item::factory()->create([
            'starting_price' => 100,
            'current_price' => 200,
            'status' => 'active',
            'ends_at' => now()->addDays(1),
        ]);

        $bidder = User::factory()->create();

        $bid = Bid::factory()->create([
            'item_id' => $item->id,
            'user_id' => $bidder->id,
            'amount' => 200,
        ]);

        $event = new BidPlaced($bid, null);
        $listener = new SendOutbidNotification();
        $listener->handle($event);

        Notification::assertNothingSent();
    }
}
