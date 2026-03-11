# Workshop Setup Guide

Follow these steps **before** the workshop starts. The entire setup takes about 10–15 minutes on a good connection.

## Prerequisites

You need:

- A **Claude subscription** — Pro, Max, Teams, or Enterprise ([claude.com/pricing](https://claude.com/pricing))
- **Docker Desktop** — the demo app runs in Docker Compose (PHP backend + Vue frontend + MySQL)
  - macOS / Windows: [docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop/)
  - Linux: [docs.docker.com/engine/install](https://docs.docker.com/engine/install/) + [Docker Compose plugin](https://docs.docker.com/compose/install/linux/)
- **Git** (any recent version)
- A **terminal** you're comfortable with (Terminal.app, iTerm2, Windows Terminal, etc.)

---

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

---

## 2. Log in

Start Claude Code and follow the login prompt:

```bash
claude
```

You'll be asked to authenticate in your browser. Once done, your credentials are stored locally — you won't need to log in again.

If you need to switch accounts later, use `/login` inside a session.

---

## 3. Install Chromium (for Playwright MCP)

During the workshop we use the Playwright MCP server to let Claude browse the running app and take screenshots. This requires a Chromium browser:

```bash
npx playwright install chromium
```

This downloads a standalone Chromium binary (~150 MB). It does **not** affect your system browsers.

> If you're behind a corporate proxy or firewall, you may need to set `HTTPS_PROXY` before running this command.

---

## 4. Verify Docker is running

The demo app (BidBoard) runs in Docker Compose with three containers: a PHP backend, a Vue frontend, and MySQL 8.0.

```bash
docker compose version
```

You should see a version number (v2.x+). If not, install or start Docker Desktop.

During the workshop, we'll clone the repo and run:

```bash
docker compose up -d
```

This pulls images for PHP 8.4, MySQL 8.0, and Node.js. **If your internet is slow, you can pre-pull them:**

```bash
docker pull php:8.4-cli
docker pull mysql:8.0
docker pull node:20-alpine
```

---

## 5. Verify everything works

Run these checks:

```bash
# Claude Code installed?
claude --version

# Docker running?
docker compose version

# Chromium installed?
npx playwright install --dry-run chromium
```

If all three succeed, you're ready for the workshop.

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| `claude: command not found` | Close and reopen your terminal, or check that `~/.claude/bin` is in your PATH |
| Docker permission denied (Linux) | Add your user to the docker group: `sudo usermod -aG docker $USER`, then log out and back in |
| Chromium download fails | Try setting `PLAYWRIGHT_BROWSERS_PATH=0` and running `npx playwright install chromium` again |
| Windows: `curl` not found | Use PowerShell (`irm`) or install Git for Windows which includes curl |
| Corporate proxy blocking downloads | Set `HTTP_PROXY` and `HTTPS_PROXY` environment variables before running install commands |
