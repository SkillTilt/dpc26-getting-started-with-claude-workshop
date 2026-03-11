# Bug #3: Category page takes forever to load — N+1 query

**Issue:** [#3](https://github.com/SkillTilt/dpc26-getting-started-with-claude-workshop/issues/3)
**Severity:** Performance
**Status:** Open

## Description

When clicking on a category with many items (like "Electronics" with 50+ listings), the page takes noticeably long to load. The API response for `/api/categories/{id}/items` is very slow compared to other item listing endpoints.

## Steps to Reproduce

1. Seed the database with at least 30-40 items in a single category
2. Navigate to that category page
3. Watch the network tab — the API response for `/api/categories/{id}/items` is very slow

## Expected Behavior

The category items endpoint should respond in under 500ms, similar to other item listing endpoints.

## Actual Behavior

Response time grows linearly with the number of items. With 50 items it takes 3-4 seconds. Laravel Debugbar shows hundreds of individual queries being executed.

## Where to Look

- `backend/app/Http/Controllers/Api/CategoryController.php` lines 33-46
- The `items()` method loads items but never eager loads the `bids` and `seller` relationships, even though both are accessed in the `map()` closure that builds the response
- This is a classic N+1 query problem: 1 query for items + N queries for each item's seller + N queries for each item's bids
