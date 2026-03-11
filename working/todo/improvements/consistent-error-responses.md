# Proposal: Standardize API error response format in BidController

## Current State

`BidController` returns at least three different response shapes depending on the outcome:

- **Success:** Returns a `BidResource` (JSON:API-style with `data` wrapper)
- **Validation errors:** Returns a plain array like `{'error': 'message', 'min_amount': 105.00}` with a 422 status
- **Auto-close response:** Returns a different structure with bid data plus an `item_closed` flag

This means frontend code has to handle multiple response shapes for the same endpoint, leading to brittle parsing logic and inconsistent error display.

## Proposal

Adopt a consistent response envelope for all bid endpoint responses:

```json
// Success
{
  "data": { /* BidResource */ },
  "meta": { "item_closed": false }
}

// Error
{
  "error": {
    "message": "Bid must be at least $105.00",
    "code": "bid_too_low",
    "details": { "min_amount": 105.00 }
  }
}
```

## Benefits

- Frontend can use a single response handler for the bid endpoint
- Error codes enable localization without parsing error message strings
- The `meta` field provides a clean extension point for additional context (auto-close, outbid status, etc.)
- Consistent with REST API best practices

## Suggested Approach

1. Define a standard error response format (either a trait, a base controller method, or a dedicated `ApiResponse` class)
2. Update all error returns in `BidController` to use the standard format
3. Add the `meta` key to success responses for contextual information
4. Update the frontend bid submission handler to use the new format
5. Consider applying the same pattern to other controllers for full API consistency
