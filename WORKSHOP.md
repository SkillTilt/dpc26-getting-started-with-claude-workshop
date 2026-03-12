# Workshop: Getting Started with Claude Code

Welcome to the **Getting Started with Claude Code** workshop. You'll inherit BidBoard — a Laravel auction platform with intentional bugs and code quality issues — and learn to work with Claude Code through 7 phases.

---

## Prerequisites

Before the workshop starts, make sure you have:

- [ ] **Claude Code** installed and logged in
- [ ] **Docker** installed and running
- [ ] **Chromium** installed for Playwright MCP
- [ ] **GitHub Personal Access Token** configured
- [ ] **BidBoard app** running (`docker compose up -d` + migrations seeded)

> **See [SETUP.md](SETUP.md) for full installation instructions, step-by-step setup, and troubleshooting.**

---

## Workshop Flow

The workshop follows the **7-phase framework**:

```
Configure → Classify → Plan → Execute → Verify → Commit → Compound
```

The workshop starts on the `phase-1-configure` branch. Each phase has its own **git branch** containing:
- All changes from previous phases (cumulative)
- The starting state for that phase's exercises

### Branches

| Branch | Phase | Starting state |
|--------|-------|----------------|
| `main` | — | Raw BidBoard app, no Claude configuration |
| `phase-1-configure` | 1. Configure | Same as main — this is where you start |
| `phase-2-classify` | 2. Classify | CLAUDE.md, permissions, hooks from Phase 1 |
| `phase-3-plan` | 3. Plan | Phase 2 complete + `/design` slash command setup |
| `phase-4-execute` | 4. Execute | Phase 3 complete + plans ready for execution |
| `phase-5-verify` | 5. Verify | Phase 4 complete + features built, ready for verification |
| `phase-6-commit` | 6. Commit | Phase 5 complete + verified changes |
| `phase-7-compound` | 7. Compound | Phase 6 complete + clean commit history |
| `phase-7-compound-complete` | Done | All phases complete — reference implementation |

### Switching phases

At the start of each phase, check out the corresponding branch:

```bash
git checkout phase-1-configure
```

If you have uncommitted changes from the previous phase, either commit or stash them first:

```bash
# Option A: commit your work
git add -A && git commit -m "My phase 1 work"
git checkout phase-2-classify

# Option B: stash and switch
git stash
git checkout phase-2-classify
git stash pop   # optional: apply your changes on top
```

> **Important:** Each phase branch already includes the correct starting state. You don't need to carry your changes forward — the branch has the "ideal" result of previous phases built in.

### If you fall behind

No problem. Check out the next phase branch and you'll have a clean starting point with all previous phases complete. The branches are designed so you can jump in at any phase.

---

## Quick Reference

### Backend commands (run from the repo root, where `docker-compose.yml` lives)

```bash
docker compose exec app php artisan test                    # Run tests
docker compose exec app php artisan test --filter=TestName  # Single test
docker compose exec app ./vendor/bin/pint                   # Format code
docker compose exec app ./vendor/bin/phpstan analyse        # Static analysis
docker compose exec app php artisan migrate                 # Run migrations
docker compose exec app php artisan migrate --seed          # Migrate + seed
docker compose exec app php artisan optimize:clear          # Clear caches
```

### Frontend commands

```bash
docker compose exec frontend npm install    # Install dependencies
docker compose exec frontend npm run dev    # Dev server (auto-started)
docker compose exec frontend npm run build  # Production build
```

### Docker lifecycle

```bash
docker compose up -d       # Start all containers
docker compose down        # Stop and remove containers
docker compose restart     # Restart all containers
docker compose logs -f     # Follow container logs
```

> **Note:** Most tests pass out of the box. `BidTest` has one intentional failure (references `User::find(1)` without seeding). See [SETUP.md](SETUP.md) for test account credentials and troubleshooting.
