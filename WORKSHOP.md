# Workshop Setup Guide

This guide walks you through setting up the BidBoard application on your machine.

## Prerequisites

- **Docker Desktop** (includes Docker Compose)
  - macOS: [Download Docker Desktop for Mac](https://docs.docker.com/desktop/install/mac-install/)
  - Linux: [Install Docker Engine](https://docs.docker.com/engine/install/) + [Docker Compose](https://docs.docker.com/compose/install/)
  - Windows: [Download Docker Desktop for Windows](https://docs.docker.com/desktop/install/windows-install/) (WSL 2 backend recommended)
- **Git**

## Quick Start

### 1. Clone the repository

```bash
git clone git@github.com:SkillTilt/dpc26-getting-started-with-claude-workshop.git
cd dpc26-getting-started-with-claude-workshop
```

### 2. Build and start containers

```bash
docker compose build
docker compose up -d
```

This starts two containers:
- **app** — Laravel backend on [http://localhost:80](http://localhost:80)
- **frontend** — Vue frontend on [http://localhost:3000](http://localhost:3000)

### 3. Set up the database

```bash
docker compose exec app php artisan storage:link
docker compose exec app php artisan migrate:fresh --seed
```

This creates the SQLite database, runs all migrations, seeds 5 users + 4 categories + 15 items with bids, and copies the product images into storage.

### 4. Verify it works

- **Frontend:** Open [http://localhost:3000](http://localhost:3000) — you should see the BidBoard homepage with 4 categories and product images.
- **Admin panel:** Open [http://localhost:80/admin](http://localhost:80/admin) — log in with `alice@example.com` / `password`.
- **API:** Run `curl http://localhost:80/api/categories` — should return JSON with 4 categories.

## Platform-Specific Notes

### macOS

No special steps. Docker Desktop for Mac handles everything.

### Linux

If using Docker Engine (not Docker Desktop), you may need to run Docker commands with `sudo` or add your user to the `docker` group:

```bash
sudo usermod -aG docker $USER
# Log out and back in for the group change to take effect
```

### Windows

- Use **WSL 2** backend for Docker Desktop (recommended for performance).
- Clone the repo inside WSL 2 for best file system performance:
  ```bash
  # Inside WSL 2 terminal
  cd ~
  git clone git@github.com:SkillTilt/dpc26-getting-started-with-claude-workshop.git
  cd dpc26-getting-started-with-claude-workshop
  docker compose build
  docker compose up -d
  ```
- If using PowerShell or CMD instead of WSL, the Docker commands are the same but file system performance may be slower.

## Resetting the Application

To start fresh at any time:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

This drops all tables, re-runs migrations, and re-seeds all data including product images.

## Stopping the Application

```bash
docker compose down
```

## Running Tests

```bash
docker compose exec app php artisan test
```

Expected result: 17 passing tests, 1 intentional failure (`BidTest`).

## Troubleshooting

**Containers won't start / port conflict:**
Check if ports 80 or 3000 are already in use:
```bash
# macOS / Linux
lsof -i :80
lsof -i :3000

# Windows (PowerShell)
netstat -ano | findstr :80
netstat -ano | findstr :3000
```
Stop the conflicting process or change the ports in `docker-compose.yml`.

**Frontend shows "No categories found":**
The database hasn't been seeded. Run:
```bash
docker compose exec app php artisan migrate:fresh --seed
```

**Images not loading on frontend:**
The storage symlink may be missing. Run:
```bash
docker compose exec app php artisan storage:link
```

**Permission errors (Linux):**
Storage directories may need write permissions:
```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
```
