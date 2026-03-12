# Workshop: Getting Started with Claude Code

Welcome to the **Getting Started with Claude Code** workshop. You'll inherit BidBoard — a Laravel auction platform with intentional bugs and code quality issues — and learn to work with Claude Code through 7 phases.

---

## Prerequisites Setup

Complete these steps **before** the workshop. The entire setup takes about 10–15 minutes.

### 1. Install Claude Code

You need a **Claude subscription** (Pro, Max, Teams, or Enterprise) — [claude.com/pricing](https://claude.com/pricing)

Full docs: [code.claude.com/docs/en/quickstart](https://code.claude.com/docs/en/quickstart)

**macOS / Linux / WSL (recommended):**
```bash
curl -fsSL https://claude.ai/install.sh | bash
```

**macOS with Homebrew:**
```bash
brew install --cask claude-code
```

**Windows PowerShell:**
```powershell
irm https://claude.ai/install.ps1 | iex
```

**Windows CMD:**
```batch
curl -fsSL https://claude.ai/install.cmd -o install.cmd && install.cmd && del install.cmd
```

**Windows WinGet:**
```powershell
winget install Anthropic.ClaudeCode
```

> **Windows requires [Git for Windows](https://git-scm.com/downloads/win).** Install it first if you don't have it.

After installing, log in:

```bash
claude
# Follow the browser prompt to authenticate
```

Your credentials are stored locally — you won't need to log in again.

### 2. Install Docker

The demo app runs in Docker Compose with three containers: PHP backend, Vue frontend, and MySQL 8.0.

- **macOS / Windows:** Install [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- **Linux:** Install [Docker Engine](https://docs.docker.com/engine/install/) + [Docker Compose plugin](https://docs.docker.com/compose/install/linux/)

Verify:
```bash
docker compose version
# Should show v2.x+
```

**Pre-pull images** (optional, saves time during the workshop):
```bash
docker pull php:8.4-cli
docker pull mysql:8.0
docker pull node:20-alpine
```

### 3. Install Chromium (for Playwright MCP)

During the workshop, Claude uses a browser to verify changes visually. This requires a standalone Chromium:

```bash
npx playwright install chromium
```

This downloads ~150 MB. It does **not** affect your system browsers.

> On Windows, run this in PowerShell or WSL. If behind a proxy, set `HTTPS_PROXY` first.

### 4. Create a GitHub Personal Access Token

The workshop uses the GitHub MCP server so Claude can read issues directly from the repository. This requires a token.

1. Go to [github.com/settings/tokens](https://github.com/settings/tokens?type=beta) (Fine-grained tokens)
2. Click **Generate new token**
3. Give it a name (e.g., `claude-workshop`)
4. Under **Repository access**, select **Only select repositories** and pick this workshop repo
5. Under **Permissions → Repository permissions**, grant:
   - **Issues**: Read-only
   - **Pull requests**: Read-only
   - **Contents**: Read-only
6. Click **Generate token** and copy the value

Set it in your terminal before starting Claude:

```bash
export GITHUB_TOKEN=github_pat_your_token_here
```

> **Tip:** Add this to your shell profile (`~/.zshrc`, `~/.bashrc`) so it persists across terminal sessions.

### 5. Verify everything

```bash
# Claude Code installed?
claude --version

# Docker running?
docker compose version

# Chromium installed?
npx playwright install --dry-run chromium

# GitHub token set?
echo $GITHUB_TOKEN
```

---

## Starting the Application

### Clone and start

```bash
git clone git@github.com:SkillTilt/dpc26-getting-started-with-claude-workshop.git
cd dpc26-getting-started-with-claude-workshop
docker compose build
docker compose up -d
```

This starts three containers:
- **app** — Laravel backend on [http://localhost:80](http://localhost:80)
- **mysql** — MySQL 8.0 database (persistent volume)
- **frontend** — Vue frontend on [http://localhost:3000](http://localhost:3000)

### Set up the database

```bash
docker compose exec app php artisan migrate --seed
```

This runs all migrations and seeds: 5 users, 4 categories, 15 items with bids.

### Verify it works

- **Frontend:** [http://localhost:3000](http://localhost:3000) — BidBoard homepage with 4 categories
- **Admin panel:** [http://localhost:80/admin](http://localhost:80/admin) — log in with `alice@example.com` / `password`
- **API:** `curl http://localhost:80/api/categories` — returns JSON with 4 categories

### Test accounts

All passwords are `password`.

| Name | Email | Role |
|------|-------|------|
| Alice Mercer | alice@example.com | Seller |
| Bob Tanaka | bob@example.com | Seller |
| Clara Voss | clara@example.com | Seller |
| Dave Park | dave@example.com | Seller |
| Eve Santos | eve@example.com | Buyer |

---

## Workshop Flow

The workshop follows the **7-phase framework**:

```
Configure → Classify → Plan → Execute → Verify → Commit → Compound
```

Each phase has its own **git branch**. The branch contains:
- All changes from previous phases (cumulative)
- The starting state for that phase's exercises

### Branches

| Branch | Phase | Starting state |
|--------|-------|----------------|
| `main` | — | Raw BidBoard app, no Claude configuration |
| `phase-1-configure` | 1. Configure | Same as main — you build the configuration |
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

### Backend commands (run from project root)

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

### Running tests

```bash
docker compose exec app php artisan test
```

Expected: 17 passing, 1 intentional failure (`BidTest` — references `User::find(1)` without seeding).

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| `claude: command not found` | Close and reopen your terminal, or check `~/.claude/bin` is in your PATH |
| Docker permission denied (Linux) | `sudo usermod -aG docker $USER`, then log out and back in |
| Port 80 or 3000 in use | Stop the conflicting process or change ports in `docker-compose.yml` |
| Frontend shows "No categories found" | Run `docker compose exec app php artisan migrate --seed` |
| Chromium download fails | Set `PLAYWRIGHT_BROWSERS_PATH=0` and retry `npx playwright install chromium` |
| `GITHUB_TOKEN` not working | Verify the token has Issues + Contents read access on this repo |
| Windows: slow file system | Clone the repo inside WSL 2 (`cd ~` first), not on the Windows filesystem |
| Corporate proxy blocks downloads | Set `HTTP_PROXY` and `HTTPS_PROXY` environment variables |
