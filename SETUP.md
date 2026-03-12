# Workshop Setup Guide

Follow these steps **before** the workshop starts. The entire setup takes about 10–15 minutes on a good connection.

## Prerequisites

You need:

- A **Claude subscription** — Pro, Max, Teams, or Enterprise ([claude.com/pricing](https://claude.com/pricing))
- **Docker** (installed in step 3)
- **Node.js** v18+ — required for `npx playwright install` in step 4 ([nodejs.org](https://nodejs.org/))
- **Git** (any recent version)
- A **terminal** you're comfortable with (Terminal.app, iTerm2, Windows Terminal, etc.)

## 1. Install Claude Code

Full docs: [code.claude.com/docs/en/quickstart](https://code.claude.com/docs/en/quickstart)

### macOS / Linux / WSL (recommended)

```bash
curl -fsSL https://claude.ai/install.sh | bash
```

### macOS with Homebrew

```bash
brew install --cask claude-code
```

> Homebrew installations don't auto-update. Run `brew upgrade claude-code` periodically.

### Windows PowerShell

```powershell
irm https://claude.ai/install.ps1 | iex
```

### Windows CMD

```batch
curl -fsSL https://claude.ai/install.cmd -o install.cmd && install.cmd && del install.cmd
```

> **Windows requires [Git for Windows](https://git-scm.com/downloads/win).** Install it first if you don't have it.

### Windows WinGet

```powershell
winget install Anthropic.ClaudeCode
```

## 2. Log in

Start Claude Code and follow the login prompt:

```bash
claude
```

You'll be asked to authenticate in your browser. Once done, your credentials are stored locally — you won't need to log in again.

If you need to switch accounts later, use `/login` inside a session.

## 3. Install Docker

The demo app runs in Docker Compose with three containers: PHP backend, Vue frontend, and MySQL 8.0.

- **macOS / Windows:** Install [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- **Linux:** Install [Docker Engine](https://docs.docker.com/engine/install/) + [Docker Compose plugin](https://docs.docker.com/compose/install/linux/)

Verify:

```bash
docker compose version
# Should show v2.x+
```

## 4. Install Chromium (for Playwright MCP)

During the workshop we use the Playwright MCP server to let Claude browse the running app and take screenshots. This requires a Chromium browser:

```bash
npx playwright install chromium
```

This downloads a standalone Chromium binary (~150 MB). It does **not** affect your system browsers.

> On Windows, run this in PowerShell or WSL. If behind a corporate proxy or firewall, set `HTTPS_PROXY` before running this command.

## 5. Create a GitHub Personal Access Token

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

**macOS / Linux / WSL:**
```bash
export GITHUB_TOKEN=github_pat_your_token_here
```

**Windows PowerShell:**
```powershell
$env:GITHUB_TOKEN = "github_pat_your_token_here"
```

**Windows CMD:**
```batch
set GITHUB_TOKEN=github_pat_your_token_here
```

> **Tip:** Add this to your shell profile (`~/.zshrc`, `~/.bashrc`, or PowerShell `$PROFILE`) so it persists across terminal sessions.

## 6. Verify everything works

Run these checks:

```bash
# Claude Code installed?
claude --version

# Node.js installed?
node --version

# Docker running?
docker compose version

# Chromium installed?
npx playwright install --dry-run chromium

# GitHub token set? (Windows CMD: echo %GITHUB_TOKEN%, PowerShell: echo $env:GITHUB_TOKEN)
echo $GITHUB_TOKEN
```

If all five succeed, you're ready to start the application.

## 7. Start the Application

### Clone and start

**SSH:**
```bash
git clone git@github.com:SkillTilt/dpc26-getting-started-with-claude-workshop.git
```

**HTTPS:**
```bash
git clone https://github.com/SkillTilt/dpc26-getting-started-with-claude-workshop.git
```

Then build and start:

```bash
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
cp backend/.env.example backend/.env
docker compose exec app php artisan key:generate
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

## Troubleshooting

| Problem | Fix |
|---------|-----|
| `claude: command not found` | Close and reopen your terminal, or check that `~/.claude/bin` is in your PATH |
| Docker permission denied (Linux) | Add your user to the docker group: `sudo usermod -aG docker $USER`, then log out and back in |
| Port 80, 3000, or 3306 in use | Stop the conflicting process (e.g. a local MySQL on 3306) or change ports in `docker-compose.yml` |
| Frontend shows "No categories found" | Run `docker compose exec app php artisan migrate --seed` |
| Chromium download fails | Set `PLAYWRIGHT_BROWSERS_PATH=0` and retry `npx playwright install chromium` |
| `GITHUB_TOKEN` not working | Verify the token has Issues + Contents read access on this repo |
| Windows: `curl` not found | Use PowerShell (`irm`) or install Git for Windows which includes curl |
| Windows: slow file system | Clone the repo inside WSL 2 (`cd ~` first), not on the Windows filesystem |
| Corporate proxy blocks downloads | Set `HTTP_PROXY` and `HTTPS_PROXY` environment variables before running install commands |

