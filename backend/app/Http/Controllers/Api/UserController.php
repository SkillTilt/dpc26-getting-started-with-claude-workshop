<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(): JsonResponse
    {
        return response()->json([
            'data' => auth()->user(),
        ]);
    }

    /**
     * Get the authenticated user's listings (active and sold).
     */
    public function listings(): JsonResponse
    {
        $user = auth()->user();

        // Active listings use Eloquent — clean and consistent
        $active = ItemResource::collection(
            $user->listings()->where('status', 'active')->get()
        );

        // Sold items use a raw DB query with a JOIN — inconsistent with active listings
        $sold = DB::select('
            SELECT items.id, items.title, items.current_price AS final_price,
                   items.ends_at AS sold_at, users.name AS buyer_name
            FROM items
            INNER JOIN users ON users.id = items.winner_id
            WHERE items.seller_id = ?
            AND items.status = ?
        ', [$user->id, 'closed']);

        return response()->json([
            'active' => $active,
            'sold' => $sold,
        ]);
    }

    /**
     * Get the authenticated user's bids grouped by status.
     */
    public function bids(): JsonResponse
    {
        $user = auth()->user();

        // Bids on items that are still active and where this user has the highest bid
        $winning = $user->bids()
            ->whereHas('item', function ($query) {
                $query->where('status', 'active');
            })
            ->whereRaw('amount = (SELECT MAX(amount) FROM bids AS b2 WHERE b2.item_id = bids.item_id)')
            ->with('item')
            ->get();

        // Bids on items that are sold and this user won
        $won = $user->bids()
            ->whereHas('item', function ($query) use ($user) {
                $query->where('status', 'closed')
                    ->where('winner_id', $user->id);
            })
            ->with('item')
            ->get();

        // Bids on items that are sold and this user did NOT win
        $lost = $user->bids()
            ->whereHas('item', function ($query) use ($user) {
                $query->where('status', 'closed')
                    ->where('winner_id', '!=', $user->id);
            })
            ->with('item')
            ->get();

        return response()->json([
            'winning' => $winning,
            'won' => $won,
            'lost' => $lost,
        ]);
    }
}
