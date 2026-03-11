# The countdown timer shows the wrong time remaining

## Description

I listed an item that ends at 8:00 PM tonight (my local time, CET). But the countdown timer on the item detail page shows a completely wrong number of hours remaining. It seems to be off by exactly my timezone offset from UTC. My friend in the UK says it looks correct for him.

## Steps to Reproduce

1. Create a listing that ends at a specific time you can easily verify
2. Open the item detail page and look at the countdown timer
3. Compare the countdown to the actual time remaining

## Expected Behavior

The countdown should show the correct time remaining until the auction ends, regardless of which timezone I'm in.

## Actual Behavior

The countdown is off by my UTC offset. I'm in CET (UTC+1) and the timer shows 1 hour less than it should. During summer time (CEST, UTC+2) it would be off by 2 hours. For someone in New York (UTC-5) it would show 5 hours too many.

## Where to Look

- `frontend/src/components/CountdownTimer.vue` line 17
- The component does `new Date(props.endsAt)` but the server sends the timestamp in UTC without a `Z` suffix or timezone indicator
- JavaScript's `new Date()` parses strings without timezone info as local time, not UTC
- So if the server says "2026-03-11 20:00:00" meaning 8 PM UTC, a browser in CET interprets it as 8 PM CET (which is 7 PM UTC), making the countdown 1 hour short

## Environment

- Reproduced in Chrome and Firefox
- Only affects users not in UTC timezone
- Backend sends timestamps from Laravel which formats as `Y-m-d H:i:s` (no timezone suffix)
