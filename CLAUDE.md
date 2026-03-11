# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

BidBoard is a real-time auction platform with a decoupled architecture:
- **Backend:** Laravel 12 API + Filament v3 admin panel (`backend/`)
- **Frontend:** Vue 3 SPA with Composition API (`frontend/`)
- **Database:** MySQL 8.0 (Docker container, persistent volume)
- **Infrastructure:** Docker Compose (two containers: `app` on port 80, `frontend` on port 3000)

This is a workshop demo app with **intentional bugs and improvements** tracked as GitHub issues.

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

## Architecture

### Conventions

- All Vue components use Composition API with `<script setup>` — never Options API
- All API responses use API Resources (`App\Http\Resources\*`) — never return raw arrays or models
- All request validation uses Form Requests (`App\Http\Requests\*`) — never validate inline in controllers
- Route names use dot notation (`category.items`) — not camelCase
- Follow PSR-12 coding style; Laravel Pint is configured for formatting

### Backend API

- **Routes:** `routes/api.php` — REST API with Sanctum auth middleware
- **Controllers:** `app/Http/Controllers/Api/` — AuthController, ItemController, BidController, CategoryController, UserController
- **Models:** `app/Models/` — User, Item, Bid, Category (User implements FilamentUser + HasApiTokens)
- **Resources:** `app/Http/Resources/` — API response transformers (ItemResource, BidResource, CategoryResource)
- **Requests:** `app/Http/Requests/` — Form request validation classes
- **Filament:** `app/Filament/` — Admin panel resources and dashboard widget

### Frontend SPA

- **Pages:** `src/pages/` — Route-level components (HomePage, ItemDetailPage, SellPage, etc.)
- **Composables:** `src/composables/` — useAuth (token management), useApi (Axios with Bearer interceptor), useCategories
- **Router:** `src/router/index.js` — Route guards check `requiresAuth` meta, token in localStorage

### Database

Five tables: users, categories, items (with seller_id, winner_id, status enum), bids, personal_access_tokens (Sanctum). Seeder creates 5 test users (alice/bob/clara/dave/eve @example.com, password: `password`), 4 categories, 15 items with bids.

### Domain Rules

- Monetary values are stored as decimal(10,2) in the database
- Bids are never deleted — they are the audit trail for an auction
- An auction can be active, closed, or cancelled — status transitions are one-way
- Only the seller can cancel an auction; the system closes auctions when they expire
- A user cannot bid on their own item

## Testing

- PHPUnit with in-memory SQLite (`phpunit.xml`)
- Tests use `RefreshDatabase` trait
- **BidTest intentionally fails** — references `User::find(1)` without seeding (this is a workshop exercise)
- Expected: 17 pass, 1 fail

## Test Accounts

All accounts use password: `password`
- alice@example.com, bob@example.com, charlie@example.com, diana@example.com, eve@example.com


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
