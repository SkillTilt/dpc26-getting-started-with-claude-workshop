# Phase 5: Verify — Demo & Code-Along Script

Claude built it. Now the real engineering starts — reading, questioning, and catching what Claude got wrong. This is where you earn your keep. Verification is not a checkbox at the end. It is the phase where your expertise matters most, because Claude fails confidently, not randomly. Its first-pass success rate is about one third. The other two thirds look just as polished — and ship bugs.

**Prerequisites:** BidBoard is running via Docker Compose (`docker compose up -d`), database is seeded, frontend at `http://localhost:3000`, backend API at `http://localhost:80`, admin panel at `http://localhost:80/admin`. Playwright MCP is configured from Phase 1. The test suite has 16 passing tests and 1 intentional failure (`BidTest`). Phases 1-4 are complete.

## Demo 1 — Read the Output Carefully

Line-by-line reading of Claude's generated code. Not skimming the summary, not trusting the confident explanation — reading the actual implementation, one statement at a time.

### Setup

We will simulate what happens after Phase 4's Execute phase. Claude was asked to build a scheduled Artisan command (`CloseExpiredAuctions`) that runs every minute, finds auctions past their `ends_at` timestamp, sets their status to `closed`, and assigns `winner_id` to the highest bidder.

Open a terminal in the BidBoard project root

### Step 1 — Ask Claude to build the command

```
Create a scheduled Artisan command called CloseExpiredAuctions that:
- Runs every minute via the scheduler
- Finds items where ends_at has passed and status is 'active'
- Sets status to 'closed'
- Assigns winner_id to the highest bidder
- Register it in the scheduler
```

Watch Claude work. It will create the command, register it in the scheduler, and report back with a confident summary. It will sound complete. It will sound correct. Let's see.

### Step 2 — Read the summary, then read the code

After Claude finishes, it will give you a summary. The summary sounds great. Clean, professional, complete. Now let's do what Phase 5 is actually about — reading the code, not the summary.

Open the generated command file:

```
Show me the full contents of app/Console/Commands/CloseExpiredAuctions.php
```

### Step 3 — Walk through the code line by line

Read each line. Stop at each problem area.

**What to look for (the three catches):**

1. **Timezone handling.** Look for the query: `where('ends_at', '<', now())`. BidBoard stores `ends_at` as a `dateTime` column (see `database/migrations/2025_01_01_000002_create_items_table.php`). The `now()` helper returns the application timezone from `config('app.timezone')`. If the server timezone is not UTC, auctions close early or late. The correct call is `now('UTC')` or `Carbon::now('UTC')`.

2. **Winner assignment with zero bids.** Look for something like `$item->bids()->orderBy('amount', 'desc')->first()->user_id`. When an auction expires with zero bids, `->first()` returns `null`, and `->user_id` throws "Trying to get property of non-object." The command crashes on the first item with no bids, and every subsequent item in that batch is skipped.

3. **Status transition gap.** The command updates items where `status = 'active'`. But the items table has three statuses: `active`, `closed`, and `cancelled` (see the enum in the migration). What about items that were cancelled while their `ends_at` hadn't passed? Or future statuses? The query only looks at `active` — which is probably correct today, but worth questioning.

**What to notice:**

- Would you have caught all of these from the summary alone?
- The summary described the intended behaviour. The code implements something slightly different. This is Claude's characteristic failure mode — confident wrongness that follows the shape of correct output.

### Step 4 — Fix one problem already

```
Fix the winner assignment in CloseExpiredAuctions.
When an item has zero bids, winner_id should remain null.
Add a null check before accessing ->user_id.
```

After Claude fixes it, read the fix:

```
Show me the updated handle() method.
```

One fix, verified by reading the code. Not by reading Claude's description of the fix. The diff, not the explanation.

> **Note — if Claude gets it right:** Claude may handle the null check and timezone correctly on the first try. If so, you got lucky :) Claude got this one right — but only because the task was well-scoped with clear inputs. The lesson still applies: you read the code to verify, and this time the verification confirmed correctness. That's the happy path. It won't always be.

## Demo 2 — Stack Trace Debugging

Pasting a stack trace into Claude, watching it trace through PHP's method chains and class hierarchies, getting a fix — and then verifying that fix addresses the root cause, not just the symptom.

### Setup

Stay in the same Claude session. We are going to use the existing intentionally broken test as our stack trace source.

### Step 1 — Trigger the error

```
Run the test suite and show me the full output.
```

We know there's one intentionally broken test — BidTest. Let's look at the actual error, not just the pass/fail summary.

The output will show `BidTest::test_bid_must_be_higher_than_current_price` failing. The error is that `User::find(1)` returns `null` because `RefreshDatabase` wipes the database and there is no seeder — so `actingAs($user)` receives `null`. (Documented in `working/todo/bugs/bug-001-bid-test-fails.md`.)

### Step 2 — Feed the error to Claude

```
That BidTest failure — diagnose the root cause and suggest a fix.
```

**What to watch for:**

Claude will likely propose one of these fixes:
- **Option A:** Add a `User::factory()->create()` call — this is the correct fix (the test should create its own user, not rely on `User::find(1)`)
- **Option B:** Add a seeder call in the test setup — this works but is the wrong pattern (tests should be self-contained)
- **Option C:** Remove `RefreshDatabase` — this "fixes" the test by keeping stale data, which breaks test isolation

**What to notice:**

- If Claude suggests Option A, you got lucky — but note that claude often defaults to writing new code rather than checking whether existing code was executed correctly
- If Claude suggests Option B or C, this is the perfect teaching moment:

Ask yourself: is this fixing the cause, or the symptom? The cause is that the test uses `User::find(1)` instead of creating its own user via the factory. Claude's instinct is to write code. Sometimes the fix is to write less code, not more.

### Step 3 — Show the correct fix

```
The correct fix is to replace User::find(1) with User::factory()->create().
Show me what the fixed test should look like.
```

Read the output. Verify it matches this pattern:

```php
$user = User::factory()->create();
```

Stack trace debugging is one of Claude's strongest capabilities. It reads error messages accurately. But its fix proposals still need human judgment about architectural direction. The question is always: cause or symptom?

**Do not actually apply this fix** — the broken test is intentional and used throughout the workshop.

## Demo 3 — Diff Review

Reviewing a diff of Claude's implementation with a critical eye. Not checking whether it runs — checking whether it is correct, complete, and safe.

### Setup

We are going to ask Claude to implement outbid notifications and then review the diff carefully.

### Step 1 — Ask Claude to implement the feature

```
When a new bid is placed on an item, the previous highest bidder should receive
a notification. Implement this using Laravel's notification system.
Keep it simple — one listener, one notification class, one mail template.
```

Watch the file count. We asked for something simple. Let's see what we get.

### Step 2 — Review the diff

After Claude finishes, do NOT accept it at face value. Review the changes:

```
Show me a git diff of all the changes you just made.
```

Walk through the diff section by section. Frame the review using three categories from the workshop content:

- **Scope creep** — Did Claude change things beyond the prompt? Did it add features you didn't ask for?
- **Missing cases** — Did the implementation handle all the cases you'd expect? What paths are not covered?
- **Assumptions made explicit** — Where did Claude make a decision without asking? Is that decision correct?

Now look for these specific issues:

**What to look for (the three catches):**

1. **Missing null check.** In the listener, look for the query that gets the previous highest bidder. Something like `$item->bids()->latest()->first()->user`. If this is the first bid on the item, `first()` returns `null`, and `->user` throws an error. The notification system crashes on every item's first bid.

2. **Self-outbid notification.** If a user places a higher bid on an item where they are already the highest bidder, does the listener check whether the previous highest bidder is the same person as the new bidder? If not, users get notified that they outbid themselves.

3. **Variable name mismatches.** Check the notification class's `toMail()` method against what the Blade template expects. Claude often uses different variable names in different files — `$bid` vs `$auction` vs `$item`. The email either crashes or displays nothing where the item name should be.

**What to notice:**

- All three issues would pass a casual "does it look right" scan
- They require reading the code as if you were executing it in your head — tracing data flow across files

The diff is the source of truth. Claude's summary said 'notification sent to previous highest bidder.' The diff shows that the first bid on any item will crash, users get notified when they outbid themselves, and the email template might reference the wrong variable. Three bugs, all invisible in the summary.

> **Note — what if Claude avoids these bugs:** Claude may produce a clean implementation that handles null checks and self-outbid correctly. If so, pivot to reviewing for other concerns: does it handle the case where the item has been cancelled? Does it check that the notification recipient has a valid email? Is the Blade template using `{{ }}` (escaped) or `{!! !!}` (raw) for user-provided data like item titles? There is always something to find in a diff — the specific catches may differ from what's listed here.

### Step 3 — Fix one issue live

```
Fix the null check issue: when there is no previous bidder
(first bid on the item), skip the notification entirely.
```

Read the fix. Verify it adds a null check before dispatching.

**Undo all notification changes** before proceeding — this was a demo, not a feature we are keeping:

```
Undo all the notification changes you just made. Revert everything.
```

## Demo 4 — Test Review: What Is NOT Tested

Reviewing the tests Claude wrote — not for whether they pass, but for what they do not test. Missing test cases are invisible unless you actively look for them.

### Setup

Stay in the Claude session.

### Step 1 — Show the existing test suite

```
Run the test suite and show me the results. Then show me the contents of
all test files in tests/Feature/.
```

17 tests pass. One fails intentionally. Claude would report this as 'comprehensive test coverage.' Let's actually look at what's covered.

### Step 2 — Review what IS tested

Walk through the test files:

- **AuthTest.php** (6 tests): register, login, logout, validation, duplicates, bad credentials
- **CategoryTest.php** (1 test): list categories
- **ItemTest.php** (1 test): view single item
- **ItemStoreTest.php** (3 tests): create item, auth required, validation
- **BidTest.php** (1 test, broken): bid below current price
- **UserEndpointTest.php** (4 tests): profile, listings, bids, auth

### Step 3 — What is NOT tested?

Auth looks solid — six tests covering the main paths. But look at the bid placement endpoint. It has ONE test — and that test is broken. This is the most critical business logic in the entire app. Let's think about what's missing.

Walk through the gaps. Think of missing cases before checking below:

- **Same amount bid.** What happens when someone bids the exact same amount as the current price? The `BidController` uses `<` instead of `<=` on line 56 — this is a known bug (documented in `working/todo/bugs/bug-002-bid-price-off-by-one.md`). No test catches it.
- **Bid on ended auction.** The controller checks `$item->ends_at < now()`, but there's no test for a bid on an expired auction.
- **Seller bidding on own item.** The controller checks `$item->seller_id === auth()->id()`, but there's no test verifying this returns 403.
- **Bid on cancelled item.** The controller checks `$item->status !== 'active'`, but no test for a cancelled auction.
- **Negative amount.** Does validation reject a bid of -500? No test.
- **Minimum increment.** The controller enforces a $1.00 minimum increment above current price. No test.

### Step 4 — Ask Claude to add two missing tests

```
Add two tests to tests/Feature/BidTest.php:
1. Test that a seller cannot bid on their own item (expect 403)
2. Test that a bid on an ended auction is rejected (expect 422)

Use User::factory()->create() for test users — do not use User::find().
Both tests should use RefreshDatabase.
```

### Step 5 — Run the new tests

```
Run only the BidTest tests.
```

**What to notice:**

- The new tests should pass (if Claude wrote them correctly)
- The original broken test still fails
- We went from "1 test (broken)" to "3 tests (2 passing, 1 still broken)" — a meaningful improvement in coverage for the most critical endpoint

Claude writes tests that confirm the happy path and obvious error cases. It rarely writes tests that challenge the business logic or probe for boundary conditions. The question 'what is NOT tested?' is always yours to ask.

```
Keep the two new passing tests. Revert any other changes.
```

## Demo 5 — Verify with Second Claude

Using a second Claude session as an independent reviewer. Fresh context, no sunk cost, no attachment to the implementation.

### Setup

We are going to review the existing `BidController@store` method — the over-100-line "fat controller" that was flagged as a gotcha in CLAUDE.md. This is real code already in the repo, not something we need to generate.

### Step 1 — Show the code in the first session

```
Show me the full contents of app/Http/Controllers/Api/BidController.php
```

This is the bid placement controller. Over 100 lines, inline validation, inline business logic, no transaction, no locking. It works — but let's get a second opinion.

### Step 2 — Open a second terminal and start a fresh Claude session

Open a new terminal tab. Start a new Claude session:


Paste this prompt:

```
Read @app/Http/Controllers/Api/BidController.php and review it for correctness
and edge cases. List issues ordered by severity.
```

**What to watch for:**

The second Claude, with fresh context, should spot issues like:

- **No database transaction.** The bid creation and price update are separate operations (lines 79-87). Two simultaneous bids could both pass validation, both create bid records, and the last `$item->save()` wins — losing the other bid's price update.
- **No pessimistic locking.** Related to the above — `lockForUpdate()` is not used, so the race condition has no protection.
- **Bug on line 56.** Uses `<` instead of `<=`, so a bid equal to the current price is accepted when it should be rejected.
- **Inconsistent response shapes.** Lines 107-111 return a different JSON structure than line 116. The Vue frontend has to handle both shapes. (Documented in `working/todo/features/feature-007-standardize-api-errors.md`.)
- **Inline validation.** No Form Request — violates the project convention documented in CLAUDE.md.
- **Hardcoded minimum increment.** `$minIncrement = 1.00` on line 62 — should be configurable.

**What to note:**

- The first Claude built this code (or it was in the codebase). The second Claude reviews it without emotional attachment.
- The second Claude has a reviewing mindset, not a building mindset. These are different cognitive modes, and even an LLM benefits from the separation.
- This is cheap verification — no tooling, no setup, just a second session and a read command.

> "Fresh eyes catch things. Even artificial eyes. The second Claude doesn't know this code was hard to write. It doesn't care. It just reads it and tells you what's wrong."

Close the second session:

```
/exit
```

## Demo 6 — Browser Verification with Playwright

Using the Playwright MCP (configured in Phase 1) to verify that the application works in a real browser. Tests pass does not mean users see the right thing.

### Setup

Return to your main Claude session (or start a new one)

### Step 1 — Verify the homepage renders correctly

```
Open a browser and navigate to http://localhost:3000.
Take a snapshot and tell me:
1. Does the BidBoard hero section appear?
2. Are categories displayed in a grid?
3. How many categories are visible?
```

This is the Playwright MCP we set up in Phase 1. Claude opens a real Chromium browser, navigates to the page, and sees what users see. Not 'does the code compile' — 'does the page look right.'

**What to note:**

- Claude describes the page layout, the hero section, the category cards
- It can count elements, read text, verify that data from the database is actually rendering
- This catches an entire class of bugs that unit tests miss: broken imports, missing CSS, failed API calls, empty states that shouldn't be empty

### Step 2 — Navigate to a category and verify items

```
Click on the Electronics category (or the first category you see).
Take a snapshot and tell me:
1. Are auction items displayed?
2. Does each item show a title, price, and image?
3. Are countdown timers visible and ticking?
```

**What to note:**

- Claude navigates through the Vue Router SPA — clicks a link, waits for the page to load, reads the content
- It can verify that the API data flows from backend to frontend correctly
- If an item is missing its image or price, Claude would report it — a test wouldn't catch a broken image URL

### Step 3 — Verify the item detail page

```
Click on any item to go to its detail page.
Take a snapshot and tell me:
1. Is the item title and description displayed?
2. Is the current price showing?
3. Is the countdown timer present?
4. Is the bid history section visible?
5. Is there a bid form (or a "log in to bid" message)?
```

**What to note:**

- The item detail page at `http://localhost:3000/item/:id` loads data from the `GET /api/items/{item}` endpoint
- The `ItemResource` formats the response, including nested `bids` and `seller` relationships
- Claude can verify the full data chain: database -> API -> Vue component -> rendered HTML

### Step 4 — Verify the admin panel

```
Navigate to http://localhost:80/admin and log in with alice@example.com / password.
Take a snapshot and describe what you see in the admin dashboard.
```

**What to note:**

- The admin panel runs on the backend port (80), not the frontend port (3000)
- Filament v3 provides the admin interface
- Claude can verify that admin resources (Users, Items) are accessible and displaying data

Four pages verified in under two minutes. No manual clicking, no browser switching. Claude sees what users see. This is the verification loop that Boris Cherny runs on every change to claude.ai/code — browser verification after every implementation.

### Step 5 — Close the browser

```
Close the browser.
```

## Demo 7 — Review Before Applying (Migrations)

Reviewing generated infrastructure/database migration changes before applying them. 
### Setup

Stay in the Claude session.

### Step 1 — Ask Claude to generate a migration

```
The bid queries are slow. Generate a migration to add indexes to the bids table
that would optimize the most common queries: finding the highest bid for an item,
and listing bids in chronological order.
```

### Step 2 — Review before running

After Claude generates the migration:

```
Show me the full migration file. Do NOT run it yet.
```

Before we run `php artisan migrate`, we review. 

Ask three questions out loud:

**Question 1: "Are the column names correct?"**

Check the migration's index columns against the actual `bids` table schema. The `bids` migration (`database/migrations/2025_01_01_000003_create_bids_table.php`) has these columns: `id`, `item_id`, `user_id`, `amount`, `created_at`, `updated_at`. If Claude's index references a column that doesn't exist (like `auction_id` or `bid_amount`), the migration will fail.

**Question 2: "Do our queries actually use these indexes?"**

Look at the `BidController` — the queries are:
- `$item->bids()->orderBy('amount', 'desc')->first()` (highest bid) — benefits from an index on `(item_id, amount)`
- `$item->bids()->where('id', '!=', $bid->id)->orderBy('amount', 'desc')->first()` — same index helps

A `created_at` index might be added "because it seems useful" — but check: does any query filter or sort by `created_at` alone? If not, it costs write performance for no read benefit.

**Question 3: "What happens in production?"**

On SQLite in local dev, adding an index is instant and non-blocking. On MySQL in production with a large bids table, `ALTER TABLE` can lock the table for writes. During a live auction, that means bidding is blocked until the migration completes. This is the kind of thing that testing locally will never reveal.

**What to note:**

- The migration itself might be correct, but the review surfaces questions that no test can answer
- This is the same "review before apply" pattern for any generated infrastructure: migrations, Docker Compose changes, CI/CD configs, deployment scripts

Generated infrastructure changes get reviewed before they touch real state. Always. A bad migration on the bids table during a live auction could lock bidding for seconds or minutes. No test catches that — only review does.

**Do not run the migration.** Delete the generated file:

```
Delete the migration file you just created. We were reviewing, not applying.
```
