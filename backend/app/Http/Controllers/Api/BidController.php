<?php

namespace App\Http\Controllers\Api;

use App\Events\BidPlaced;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceBidRequest;
use App\Http\Resources\BidResource;
use App\Models\Bid;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BidController extends Controller
{
    /**
     * Place a bid on an item.
     *
     * This handles bid placement and post-bid logic
     * including checking if the auction should auto-close.
     */
    public function store(PlaceBidRequest $request, Item $item): JsonResponse
    {
        $amount = (float) $request->validated('amount');

        // create the bid record
        $bid = Bid::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'amount' => $amount,
        ]);

        // update the item's current price
        $item->current_price = $amount;
        $item->save();

        // find the previous high bidder and dispatch the outbid event
        $previousHighBid = $item->bids()
            ->where('id', '!=', $bid->id)
            ->orderBy('amount', 'desc')
            ->first();

        BidPlaced::dispatch($bid, $previousHighBid?->user);

        // snipe protection: extend auction if bid placed within last 30 seconds
        if ($item->ends_at->diffInSeconds(now(), false) >= -30) {
            $item->ends_at = now()->addMinutes(2);
            $item->save();
        }

        // check if the auction should auto-close
        // (e.g. if someone set a "buy now" threshold or time is about to expire)
        if ($item->ends_at <= now()->addMinutes(1)) {
            // auction is about to end or has ended, close it out
            $winningBid = $item->bids()->orderBy('amount', 'desc')->first();

            if ($winningBid) {
                // update the item status and set the winner
                $item->status = 'closed';
                $item->winner_id = $winningBid->user_id;
                $item->save();

                Log::info("Auction for item {$item->id} ({$item->title}) has been auto-closed. Winner: user {$winningBid->user_id}");

                // return auction close result
                return response()->json([
                    'message' => 'Bid placed and auction closed!',
                    'winning_bid' => $winningBid->amount,
                    'item_status' => 'sold',
                ]);
            }
        }

        // return the new bid
        return response()->json(new BidResource($bid), 201);
    }
}
