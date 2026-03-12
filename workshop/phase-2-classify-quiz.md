# Phase 2: Classify — Quiz

Before writing a single prompt, spend thirty seconds deciding what kind of task you're dealing with. That decision determines how much Claude does, how much you do, and whether you even use Claude at all.

---

## How to Use This Quiz

For each case below:

1. Read the task description
2. Answer the **triage questions** honestly
3. Check the task against the **delegation criteria**
4. Pick where on the **spectrum** this task belongs

When you've classified all six cases, check your answers against the **[Answer Key](#answer-key)** at the end.

---

## Triage Questions

Use these questions to assess the task. Then check your assessment against the delegation criteria to confirm your instinct.

For every task, ask yourself:

| # | Question |
|---|----------|
| **Q1** | Is the output verifiable? (Can you objectively tell whether it worked?) |
| **Q2** | Is it boring or repetitive? (Correctness, not creativity?) |
| **Q3** | Is it peripheral to the critical path? (Low cost if slightly wrong?) |
| **Q4** | Can you sniff-check the output in under two minutes? |
| **Q5** | Does it take less than ten minutes to do yourself? |

## Delegation Criteria

Use this table to confirm your assessment. If most criteria land on the left, delegate. If any land on the right, consider staying closer.

| Delegate fully | Stay close |
|----------------|------------|
| Reversible | Hard to undo |
| Well-defined patterns | Novel design |
| Tests will catch errors | No safety net |
| Low business risk | Core business logic |

## The Spectrum

| Mode | Claude's role | Your role |
|------|---------------|-----------|
| **A — Delegate** | Does everything | Glance at diff |
| **B — Collaborate** | Plans briefly, then executes | Review plan, check result |
| **C — Plan only** | Plans deeply, doesn't execute | Iterate on plan, decide architecture |
| **D — Advise** | Explains trade-offs, pokes holes | Write the code yourself |

---

## Case 1

> The category page shows items in insertion order — oldest first. We want newest first.

| Question | Your Answer |
|----------|-------------|
| Q1 — Verifiable? | |
| Q2 — Boring/repetitive? | |
| Q3 — Peripheral? | |
| Q4 — Sniff-check in 2 min? | |
| Q5 — Under 10 min yourself? | |

**Your classification:** ☐ A — Delegate · ☐ B — Collaborate · ☐ C — Plan only · ☐ D — Advise

---

## Case 2

> The maximum auction duration is hardcoded to 7 days. Change it to 14.

| Question | Your Answer |
|----------|-------------|
| Q1 — Verifiable? | |
| Q2 — Boring/repetitive? | |
| Q3 — Peripheral? | |
| Q4 — Sniff-check in 2 min? | |
| Q5 — Under 10 min yourself? | |

**Your classification:** ☐ A — Delegate · ☐ B — Collaborate · ☐ C — Plan only · ☐ D — Advise

---

## Case 3

> Add an "Ending Soon" section to the homepage that shows auctions ending within the next 24 hours.

| Question | Your Answer |
|----------|-------------|
| Q1 — Verifiable? | |
| Q2 — Boring/repetitive? | |
| Q3 — Peripheral? | |
| Q4 — Sniff-check in 2 min? | |
| Q5 — Under 10 min yourself? | |

**Your classification:** ☐ A — Delegate · ☐ B — Collaborate · ☐ C — Plan only · ☐ D — Advise

---

## Case 4

> When a user is outbid on an auction, they should receive an email notification.

| Question | Your Answer |
|----------|-------------|
| Q1 — Verifiable? | |
| Q2 — Boring/repetitive? | |
| Q3 — Peripheral? | |
| Q4 — Sniff-check in 2 min? | |
| Q5 — Under 10 min yourself? | |

**Your classification:** ☐ A — Delegate · ☐ B — Collaborate · ☐ C — Plan only · ☐ D — Advise

---

## Case 5

> Two users occasionally end up both "winning" the same auction. The bidding endpoint doesn't seem to handle simultaneous requests correctly.

| Question | Your Answer |
|----------|-------------|
| Q1 — Verifiable? | |
| Q2 — Boring/repetitive? | |
| Q3 — Peripheral? | |
| Q4 — Sniff-check in 2 min? | |
| Q5 — Under 10 min yourself? | |

**Your classification:** ☐ A — Delegate · ☐ B — Collaborate · ☐ C — Plan only · ☐ D — Advise

---

## Case 6

> Rename the `Item` model to `Listing` across the entire codebase — models, controllers, routes, Vue components, tests, factories, seeders, Filament admin resources. Over 20 files affected.

| Question | Your Answer |
|----------|-------------|
| Q1 — Verifiable? | |
| Q2 — Boring/repetitive? | |
| Q3 — Peripheral? | |
| Q4 — Sniff-check in 2 min? | |
| Q5 — Under 10 min yourself? | |

**Your classification:** ☐ A — Delegate · ☐ B — Collaborate · ☐ C — Plan only · ☐ D — Advise

---

## Answer Key

### Case 1 — Category sort order

| Question | Answer |
|----------|--------|
| Q1 — Verifiable? | **Yes.** Open the page, check the order. |
| Q2 — Boring/repetitive? | **Yes.** One method call, no creativity required. |
| Q3 — Peripheral? | **Yes.** Display order, not business logic. |
| Q4 — Sniff-check in 2 min? | **Yes.** One-line diff. |
| Q5 — Under 10 min yourself? | **Yes.** Find the file, add one line. |

**Classification: A — Delegate**

The fix is a single method call — `->orderByDesc('created_at')` on the query in `CategoryController`. Claude's value is locating the file and making the edit without you context-switching into the codebase. No plan needed. No verification beyond a quick glance at the diff.

Delegation criteria check: Reversible ✓ · Well-defined pattern ✓ · Tests catch errors ✓ · Low business risk ✓

---

### Case 2 — Max auction duration

| Question | Answer |
|----------|--------|
| Q1 — Verifiable? | **Yes.** Change the value, run the app, confirm the duration limit changed. |
| Q2 — Boring/repetitive? | **Yes.** Find-and-replace a number. |
| Q3 — Peripheral? | **Yes.** Configuration, not logic. |
| Q4 — Sniff-check in 2 min? | **Yes.** It's a config value — the diff will be small and obvious. |
| Q5 — Under 10 min yourself? | **Yes** — if you know both locations. Maybe not if you miss the second one. |

**Classification: A — Delegate**

Config changes are tiny in effort but require knowing the codebase. The value `7` is hardcoded in two places — the controller and the form request. Claude finds both; a developer might miss the second one. Claude's value here is navigation, not reasoning. Still fully delegatable.

Delegation criteria check: Reversible ✓ · Well-defined pattern ✓ · Tests catch errors ✓ · Low business risk ✓

---

### Case 3 — "Ending Soon" homepage section

| Question | Answer |
|----------|--------|
| Q1 — Verifiable? | **Yes.** Open the homepage, check for the section. Run tests. |
| Q2 — Boring/repetitive? | **Partially.** The pattern exists (ItemCard), but the scope/endpoint is new. |
| Q3 — Peripheral? | **Mostly.** It's a new UI feature, not core business logic. |
| Q4 — Sniff-check in 2 min? | **Borderline.** Two to three files, needs a quick review of the query and component integration. |
| Q5 — Under 10 min yourself? | **No.** API endpoint + Vue component + integration takes longer. |

**Classification: B — Collaborate**

This touches a new API endpoint (or scope), the homepage Vue component, and reuses the existing ItemCard. Two or three files with a bounded scope. Without a brief plan, Claude might over-engineer it — a full filtering system, a new composable, a custom component. The brief plan keeps it scoped. Then let Claude execute.

Delegation criteria check: Reversible ✓ · Well-defined patterns (mostly) ✓ · Tests catch errors ✓ · Low business risk ✓ — but the multi-file scope means you want a quick plan to keep things focused.

---

### Case 4 — Outbid email notifications

| Question | Answer |
|----------|--------|
| Q1 — Verifiable? | **Partially.** You can test individual pieces, but the full flow is harder to verify without integration tests. |
| Q2 — Boring/repetitive? | **No.** Architectural decisions are involved: queuing, rate limiting, preferences. |
| Q3 — Peripheral? | **No.** Notifications are user-facing and affect the bidding experience. |
| Q4 — Sniff-check in 2 min? | **No.** Seven components across multiple concerns. |
| Q5 — Under 10 min yourself? | **No.** This is a multi-hour feature. |

**Classification: C — Plan only**

Start counting the pieces: a Laravel Event (`UserOutbid`), a Listener, a Notification class, a Blade email template, controller updates to fire the event, and tests for all of it. That's six or seven components. Then count the open questions: should it be queued or synchronous? What about rate limiting so users don't get spammed? Should there be notification preferences? That's three architectural decisions with no obvious default. If you let Claude execute without a plan, it makes those decisions silently — and you discover them in code review. Plan first, agree on the approach, then execute.

Delegation criteria check: Reversible ✓ · Novel design ✗ (open questions) · Tests catch errors (partially) · Low business risk (debatable) — the open questions and multi-concern scope demand a proper plan before any code is written.

---

### Case 5 — Concurrent bidding race condition

| Question | Answer |
|----------|--------|
| Q1 — Verifiable? | **Hard.** Race conditions pass every test in isolation and fail under production load. |
| Q2 — Boring/repetitive? | **No.** This is a design decision: pessimistic locking, optimistic locking, or queue serialization — each with different trade-offs. |
| Q3 — Peripheral? | **No.** This is the core business logic. Money is involved. |
| Q4 — Sniff-check in 2 min? | **No.** Concurrency logic requires careful reasoning about failure modes. |
| Q5 — Under 10 min yourself? | **Maybe** — but only if you already understand the trade-offs. |

**Classification: D — Advise**

This is the most debated case. Investigation reveals `BidController@store` is 120 lines with no transaction and no locking — a textbook race condition. The fix itself might be only ~6 lines of code, but it's business-critical code that handles money. It requires choosing between `SELECT FOR UPDATE`, a version column, or queue serialization. Claude can explain those trade-offs brilliantly — that's where it shines. But the decision and the code should be yours. At 2 AM during a production incident, you need to explain every line.

Delegation criteria check: Hard to undo ✗ · Novel design ✗ · No safety net ✗ (race conditions evade tests) · Core business logic ✗ — every criterion says "stay close." Use Claude to think. Write the code yourself.

---

### Case 6 — Rename Item to Listing across 20+ files

| Question | Answer |
|----------|--------|
| Q1 — Verifiable? | **Yes.** Tests pass, app runs, no references to the old name remain. |
| Q2 — Boring/repetitive? | **Yes.** Every change follows the same pattern: find `Item`, replace with `Listing`. |
| Q3 — Peripheral? | **Yes.** A rename doesn't change behaviour. |
| Q4 — Sniff-check in 2 min? | **Yes.** The diff is large but every change is the same pattern — scan for outliers. |
| Q5 — Under 10 min yourself? | **No.** Twenty-plus files, manually tedious. |

**Classification: A — Delegate**

This one surprises people. A rename touching 20+ files sounds complex, but it's entirely mechanical. Every change follows the same pattern. There are no architectural decisions, no trade-offs, no business logic. Claude handles the ripple; you verify the edges.

Delegation criteria check: Reversible ✓ · Well-defined pattern ✓ · Tests catch errors ✓ · Low business risk ✓

**The key insight:** complexity is not the same as size. A 20-file rename is large but simple. A 6-line race condition fix is small but complex. The classification is about judgment required, not file count.
