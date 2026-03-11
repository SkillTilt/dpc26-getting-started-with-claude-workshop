<?php

namespace App\Listeners;

use App\Events\BidPlaced;
use App\Notifications\OutbidNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOutbidNotification implements ShouldQueue
{
    public function handle(BidPlaced $event): void
    {
        // No previous bidder means this is the first bid — nothing to notify
        if (! $event->previousBidder) {
            return;
        }

        // Don't notify if the bidder outbid themselves
        if ($event->previousBidder->id === $event->bid->user_id) {
            return;
        }

        $event->previousBidder->notify(new OutbidNotification($event->bid));
    }
}
