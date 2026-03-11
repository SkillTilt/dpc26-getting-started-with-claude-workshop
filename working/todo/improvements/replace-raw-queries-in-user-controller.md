# Proposal: Replace raw DB queries in UserController with Eloquent

## Current State

`UserController@listings` uses `DB::select()` with raw SQL to fetch the authenticated user's sold items. This bypasses Eloquent entirely -- no model casting, no relationship loading, no scope reuse, and no protection from SQL dialect differences.

The rest of the codebase consistently uses Eloquent for all database access, making this an outlier that's harder to maintain and reason about.

## Proposal

Replace the raw query with an Eloquent equivalent using the existing `Item` model and its relationships:

```php
// Before (raw)
$sold = DB::select('SELECT * FROM items WHERE seller_id = ? AND status = ?', [$user->id, 'sold']);

// After (Eloquent)
$sold = $user->items()->where('status', 'sold')->with('bids', 'winner')->get();
```

## Benefits

- Consistent with the rest of the codebase
- Gets automatic model casting (dates, enums, money fields)
- Can eager load relationships to avoid N+1
- Reuses any global scopes or soft delete behavior on the Item model
- Safer against SQL injection edge cases (though parameterized queries are already safe, Eloquent adds the query builder's type safety layer)

## Suggested Approach

1. Identify the exact raw query in `UserController`
2. Write the Eloquent equivalent using existing model relationships
3. Verify the response shape matches (raw queries return stdClass, Eloquent returns model instances -- may need to update the resource/response)
4. Add a test that covers the listings endpoint
