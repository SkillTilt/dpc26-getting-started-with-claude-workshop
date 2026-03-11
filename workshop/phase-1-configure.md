# Phase 1: Configure — Demo Outline

**Narrative arc:** "You just inherited BidBoard — a Laravel auction platform. You don't know the codebase. Claude doesn't either. Before you write a single line of code, you teach Claude about the project. Everything you invest here pays dividends in every phase that follows."

**Total time estimate:** ~45 minutes

---

## Demo 1 — CLAUDE.md Setup

**What we show:** CLAUDE.md is the onboarding document for an agent that starts fresh every session. Writing one from scratch turns Claude from a talented stranger into an informed teammate.

**The BidBoard scenario:** BidBoard has no CLAUDE.md. Attendees open the project in Claude Code and run `/init`. Claude explores the codebase and produces a baseline. Then we improve it manually — because Claude gets the obvious stuff right but misses what only a human who knows the project would know.

**Key moments:**

- Run `/init` and watch Claude scan the project — it detects Laravel, Filament, Vue, Tailwind, Composer, PHPUnit
- Open the generated CLAUDE.md — point out what it got right (stack, structure, main commands) and what it missed (domain rules, conventions, gotchas)
- Add the missing sections manually:
  - Stack details: Laravel + Filament admin, Vue 3 + Tailwind frontend, SQLite for local dev
  - Conventions: Composition API (not Options API), Form Requests for validation, API Resources for responses, PSR-4 autoloading
  - Package manager: Composer
  - Test runner: PHPUnit (`./vendor/bin/phpunit`)
  - Formatter: Laravel Pint (`./vendor/bin/pint`)
  - Static analysis: PHPStan (`./vendor/bin/phpstan`)
  - Domain rules: money stored as integers (cents), dates in UTC, bids are never deleted
- Before/after comparison: give Claude a prompt like "Add an API endpoint for placing a bid" — without CLAUDE.md it returns raw arrays and inline validation; with CLAUDE.md it uses Form Requests and API Resources from the start

**Approximate time:** 8 minutes

---

## Demo 2 — CLAUDE.md Gotchas

**What we show:** The gotchas section is where project-specific knowledge lives — the things a new developer would get wrong on their first day. These are the highest-leverage lines in the entire CLAUDE.md.

**The BidBoard scenario:** BidBoard has real traps. Money is in cents but the database column is called `price` (not `price_cents`). Bids must never be deleted. The Vue frontend uses Composition API exclusively. Filament resources live in a non-obvious directory. These are exactly the kind of things Claude will get wrong without explicit instruction.

**Key moments:**

- Add gotchas one at a time, explaining why each one matters:
  - "Never delete bids — only soft-delete items" (data integrity for auction history)
  - "All monetary values are integers representing cents" (avoids floating-point rounding in auction math)
  - "Vue components use Composition API with `<script setup>`, never Options API" (consistency across the frontend)
  - "Filament resources live in `app/Filament/Resources/`" (Claude tends to look in the wrong place)
  - "API responses must use API Resources (`App\Http\Resources\`), never return raw arrays or models" (contract stability)
- Show what happens when Claude violates one: prompt "Show me how to display a bid amount in the Vue frontend" — without the gotcha it uses `bid.price` as dollars; with it, it divides by 100 and formats correctly
- The takeaway: gotchas are cheaper to write than bugs are to fix

**Approximate time:** 5 minutes

---

## Demo 3 — Permissions Setup

**What we show:** Permission rules eliminate the "allow/deny" prompt fatigue for safe commands while keeping guardrails on dangerous ones. This is committed to git — the whole team gets the same boundaries.

**The BidBoard scenario:** Claude will need to run PHPUnit, Pint, PHPStan, and Artisan commands constantly while working on BidBoard. Without permissions configured, every single invocation triggers a confirmation prompt.

**Key moments:**

- Start without permissions — ask Claude to "run the test suite and fix any failures." Count the permission prompts (there will be several)
- Create `.claude/settings.json` with allow rules:
  - `./vendor/bin/phpunit *`
  - `./vendor/bin/pint *`
  - `./vendor/bin/phpstan *`
  - `php artisan *`
- Run the same prompt again — zero interruptions on allowed commands
- Point out that this file is committed to version control — next developer who clones BidBoard gets the same setup on day one
- Mention the deny list exists too (we will use it in demo 6)

**Approximate time:** 4 minutes

---

## Demo 4 — Auto-Memory

**What we show:** Beyond CLAUDE.md (what you teach deliberately), Claude also learns from sessions automatically. Auto-memory captures patterns Claude discovers while working, so future sessions start smarter.

**The BidBoard scenario:** During a session, Claude encounters the cents-based money pattern in the codebase. It makes a mistake (displays raw cents as dollars), gets corrected, and that correction gets stored in memory. Next session, it knows without being told.

**Key moments:**

- In a session, prompt Claude with something that exposes the money pattern — e.g., "Add a helper that formats a bid amount for display"
- Claude gets it wrong (uses the raw integer as dollars)
- Correct it: "That's cents, not dollars. Divide by 100 and format as currency."
- Claude fixes it and stores the learning
- Show the memory: `/memory` to see what Claude retained
- Start a new session and ask a similar question — Claude gets it right this time without being told
- Explain the relationship: CLAUDE.md is for rules you control and enforce; auto-memory is for patterns Claude picks up organically. Critical rules belong in CLAUDE.md because you cannot rely on auto-memory for correctness guarantees

**Approximate time:** 5 minutes

---

## Demo 5 — Hook: Auto-Format on Save

**What we show:** Hooks let you run commands automatically when Claude performs certain actions. A PostToolUse hook on file writes ensures every file Claude touches is formatted before you even see it.

**The BidBoard scenario:** Claude writes a new controller method in BidBoard. The code works but has minor formatting inconsistencies that would fail Laravel Pint. Instead of catching this in review, the hook runs Pint automatically after every file write.

**Key moments:**

- First, show the problem: ask Claude to "Add a method to the ItemController that returns paginated items with their current highest bid." The code works but has formatting issues. Run `./vendor/bin/pint --test` — it reports violations.
- Add the PostToolUse hook to `.claude/settings.json`:
  - Matcher: `Write|Edit`
  - Command: `./vendor/bin/pint --dirty || true`
- Run the same prompt again
- Show that after every file write, Pint fires automatically in the background
- Open the file — it is perfectly formatted. Run `./vendor/bin/pint --test` — clean
- Explain the `|| true`: the hook should never block Claude. If Pint errors on a syntax issue, Claude should still proceed. The goal is improvement, not interruption

**Approximate time:** 5 minutes

---

## Demo 6 — Hook: Block Dangerous Commands

**What we show:** PreToolUse hooks can intercept and block commands before they execute. This is your safety net for destructive operations that should never run without explicit human intent.

**The BidBoard scenario:** BidBoard uses SQLite with seed data (categories, sample items, users). Running `php artisan migrate:fresh` or `php artisan db:wipe` would destroy everything and require a full reseed. These commands should be blocked.

**Key moments:**

- Add a PreToolUse hook to `.claude/settings.json`:
  - Matcher: `Bash`
  - Command: a script that checks `$CLAUDE_TOOL_INPUT` for `migrate:fresh` or `db:wipe` — if found, prints a block message and exits with code 1
- Prompt Claude with something that might trigger it: "The database schema seems out of date. Reset it and start fresh."
- Claude attempts `php artisan migrate:fresh` — the hook intercepts it
- Claude receives the block message, explains what happened, and asks for guidance instead of silently destroying data
- Point out: Claude does not just fail. It receives the block message and can reason about it. Write hooks that explain *why* something was blocked — Claude will surface that explanation to you

**Approximate time:** 4 minutes

---

## Demo 7 — Hook: Auto-Test After Edits

**What we show:** Hooks can run tests automatically after Claude edits specific types of files — creating a tight feedback loop where Claude knows immediately if it broke something.

**The BidBoard scenario:** Claude edits the `Bid` model or the `BidController`. The hook detects the file path and runs the relevant PHPUnit test file automatically. If the test fails, Claude sees the failure output and can self-correct before you even review the change.

**Key moments:**

- Add a PostToolUse hook that:
  - Matches `Write|Edit`
  - Checks if the edited file is a model or controller
  - Runs the corresponding PHPUnit test
- Ask Claude to "Add a scope to the Bid model that returns only bids above a given amount"
- Claude writes the scope, the hook fires, the relevant test runs
- If the test passes: Claude proceeds confidently. If it fails: Claude sees the output and fixes the issue immediately
- Point out the feedback loop: Claude does not need you to tell it something broke. The hook gives it the signal automatically. This is the beginning of a self-correcting workflow

**Approximate time:** 5 minutes

---

## Demo 8 — MCP: GitHub

**What we show:** MCP (Model Context Protocol) servers connect Claude to external tools. The GitHub MCP lets Claude read issues, PRs, and comments directly — no browser switching, no copy-pasting ticket descriptions into prompts.

**The BidBoard scenario:** BidBoard's GitHub repo has open issues — bug reports, feature requests, questions from users. Instead of reading them in the browser and summarizing for Claude, Claude reads them directly.

**Key moments:**

- Show the MCP setup: `claude mcp add github -- npx @modelcontextprotocol/server-github`
- Prompt: "Look at the open issues in this repo. Find any bug reports and summarize them."
- Claude reads the issues directly from GitHub, returns summaries with issue numbers, labels, and key details from the issue body
- Follow-up prompt: "Pick the highest-priority bug and give me a plan for fixing it." — Claude pulls context from the issue description, comments, and any linked files to produce a specific plan
- Point out: the context flows from issue tracker to implementation plan without any human copy-paste step. The issue becomes the spec

**Approximate time:** 4 minutes

---

## Demo 9 — MCP: Context7

**What we show:** Context7 gives Claude access to up-to-date library documentation. Instead of relying on training data (which may be outdated), Claude can look up current docs for Laravel, Filament, and other dependencies.

**The BidBoard scenario:** BidBoard uses Filament for its admin panel. Attendees need to add a relation manager to the Category resource (to manage items within a category). Filament's API for relation managers has specific conventions that Claude's training data might not cover precisely.

**Key moments:**

- Show the MCP setup: `claude mcp add context7 -- npx @anthropic/context7-mcp`
- Prompt: "I need to add a relation manager to the Filament Category resource so I can manage items from within the category edit page. Look up the current Filament docs for relation managers."
- Claude fetches the current Filament documentation, finds the relation manager section, and produces code that matches the current API — not a hallucinated version from older training data
- Another example: "Look up Laravel's pessimistic locking docs — I need to lock a row when placing a bid to prevent race conditions"
- Point out: this is particularly valuable for fast-moving libraries like Filament where the API changes between major versions. Claude checks the docs instead of guessing

**Approximate time:** 4 minutes

---

## Demo 10 — MCP: DevTools (Browser)

**What we show:** A browser MCP (such as Playwright) lets Claude open a real browser, navigate to your app, and verify that what it built actually works visually — not just that the code compiles.

**The BidBoard scenario:** BidBoard runs on `localhost:8000`. After making changes, Claude can open the auction listing page in a real browser, take a screenshot, and verify the layout, data display, and formatting are correct.

**Key moments:**

- Show the MCP setup for a browser tool (Playwright MCP or similar)
- Prompt: "Open BidBoard at localhost:8000 and verify the auction listing page displays correctly. Check that item prices show as formatted currency, not raw cents."
- Claude opens the browser, navigates to the page, captures what it sees, and reports back: "The listing page loads. Items display with images, titles, and prices. Prices show correctly as $12.50 format."
- Follow-up: "Now check the admin panel at localhost:8000/admin — verify the dashboard loads and shows the category count"
- Point out: this closes the verification loop. Claude can now write code, run tests, AND check the browser. The three-layer verification (tests pass, linter passes, browser looks right) is the foundation for the Verify phase later

**Approximate time:** 5 minutes

---

## Phase 1 Wrap-Up

**Time check:** ~45 minutes total

**The arc to reinforce:** In 45 minutes, we went from "Claude knows nothing about this project" to a fully configured environment where:

- Claude knows the stack, conventions, and domain rules (CLAUDE.md)
- Claude remembers patterns across sessions (auto-memory)
- Safe commands run without friction (permissions)
- Code is auto-formatted on every write (PostToolUse hook)
- Destructive commands are blocked before they execute (PreToolUse hook)
- Tests run automatically after edits (PostToolUse hook)
- Claude can read GitHub issues, look up current docs, and verify work in a real browser (MCP servers)

**Transition to Phase 2:** "Claude now knows BidBoard as well as any new team member. Better, actually — because it will read the CLAUDE.md every single time, which is more than most humans do with onboarding docs. Now the question is: what kind of work do you hand it? That's Phase 2 — Classify."
