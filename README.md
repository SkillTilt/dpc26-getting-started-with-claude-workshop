# BidBoard

A real-time auction platform where users can list items for sale, browse auctions by category, and place bids.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.4, SQLite, Laravel Sanctum (API auth), Filament v3 (admin panel)
- **Frontend:** Vue 3 (Composition API), Vue Router, Axios, Tailwind CSS v4, Vite
- **Infrastructure:** Docker Compose (PHP dev server + Node dev server)

## Features

- Browse auction items by category with live countdown timers
- User registration and authentication (token-based)
- Place bids on active auctions with real-time price updates
- List items for sale with image upload
- Personal dashboards: My Listings, My Bids (winning/won/lost)
- Admin panel at `/admin` for managing users, categories, and items

## Project Structure

```
├── backend/          # Laravel API + Filament admin
├── frontend/         # Vue 3 SPA
├── seed-images/      # Product images used by the database seeder
└── docker-compose.yml
```

## API Endpoints

| Method | Endpoint                         | Auth | Description              |
|--------|----------------------------------|------|--------------------------|
| POST   | `/api/register`                  | No   | Register a new user      |
| POST   | `/api/login`                     | No   | Login and receive token  |
| POST   | `/api/logout`                    | Yes  | Revoke current token     |
| GET    | `/api/user`                      | Yes  | Current user profile     |
| GET    | `/api/user/listings`             | Yes  | User's listed items      |
| GET    | `/api/user/bids`                 | Yes  | User's bid history       |
| GET    | `/api/categories`                | No   | List all categories      |
| GET    | `/api/categories/{slug}/items`   | No   | Items in a category      |
| GET    | `/api/items/{id}`                | No   | Single item with bids    |
| POST   | `/api/items`                     | Yes  | Create a new listing     |
| POST   | `/api/items/{id}/bids`           | Yes  | Place a bid              |

## Test Users

| Name          | Email               | Password   |
|---------------|---------------------|------------|
| Alice Mercer  | alice@example.com   | password   |
| Bob Tanaka    | bob@example.com     | password   |
| Clara Voss    | clara@example.com   | password   |
| Dave Park     | dave@example.com    | password   |
| Eve Santos    | eve@example.com     | password   |
