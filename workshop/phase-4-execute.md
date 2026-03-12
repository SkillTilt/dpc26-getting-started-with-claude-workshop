# Phase 4: Execute — Demo & Code-Along Script

The plan is ready. The CLAUDE.md is configured. Permissions are set. Hooks are running. You know what to build and how to classify it. Now we execute — but execution is not 'hand it to Claude and walk away.' It is directed, contextual, and you are watching every step. You steer. Claude builds. The preparation from Phases 1-3 is what makes this phase productive instead of chaotic."

The outbid notification feature was planned in Phase 3. You should have this plan stored in a separate file.

## Demo 1 — Medium Feature: "Ending Soon" Section

A bounded, multi-file feature executed with a brief plan. This is the "collaborate" mode from Phase 2 — Claude plans briefly, you approve, Claude builds. You finally see your first real code generation.

### Setup

Open a new claude session (or /clear a running one) in the BidBoard project root.

### Step 1 — Reminder

Remember Task 3 from the classification exercise? 'Add an Ending Soon section to the homepage.' We classified it as collaborate — brief plan, then execute. Let's see that in action.

### Step 2 — Prompt Claude

```
I want to add an "Ending Soon" section to the homepage that displays items ending within
the next 24 hours. It should show a row of ItemCard components. The homepage currently
only shows category cards. Plan this for me before implementing.
```

**What to watch for in the plan:**

- Claude should identify the files involved:
  - Backend: a new route and controller method (or addition to `ItemController`) to return items ending soon
  - Frontend: `frontend/src/pages/HomePage.vue` to add the section
  - It will reuse `frontend/src/components/ItemCard.vue` — no new component needed
- The plan should be short — a few bullet points, not an essay
- If Claude proposes a full search/filter system or a caching layer, that's over-engineering

### Step 3 — Review and accept

Look at this plan. A new API endpoint, a section on the homepage, reuses existing components. Three or four files. Reviewing the plan took 15 seconds and confirmed Claude isn't going to over-engineer it. Accept.

Accept the plan and let Claude execute.

### Step 4 — Show the result

After Claude implements, verify in the browser:

```
Open a browser and navigate to http://localhost:3000.
Verify the "Ending Soon" section appears on the homepage with item cards.
```

**What to notice:**

- The "Ending Soon" section appears on the homepage with auction items, if it does not, you might not have data seeded. Try seeding again.
- Claude reused the existing `ItemCard` component — no new components invented
- Claude followed the conventions from CLAUDE.md: the API endpoint returns data through an API Resource, the route uses dot notation
- Total time: about 2-3 minutes of Claude working, 15 seconds of plan review

This is the middle of the spectrum. Not a one-liner you'd delegate blindly, not complex enough for a full planning session. Most day-to-day development tasks fall here. The brief plan is the habit that keeps Claude on track.

**Undo the changes:**

```
/rewind
```

Select the checkpoint from before the "Ending Soon" feature. All new files, route changes, and component edits are reverted in one step.

## Demo 2 — Git Worktrees for Parallel Work

Running two Claude Code sessions simultaneously on the same repo without conflicts. `claude --worktree` creates an isolated branch in a git worktree, so a second session can work on a completely separate concern while the main session keeps going.

### Setup

Open a terminal in the BidBoard project root. Make sure you have two terminal tabs or panes visible side by side.

### Step 1 — Start the main session

In the first terminal tab open a claude session as normal:

```bash
claude
```

Give it the first task — the big feature from the Phase 3 plan:

```
We're going to build the outbid notification feature from the plan below.
Before we start building, read all files in app/Models/ to understand
the relationships between User, Item, Bid, and Category.

[copy here the plan you saved earlier, or if you have stored it in the code base, you can also refer to it using @ notation]
```

This is our main session. It's going to work on the outbid notification feature, the multi-file task we planned in Phase 3. But while this session is loading context and getting ready to build, we have another task that's completely independent: a timezone bug fix. Instead of waiting for this to finish, we'll run both at the same time.

### Step 2 — Open a parallel session with --worktree

In the second terminal tab:

```bash
claude --worktree
```

Watch what happens. Claude creates a new git worktree — a lightweight linked copy of the repo with its own branch. No full clone. No disk duplication. Same `.git` history, separate working directory.

### Step 3 — Give the second session its task

In the worktree session:

```
Fix the timezone bug in the CountdownTimer. The auction end times come
from the server as UTC but the component parses them without timezone
info — new Date(props.endsAt) treats the string as local time.
Also check @frontend/src/components/CountdownTimer.vue and
@backend/app/Http/Resources/ItemResource.php — the API response for
ends_at should include timezone information.

> **See:** `working/todo/bugs/bug-005-countdown-timezone.md`
```

### Step 4 — Show the two sessions side by side

**What you should see:**

- Both sessions are running simultaneously. One is reading models, the other is fixing a Vue component.
- Run `git status` in each — different files changed, different branches, zero conflicts.
- Both sessions read from the same CLAUDE.md and `.claude/settings.json` — the configuration you invested in Phase 1 compounds here.
- The worktree session has its own directory: something like `demo-repo-worktree-xxxxxx/`.

This is Boris Cherny's workflow in miniature. He runs five of these. We're running two. The key insight: this is not multitasking. Each session is independently well-scoped. You classified the work, you planned it, and now two sessions execute in parallel. The preparation is what makes the parallelism manageable.

### Step 5 — Merge the fix

When both tasks are finished (tests passing etc), ask the main terminal to commit the main terminal's changes. 

Make sure in the terminal with the worktree all changes have been committed.

Then in the main terminal merge the timezone branch in it:

```bash
git merge <worktree-branch-name>
```

The timezone fix ships while the notification feature is still being built. Two tasks, two branches, one developer, zero context switches.

## Demo 3 — @-Mention a Model

Using `@` to give Claude precise file context. Instead of describing where the code lives, you point directly at it.

### Setup

Continue in the main session (or start a new one):

### Step 1 — Use @-mention to add a scope

Type the `@` character and show the autocomplete suggesting project files. Then enter:

```
@backend/app/Models/Item.php — add a scope `scopeExpiringSoon` that
returns items ending within the next hour. Use Carbon for the time
comparison. Only include active items.
```

**What you might notice:**

- Claude sees the existing `ends_at` cast (`'datetime'`) and uses Carbon methods that work with it.
- It sees the existing `scopeActive` scope and follows the same `Builder` return type pattern.
- The scope chains correctly with other scopes: `Item::active()->expiringSoon()->get()`.
- Without the @-mention, you would need to describe the model structure or hope Claude reads the right file. The `@` removes ambiguity.

> The scope will be used in the notification system — we need to know which auctions are ending soon so we can alert bidders. This is a small, precise task. One file, one method, one @-mention.

## Demo 4 — @-Mention a Controller for Refactoring

Using @-mention on a fat controller to extract logic. Same mechanic as Demo 27, but now the task is a refactor.

### Step 1 — Extract validation into a Form Request

```
@backend/app/Http/Controllers/Api/BidController.php — the store method
has inline validation scattered throughout it (checking amount, checking
status, checking ends_at, checking seller). Extract the validation logic
into a new PlaceBidRequest Form Request class. Update the controller to
use it. Keep the existing StoreBidRequest — it only has basic rules.

> **See:** `working/todo/features/feature-009-extract-bid-service.md`
```

Look at what Claude does with this. It reads the BidController — all the store method's lines — and identifies every validation check buried in the logic. It separates 'is this input valid?' from 'what do we do with valid input?'

### What you might notice

- Claude reads the controller and identifies the validation rules: amount must be numeric, must be higher than current price, auction must be active, must not have ended, bidder must not be the seller.
- It creates `app/Http/Requests/PlaceBidRequest.php` with extracted rules.
- It updates the controller's `store` method signature from `Request $request` to `PlaceBidRequest $request`.
- The inline validation checks in the controller are removed or simplified.
- Claude touched two files from one prompt because the @-mention gave it enough context to understand both source and destination.

> Notice that the existing `StoreBidRequest` in the codebase only validates that `amount` is required and numeric — three basic rules. The new `PlaceBidRequest` encodes actual domain rules. This is the difference between syntax validation and business validation.

## Demo 5 — Context Loading: Study Before You Build

Loading an entire directory into Claude's context to build a mental model of the domain before doing any work.

### Step 1 — Load all models

```
Read all files in backend/app/Models/ to understand the relationships
between User, Item, Bid, and Category. Summarise the relationships
you found — I need to know who connects to what.
```

Before building the notification feature, we want Claude to understand how the domain fits together. This is the 'study before you act' pattern. Load context deliberately so Claude's subsequent output is informed by relationships it would otherwise miss.

### What you might notice

- Claude reads 4 model files and produces a relationship map:
  - **User** hasMany Items (as seller, via `seller_id`), hasMany Items (as winner, via `winner_id`), hasMany Bids
  - **Item** belongsTo User (seller), belongsTo User (winner), belongsTo Category, hasMany Bids
  - **Bid** belongsTo User, belongsTo Item
  - **Category** hasMany Items
- The critical distinction: a Bid has a `user_id` (the bidder) and the Item has a `seller_id` (the seller). The notification goes to the previous highest bidder — not the seller, not the new bidder.
- This context stays in the session for all subsequent prompts. Every file Claude writes from here benefits from this understanding.

Claude now knows something that matters for the notification feature: when someone is outbid, the person to notify is the previous highest bidder. That's the `user_id` on the previous Bid, not the `seller_id` on the Item. This distinction is easy to get wrong. Loading context first prevents that mistake.

## Demo 6 — /compact Mid-Session

Compressing context before the big build.

### Step 1 — Check context usage

Look at the context usage indicator in your terminal. After loading all the models, reading the controller, and discussing relationships, we've used a noticeable chunk of the context window. We're about to build a multi-file feature. Let's free up space.

### Step 2 — Run /compact

```
/compact
```

Watch what Claude does. It summarises everything it has learned so far: the domain relationships, the plan from Phase 3, the scope we added, the Form Request we extracted. Then it drops the verbatim file contents.

### What you might notice

- The context usage indicator drops significantly.
- The knowledge is preserved in compressed form: Claude still knows Item has `ends_at`, still knows Bid belongs to both User and Item, still knows the project uses Form Requests and API Resources.
- But the raw file contents are gone, freeing space for the implementation ahead.

This is a habit, not an emergency measure. Compact before building, not when you hit the wall. Think of it as clearing your desk before starting a big project. The references are filed away — you can still use them, but they're not covering your workspace.

## Demo 7 — Rename Refactor: Mechanical Ripple

A codebase-wide rename that touches every layer. This is the kind of task that takes a human an hour of find-and-replace and inevitably misses something.

### Step 1 — Give the rename instruction

```
The team has decided "Item" is too generic for an auction platform.
Rename the Item model to Listing across the entire codebase. This means:
- Rename the model file and class (Item.php -> Listing.php)
- Update the migration table name to "listings"
- Update all relationships in User, Bid, and Category models
- Update the Filament Resource (ItemResource.php in app/Filament/)
- Update API routes and controllers (ItemController, ItemResource)
- Update Vue components that reference "item" (ItemCard, ItemDetailPage, etc.)
- Update all tests
- Update the factory and seeder

Keep the database table name as "listings" (plural convention).
```

Count the files as they change. This is a rename that touches every layer of the stack — models, controllers, resources, routes, Vue components, tests, factories, seeders, and the Filament admin panel. By hand, you'd miss something. You always miss something.

### What you might notice

- Relationships in other models update: `hasMany(Item::class)` becomes `hasMany(Listing::class)`.
- Vue components update: `ItemCard.vue` references, API endpoint paths in Axios calls, prop names.
- The Filament Resource updates: class name, `$model` property, navigation label.
- Route model binding updates: `{item}` becomes `{listing}` in `routes/api.php`.
- Count the files out loud as they appear. There will be 15-25 file touches.
- If Claude misses a file, point it out — this is a teaching moment about verification.

Claude handles the mechanical ripple. But you verify the result. A rename is exactly the kind of task where one missed reference breaks the whole app silently — a string literal in a Vue API call, a test fixture, a Filament navigation label. The Phase 5 verification step is not optional after a change like this.

### Step 2 — Undo the rename

After demonstrating, `/rewind` or

```bash
git checkout .
```

We're reverting this — it was a demonstration, not a permanent change. The codebase stays as 'Item' for the remaining demos.

## Demo 8 — Legacy Code Explainer

Using Claude to understand inherited code before refactoring it. Instead of jumping straight to "fix this," you ask Claude to explain.

### Step 1 — Ask Claude to explain the BidController

```
@backend/app/Http/Controllers/Api/BidController.php — Explain the store
method in detail. It's over 100 lines. What does each section do?
Why might it have been written this way? Are there any hidden assumptions
or edge cases I should worry about?
```

This is onboarding in 30 seconds. Instead of reading 120 lines and tracing every branch yourself, Claude breaks it down into logical sections and tells you what each one does.

### What you might notice

Claude should identify these sections:
1. **Item lookup** (line 24) — manual `findOrFail` instead of route model binding
2. **Status check** (lines 27-31) — verifies auction is active
3. **Expiry check** (lines 34-38) — verifies auction hasn't ended
4. **Self-bid prevention** (lines 41-45) — seller can't bid on own item
5. **Amount validation** (lines 48-52) — manual check instead of Form Request
6. **Price comparison** (line 56) — uses `<` instead of `<=` (the bug)
7. **Minimum increment** (lines 62-69) — hardcoded `$1.00` minimum
8. **Bid creation** (lines 72-80) — no transaction, no locking (race condition)
9. **Previous bidder logging** (lines 82-90) — previous bidder logging
10. **Auto-close logic** (lines 92-113) — closes auction if ending within 1 minute

Claude should flag:
- The race condition: no transaction or locking means two simultaneous bids could both pass validation.
- The `<` vs `<=` bug: a bid equal to the current price is accepted.
- The hardcoded increment: should be configurable.
- The inconsistent response format: normal bids return `BidResource`, auto-close returns a plain array.

> "You now understand code you didn't write, in 30 seconds, well enough to make informed decisions about what to refactor and what to leave alone. This is one of the highest-value uses of Claude Code — it produces no code at all, just understanding."

## Demo 9 — Test-First Execution: Red, Green, Done

Writing the test first, then implementing to make it pass. Claude follows the TDD loop.

### Step 1 — Write the test

```
Write a PHPUnit feature test for snipe protection: if a bid is placed
when the auction has less than 30 seconds remaining, the auction's
ends_at should be extended by 2 minutes from the time of the bid.
Include two test cases:
1. A bid placed with less than 30 seconds remaining SHOULD extend
2. A bid placed with plenty of time remaining should NOT extend

Create the test at tests/Feature/SnipeProtectionTest.php. Use
RefreshDatabase. Create users with User::factory()->create() —
don't use User::find().

> **See:** `working/todo/bugs/bug-001-bid-test-fails.md`
```

Notice I'm being specific about the test setup. I said `User::factory()->create()`, not `User::find(1)`. Why? Because there's a broken test in this codebase — `BidTest.php` — that uses `User::find(1)` without seeding the database. That's a gotcha we documented in CLAUDE.md. Specificity in the prompt prevents repeating mistakes.

### Step 2 — Run the test (expect failure)

```
Run the snipe protection test. It should fail because we haven't
implemented the feature yet.
```

**What you might notice:**

- The test runs and fails — this is the correct state (red).
- The failure message tells us exactly what's missing: the `ends_at` was not extended.
- This proves the test actually tests something. A test that passes before implementation is suspicious.

Red. The test fails. That's what we want. The test is real. It asserts against actual database state. Now we implement.

### Step 3 — Implement the feature

```
Now implement the snipe protection logic to make the test pass. Add it
to BidController@store — after the bid is created, check if the auction
ends within 30 seconds and extend by 2 minutes if so.
```

**What to notice:**

- Claude implements the logic in the controller, right after the bid creation.
- It re-runs the test — it passes (green).
- Claude did not skip the red step. It wrote the test, confirmed failure, then implemented.

Red, green, done. Claude respects your workflow. It didn't skip ahead and implement before confirming the test fails. It didn't write a test that passes trivially. The TDD loop works with Claude just like it works with a pair programming partner.

## Demo 10 — Form Request with Domain Rules

Claude generating validation logic with complex business rules — not just `required|string`, but real domain constraints.

### Step 1 — Create a comprehensive Form Request

```
Update the PlaceBidRequest Form Request we created earlier (or create
it if it doesn't exist) with these domain-aware validation rules:
- amount: required, numeric, must be greater than the item's current_price
  (not greater than or equal — strictly greater)
- The item must be active (status = 'active')
- The item must not have ended (ends_at > now)
- The authenticated user must not be the item's seller
- The bid must be at least $1.00 more than the current price (minimum
  increment)

Use custom validation rules or closures where Laravel's built-in rules
aren't enough. Include meaningful error messages — not "The field is
invalid" but "You cannot bid on your own listing." Use the authorize()
method for the self-bid check.
```

This is domain logic encoded as validation. Not just 'is this a number?' but 'does this bid make sense in the context of an auction?' Claude needs to understand the relationships we loaded in Demo 29 to get this right — it needs to know that Item has a `seller_id` and that the authenticated user's ID should not match it.

### What to notice

- The `authorize()` method checks `auth()->id() !== $this->route('item')->seller_id` — the seller cannot bid.
- The `amount` rule uses a closure or custom rule to compare against the item's `current_price` from the database.
- The minimum increment check enforces `amount >= current_price + 1.00`.
- Custom error messages are human-readable: "You cannot bid on your own listing" instead of "The user id field is invalid."
- The `prepareForValidation()` method might resolve the Item from the route for reuse.

Compare this to the original `StoreBidRequest` in the codebase. That one has three lines: required, numeric, min:0.01. This one encodes the entire bidding ruleset. The difference is context — Claude knew the domain because we loaded it.

## Demo 11 — Skill: Scaffold a New API Endpoint

Creating a reusable Claude Code skill that scaffolds a complete API endpoint every time it's invoked. Skills live in `.claude/skills/` and encode repeatable expertise.

### Step 1 — Create the skill file

```bash
mkdir -p demo-repo/.claude/skills
```

Create `.claude/skills/api-endpoint.md`:

```markdown
# Skill: Scaffold API Endpoint

When asked to create a new API endpoint, always create ALL of these:

1. **Route** — Add to `routes/api.php` inside the appropriate group
   (public or auth:sanctum)
2. **Controller method** — In the relevant controller, or create a new
   single-action controller if it doesn't fit an existing one
3. **Form Request** — Even for GET endpoints (for query parameter validation)
4. **API Resource** — For consistent response formatting (reuse existing
   Resources when the data shape matches)
5. **PHPUnit Feature Test** — Covering the happy path and at least one
   error case (404, 422, or 403)

Follow these conventions:
- Use route model binding where appropriate
- Use `slug` instead of `id` for public-facing category endpoints
- Return 200 for success, 201 for creation, 422 for validation, 404 for not found
- Wrap all responses in API Resources — never return raw arrays
- Use dot notation for route names (e.g., `category.items`)
- Run `docker compose exec app php artisan test` after scaffolding to verify
```

This skill file is committed to git. Every developer on the team, every Claude session, gets the same scaffolding standard. Write it once, benefit every time. Let's see it in action.

### Step 2 — Invoke the skill

Open a new claude session (or /exit / reopen an existing session) and type:

```
Using the api-endpoint skill, create GET /api/categories/{slug}/items
that returns all active items in a category, paginated (12 per page),
sorted by ending soonest. Include eager loading for seller and bids
count to avoid N+1 queries.
```

**What to notice:**

- Claude reads the skill file and follows every step without being reminded.
- All five artefacts are created: route in `routes/api.php`, controller method (likely added to `CategoryController` or a new `CategoryItemController`), Form Request for query params, reuses existing `ItemResource`, and a feature test.
- The test covers the happy path and a 404 for a nonexistent slug.
- The eager loading (`with(['seller'])->withCount('bids')`) prevents the N+1 bug that exists in the current `CategoryController@items` method.
- The route uses `{slug}` not `{id}` per the skill convention.

Five artefacts from one prompt, all following the same standard. The next time someone on the team needs a new endpoint, they type the same thing. The skill is the team's playbook for Claude. It compounds.

**Note:** This endpoint largely mirrors what `CategoryController@items` already does, but done correctly — with eager loading, API Resources, and a test. Point out the contrast with the existing implementation.

## Demo 12 — Anti-Pattern Catch: You Are the Quality Gate

Claude making a mistake during execution, and you catching it. This is the most important demo in Phase 4.

### Setup — Provoke an anti-pattern

We'll demonstrate Anti-pattern C: duplicated validation with divergent rules. This is already present in the codebase — the `BidForm.vue` frontend component has different validation rules than the backend.

### Step 1 — Ask Claude to add frontend validation

```
The BidForm.vue component at frontend/src/components/BidForm.vue needs
better user feedback. Add client-side validation that checks the bid
amount before submitting to the server. Show an error message if the
amount is too low.
```

I'm being deliberately vague. I didn't tell Claude what the server rules are. Let's see what it does.

### Step 2 — Let Claude write the code

**Do not interrupt.** Let Claude finish writing.

### Step 3 — Review the output

**What to watch for and notice:**

Look at the validation logic Claude adds. Compare it to the backend:

- **Backend** (`BidController@store`, line 56): uses `<` — accepts bids equal to current price (this is the bug).
- **Existing frontend** (`BidForm.vue`, line 25): uses `<=` — rejects bids equal to current price.
- Claude may add yet another variation, potentially matching either the buggy backend or the correct frontend.

What's wrong here? Look at the comparison operators...

- The frontend says `amount <= currentPrice` — "must be higher than."
- The backend says `amount < currentPrice` — "must be higher than or equal to" (the bug).
- If a user bids exactly the current price: the frontend blocks it, but the backend would accept it. Or vice versa.

### Step 4 — Correct it with Claude

```
There's a validation mismatch. The backend BidController uses < instead
of <= when comparing the bid amount to current_price (line 56) — that's
actually a bug. The correct check is <=. Fix the backend bug. Then make
sure the frontend validation matches exactly: amount must be strictly
greater than current_price. Don't duplicate the minimum increment check
on the frontend — let the server be the source of truth for business
rules. The frontend should only do basic sanity checks.
```

**What to take away from this:**

- Claude fixes it immediately — no argument, no repeated mistake.
- The anti-pattern was not random. Duplicated validation with slightly different rules is one of the most common bugs in full-stack applications.
- The lesson: Claude wrote reasonable code. But "reasonable" and "correct" are different things. The developer's job is to know the difference.

The anti-pattern was not malicious or random. Claude doesn't know your production data volume, your exact business rules, or the subtle difference between < and <=. That knowledge is yours. Claude handles the mechanics; you handle the judgement. This is why we call it 'collaboration at speed,' not 'delegation.'

> **Note — Escaping a bad trajectory:** If you've been correcting Claude repeatedly and the corrections keep generating new corrections, that's a signal. Don't push through — close the session, reset, and return to Plan Mode with what you've learned. Boris Cherny abandons 10–20% of sessions intentionally. A failing session is cost, not progress. Remember: walking away from a bad trajectory is a skill, not a failure.

> **Note — Other anti-patterns to watch for:** Beyond the duplicated validation shown here, two other common anti-patterns during execution: **(A) `Item::all()` without pagination** — fine with 10 test records, catastrophic with 100,000 production rows. **(B) Circular eager loading** — Claude adds `Item::with('bids.user.items.bids')` trying to be thorough, creating a reference that pulls the entire database into memory. If either surfaces naturally during your test, pause and ponder on it.
