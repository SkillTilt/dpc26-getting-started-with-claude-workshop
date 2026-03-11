# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

BidBoard is a real-time auction platform with a Laravel 12 API backend and Vue 3 SPA frontend, containerized with Docker.

## Architecture

- **Backend** (`/backend`): Laravel 12 (PHP 8.4), MySQL 8.0 database, Sanctum token auth, Filament v3 admin panel at `/admin`
- **Frontend** (`/frontend`): Vue 3 (Composition API), Vite 7, Tailwind CSS v4, Axios for API calls
- **API proxy**: Frontend dev server (port 3000) proxies `/api/*` requests to Laravel backend (port 80) via Vite config

### Backend Structure

- `app/Http/Controllers/Api/` — API controllers (Auth, Item, Bid, Category, User)
- `app/Http/Requests/` — Form request validation classes
- `app/Http/Resources/` — JSON API resource transformers
- `app/Models/` — Eloquent models: User, Item, Bid, Category
- `app/Filament/` — Admin panel resources and widgets
- `routes/api.php` — All API route definitions
- `database/migrations/` — Schema migrations
- `database/seeders/` — Seeds 5 users, 4 categories, 15 items with bids

### Frontend Structure

- `src/pages/` — Route-level page components
- `src/components/` — Reusable UI (NavBar, ItemCard, BidForm, CountdownTimer, etc.)
- `src/composables/` — Shared logic (useAuth, useApi, useCategories)
- `src/router/index.js` — Vue Router with auth guards on protected routes

### Key Model Relationships

- User → has many Items (as seller), has many Bids, has many won Items
- Item → belongs to User (seller), belongs to Category, has many Bids, belongs to User (winner)
- Category → has many Items; routes use slug (not id) as route key

## Commands

All backend commands run inside Docker:

```bash
# Start/stop
docker compose up -d
docker compose down

# Database
docker compose exec app php artisan migrate:fresh --seed

# Tests
docker compose exec app php artisan test
# Single test file
docker compose exec app php artisan test --filter=BidTest
# Single test method
docker compose exec app php artisan test --filter=BidTest::test_user_can_place_bid

# Lint (Laravel Pint, PSR-12)
docker compose exec app ./vendor/bin/pint
# Lint check only
docker compose exec app ./vendor/bin/pint --test

# Frontend
cd frontend && npm run dev      # Dev server
cd frontend && npm run build    # Production build
```

## Test Configuration

- PHPUnit uses in-memory SQLite (`phpunit.xml`)
- Test suites: `tests/Feature/` and `tests/Unit/`
- Tests should use `RefreshDatabase` trait and factories — not assume seeder data exists

## Test Accounts

All accounts use password: `password`
- alice@example.com, bob@example.com, charlie@example.com, diana@example.com, eve@example.com
