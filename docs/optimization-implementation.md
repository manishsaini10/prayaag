# Optimization Implementation

This project now has a repeatable optimization path for production use.

## What Was Added

- Composite database indexes for page analytics, popup targeting, popup analytics, chatbot conversations, messages, leads, and chatbot event reports.
- A safe maintenance command: `php artisan cms:maintenance`.
- Git ignore rules for generated Laravel caches, uploaded local files, build artifacts, and zip archives.
- Documentation for expected effects and operating commands.

## Expected Effect

- Admin analytics pages should scan fewer rows when filtering by path, popup, event type, date, visitor, operator, and lead status.
- Popup display checks should be faster because active rule lookup has a composite index.
- Chatbot inbox/report queries should improve for operator dashboards, unread message checks, visitor history, and event reporting.
- Git status should stay cleaner after running Laravel, tests, builds, uploads, and cache commands.

## Operating Commands

Run after deployment:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

Run maintenance report:

```bash
php artisan cms:maintenance
```

Prune old popup analytics after confirming the report:

```bash
php artisan cms:maintenance --days=365 --prune-analytics
```

Refresh compiled Blade views after template changes:

```bash
php artisan cms:maintenance --clear-views
```

## Notes

- The maintenance command does not delete analytics by default.
- Existing scheduled popup cleanup still runs daily with a 365-day retention window.
- Keep uploads on durable storage in production and avoid committing local upload folders.
