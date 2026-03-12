# Phase 7: Compound — Demo & Code-Along Script

You've learned the phases individually. Now watch them flow together — this is what daily work with Claude Code actually looks like. The individual skills compound: configuration makes classification faster, classification makes planning sharper, planning makes execution cleaner, and every mistake becomes a permanent rule that makes tomorrow better than today.

BidBoard is running via Docker Compose (`docker compose up -d`), database is seeded, frontend is at `http://localhost:3000`, backend API at `http://localhost:80`. CLAUDE.md from Phase 1 is in place with Stack, Commands, Conventions, Domain Rules, and Gotchas sections. The `.claude/settings.json` from Phase 1 has permissions and hooks configured.

## Demo 1 — Package Hallucination: Mistakes Become Rules

Claude confidently recommends packages that do not exist. This is the most common hallucination pattern in code assistants — and the first example of the reactive compounding loop: a mistake becomes a permanent rule in CLAUDE.md.

### Setup

Open a terminal in the BidBoard project root and start a Claude session

### Step 1 — Ask for a package recommendation

```
Is there a Composer package that handles auction bidding rules — minimum increments,
reserve prices, anti-sniping? I'd rather not reinvent the wheel for BidController.
```

Watch how confident Claude is. It will give you a package name, a version, maybe even a usage example showing how to integrate it with the `BidController@store` method. It sounds completely real.

**What to watch for:**

- Claude will suggest something like `composer require auction-tools/bidding` or `laravel-auction/bid-engine`
- It will describe the API with specifics — method names, configuration files, service providers
- It may even show you how to inject it into `BidController`
- None of this exists

### Step 2 — Verify on Packagist

Open a browser (or ask Claude to check):

```
Search Packagist for that package. Does it actually exist?
```

**What to note:**

- The package is not on Packagist — zero results
- This is not a Claude bug. Language models produce plausible text. A package name like `auction-tools/bidding` is plausible. That does not make it real
- If you had run `composer require` without checking, you would get an error. Worse: a typosquatter could register that name with malicious code

This is what language models do. They generate text that fits the pattern. The pattern says 'a Composer package for X looks like vendor/package.' So Claude produces one. The lesson is not 'Claude is broken.' The lesson is: never install a package you have not verified on Packagist yourself.

### Step 3 — Add the rule to CLAUDE.md

```
Add a new entry to the Gotchas section of CLAUDE.md: "Never suggest installing
Composer packages without explicitly stating that the developer should verify
the package exists on packagist.org first."
```

**What to note:**

- Claude opens `CLAUDE.md` and adds the entry to the existing Gotchas section
- This is the **reactive loop** in action: mistake happened, lesson learned, lesson written down
- Every future session in this codebase will read this rule before generating any output
- The mistake cost us two minutes. The rule prevents it from costing time ever again

This is compounding. One bad recommendation. One line in CLAUDE.md. Every future session in this codebase benefits. The mistake was not wasted — it was converted into a permanent rule.

## Demo 2 — Session Memory: Return on Configuration

When CLAUDE.md is properly maintained, knowledge persists across sessions. Claude does not start from zero — it starts from the accumulated understanding of the project.

### Setup

Clear the current context to simulate a fresh session:

```
/clear
```

### Step 1 — Ask for a price formatting helper

```
Add a formatPrice helper function to the Vue frontend that takes a bid amount
from the API and returns a display string. Put it in the existing utils/format.js file.
```

**What to watch for:**

- Claude reads CLAUDE.md and picks up the domain rules about monetary values (`decimal(10,2)`)
- It sees that `frontend/src/utils/format.js` already has a `formatCurrency` function using `Intl.NumberFormat`
- It either extends the existing function or adds a complementary `formatPrice` that follows the same pattern
- It knows from the Conventions section that the frontend uses Composition API, so any usage example will use `<script setup>`

**What to note:**

- Claude did not need to be told the monetary format — CLAUDE.md told it
- Claude did not need to be told where utility functions live — it found the existing `format.js`
- Claude matched the existing pattern (`Intl.NumberFormat` with `'en-US'` and `currency: 'USD'`) without being told to

This is the return on investment from Phase 1. We spent eight minutes writing CLAUDE.md. Every session since has benefited. Claude starts from the accumulated understanding of the project — not from zero. This is compound interest on configuration.

### Step 2 — Contrast with no CLAUDE.md

Without CLAUDE.md, Claude would guess. It might use `toFixed(2)` manually instead of `Intl.NumberFormat`. It might default to a different locale. It might put the function in a new file instead of the existing `format.js`. Same prompt. Different CLAUDE.md. Completely different output.

**Undo any changes Claude made** — this was a demonstration, not a feature we are shipping.

## Demo 3 — The Reactive Loop: Bug to Fix in One Flow

(this was already fixed in previous phases, you can read this scenario as an example of how such a loop would work)

The full reactive cycle — from bug report to committed fix — in one unbroken flow. Every phase is present. This is the bread and butter of daily work with Claude Code.

### Setup

Start a fresh Claude session

### Step 1 — Present the bug report

```
A user reports: "I placed a bid for the exact same amount as the current highest bid,
and it was accepted. That shouldn't be allowed — bids should be strictly greater
than the current price." Find and fix this bug.
```

Watch Claude work through this. It's going to classify, plan, execute, verify, and commit — all from one prompt. Not because we told it to follow the phases, but because this is how a well-configured agent naturally handles a bug report.

**What to watch for — Phase by phase:**

- **Classify (Phase 2):** Claude recognizes this as a bug fix — targeted investigation, not a feature. It does not over-plan
- **Plan (Phase 3):** Claude outlines its approach: find the validation logic in `BidController`, identify the comparison operator, fix it, add a regression test
- **Execute (Phase 4):** Claude opens `app/Http/Controllers/Api/BidController.php`, locates line 56 where the comparison reads `if ($amount < $item->current_price)`, and changes it to `if ($amount <= $item->current_price)` — the `<` should be `<=` so that equal bids are rejected (documented in `working/todo/bugs/bug-002-bid-price-off-by-one.md`)
- **Verify (Phase 5):** Claude writes a test — something like `test_bid_equal_to_current_price_is_rejected()` — and runs the suite with `docker compose exec app php artisan test`

### Step 2 — Commit the fix

If Claude does not commit automatically, prompt:

```
Commit this fix with an appropriate message.
```

**Expected commit message:** something like `fix: reject bid amounts equal to current price — require strictly greater`

### Step 3 — Compound the lesson

```
Add a rule to the Domain Rules section of CLAUDE.md:
"A bid must be strictly greater than the current price — reject bids where
amount <= current_price. Equal bids are not valid."
```

**What to note:**

- Every phase was present in that flow. Not because we forced it, but because this is how real work happens
- Phase 1's CLAUDE.md gave Claude the domain context (bids are never deleted, status transitions)
- Phase 2's classification kept the scope tight — no refactoring tangents
- Phase 3's planning was lightweight but present — Claude checked the right file first
- Phase 4's execution was surgical — one operator changed
- Phase 5's verification caught any regression
- Phase 6's commit was clean and descriptive
- Phase 7's CLAUDE.md update ensures this specific mistake never recurs

That was the full loop. Bug report to committed fix with a regression test and a permanent rule — in about five minutes. Every phase contributed. This is the rhythm you develop with practice.

## Demo 4 — Proactive Suggestions: Claude Notices Things

While working on one task, Claude notices an unrelated issue and raises it. This is Claude acting as a code reviewer, not just a code writer. This is the proactive compounding loop.

### Setup

Continue in the same session or start a new one.

### Step 1 — Give Claude a focused task

```
Add a method to the UserController called `watchedItems` that returns the items
a user is watching. Assume a watchlist table exists with user_id and item_id columns.
Use proper API Resources and follow the conventions in CLAUDE.md.
```

**What to watch for:**

- Claude writes the method following conventions (API Resources, proper Eloquent relationships)
- While reading `UserController`, Claude may notice the inconsistency between the `listings` method (which uses a raw DB query for sold items on line 35) and the rest of the controller that uses Eloquent
- Claude may also notice the inconsistent route naming (`userBids` vs `user.listings`) in `routes/api.php`
- If Claude raises either issue, this is the proactive loop in action

**If Claude does not raise anything proactively, prompt:**

```
While you were reading UserController, did you notice anything else that looks
inconsistent or could be improved? Take a careful look at the listings method
and the route definitions.
```

**What to note:**

- Claude identifies the raw DB query in `UserController@listings` (lines 35-42) that should use Eloquent and API Resources (documented in `working/todo/features/feature-010-eloquent-user-controller.md`)
- Claude identifies the inconsistent route naming: `user.listings` (dot notation, correct) vs `userBids` (camelCase, inconsistent with CLAUDE.md conventions)
- Claude did not need to be told to look for these. It scanned the surrounding code while working on the task you asked for

This is the proactive loop. Claude reads more code than you do while working. It catches things you would only find during a dedicated code review. The proactive loop captures what went right and what was discovered — not just what went wrong.

### Step 2 — Capture the discovery

```
Add a note to the Gotchas section of CLAUDE.md:
"UserController@listings uses a raw DB query for sold items — this should be
refactored to use Eloquent with ItemResource, consistent with the rest of the codebase."
```

The proactive loop requires a habit: before you close any session, spend ninety seconds asking Claude what it learned. This is how CLAUDE.md evolves from a configuration file into a living knowledge base.

> **Note:** The watchlist table doesn't actually exist in the database. Claude may notice this and create a migration, or it may write the method assuming the table exists and only fail when tested. Either outcome is fine for this demo — the point is observing what Claude notices in the surrounding code while working on the assigned task, not the watchlist feature itself.

**Undo the `watchedItems` changes** — the watchlist table does not actually exist.

## Demo 5 — Architecture Review: Thinking, Not Coding

Claude as an architecture advisor. Not writing code — analyzing systems. This is high-leverage Claude usage: getting a second opinion from someone who has read the entire codebase.

### Setup

Continue in the same session:

```
Review BidBoard's architecture. If this had 10,000 concurrent users placing bids,
what would break first? Give me the top 5 concerns in order of severity.
```

No code is being written here. Claude is reading the codebase and thinking about systems. This is Phase 2 and Phase 3 work — classify and plan — applied at the architecture level.

**What to watch for — Claude should identify these concrete issues:**

1. **No database transaction or locking in BidController/BiddingService** — the original `BidController` has no transaction or locking around the bid creation (lines 71-80) — a race condition. Two users bidding at the same time could both pass validation
2. **No queue for notifications** — the `Log::info` on line 89 is a placeholder, but even when real notifications are added, doing it synchronously means every bid triggers inline processing that slows the response
3. **N+1 query in CategoryController@items** — `bids` and `seller` are accessed inside the `through()` closure (line 33) without eager loading. At scale this is a database killer
4. **No rate limiting on the bid API endpoint** — `routes/api.php` shows throttling on auth routes (line 11-12) but none on `POST /items/{item}/bids`. A script could flood the system

**What to note:**

- Claude did not write any code. It read, analyzed, and advised
- Each concern references specific files and line numbers from the actual codebase
- The suggestions are actionable: queue workers, pessimistic locking with `lockForUpdate()`, Redis cache, rate middleware, PostgreSQL
- This is Phase 2 (Classify) thinking at the architecture level — knowing when to think before coding

Claude's value is not limited to writing code. This architecture review would feed into weeks of Phase 4 work. Each improvement planned and executed through the full loop. And the review itself? It took one prompt and two minutes.

## Demo 6 — Error Hierarchy: Systematic Consistency

Creating consistent error handling across the bidding domain. This is cross-cutting work that Claude handles well because it holds all the files in context simultaneously.

### Setup

Start a fresh Claude session.

### Step 1 — Frame the problem

```
BidBoard's error handling is inconsistent. Look at BidController (or BiddingService if
we extracted it) — it returns ad-hoc JSON error responses with different shapes and
status codes. Create custom exception classes for the bidding domain and register them
in the exception handler for clean, consistent API responses. The exceptions should be:
BidTooLowException, AuctionEndedException, SelfBidException, and AuctionNotFoundException.
```

**What to watch for:**

- Claude creates four exception classes in `app/Exceptions/`:
  - `BidTooLowException` — includes the current price and the attempted amount, returns 422
  - `AuctionEndedException` — includes the auction end time, returns 409
  - `SelfBidException` — you cannot bid on your own item, returns 403
  - `AuctionNotFoundException` — the item does not exist or is not active, returns 404
- Claude registers them in `bootstrap/app.php` (Laravel 12 uses the application bootstrap for exception rendering, not a separate `Handler.php`)
- Claude updates `BidController` (or `BiddingService`) to throw these exceptions instead of returning inline error responses
- Claude updates the tests to expect the new exception responses

### Step 2 — Run the test suite

```
Run the test suite and make sure everything still passes.
```

**What to note:**

- Tests that expected the old error format (`{'error': '...'}`) need updating to match the new structured responses
- Claude handles the test updates as part of the same flow — it does not leave broken tests behind
- The error responses are now consistent: every bidding error has the same JSON structure with `message`, `error_code`, and relevant context fields

### Step 3 — Commit

```
Commit this with the message:
"refactor: add custom exception hierarchy for bidding domain"
```

This is systematic, multi-file consistency work — Claude's sweet spot. Four exception classes, updated controller logic, updated exception handler, updated tests. Tedious for a human. Trivial for an agent that holds all the files in context. And the error hierarchy itself is a form of compounding — investing in structure now so that every future bidding feature gets clean error handling for free.

## Demo 7 — Prototype Feature: Speed Over Polish

Claude's speed for exploratory work. When you are not sure if a feature is worth building, Claude can produce a working prototype fast enough to evaluate the idea instead of debating it.

### Setup

Start a fresh Claude session.

### Step 1 — Request the prototype

```
Prototype a watchlist feature. Users can watch auction items and see a list of items
they're watching. Keep it simple — this is a proof of concept, not production code.
I need: a migration for the watchlist table, a Watchlist model, API endpoints to
watch/unwatch an item and list watched items, and a basic Vue page to display the list.
```

Notice the framing: 'proof of concept, not production code.' This is Phase 2 classification in the prompt itself. We are telling Claude the quality bar. A prototype needs to work — it does not need to be perfect.

**What to watch for:**

- Claude builds the full stack end to end:
  - Migration for `watchlists` table with `user_id`, `item_id`, timestamps, and a unique constraint on `[user_id, item_id]`
  - `Watchlist` model with `belongsTo` relationships to `User` and `Item`
  - API endpoints: `POST /api/items/{item}/watch`, `DELETE /api/items/{item}/watch`, `GET /api/user/watchlist`
  - A controller using Form Requests and API Resources (following CLAUDE.md conventions even in a prototype)
  - A basic `WatchlistPage.vue` using Composition API with `<script setup>`
  - Route added to `frontend/src/router/index.js`

### Step 2 — Run the migration and test

```
Run the migration and verify the endpoints work by running the test suite.
```

### Step 3 — Acknowledge what is missing

Let me be explicit about what this prototype does NOT have: no real-time updates, no notification when a watched item gets a new bid, no pagination on the watchlist, no bulk unwatch. These are all intentional omissions. The prototype validates the idea — is a watchlist useful? Does the data model work? Can we build the UI? Those questions are answered. The implementation details are for later.

**What to point out:**

- From prompt to working feature in minutes
- Claude followed CLAUDE.md conventions even in prototype mode — Form Requests, API Resources, Composition API
- The unique constraint on `[user_id, item_id]` prevents duplicate watchlist entries
- The prototype is not production-ready, and that is the point. Five minutes to learn whether the feature is worth five days

This is Phase 2 judgment applied to the quality bar. Prototypes validate ideas, not implementations. You spent five minutes to learn whether this feature is worth building properly. That is the highest-leverage use of Claude's speed.

**Undo all watchlist changes:**

```
Undo all the watchlist changes — remove the migration, model, controller,
routes, and Vue page. Revert everything to the state before we started.
```

## Demo 8 — Full Workflow: The Grand Finale

The complete workflow — every phase, one continuous flow, driven by a real-world bug report. This is the capstone. Everything the audience learned today, in action.

### Setup

Start a fresh Claude session.

### Step 1 — Present the bug report

```
Bug report from a user: "I won an auction, but then I got an email saying I was outbid
after the auction had already closed. The auction shows I won, but the outbid notification
arrived after it ended. Something is wrong with the timing."

Investigate this, find the root cause, and make a plan to fix it.
```

This is the grand finale. Every phase in one flow. Watch Claude classify the problem, plan the investigation, execute the fix, verify it works, and commit it cleanly.

### Phase 2 — Classify

**What to watch for:**

- Claude recognizes this is not a simple fix — it involves timing, events, and the interaction between bidding and notifications
- Claude classifies it as a medium-complexity bug requiring investigation before coding

### Phase 3 — Plan

**What to watch for:**

- Claude reads the bid flow in `BidController.php` (or `BiddingService.php` if we kept earlier refactors):
  - Bid is placed
  - `current_price` is updated
  - Previous high bidder is identified (lines 82-90)
  - Notification is logged (line 89)
- Claude reads the auto-close logic (lines 100-121): if the auction ends within 1 minute, it closes and sets a winner
- Claude identifies the gap: the notification logging on line 89 happens *before* the auto-close check on line 92. A bid placed in the final minute triggers a "you were outbid" notification *and then* closes the auction. The previous bidder gets notified they were outbid, but then finds out they actually lost to a last-second bid — or worse, they won and still got an outbid notification

### Phase 4 — Execute

After reviewing the plan (and adjusting if needed):

`Execute the plan`

**What to watch for:**

- Claude implements the fix:
  - Move the notification logic *after* the auto-close check
  - Add a guard: do not send "outbid" notifications if the auction has closed (the winner notification is different from the outbid notification)
  - Add an `is_active` check before any notification logic: `if ($item->status === 'active')`
  - Optionally: add a guard in the bid endpoint itself to reject bids on items where `ends_at` has passed (belt and suspenders with the existing check on line 34)

### Phase 5 — Verify

```
Write tests for both scenarios:
1. A notification should NOT be sent when a bid closes the auction
2. A bid should be rejected if the auction has already ended
Run the full test suite.
```

**What to watch for:**

- Claude writes `test_no_outbid_notification_after_auction_closes()` — places a bid when `ends_at` is within 1 minute, verifies the auction closes, verifies no outbid notification is triggered
- Claude writes `test_bid_rejected_after_auction_ends()` — sets `ends_at` to the past, attempts a bid, expects 422
- The full test suite passes (except the intentionally broken `BidTest`)

### Phase 6 — Commit

```
Commit this fix.
```

**Expected commit message:** something like `fix: prevent outbid notifications after auction auto-close`

### Phase 7 — Compound

```
Add two rules to CLAUDE.md based on what we learned:
1. In Domain Rules: "Always check auction status (is_active) before sending any
   auction-related notification — races between bid placement and auction closing
   can cause stale notifications"
2. In Gotchas: "The notification logic in BidController/BiddingService must run
   AFTER the auto-close check, never before — ordering matters for correctness"
```

**What to note:**

- You just saw the full arc: confused user report to committed fix with regression tests and permanent rules
- Phase 1's CLAUDE.md gave Claude the domain context to understand auction status transitions
- Phase 2's classification prevented Claude from jumping to a shallow fix (moving one line vs understanding the race condition)
- Phase 3's planning uncovered the root cause — the ordering of notification vs auto-close
- Phase 4's execution was multi-point: reorder logic, add guard, add belt-and-suspenders check
- Phase 5's verification included targeted tests for both the happy path and the edge case
- Phase 6's commit was clean and descriptive
- Phase 7's compounding ensured the lesson is permanent — both the general principle (check status before notifying) and the specific gotcha (ordering matters)

That was every phase. Not a checklist we followed mechanically, but a rhythm. Classify the problem. Plan the investigation. Execute the fix. Verify it works. Commit it cleanly. Compound the lesson. This is what daily work with Claude Code looks like after you have internalized the framework.

## Phase 7 Wrap-Up

**Revisit CLAUDE.md — the living document:**

```bash
cat CLAUDE.md
```

Look at this file now. Compare it to what we started with in Phase 1. Every demo added something. The package hallucination rule. The bid comparison rule. The UserController tech debt note. The notification ordering gotcha. This file is no longer the onboarding document we wrote in Phase 1. It is the accumulated knowledge of every session we ran today.

**The two loops:**

| Loop | Trigger | Example from today | What it captures |
|------|---------|-------------------|-----------------|
| **Reactive** | A mistake happens | Package hallucination, bid comparison bug | What went wrong — so it never recurs |
| **Proactive** | A session ends | UserController inconsistency, architecture concerns | What was discovered — so it is not lost |

**The compounding arc:**

- **Configuration compounds:** every CLAUDE.md entry makes every future session better
- **Context compounds:** sessions build on each other through git history, memory, and documentation
- **Loops compound:** bug, investigate, fix, test, commit, rule — becomes a single fluid motion- **Awareness compounds:** Claude notices more as it reads more of your codebase
- **Structure compounds:** refactoring and error hierarchies make future work faster and cleaner 
- **Judgment compounds:** knowing when to prototype versus when to build for production saves days 
- **Everything compounds:** the grand finale is not a new skill — it is all the old skills flowing together

> **Note — What Compound Is Not:** Before the closing, let's make one important caveat: compounding is not the same as bloating. A CLAUDE.md that grows without discipline becomes noise. Boris keeps his at ~2,500 tokens — that's a ceiling maintained by curation, not accumulation. The rule: if an entry applies to one specific task that will never recur, it doesn't belong. If a gotcha was fixed at the source, remove the entry. Review CLAUDE.md quarterly. Retire stale slash commands. Update skills when the team's approach evolves. Compounding requires adding the right things *and removing the wrong ones*.

> **Note — Slash Commands and Skills as Compound Infrastructure:** We created a skill in Phase 4 and mentioned custom slash commands in Phase 6. These are the process and expertise layers of compounding — CLAUDE.md captures knowledge, slash commands capture process, skills capture deep expertise. The Anthropic Security Engineering team owns over 50% of their monorepo's slash commands, added one at a time as each process became well-understood. Skills compound differently — they improve in episodes through use-refine-use cycles. After enough iterations, a skill for writing tests in your codebase's style stops producing tests that need correction and starts producing tests that go straight to review. The ceiling is Boris's one-sentence prompts producing team-standard output.

**What to leave the workshop with:**

Claude Code is not magic. It is a tool — a remarkably capable one — that gets better the more intentionally you use it. The seven phases are not rules to memorize. They are a way of thinking about how you and an AI agent collaborate. Configure it well, classify the work honestly, plan before you code, execute with guardrails, verify relentlessly, commit cleanly, and compound every lesson. That is how you stay in control. That is how you stay sharp. That is how you get better, faster, and more intentional — without dulling your craft.
