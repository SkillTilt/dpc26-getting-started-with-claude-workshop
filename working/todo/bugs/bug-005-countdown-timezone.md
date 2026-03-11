# Bug #5: Countdown timer shows wrong time remaining — timezone bug

**Issue:** [#5](https://github.com/SkillTilt/dpc26-getting-started-with-claude-workshop/issues/5)
**Severity:** Frontend
**Status:** Open

## Description

The countdown timer on the item detail page shows a completely wrong number of hours remaining. It is off by exactly the user's timezone offset from UTC. Users in UTC see the correct time; everyone else sees an incorrect countdown.

## Steps to Reproduce

1. Create a listing that ends at a specific time you can easily verify
2. Open the item detail page and look at the countdown timer
3. Compare the countdown to the actual time remaining

## Expected Behavior

The countdown should show the correct time remaining until the auction ends, regardless of which timezone the user is in.

## Actual Behavior

The countdown is off by the user's UTC offset. In CET (UTC+1) the timer shows 1 hour less than it should. In New York (UTC-5) it would show 5 hours too many.

## Where to Look

- `frontend/src/components/CountdownTimer.vue` line 17
- The component does `new Date(props.endsAt)` but the server sends the timestamp in UTC without a `Z` suffix or timezone indicator
- JavaScript's `new Date()` parses strings without timezone info as local time, not UTC
- So if the server says `"2026-03-11 20:00:00"` meaning 8 PM UTC, a browser in CET interprets it as 8 PM CET (which is 7 PM UTC), making the countdown 1 hour short

## Environment

- Reproduced in Chrome and Firefox
- Only affects users not in UTC timezone
- Backend sends timestamps from Laravel which formats as `Y-m-d H:i:s` (no timezone suffix)
