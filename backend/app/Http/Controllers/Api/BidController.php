<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BidResource;
use App\Models\Bid;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BidController extends Controller
{
    /**
     * Place a bid on an item.
     *
     * This handles all the bid validation, placement, and post-bid logic
     * including checking if the auction should auto-close.
     */
    public function store(Request $request, int $itemId): JsonResponse
    {
        // manually look up the item instead of using route model binding
        $item = Item::findOrFail($itemId);

        // check that the auction is still active
        if ($item->status !== 'active') {
            return response()->json([
                'error' => 'This auction is no longer active.',
            ], 422);
        }

        // check if the auction has expired
        if ($item->ends_at < now()) {
            return response()->json([
                'error' => 'This auction has already ended.',
            ], 422);
        }

        // make sure the seller can't bid on their own item
        if ($item->seller_id === auth()->id()) {
            return response()->json([
                'error' => 'You cannot bid on your own item.',
            ], 403);
        }

        // validate the bid amount is present
        if (! $request->has('amount') || ! is_numeric($request->amount)) {
            return response()->json([
                'error' => 'A valid bid amount is required.',
            ], 422);
        }

        $amount = (float) $request->amount;

        if ($amount < $item->current_price) {
            return response()->json([
                'error' => 'Your bid must be higher than the current price of $' . number_format($item->current_price, 2) . '.',
            ], 422);
        }

        $minIncrement = 1.00;

        // check that the bid meets the minimum increment above current price
        if ($amount < ($item->current_price + $minIncrement)) {
            return response()->json([
                'error' => 'Bid must be at least $' . number_format($minIncrement, 2) . ' more than the current price.',
            ], 422);
        }

        // create the bid record
        $bid = Bid::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'amount' => $amount,
        ]);

        // update the item's current price
        $item->current_price = $amount;
        $item->save();

        // log that the previous high bidder should be notified
        $previousHighBid = $item->bids()
            ->where('id', '!=', $bid->id)
            ->orderBy('amount', 'desc')
            ->first();

        if ($previousHighBid) {
            Log::info("User {$item->bids()->latest()->first()->user->name} should be notified they were outbid");
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
