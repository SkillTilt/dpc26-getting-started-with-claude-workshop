# Phase 6: Commit — Demo & Code-Along Script

The code works. The tests pass. The browser looks right. Now make your future self — and your team — happy with clean, well-scoped commits and clear PR descriptions. This is where session work becomes permanent history.

You have been working through Phases 1–5. The BidBoard codebase has uncommitted changes from those phases: the outbid notification feature files, a timezone fix in `CountdownTimer.vue`, and possibly other modifications. Docker Compose is running (`docker compose up -d`), tests pass, and the working tree is dirty — exactly the state you'd be in after a real development session.

## Demo 1 — Smart Commit Messages

Claude generates precise, descriptive commit messages by reading the staged diff. The result tells a future reader exactly what happened and why — without opening the diff.

### Setup

Open a terminal in the BidBoard project root.

> **Note — Clean Git State:** Remember Boris's rule: before any new session, `git status` shows nothing to commit. When you start from clean state, the diff contains exactly this session's work. The commit is clean. The PR is coherent. Start from dirty state and you spend ten minutes untangling what belongs where. This is the ideal — in practice, after Phases 4-5, if you did not commit, our working tree is dirty. That's fine for the demo, but not something for in real workflows.

Make sure there are unstaged changes to work with. If the working tree is clean from a previous reset, create a small realistic change first. 

Start a new claude session.

### Step 1 — Stage specific files and ask for a commit message

Assume the outbid notification feature was built in Phase 4 (Execute). Stage those files:

```
I've staged some files for the outbid notification feature. Look at the current
git diff --staged and write me a commit message for these changes.
```

Watch what Claude does. It reads the actual diff — not just the file names. It sees that there's an event, a listener wired to it, a mailable, and a Blade template. The commit message reflects the architecture, not just 'added some files.'

### Step 2 — Compare good vs. lazy

**Claude's message:** `feat: add outbid email notification with BidPlaced event and listener`

**The alternative:** `added notification stuff`

Which one do you want to find in `git log` six months from now when something breaks in the notification system and you're tracing the history at 11 PM?

### What to point out

- Claude wrote the message from the diff, not from guessing. It knows the event name, that a listener is wired up, and that email is the delivery channel.
- You still review the message before confirming. Claude drafts. You approve.
- This takes seconds. There is no excuse for lazy commit messages when the cost of a good one is literally zero.

## Demo 2 — Commit Scope Check: One Logical Change Per Commit

Atomic commits. One logical change per commit. Claude sometimes bundles unrelated changes — you need to catch that and split them apart.

### Setup

Make sure the working tree has at least two unrelated changes. The BidBoard repo has a timezone bug in `frontend/src/components/CountdownTimer.vue` (the `new Date(props.endsAt)` call that doesn't handle UTC) plus the outbid notification feature from previous phases.

### Step 1 — Ask Claude to commit everything

```
Commit all my current changes with an appropriate message.
```

Watch what happens. Claude will stage everything — the notification feature AND the timezone fix — and propose one commit message that tries to cover both.

### Step 2 — What's wrong here?

What's wrong here? Two unrelated changes in one commit. If you need to revert the timezone fix next week because it broke something, you also lose the notification feature. If a reviewer is looking at the notification PR, the timezone fix is noise they have to mentally filter out.

### Step 3 — Split into atomic commits

```
Actually, let's split this. First, commit only the timezone fix in
CountdownTimer.vue — that's a separate bug fix. Then commit the notification
feature files separately.
```

**What to note:**

- Claude creates two clean, self-contained commits
- The first: `fix: handle UTC timezone in auction countdown timer` (see `working/todo/bugs/bug-005-countdown-timezone.md`)
- The second: `feat: add outbid email notification with BidPlaced event and listener`
- Show the result with `git log --oneline` — two entries, each telling a complete story

Claude optimizes for getting things done. You optimize for maintainability. Atomic commits are a human.

## Demo 3 — Pre-Commit Verification

Never commit code that breaks tests or fails static analysis. The Claude Code hooks we set up in Phase 1 (Configure) pay off at commit time — and we discuss how git pre-commit hooks add a second safety net.

### Step 1 — Run verification manually before committing

```
Before we commit the next change, run the test suite and Pint to make sure
everything is clean.
```

In Phase 1, we set up PostToolUse hooks that run Pint after every file write. Those hooks have been working silently throughout Phases 4 and 5. Now let's run the full verification suite manually to see what those hooks are checking.

Claude runs:

```bash
docker compose exec app php artisan test
docker compose exec app ./vendor/bin/pint --test
```

Both should pass (aside from the intentionally broken `BidTest`).

### Step 2 — The two layers of hooks

> **Note:** Phase 1 configured **Claude Code hooks** (PostToolUse) — these run inside Claude's tool loop and catch issues as Claude writes code. **Git pre-commit hooks** are a separate mechanism — they run when `git commit` is executed, catching issues from any source (Claude, manual edits, other tools). BidBoard doesn't have git pre-commit hooks configured yet. Tools like CaptainHook or Husky can add them, and the PostToolUse hooks are the Claude-specific equivalent. Both serve the same purpose: making discipline structural, not personal.

### Step 3 — The safety net in action

Make a quick edit that would normally fail Pint:

```
Add a public method called `recentBids()` to the Item model that returns the
5 most recent bids.
```

Watch the PostToolUse hook. After Claude writes the file, Pint runs automatically and fixes any formatting issues. The commit you're about to make is already clean — the hook handled it.

**What to note:**

- The PostToolUse hook from Phase 1 already caught and fixed formatting
- You feel the temptation: `git commit --no-verify`. But skipping hooks is borrowing against your future self
- If the hook is wrong, fix the hook — don't bypass it

Claude will never suggest `--no-verify` — it's trained not to. But you might be tempted when a hook fails at 5 PM on a Friday. The hook is doing its job.

## Demo 4 — PR Description and Creation with `gh`

A pull request description is the first thing a reviewer reads. Claude drafts a structured, informative PR body that saves review time and documents the "why" behind the change.

### Step 1 — Create a feature branch and generate a PR

First, make sure you're on a feature branch (not main):

```
Create a new branch called feature/outbid-notifications, commit the notification
feature files there, push to origin, and open a pull request against main.
Use gh to create the PR. Write a thorough PR description that includes:
- A summary of what and why
- What changed (component by component)
- How to test manually
- Any notes for reviewers
```

**Note:** You can simulate this: have Claude generate the PR description without pushing, and show it on screen. The teaching point is the description quality, not the `gh` mechanics. 

Watch the PR description Claude generates. It's not a formality — it's the reviewer's map."

### Step 2 — Review the generated PR

**Example structure:**

```markdown
## Summary
Add email notifications when a user is outbid on an auction item...

## What changed
- **BidPlaced event** — fired after a successful bid...
- **SendOutbidNotification listener** — listens for BidPlaced...
- **OutbidNotification mailable** — email template...
- **Event registration** — wires event to listener via auto-discovery or AppServiceProvider...

## How to test
1. Log in as alice@example.com
2. Place a bid on an item
3. Log in as another user, place a higher bid
4. Check the log (MAIL_MAILER=log) for the outbid email

## Notes
- Requires MAIL_MAILER=log in .env for local testing
- Queue configuration: uses sync driver by default
```

### Step 3 — Refine the description

```
The summary is too technical. Rewrite it so a product manager could understand
the first two sentences. Keep the technical details in the "What changed" section.
```

**What to note:**

- Claude wrote the first draft in seconds. You spent one minute refining.
- A reviewer now has everything they need before reading a single line of code.
- The PR description is not overhead — it's documentation that lives forever in the repo history.

The PR is where session work becomes visible to the team. Done well, it explains what changed, why it changed, and what a future developer needs to know if they ever touch this code again.

> **Note — PR as Knowledge Artifact and `@.claude`:** Boris uses the `@.claude` tag in PR reviews to create a feedback loop. When a reviewer notices something — a pattern that should be in CLAUDE.md, a mistake Claude made that should become a rule — tagging `@.claude` captures the learning. It flows back into CLAUDE.md and the next session starts with that knowledge. This is the bridge between Commit and Compound. It's one of the highest-leverage habits in Boris's workflow.

## Demo 5 — Conventional Commits in CLAUDE.md

Establish a commit message convention for the project and encode it in CLAUDE.md so Claude follows it automatically in every future session.

### Step 1 — Add the convention to CLAUDE.md

Open `CLAUDE.md` in the demo-repo root and add:

```markdown
## Commit Convention

- Use conventional commits: `type: description`
- Types: feat, fix, refactor, test, docs, chore
- Keep the subject line under 72 characters
- Use imperative mood: "add" not "added", "fix" not "fixes"
- Reference GitHub issue numbers when applicable: `fix: correct timezone in countdown (#5)`
- Examples:
  - feat: add outbid email notification
  - fix: correct timezone display in auction countdown
  - refactor: extract bidding logic to BiddingService
  - test: add coverage for snipe protection edge cases
```

### Step 2 — Commit the convention itself


```
Commit CLAUDE.md changes
```

Watch the commit message Claude generates for this change. It should follow the convention we just defined — something like `docs: add conventional commit convention to CLAUDE.md`. Claude read the file, saw the convention, and applied it to its own commit. That's the loop.

### Step 3 — Verify it sticks across commits

Make another small change:

```
Add a `highestBid()` method to the Item model that returns the highest bid
relationship using a subquery.
```

Then:

```
Commit
```

**What to note:**

- The commit message follows the convention: `feat: add highestBid accessor to Item model`
- You configured this once. Every developer who clones the repo inherits the convention through CLAUDE.md.
- No linter plugin required. No commit-msg hook to install. Claude reads the file and complies.

This is Phase 6 feeding back into Phase 1. The commit convention is a CLAUDE.md rule. The 7-phase framework is not a straight line — it's a loop. Every phase strengthens the others.

> **Note — Custom Slash Commands:** Teams create custom slash commands in `.claude/commands/` for repeatable workflows: `/commit-push-pr` (commit, push, open PR in one keystroke), `/review-pr` (Claude reviews its own work before submitting), `/changelog-entry` (generate a user-facing changelog entry). These live in git — every developer who clones the repo gets them. This is the team's commit infrastructure, built once and shared automatically. We'll see more of this in Phase 7.
