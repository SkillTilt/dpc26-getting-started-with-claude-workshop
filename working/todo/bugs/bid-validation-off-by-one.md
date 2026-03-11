# Off-by-one in bid price validation -- allows bids equal to current price

## Description

`BidController@store` uses a strict less-than (`<`) comparison instead of less-than-or-equal (`<=`) when validating the incoming bid amount against the current price. This allows a user to submit a bid that is exactly equal to the current highest bid, which should be rejected.

## Location

`backend/app/Http/Controllers/Api/BidController.php` line 58

The validation check reads something like:

```php
if ($request->amount < $item->current_price) {
```

This rejects bids that are *lower* than the current price, but accepts bids that are *equal* to it.

## Expected Behavior

A bid must be strictly greater than the current price to be accepted. A bid of $100 on an item whose current price is $100 should be rejected.

## Actual Behavior

A bid of $100 on a $100 item passes validation and is recorded as a new highest bid, even though it doesn't actually outbid anyone.

## Suggested Fix

Change `<` to `<=`:

```php
if ($request->amount <= $item->current_price) {
```

## Severity

Medium -- does not cause data corruption but breaks the fundamental auction rule that each bid must exceed the previous one. Combined with the minimum increment logic, this may or may not be reachable depending on how the frontend constrains input.
