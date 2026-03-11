# Phase 3: Plan — Demo & Live Demo Script

You've classified the outbid notification as complex. Don't just tell Claude 'build it.' Plan first — it's the difference between a 1/3 success rate and 80%+. This phase shows the audience that planning is not overhead. It is the work. Every minute spent here saves ten in execution.

## Demo 1 — Slash Command for Plans

Custom slash commands encode team conventions into reusable templates. Instead of every developer writing their own "give me a plan" prompt, you define the planning structure once and everyone uses it.

### Setup

Open a terminal in the BidBoard project root (the directory containing `docker-compose.yml`, `backend/`, `frontend/`).

### Step 1 — Create the /design slash command

```bash
mkdir -p .claude/commands
```

Create `.claude/commands/design.md`:

```markdown
Based on the task described below, produce a structured implementation plan.

## Format

### WHAT
Describe the task precisely. What is being built or changed? What is the expected behavior?

### WHERE
List every file that will be created or modified. Use full paths from project root. Typical locations in this project:
- Models: `backend/app/Models/`
- Controllers: `backend/app/Http/Controllers/Api/`
- Form Requests: `backend/app/Http/Requests/`
- Middleware: `backend/app/Http/Middleware/`
- Events: `backend/app/Events/`
- Listeners: `backend/app/Listeners/`
- Notifications: `backend/app/Notifications/`
- Filament Resources: `backend/app/Filament/Resources/`
- Filament Widgets: `backend/app/Filament/Widgets/`
- Blade views: `backend/resources/views/`
- Vue pages: `frontend/src/pages/`
- Vue components: `frontend/src/components/`
- Vue composables: `frontend/src/composables/`
- Routes: `backend/routes/api.php`, `backend/routes/web.php`
- Migrations: `backend/database/migrations/`
- Tests: `backend/tests/Feature/`, `backend/tests/Unit/`

### HOW
Describe the implementation approach step by step. Include:
- Database changes (migrations, schema)
- Backend logic (models, relationships, events, queues)
- API surface (routes, controllers, validation)
- Frontend changes (components, pages, API calls)
- Any configuration or service registration needed

### VERIFY
Define what success looks like:
- Specific tests that should pass
- Manual verification steps
- Edge cases to confirm

$ARGUMENTS
```

### Step 2 — Start a Claude session and show the command

Staert typing `/design` in a new claude session and watch... it appears in the autocomplete. Every developer who clones this repo gets this command. It's committed to git, just like the CLAUDE.md and settings we created in Phase 1.

### Step 3 — Run the command with a real feature

```
/design Add a watchlist feature so users can follow items they're interested in
```

- The output follows WHAT/WHERE/HOW/VERIFY exactly as templated
- Claude fills in BidBoard-specific paths — `backend/app/Models/Watchlist.php`, `backend/app/Http/Controllers/Api/WatchlistController.php`, `frontend/src/pages/WatchlistPage.vue`, etc.
- The WHERE section references the actual project structure because we pre-loaded it into the template
- The VERIFY section includes specific test cases, not generic "it should work"

The template did two things. First, it gave Claude a structure to follow, so you get consistent plans every time. Second, it pre-loaded the project's directory layout, so Claude doesn't have to discover it. That's time saved on every plan, for every developer on the team.

Do /clear to reset the state, we do not need this plan.

## Demo 2 — Full Plan Demo: Outbid Notification

Planning a complex, multi-layer feature end-to-end before writing a single line of code. This is the flagship demo for Phase 3. The audience sees Plan Mode produce a complete implementation blueprint.

### Setup

The scenario: when someone places a bid that outbids the previous highest bidder, the previous bidder should be notified by email and see a toast notification in the Vue frontend. This touches events, listeners, notifications, email templates, and frontend components.

BidBoard's current bid logic is all inline in `backend/app/Http/Controllers/Api/BidController.php` — over 100 lines of unstructured code with a placeholder `Log::info` where notifications should go (line 89). The plan needs to account for this existing mess.

### Step 1 — Enter Plan Mode

Press **Shift+Tab twice**. The mode indicator in the terminal changes.

Watch the mode indicator. Normal mode — one Shift+Tab — that's auto-accept. Two Shift+Tabs — that's Plan Mode. In Plan Mode, Claude does not write code. It does not edit files. It thinks and explains. That's exactly what we want right now.

### Step 2 — Send the plan prompt

```
/design When a user places a bid that outbids the current highest bidder,
notify the outbid user via email and show an in-app toast notification.
The notification should include the item name, the new bid amount,
and a link to the item page.
```

### Step 3 — Walk through the plan

**What Claude will produce (expected structure):**

```
WHAT
  Outbid notification system — email + in-app toast when a user is outbid

WHERE
  New files:
    backend/app/Events/BidPlaced.php
    backend/app/Listeners/SendOutbidNotification.php
    backend/app/Notifications/OutbidNotification.php
    backend/resources/views/emails/outbid.blade.php
    frontend/src/components/ToastNotification.vue
    backend/tests/Feature/OutbidNotificationTest.php
  Modified files:
    backend/app/Providers/AppServiceProvider.php (register event -> listener)
    backend/app/Http/Controllers/Api/BidController.php (dispatch event after bid)
    frontend/src/layouts/AppLayout.vue (mount toast component)

HOW
  1. Create BidPlaced event — carries the Bid model and previous highest bidder
  2. Create SendOutbidNotification listener — checks previous bidder exists,
     sends notification only if different from current bidder
  3. Create OutbidNotification — toMail() with Blade template,
     toBroadcast() for real-time via Laravel Echo
  4. Register event/listener binding in AppServiceProvider
  5. Create Blade email template with item name, amount, link
  6. Create Vue toast component — listens on Echo private channel
  7. Mount toast in AppLayout so it's available on all pages

VERIFY
  - PHPUnit: placing a bid dispatches BidPlaced event
  - PHPUnit: listener sends notification to previous bidder
  - PHPUnit: no notification if bidder outbids themselves
  - PHPUnit: no notification on first bid (no previous bidder)
  - Manual: place a bid, check email arrives, check toast appears
```

> **WHAT:** "Precise. Not 'add notifications.' It says what triggers it, who gets it, and through which channels."
>
> **WHERE:** "Several new files, several to be modified. Claude knows BidBoard's directory structure from the CLAUDE.md and the `/design` template. Notice it put the event in `backend/app/Events/`, the listener in `backend/app/Listeners/`, the notification in `backend/app/Notifications/` — all standard Laravel conventions."
>
> **HOW:** "Seven steps. The event carries the data, the listener decides whether to send, the notification handles the delivery. This is the kind of separation of concerns you'd want in a code review."
>
> **Note:** The broadcasting/Echo approach for the toast assumes infrastructure (Pusher, Soketi, or similar) that BidBoard doesn't have yet. In practice, you'd set up Echo or adjust the plan to use polling, which is exactly the kind of thing plan review catches.

> **VERIFY:** "Four automated tests and one manual check. Notice the edge cases: no self-notification, no notification on first bid. These are the tests a senior engineer would ask for."

### Step 4 — Iterate on the plan

Ask Claude to refine (for example: it did not specify ShouldQueue on the listener):

```
What about queuing the notification so it doesn't slow down the bid response?
```

**What to point out:**

- Claude revises the plan — it adds `ShouldQueue` to the listener
- It might also add a queue worker configuration note
- The plan evolves through conversation, not through code changes

We haven't written a line of code. But we already know every file, the order of operations, and the test cases. When we switch to execution, Claude has all of this loaded in context. That's why it will one-shot the implementation.

### Step 5 — Exit Plan Mode

Press **Shift+Tab** to return to normal mode. Do NOT execute the plan... execution is for Phase 4. Copy the plan in a new file, we will use it later.

## Demo 3 — Plan with Constraints

Constraints change plans fundamentally. Stating them upfront avoids rework. This demo shows the same task prompted two ways — once open-ended, once with constraints — to demonstrate how much the approach shifts.

### Setup

Same Claude session, still in the BidBoard project. The scenario: add CSV export of bid history for an item.

### Step 1 — Plan without constraints

Enter Plan Mode (Shift+Tab twice).

```
/design Add CSV export of bid history for an auction item
```

**What Claude will produce:** A simple plan — a controller method that queries all bids, builds a CSV string, returns it as a download. Maybe four or five lines in the HOW section. Straightforward. No authorization. No memory management. No row limits.

This plan works. It's correct. It will produce working code. But let's see what happens when we add the constraints that a production system actually needs.

### Step 2 — Plan with constraints

```
/design Add CSV export of bid history for an auction item.
Constraints:
- Only the item's seller can trigger the export (authorization)
- Maximum 10,000 rows — if more exist, export the most recent 10k
- Response must be streamed using a StreamedResponse, not buffered in memory
- Include a test that verifies the authorization check
```

**What Claude will produce (expected structure):**

```
WHAT
  Seller-only streamed CSV export of bid history, capped at 10k rows

WHERE
  backend/app/Http/Controllers/Api/BidExportController.php
  backend/app/Policies/ItemPolicy.php (add exportBids method)
  backend/app/Http/Requests/ExportBidHistoryRequest.php
  backend/routes/api.php
  backend/tests/Feature/BidExportTest.php

HOW
  1. Add exportBids gate to ItemPolicy — only seller
  2. Create Form Request with authorization via policy
  3. Controller uses StreamedResponse with callback
  4. Query uses ->latest()->limit(10000)->cursor() for memory efficiency
  5. Write CSV headers, then stream rows via cursor
  6. Route: GET /api/items/{item}/bids/export

VERIFY
  - Test: seller can export — returns 200 with CSV content-type
  - Test: non-seller gets 403
  - Test: export caps at 10,000 rows
  - Test: response headers indicate streamed content
```

**What changed?**

- The unconstrained plan: ~4 files, simple controller, no authorization, no streaming, no limits
- The constrained plan: 5 files including a Policy and Form Request, cursor-based streaming, row cap, authorization tests
- These are not the same plan with extras bolted on. The constrained version uses `StreamedResponse` with `cursor()` instead of buffering. It has a Policy. It has a Form Request. The architecture is different

If you'd executed the first plan and then realized you needed these constraints, how much would you throw away? The answer: most of it. The streaming alone forces a different controller structure. The authorization requires a Policy that didn't exist. Constraints don't add complexity to a plan. They produce a fundamentally different plan.

Clear the session using `/clear`.

## Demo 4 — Ultrathink Plan

Some problems are hard enough that standard planning misses edge cases. Ultrathink extends Claude's reasoning for architecturally significant decisions. This demo shows the qualitative difference in plan depth.

### Setup

Same Claude session. The scenario: design the auction closing system. When an auction's end time arrives, the system must close it and declare a winner. But there are hard edge cases — snipe protection, late schedulers, suspended users, concurrent bids during the closing window.

### Step 1 — Send the ultrathink prompt

```
Ultrathink about how to design BidBoard's auction closing system.

Requirements:
- A scheduled command checks for auctions past their end time
- If a bid was placed in the last 30 seconds, extend the end time by 30 seconds (snipe protection)
- Handle the case where the scheduler runs late (e.g., 2 minutes after end time)
- Handle items that close with no bids
- Handle items where the winning bidder's account was suspended between bidding and close
- This must be safe under concurrent bid placement during the closing window

For context, the Item model is at backend/app/Models/Item.php with fields:
status (active/closed), ends_at (datetime), winner_id (nullable), current_price.
The Bid model is at backend/app/Models/Bid.php.
There is no Services directory yet — backend/app/Services/ does not exist.
```

Notice the word 'ultrathink' at the start. That's the trigger. Alternatives are 'think hard' and 'think harder'... progressively deeper reasoning. 'Ultrathink' is the maximum. Watch the response time. It takes noticeably longer. That's the point.

### Step 2 — Wait some more......

It's thinking harder. That's what you're paying for: not speed, but depth. Watch what comes back. The difference isn't more words. It's more edge cases considered, more race conditions analyzed, more failure modes addressed.

### Step 3 — Walk through the plan

**What Claude will produce (expected structure):**

```
WHAT
  Auction closing system with snipe protection, late-scheduler resilience,
  and edge case handling

WHERE
  backend/app/Console/Commands/CloseExpiredAuctions.php
  backend/app/Services/AuctionClosingService.php
  backend/app/Events/AuctionClosed.php
  backend/app/Listeners/NotifyAuctionWinner.php
  backend/app/Listeners/NotifyAuctionSeller.php
  backend/app/Enums/AuctionStatus.php
  backend/database/migrations/xxxx_add_auction_status_fields.php
  backend/tests/Feature/AuctionClosingTest.php

HOW
  1. Scheduled command runs every 15 seconds (not every minute — closing
     precision matters for snipe protection)
  2. Query: items WHERE status = 'active' AND ends_at <= now()
  3. For each item, acquire a database row lock (SELECT FOR UPDATE)
     to prevent concurrent close attempts
  4. Check: was the most recent bid placed within 30 seconds of ends_at?
     If yes, extend ends_at by 30 seconds, release lock, skip to next
  5. Late scheduler handling: if now() - ends_at > 5 minutes, log a warning
     but still close normally — the snipe window has clearly passed
  6. No-bids handling: set status to closed, winner_id stays null, notify seller
  7. Winner-suspended handling: check winner's account status at close time.
     If suspended, fall back to second-highest bidder. If none valid,
     treat as no-bids.
  8. Dispatch AuctionClosed event with outcome details
  9. Separate listeners handle winner notification and seller notification

  Race condition analysis:
  - Bid placed during close: row lock prevents close from reading stale
    last-bid time. Bid must wait for lock release, then close re-evaluates.
  - Two scheduler instances running: row lock ensures only one closes
    any given auction.
  - Extension loop: cap at max 10 extensions to prevent infinite auctions.

VERIFY
  - Test: auction with no bids closes with null winner_id
  - Test: auction closes with correct winner
  - Test: bid in last 30 seconds extends ends_at
  - Test: extension caps at 10
  - Test: late scheduler (5+ minutes) still closes correctly
  - Test: suspended winner falls back to second bidder
  - Test: concurrent close attempts (simulate with two service calls)
```

**What you might notice:**

- **The race condition analysis section.** Standard planning would not have produced this. Ultrathink surfaces the row lock strategy, the concurrent scheduler scenario, and the extension cap — all without being prompted for them specifically.
- **The 15-second scheduler interval.** A standard plan would use the Laravel default of running every minute. Ultrathink reasoned that 60 seconds is too coarse for a 30-second snipe window.
- **The suspended-winner fallback chain.** Most developers would miss this entirely until it happened in production.
- **The Services directory.** Claude proposes `backend/app/Services/AuctionClosingService.php` even though the directory doesn't exist yet — it's the right architectural pattern for this level of complexity.

This is a plan you could hand to a senior engineer for review. It has the race condition analysis. It has the edge cases. It has the timing constraints. That's what ultrathink buys you. Use it when the cost of getting the architecture wrong is high — like a system that handles money and timing simultaneously.

BUT... Ultrathink is not for every task. It's slow and uses significant context. Don't ultrathink about adding a getter method or a simple CRUD endpoint. Use it when you would otherwise spend thirty minutes thinking about the problem yourself before feeling comfortable making a decision.



## Demo 5 — Plan with Caching

Caching plans expose hidden complexity. The cache itself is simple, the invalidation strategy is where things get complicated. Planning surfaces this before you're knee-deep in stale data bugs.

### Setup

Same Claude session. The scenario: the category items list page is slow. Cache it. But the cache needs to invalidate when a new bid is placed (changes "current bid" display) or when an item's status changes.

Caching sounds simple. It's the kind of task most developers would classify as simple and skip planning entirely. Let's see what happens when we plan it.

### Step 1 — Send the plan prompt

```
/design Add caching to the category items list endpoint.
The cache should invalidate when:
- A new bid is placed on any item in the category
- An item's status changes (new listing, auction closed, item removed)

Currently the endpoint is in backend/app/Http/Controllers/Api/CategoryController.php
and returns items with their current highest bid via the items() method.
The N+1 bug in that controller is a known issue (documented in `working/todo/bugs/category-page-slow-loading.md`) — don't fix it in this plan,
just cache around it.
```

### Step 2 — Walk through the plan

**What Claude will produce (expected structure):**

```
WHAT
  Cache category items list with targeted invalidation

WHERE
  backend/app/Http/Controllers/Api/CategoryController.php (add cache layer)
  backend/app/Observers/BidObserver.php (new — invalidate on bid)
  backend/app/Observers/ItemObserver.php (new — invalidate on status change)
  backend/app/Providers/AppServiceProvider.php (register observers)
  backend/tests/Feature/CategoryCacheTest.php

HOW
  1. Cache key: "category:{id}:items" with 5-minute TTL as safety net
  2. Controller wraps query in Cache::remember()
  3. BidObserver::created() — resolve the bid's item's category,
     forget "category:{categoryId}:items"
  4. ItemObserver — on status change or category change,
     forget both old and new category cache keys
  5. Edge case: item moves between categories — invalidate both
     source and destination category caches

VERIFY
  - Test: second request for same category hits cache (no DB query)
  - Test: placing a bid invalidates the category cache
  - Test: changing item status invalidates the category cache
  - Test: TTL expires and cache refreshes
```

**What you might notice:**

- **The observers.** Claude didn't put cache invalidation in the controller. It identified that bids and items change from different entry points — the bid API at `backend/app/Http/Controllers/Api/BidController.php`, the Filament admin panel at `backend/app/Filament/Resources/ItemResource.php`, possibly artisan commands. Model observers catch all of them.
- **The category move edge case.** If an admin moves an item from Electronics to Home Goods via Filament, both category caches need to invalidate. A controller-level cache clear would miss this.
- **The TTL as a safety net.** Five minutes means even if an invalidation path is missed, the worst case is five minutes of stale data, not permanent staleness.

## Demo 6 — Plan Review with Second Claude

Adversarial review. A second Claude session reviews the first session's plan with fresh eyes and no commitment to the approach. This catches structural gaps that the planning session's own context anchoring would miss.

### Setup

- **Terminal tab 1:** The original Claude session with the outbid notification plan from Demo 2
- **Terminal tab 2:** A fresh Claude session in the same BidBoard project

### Step 1 — Open a second terminal and start a new Claude session

Start a new claude session in a new terminal tab in the project's directory

### Step 2 — Ask the second Claude to review the plan

Paste your copy of the plan "from Demo 2 — Full Plan Demo: Outbid Notification" into the second session:

```
Review this implementation plan for gaps, risks, or missing edge cases:

WHAT: Outbid notification system — email + in-app toast when a user is outbid

WHERE:
  New: backend/app/Events/BidPlaced.php, backend/app/Listeners/SendOutbidNotification.php,
  backend/app/Notifications/OutbidNotification.php, backend/resources/views/emails/outbid.blade.php,
  frontend/src/components/ToastNotification.vue, backend/tests/Feature/OutbidNotificationTest.php
  Modified: backend/app/Providers/AppServiceProvider.php, backend/app/Http/Controllers/Api/BidController.php,
  frontend/src/layouts/AppLayout.vue

HOW:
  1. Create BidPlaced event with Bid model and previous highest bidder
  2. Listener checks previous bidder exists, sends notification if different from current bidder
  3. OutbidNotification with toMail() and toBroadcast()
  4. Register event/listener in AppServiceProvider
  5. Blade email template with item name, amount, link
  6. Vue toast component listening on Echo private channel
  7. Mount toast in AppLayout

VERIFY:
  - Placing a bid dispatches BidPlaced event
  - Listener sends notification to previous bidder
  - No notification if bidder outbids themselves
  - No notification on first bid

Focus on:
- Missing error cases
- Race conditions
- User states that would break assumptions
- Missing tests
```

### Step 3 — Review the gaps found

**What the second Claude might identify:**

- "What if the previous highest bidder deleted their account? The notification will fail on a null user."
- "What if two bids arrive simultaneously and both think they're outbidding the same person? The listener should re-query the previous bidder, not rely on the event payload."
- "The plan doesn't address notification preferences — what if a user opted out of email notifications?"
- "No rate limiting on notifications — a rapid bidding war could spam the outbid user with dozens of emails in minutes."
- "The BidController has no transaction or locking around bid creation (lines 71-80) — a race condition. Dispatching the event within that race window means the event could carry stale data." (documented in `working/todo/bugs/concurrent-bids-race-condition.md`)

Five minutes. Four or five real gaps found. None of them would have surfaced during implementation until something broke in production.

### Step 4 — Show how to incorporate the review

In practice, you'd go back to Terminal 1 and tell the first Claude to revise the plan with these findings. The revised plan adds:

```
HOW (additions from review):
  - Listener checks previous bidder exists and account is active before sending
  - Listener checks user notification preferences
  - Debounce: if user was already notified about this item in last 5 minutes,
    skip the email (still show toast)
  - Event payload includes previous bidder ID but listener re-queries
    to handle concurrent bid race

VERIFY (additions from review):
  - Test: deleted previous bidder — no notification, no error
  - Test: user with email notifications disabled — no email, toast still shows
  - Test: rapid bidding — only one email per 5-minute window per item per user
```

**What you might notice:**

- The second Claude has no ego, no attachment to the plan, and no reluctance to find problems
- It found the race condition interaction with the existing BidController bug — that's cross-cutting knowledge that the first session might have overlooked because it was focused on the notification feature
- This is the cheapest quality improvement in the entire workflow: five minutes for structural gaps that would cost hours to fix after implementation

This is what I constantly do and what the Anthropic Security Engineering team also does. They call it adversarial review. A fresh Claude reviews a plan from a first Claude. It catches the things the planner's own context anchoring would miss. Five minutes, four gaps, zero ego.

## Demo 7 — UI Plan: Filament Dashboard

Planning UI work. Developers often skip planning for frontend and admin panel work because it feels visual and iterative. But component structure, data queries, and layout decisions benefit from planning just as much as backend logic.

### Setup

Same Claude session (or a fresh one). The scenario: enhance the Filament admin dashboard with three new widgets.

BidBoard already has Filament resources set up at `backend/app/Filament/Resources/` for Items, Users, and Categories. There are resource pages at `backend/app/Filament/Resources/ItemResource/Pages/` and `backend/app/Filament/Resources/UserResource/Pages/`. The dashboard has one existing widget — `backend/app/Filament/Widgets/StatsOverview.php` — showing basic stat cards (active auctions, bids today, revenue, new users). We want to add three more widgets alongside it.

### Step 1 — Send the plan prompt

```
/design Add three widgets to the Filament admin dashboard:
1. Revenue chart — line chart showing total winning bid amounts per day
   for the last 30 days
2. Auction activity timeline — real-time feed of the last 20 events
   (new bid, item listed, auction closed) with timestamps and links
3. Top sellers card — ranked list of the top 10 sellers by total revenue
   from closed auctions

BidBoard uses Filament v3. The existing Filament resources are at
backend/app/Filament/Resources/ (ItemResource, UserResource, CategoryResource).
The Item model has status (active/closed), current_price, winner_id, ends_at.
The Bid model has item_id, user_id, amount, created_at.
```

### Step 2 — Walk through the plan

**What Claude will produce (expected structure):**

```
WHAT
  Three Filament dashboard widgets: revenue chart, activity timeline,
  top sellers card

WHERE
  backend/app/Filament/Widgets/RevenueChartWidget.php
  backend/app/Filament/Widgets/AuctionActivityWidget.php
  backend/app/Filament/Widgets/TopSellersWidget.php
  backend/resources/views/filament/widgets/auction-activity.blade.php
    (custom view for timeline)
  backend/tests/Feature/Filament/DashboardWidgetTest.php

HOW
  1. RevenueChartWidget extends ChartWidget
     - getData() queries Item::where('status', 'closed')
       grouped by close date, summing current_price (the winning amount)
     - 30-day window, filled with zero for days with no sales
     - Chart type: line, with area fill

  2. AuctionActivityWidget extends Widget (custom Blade view)
     - Queries a union of recent events: bids (with user + item),
       new listings, auction closes
     - Ordered by timestamp, limited to 20
     - Each entry: icon, description, relative timestamp, link to resource
     - Livewire polling every 30 seconds for near-real-time updates

  3. TopSellersWidget extends Widget
     - Queries users who have sold items, joins closed items,
       sums current_price for closed auctions
     - Top 10, displayed as ranked list with name and revenue
     - Links to seller's Filament UserResource page

  4. Dashboard layout: revenue chart full-width top row,
     activity timeline and top sellers side by side below

VERIFY
  - Test: revenue chart returns correct data shape for chart.js
  - Test: activity widget shows mixed event types in order
  - Test: top sellers calculation matches manual sum
  - Manual: dashboard loads under 500ms with seeded data
```

**What you might notice:**

- **The layout decision in step 4.** Claude didn't just plan three isolated widgets. It planned how they sit together on the page. That's the kind of thing you'd discover looks wrong after building all three.
- **The Livewire polling choice for the activity widget.** Claude chose polling over websockets for the admin panel. That's a reasonable tradeoff — admin users don't need sub-second updates. This is a design decision that would normally happen mid-implementation. Now it's in the plan where you can approve or change it.
- **The data queries.** Each widget's data source is specified — `Item::where('status', 'closed')` for revenue, a union query for activity, a join with sum for top sellers. These are the queries that would need review for performance. Now you can review them before they're written.
- **The custom Blade view.** The activity timeline needs a custom view because it's not a standard chart or stat. Claude identified this and planned the view file location. The other two use built-in Filament widget types.

UI plans look different from backend plans. They include layout, data shape, interaction patterns, and refresh strategies. Planning UI work is not about designing pixels — it's about deciding on component boundaries and data flow before you start building.

> **Note:** You can also drag a wireframe or mockup directly into the Claude Code terminal. Claude reads images and incorporates them into the plan. If you had a Figma screenshot of the dashboard layout, you could drop it in instead of describing the layout in words.