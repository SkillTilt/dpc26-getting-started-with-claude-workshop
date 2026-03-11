# Proposal: Extract BidService from BidController@store

## Current State

`BidController@store` is approximately 120 lines of inline logic handling:

- Input validation
- Price and increment checks
- Bid creation
- Item price update
- Auto-close logic when buy-now price is reached
- Notification dispatch (outbid notifications, seller notifications)

This makes the controller hard to test in isolation and violates single responsibility. Any change to bidding logic (e.g., adding a snipe-protection window) requires editing a deeply nested controller method.

## Proposal

Extract a `BidService` class (`app/Services/BidService.php`) that encapsulates bidding domain logic:

```
BidService
  - placeBid(Item $item, User $user, float $amount): Bid
  - validateBidAmount(Item $item, float $amount): void
  - handleAutoClose(Item $item, Bid $bid): void
  - notifyParties(Bid $bid): void
```

The controller would become a thin HTTP adapter:

```php
public function store(StoreBidRequest $request, Item $item): BidResource
{
    $bid = $this->bidService->placeBid($item, $request->user(), $request->amount);
    return new BidResource($bid);
}
```

## Benefits

- Business logic becomes testable without HTTP layer
- Easier to add new bid-related features (snipe protection, bid retraction, proxy bidding)
- Notification logic can be moved to model events or listeners later
- Controller stays under 20 lines

## Suggested Approach

1. Create `app/Services/BidService.php`
2. Move validation, creation, auto-close, and notification logic into service methods
3. Inject `BidService` into `BidController` via constructor
4. Update existing tests to cover the service directly
5. Keep controller tests as integration/HTTP tests
