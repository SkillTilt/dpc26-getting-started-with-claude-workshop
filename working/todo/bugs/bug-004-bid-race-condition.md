# Bug #4: Race condition in BidController — no transaction or row locking

**Issue:** [#4](https://github.com/SkillTilt/dpc26-getting-started-with-claude-workshop/issues/4)
**Severity:** High
**Status:** Open

## Description

`BidController@store` reads the current highest bid, validates against it, creates a new bid, and updates the item's current price in separate operations with no database transaction or row-level locking. Two concurrent requests can both pass the price validation check before either writes, resulting in both bids being accepted even though only one should win.

## Technical Details

In `backend/app/Http/Controllers/Api/BidController.php` lines 74-87, the flow is:

1. Read current price from item
2. Validate that new bid > current price
3. Create bid record
4. Update item's current_price

Steps 1-4 are not wrapped in `DB::transaction()` and there is no `lockForUpdate()` on the item read. Under concurrent load, two requests can both complete step 2 before either reaches step 3.

## Reproduction Scenario

1. Item has current_price of $100
2. User A submits bid for $105 — passes validation (105 > 100)
3. User B submits bid for $103 — also passes validation (103 > 100) because User A's bid hasn't been written yet
4. Both bids are created. Item's final current_price depends on which UPDATE runs last, not which bid is highest.

## Expected Fix

Wrap the read-validate-create-update sequence in a `DB::transaction()` and use `lockForUpdate()` when reading the item's current price so the second request blocks until the first completes.
