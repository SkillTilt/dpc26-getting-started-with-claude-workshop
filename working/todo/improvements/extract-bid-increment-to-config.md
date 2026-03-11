# Proposal: Move hardcoded bid increment to config

## Current State

`BidController` contains a hardcoded minimum bid increment:

```php
$minIncrement = 1.00;
```

This value is buried in controller logic and cannot be changed without a code deploy. It also can't vary per environment (e.g., using a smaller increment in testing) or be overridden at runtime.

## Proposal

Move the minimum increment to Laravel's config system:

```php
// config/bidboard.php
return [
    'min_bid_increment' => env('BID_MIN_INCREMENT', 1.00),
];

// In BidController
$minIncrement = config('bidboard.min_bid_increment');
```

## Benefits

- Configurable per environment via `.env`
- Central config file documents all application-specific settings
- Easy to find and change without digging through controller code
- Could later be extended to per-category or per-item increments by reading from the database instead

## Suggested Approach

1. Create `config/bidboard.php` with the increment setting
2. Add `BID_MIN_INCREMENT=1.00` to `.env.example`
3. Replace the hardcoded value in `BidController` with `config('bidboard.min_bid_increment')`
4. Update any tests that depend on the increment value to set it via config
