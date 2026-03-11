Based on the task described below, produce a structured implementation plan.

## Format

### WHAT
Describe the task precisely. What is being built or changed? What is the expected behavior?

### WHERE
List every file that will be created or modified. Use full paths from project root. Typical locations in this project:
- Models: `backend/app/Models/`
- Controllers: `backend/app/Http/Controllers/Api/`
- Form Requests: `backend/app/Http/Requests/`
- Middleware: `backend/app/Http/Middleware/`
- Events: `backend/app/Events/`
- Listeners: `backend/app/Listeners/`
- Notifications: `backend/app/Notifications/`
- Filament Resources: `backend/app/Filament/Resources/`
- Filament Widgets: `backend/app/Filament/Widgets/`
- Blade views: `backend/resources/views/`
- Vue pages: `frontend/src/pages/`
- Vue components: `frontend/src/components/`
- Vue composables: `frontend/src/composables/`
- Routes: `backend/routes/api.php`, `backend/routes/web.php`
- Migrations: `backend/database/migrations/`
- Tests: `backend/tests/Feature/`, `backend/tests/Unit/`

### HOW
Describe the implementation approach step by step. Include:
- Database changes (migrations, schema)
- Backend logic (models, relationships, events, queues)
- API surface (routes, controllers, validation)
- Frontend changes (components, pages, API calls)
- Any configuration or service registration needed

### VERIFY
Define what success looks like:
- Specific tests that should pass
- Manual verification steps
- Edge cases to confirm

$ARGUMENTS
