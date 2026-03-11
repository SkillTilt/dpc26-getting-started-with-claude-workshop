<?php

namespace Database\Seeders;

use App\Models\Bid;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BidSeeder extends Seeder
{
    // User IDs: Alice=1, Bob=2, Clara=3, Dave=4, Eve=5

    /**
     * Seed the bids table.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Item 1: Sony Headphones, starting:80, current:145, 6 bids, ends_at:now+2days
        // Bob×3, Clara×2, Dave×1
        $this->createBids(
            itemId: 1,
            startingPrice: 80.00,
            currentPrice: 145.00,
            bidders: [2, 3, 2, 4, 3, 2], // Bob, Clara, Bob, Dave, Clara, Bob
            auctionStart: $now->copy()->subDays(5),
            auctionEnd: $now->copy()->addDays(2),
        );

        // Item 2: iPad Air, starting:200, current:340, 7 bids, ends_at:now+4days
        // Bob×3, Eve×2, Clara×2
        $this->createBids(
            itemId: 2,
            startingPrice: 200.00,
            currentPrice: 340.00,
            bidders: [2, 5, 3, 2, 5, 3, 2], // Bob, Eve, Clara, Bob, Eve, Clara, Bob
            auctionStart: $now->copy()->subDays(3),
            auctionEnd: $now->copy()->addDays(4),
        );

        // Item 3: Mechanical Keyboard — 0 bids

        // Item 4: Samsung Galaxy, starting:50, current:92, 4 bids, ends_at:now-1day, winner:Bob(2)
        // Bob×2, Clara×1, Eve×1 — Bob won (last bid must be Bob)
        $this->createBids(
            itemId: 4,
            startingPrice: 50.00,
            currentPrice: 92.00,
            bidders: [3, 5, 2, 2], // Clara, Eve, Bob, Bob
            auctionStart: $now->copy()->subDays(5),
            auctionEnd: $now->copy()->subDay(),
        );

        // Item 5: Vintage Polaroid, starting:120, current:185, 5 bids, ends_at:now+3days
        // Bob×2, Eve×2, Dave×1
        $this->createBids(
            itemId: 5,
            startingPrice: 120.00,
            currentPrice: 185.00,
            bidders: [2, 5, 4, 2, 5], // Bob, Eve, Dave, Bob, Eve
            auctionStart: $now->copy()->subDays(4),
            auctionEnd: $now->copy()->addDays(3),
        );

        // Item 6: Brass Desk Lamp, starting:25, current:47, 3 bids, ends_at:now+1day
        // Bob×2, Eve×1
        $this->createBids(
            itemId: 6,
            startingPrice: 25.00,
            currentPrice: 47.00,
            bidders: [2, 5, 2], // Bob, Eve, Bob
            auctionStart: $now->copy()->subDays(3),
            auctionEnd: $now->copy()->addDay(),
        );

        // Item 7: Neuromancer, starting:150, current:280, 8 bids, ends_at:now-3days, winner:Bob(2)
        // Bob×3, Clara×3, Eve×2 — Bob won (last bid must be Bob)
        $this->createBids(
            itemId: 7,
            startingPrice: 150.00,
            currentPrice: 280.00,
            bidders: [3, 5, 2, 3, 5, 3, 2, 2], // Clara, Eve, Bob, Clara, Eve, Clara, Bob, Bob
            auctionStart: $now->copy()->subDays(10),
            auctionEnd: $now->copy()->subDays(3),
        );

        // Item 8: Pocket Watch, starting:30, current:55, 3 bids, ends_at:now-5days, winner:Clara(3)
        // Clara×2, Alice×1 — Clara won (last bid must be Clara)
        $this->createBids(
            itemId: 8,
            startingPrice: 30.00,
            currentPrice: 55.00,
            bidders: [1, 3, 3], // Alice, Clara, Clara
            auctionStart: $now->copy()->subDays(10),
            auctionEnd: $now->copy()->subDays(5),
        );

        // Item 9: Trek Road Bike, starting:300, current:475, 5 bids, ends_at:now+5days
        // Alice×2, Eve×2, Clara×1
        $this->createBids(
            itemId: 9,
            startingPrice: 300.00,
            currentPrice: 475.00,
            bidders: [1, 5, 3, 1, 5], // Alice, Eve, Clara, Alice, Eve
            auctionStart: $now->copy()->subDays(3),
            auctionEnd: $now->copy()->addDays(5),
        );

        // Item 10: Yeti Cooler, starting:100, current:155, 4 bids, ends_at:now+12hours
        // Bob×2, Clara×1, Dave×1
        $this->createBids(
            itemId: 10,
            startingPrice: 100.00,
            currentPrice: 155.00,
            bidders: [2, 3, 4, 2], // Bob, Clara, Dave, Bob
            auctionStart: $now->copy()->subDays(4),
            auctionEnd: $now->copy()->addHours(12),
        );

        // Item 11: Patagonia Jacket, starting:60, current:88, 3 bids, ends_at:now-2days, winner:Alice(1)
        // Alice×2, Clara×1 — Alice won (last bid must be Alice)
        $this->createBids(
            itemId: 11,
            startingPrice: 60.00,
            currentPrice: 88.00,
            bidders: [1, 3, 1], // Alice, Clara, Alice
            auctionStart: $now->copy()->subDays(7),
            auctionEnd: $now->copy()->subDays(2),
        );

        // Item 12: Tennis Racket — 0 bids

        // Item 13: Le Creuset, starting:90, current:142, 4 bids, ends_at:now+2days
        // Bob×2, Eve×1, Clara×1
        $this->createBids(
            itemId: 13,
            startingPrice: 90.00,
            currentPrice: 142.00,
            bidders: [2, 5, 3, 2], // Bob, Eve, Clara, Bob
            auctionStart: $now->copy()->subDays(4),
            auctionEnd: $now->copy()->addDays(2),
        );

        // Item 14: Dyson V15, starting:150, current:210, 5 bids, ends_at:now-4days, winner:Eve(5)
        // Eve×2, Alice×2, Bob×1 — Eve won (last bid must be Eve)
        $this->createBids(
            itemId: 14,
            startingPrice: 150.00,
            currentPrice: 210.00,
            bidders: [1, 2, 5, 1, 5], // Alice, Bob, Eve, Alice, Eve
            auctionStart: $now->copy()->subDays(10),
            auctionEnd: $now->copy()->subDays(4),
        );

        // Item 15: Bonsai Kit — 0 bids
    }

    /**
     * Create a sequence of bids for an item with evenly spaced amounts and timestamps.
     *
     * @param  int    $itemId
     * @param  float  $startingPrice
     * @param  float  $currentPrice
     * @param  int[]  $bidders       Array of user IDs in bid order
     * @param  Carbon $auctionStart  When bidding started
     * @param  Carbon $auctionEnd    When the auction ends/ended
     */
    private function createBids(
        int $itemId,
        float $startingPrice,
        float $currentPrice,
        array $bidders,
        Carbon $auctionStart,
        Carbon $auctionEnd,
    ): void {
        $count = count($bidders);
        if ($count === 0) {
            return;
        }

        $totalRange = $currentPrice - $startingPrice;
        $totalSeconds = $auctionEnd->diffInSeconds($auctionStart);

        for ($i = 0; $i < $count; $i++) {
            // Evenly distribute amounts from just above starting to exactly current
            if ($count === 1) {
                $amount = $currentPrice;
            } else {
                $amount = round($startingPrice + ($totalRange * ($i + 1) / $count), 2);
            }

            // Space bids evenly across the auction lifetime, ending before auction end
            $secondsOffset = (int) ($totalSeconds * ($i + 1) / ($count + 1));
            $createdAt = $auctionStart->copy()->addSeconds($secondsOffset);

            Bid::create([
                'item_id' => $itemId,
                'user_id' => $bidders[$i],
                'amount' => $amount,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
