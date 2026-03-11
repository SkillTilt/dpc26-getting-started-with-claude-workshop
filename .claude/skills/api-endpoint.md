# Skill: Scaffold API Endpoint

When asked to create a new API endpoint, always create ALL of these:

1. **Route** — Add to `routes/api.php` inside the appropriate group
   (public or auth:sanctum)
2. **Controller method** — In the relevant controller, or create a new
   single-action controller if it doesn't fit an existing one
3. **Form Request** — Even for GET endpoints (for query parameter validation)
4. **API Resource** — For consistent response formatting (reuse existing
   Resources when the data shape matches)
5. **PHPUnit Feature Test** — Covering the happy path and at least one
   error case (404, 422, or 403)

Follow these conventions:
- Use route model binding where appropriate
- Use `slug` instead of `id` for public-facing category endpoints
- Return 200 for success, 201 for creation, 422 for validation, 404 for not found
- Wrap all responses in API Resources — never return raw arrays
- Use dot notation for route names (e.g., `category.items`)
- Run `docker compose exec app php artisan test` after scaffolding to verify
