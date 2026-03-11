# Bug #1: Bid test always fails — "Call to a member function on null"

**Issue:** [#1](https://github.com/SkillTilt/dpc26-getting-started-with-claude-workshop/issues/1)
**Severity:** Test broken
**Status:** Open

## Description

The `BidTest` fails immediately with a "Call to a member function on null" error. It's trying to use a user that doesn't exist.

## Steps to Reproduce

1. Run `php artisan migrate:fresh` (clean database)
2. Run `php artisan test --filter=BidTest`
3. Test fails on line 23

## Expected Behavior

The test should seed its own data and pass.

## Actual Behavior

```
Error: Call to a member function createToken() on null

  at backend/tests/Feature/BidTest.php:23
```

## Where to Look

- `backend/tests/Feature/BidTest.php` line 23
- The test uses `User::find(1)` to get a user, but no user with ID 1 exists because the test doesn't seed the database first
- It should use a factory (`User::factory()->create()`) instead of assuming a user with a specific ID exists

## Notes

This test probably worked on someone's local machine where they had seeded data, but it fails on any clean database or in CI. Classic case of a test depending on external state instead of setting up its own fixtures.
