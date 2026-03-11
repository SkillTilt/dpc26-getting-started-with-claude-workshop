# Phase 1: Configure — Demo & Code-Along Script

**Narrative arc:** "You just inherited BidBoard — a Laravel auction platform. You don't know the codebase. Claude doesn't either. Before you write a single line of code, you teach Claude about the project. Everything you invest here pays dividends in every phase that follows."

**Repository:** https://github.com/SkillTilt/dpc26-getting-started-with-claude-workshop

**Prerequisites:** BidBoard is running via Docker Compose (`docker compose up -d`), database is seeded, frontend is at `http://localhost:3000`, backend API at `http://localhost:80`.

## Demo 1 — CLAUDE.md: From Stranger to Teammate

### Setup

Open a terminal in the BidBoard project root (the directory containing `docker-compose.yml`, `backend/`, `frontend/`).

```bash
cd demo-repo
claude
```

### Step 1 — Run /init

```
/init
```

**After it finishes,** a new file was added in your project root `CLAUDE.md`, open it and inspect it:

- Claude probably detected: stack detection (Laravel, Vue, Tailwind), main commands (`docker compose exec app php artisan test`), project structure
- What it might have *missed* — and this is the key part:
  - It doesn't know that the Vue frontend uses Composition API exclusively (one page breaks this — but Claude doesn't know it's a convention)
  - It doesn't know that API responses must use API Resources, not raw arrays
  - It doesn't know that validation should live in Form Requests, not inline in controllers
  - It doesn't know any domain rules about auctions — bid integrity, monetary precision, status transitions

> "Claude got the easy stuff right. The stuff any developer could figure out from the file structure. But the things that make a team consistent — conventions, domain rules, gotchas — those only come from humans who know the project."

### Step 2 - Manually improve the CLAUDE.md

Exit the session (`/exit`) and edit CLAUDE.md. 

Replace the tech stack and commands section with:
```markdown 
## Stack

- **Backend:** Laravel 12, PHP 8.4, SQLite, Laravel Sanctum (API auth), Filament v3 (admin panel)
- **Frontend:** Vue 3 (Composition API), Vue Router, Axios, Tailwind CSS v4, Vite
- **Infrastructure:** Docker Compose (PHP dev server on port 80, Node dev server on port 3000)

## Commands

### Backend (run from `backend/`)

- **Setup (Install deps, copy .env, generate key, run migrations):** `docker compose exec app composer setup`
- **Start Dev (Start dev server + queue worker + logs (concurrent):** `docker compose exec app composer dev`
- **Run backend tests:** `docker compose exec app php artisan test`
- **Run single test:** `docker compose exec app php artisan test --filter=TestName`
- **Format PHP code:** `docker compose exec app ./vendor/bin/pint`
- **Static analysis:** `docker compose exec app ./vendor/bin/phpstan analyse`
- **Create storage symlink:** `docker compose exec app php artisan storage:link`
- **Run migrations:** `docker compose exec app php artisan migrate`
- **Seed database:** `docker compose exec app php artisan migrate:fresh --seed`
- **Clear caches:** `docker compose exec app php artisan optimize:clear`

### Frontend (run from `frontend/`)

- **Install dependencies:** `docker compose exec frontend npm install`
- **Vite dev server:** `docker compose exec frontend npm run dev`
- **Production build:** `docker compose exec frontend npm run build`
```

Add add following sections:

```markdown
## Conventions

- All Vue components use Composition API with `<script setup>` — never Options API
- All API responses use API Resources (`App\Http\Resources\*`) — never return raw arrays or models
- All request validation uses Form Requests (`App\Http\Requests\*`) — never validate inline in controllers
- Route names use dot notation (`category.items`) — not camelCase
- Follow PSR-12 coding style; Laravel Pint is configured for formatting

## Domain Rules

- Monetary values are stored as decimal(10,2) in the database
- Bids are never deleted — they are the audit trail for an auction
- An auction can be active, closed, or cancelled — status transitions are one-way
- Only the seller can cancel an auction; the system closes auctions when they expire
- A user cannot bid on their own item
```

### Step 3 — Verify

Start a new session:

```bash
claude
```

Ask claude a question about the conventions or domain rules:

```
Can we use camelCase in route names?
```

## Demo 2 — CLAUDE.md Gotchas: The Highest-Leverage Lines

Gotchas capture the mistakes a new developer (or Claude) would make on day one. These are the highest-leverage lines in the entire CLAUDE.md.

### Step 1 — Add gotchas to CLAUDE.md

Open CLAUDE.md and add:

```markdown
## Gotchas

- The `CategoryController@items` method returns raw arrays instead of ItemResource —
  this is a known tech debt item, do NOT copy this pattern in new code
- `CategoryPage.vue` uses Options API — this is a style inconsistency, not the standard.
  All new Vue code must use Composition API with `<script setup>`
- The `BidController@store` method is a "fat controller" with ~120 lines of inline logic —
  do not add more logic to it. Any new bid-related logic should go in a service class
- Filament admin resources live in `app/Filament/Resources/` — do not confuse with
  API Resources in `app/Http/Resources/`
- `UserController@listings` uses `DB::select()` with raw SQL for sold items — this is
  inconsistent with the Eloquent approach used for active listings. New endpoints should
  use Eloquent with API Resources
- The test suite has one intentionally broken test (`BidTest`) — don't "fix" it by
  working around the root cause
```

### Step 2 — Demonstrate a gotcha in action

```bash
claude
```

Prompt:

```
I need to add a new API endpoint that returns the items a user has won (closed auctions
where they're the winner). Look at the existing UserController for patterns to follow.
```

**What you will notice:**

- Claude will see the raw DB query in `UserController@listings` (the `$sold` query)
- **Without** the gotcha about raw arrays, Claude might copy that pattern
- **With** the gotcha, Claude should use Eloquent relationships and API Resources instead

> "The gotchas section is where project-specific knowledge lives. Every line you add prevents a class of mistakes from recurring — in Claude's output and in code reviews."

## Demo 3 — Permissions: Eliminating Prompt Fatigue

Permission rules let safe commands run without interruption while keeping guardrails on dangerous ones.

### Step 1 — Showing the problem

In a Claude session:

```
Run the test suite and tell me which tests pass and which fail.
```

Claude will show you the permission prompt. Every time Claude needs to run a command, it asks you. For a test run, that's fine — once. But in a real session, Claude might need to run tests ten times while fixing code. That's ten prompts you have to approve."

Count the prompts out loud. There will be at least one for `php artisan test`.

### Step 2 — Create permissions

Exit Claude. Create the settings file:

```bash
mkdir -p .claude
```

Create `.claude/settings.json`:

```json
{
  "permissions": {
    "allow": [
      "Bash(docker compose exec app php artisan test*)",
      "Bash(docker compose exec app php artisan *)",
      "Bash(docker compose exec app ./vendor/bin/pint*)",
      "Bash(docker compose exec app ./vendor/bin/phpstan*)",
      "Bash(docker compose exec app composer *)",
      "Bash(docker compose logs*)",
      "Bash(docker compose ps*)",
      "Bash(git status)",
      "Bash(git diff*)",
      "Bash(git log*)"
    ],
    "deny": [
      "Bash(rm -rf *)",
      "Bash(docker compose down*)",
      "Bash(git push --force*)"
    ]
  }
}
```

The `*` at the end is to make sure commands that accept parameters or piping will also be allowed

### Step 3 — Run the same prompt again

```bash
claude
```

```
Run the test suite and tell me which tests pass and which fail.
```

**What you will notice:**

- Zero permission prompts for `php artisan test`
- Claude runs the tests immediately and reports results
- The deny list means `rm -rf` and `docker compose down` still require explicit approval

> "You haven't reduced safety. You've defined what safe means for *this* project. And because `.claude/settings.json` is committed to git, the next developer who clones the repo gets the same setup on day one."

## Demo 4 — Hook: Auto-Format on Every Write

A PostToolUse hook runs Laravel Pint automatically after every file Claude writes, so code is always formatted before you see it.

### Step 1 — Show the problem (before the hook)

Open a new claude session (or type `/clear` to reset the current one.

```
Add a helper method to the Item model called `formattedPrice()` that returns
the current_price formatted as a dollar string like "$145.00".
```

After Claude writes the code, exit the session and run Pint:

```bash
docker compose exec app ./vendor/bin/pint --test
```

> "Pint should have found formatting issues. The code is correct, but there are minor inconsistencies — an extra space, a missing blank line. On a team, these show up in every PR as noise."

Undo the change before moving on. You can do this in 3 ways:

```bash
git checkout backend/app/Models/Item.php
```

Tell claude to undo the change:

```
Revert the last change
```

Or use the `/rewind` command

### Step 2 — Add the hook

Exit claude using `/exit`.

Edit `.claude/settings.json` to add hooks:

```json
{
  "permissions": {
    "allow": [
      "Bash(docker compose exec app php artisan test*)",
      "Bash(docker compose exec app php artisan *)",
      "Bash(docker compose exec app ./vendor/bin/pint*)",
      "Bash(docker compose exec app ./vendor/bin/phpstan*)",
      "Bash(docker compose exec app composer *)",
      "Bash(docker compose logs*)",
      "Bash(docker compose ps*)",
      "Bash(git status)",
      "Bash(git diff*)",
      "Bash(git log*)"
    ],
    "deny": [
      "Bash(rm -rf *)",
      "Bash(docker compose down*)",
      "Bash(git push --force*)"
    ]
  },
  "hooks": {
    "PostToolUse": [
      {
        "matcher": "Write|Edit",
        "hooks": [
          {
            "type": "command",
            "command": "docker compose exec app ./vendor/bin/pint 2>/dev/null || true"
          }
        ]
      }
    ]
  }
}
```

We run Pint on the full codebase rather than just dirty files (`--dirty`) because git may not be available inside the container.

**Note:** Claude's UI displays file edits as "Update" but the internal tool name for hook matchers is `Edit` (and `Write` for new files). Use `/hooks` → PostToolUse to verify your matchers are registered. Also note that PostToolUse hooks with exit code 0 run **silently** — output is only visible in transcript mode (ctrl+o). This is intentional: the formatter should run without interrupting the flow.

### Step 3 — Run the same prompt (with the hook)

Open a new claude session and type:

```
Add a helper method to the Item model called `formattedPrice()` that returns
the current_price formatted as a dollar string like "$145.00".
```

After Claude writes the code, exit the session and run Pint again:

```bash
docker compose exec app ./vendor/bin/pint --test
```

**What you will see:**

- Pint reports zero issues — the hook already formatted the code
- The hook runs silently (exit code 0 output is transcript-only) — it doesn't interrupt the flow
- `|| true` means the hook never blocks Claude — if Pint encounters a syntax error, Claude continues

**Same prompt. Same code. But now the file is perfectly formatted before you even look at it. Zero human effort. Zero PR comments about formatting. One hook eliminates an entire category of friction.**

## Demo 5 — Hook: Block Dangerous Commands

A PreToolUse hook intercepts destructive commands *before* they execute.

### Step 1 — Add the hook

Update `.claude/settings.json` to add a PreToolUse hook:

```json
{
  "hooks": {
    "PreToolUse": [
      {
        "matcher": "Bash",
        "hooks": [
          {
            "type": "command",
            "command": "if grep -qE 'migrate:fresh|db:wipe'; then echo 'BLOCKED: migrate:fresh and db:wipe destroy all data. Use migrate (without :fresh) for safe migrations, or ask the user explicitly if a full reset is intended.' >&2; exit 2; fi"
          }
        ]
      }
    ],
    "PostToolUse": [
      {
        "matcher": "Write|Edit",
        "hooks": [
          {
            "type": "command",
            "command": "docker compose exec app ./vendor/bin/pint 2>/dev/null || true"
          }
        ]
      }
    ]
  }
}
```

The block message goes to **stderr** (`>&2`) so Claude receives it. Exit code **2** tells Claude to block the tool call — other non-zero codes are treated as hook errors but don't block execution.

### Step 2 — Trigger the block

```bash
claude
```

```
The database seems out of date. Can you reset it and start fresh?
```

**What to narrate:**

> "Watch what Claude does. It will try to run `php artisan migrate:fresh`. The hook fires..."

**What you will notice:**

- Claude attempts the command — the hook intercepts it
- Claude receives the block message and surfaces it to you
- It doesn't silently fail. It reads the message and suggests alternatives
- The block message is crafted to be useful to Claude: it explains *why* the command was blocked and *what to do instead*. If claude asks you for permission, this is tricky... no matter how many toimes you approve, the fresh command will not be executed.

"Write hooks that explain the *why*. Claude reads the block message and can reason about it. 'BLOCKED: too dangerous' is less useful than 'BLOCKED: use migrate instead of migrate:fresh.'"

## Demo 6 — MCP: GitHub Issues (demo only)

The GitHub MCP lets Claude read issues and PRs directly — no browser switching, no copy-pasting ticket descriptions.

### Setup

```bash
# Install the GitHub MCP server
# Note: MCP servers run via npx — Node.js is required even in a PHP project
claude mcp add github -- npx -y @modelcontextprotocol/server-github

# Set your GitHub token (needs repo read access)
export GITHUB_TOKEN=your_token_here
```

### Step 1 — Read the issues

```bash
claude
```

```
Look at the open issues in the SkillTilt/dpc26-getting-started-with-claude-workshop repo.
Summarize each one briefly.
```

**What to narrate:**

> "Claude is reading GitHub directly. No browser, no copy-paste. It sees the title, labels, body, and comments for each issue."

**Expected output:** Claude summarizes the 5 planted issues (also available locally in `working/todo/bugs/`):
1. Category page slow loading — N+1 query (`working/todo/bugs/category-page-slow-loading.md`)
2. Concurrent bids race condition (`working/todo/bugs/concurrent-bids-race-condition.md`)
3. Countdown shows wrong time — timezone bug (`working/todo/bugs/countdown-shows-wrong-time.md`)
4. Bid validation off-by-one — bid comparison (`working/todo/bugs/bid-validation-off-by-one.md`)
5. Bid test fails due to missing user (`working/todo/bugs/bid-test-fails-missing-user.md`)

### Step 2 — Pick an issue and plan

```
Pick the timezone bug issue and give me a quick analysis:
what's causing it and what would the fix look like?
```

**What you notice:**

- Claude pulls context from the issue body (the reproduction steps mentioning UTC offset)
- It can cross-reference with the actual codebase (`CountdownTimer.vue`)
- The issue description flows into the analysis without any human copy-paste

> "The context goes from issue tracker to implementation plan in one step. The issue *becomes* the spec."

## Demo 7 — MCP: Playwright (Browser Verification)

A browser MCP lets Claude open the app, see what users see, and verify changes visually — not just that code compiles.

### Setup

```bash
claude mcp add playwright -- npx -y @playwright/mcp@latest
```

### Step 1 — Smoke test

```bash
claude
```

```
Open a browser and navigate to http://localhost:3000.
Take a screenshot and describe what you see.
```

**What you will see:**

- Claude opens a real browser, navigates to the BidBoard homepage
- It sees the category grid with images and descriptions
- It can describe the layout, verify data is present, and spot visual issues

### Step 2 — Check specific page

```
Navigate to the Electronics category and verify the items are displaying correctly
with prices and countdown timers.
```

Claude can now see what users see. This closes the verification loop: code works, tests pass, and the browser looks right. We'll use this heavily in Phase 5 — Verify.

### Step 3 — Check admin panel

```
Navigate to http://localhost:80/admin and log in with alice@example.com / password.
Describe what the admin dashboard looks like.
```

This is setup. We install it now in Configure so it's ready when we need it. You don't want to be installing MCPs in the middle of debugging a production issue.