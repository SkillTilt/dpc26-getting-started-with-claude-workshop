<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;

class CloseExpiredAuctions extends Command
{
    protected $signature = 'auctions:close-expired';

    protected $description = 'Close auctions that have passed their end time and assign winners';

    public function handle(): int
    {
        $items = Item::active()
            ->where('ends_at', '<=', now())
            ->get();

        $closed = 0;

        foreach ($items as $item) {
            $highestBid = $item->bids()->orderByDesc('amount')->first();

            $item->update([
                'status' => 'closed',
                'winner_id' => $highestBid?->user_id,
            ]);

            $closed++;
        }

        $this->info("Closed {$closed} expired auction(s).");

        return self::SUCCESS;
    }
}
