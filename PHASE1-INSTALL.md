# Phase 1 — CMS Foundation: install & run

These files are ADDITIVE to a fresh Laravel 12 install. Nothing here
overwrites a Laravel default. Run the steps in order on your machine
(XAMPP PHP + MySQL). Anything in `<>` you adjust.

## 1. Scaffold Laravel (folder must be empty)
    cd F:\school-website
    composer create-project laravel/laravel .

## 2. Drop these files in
Extract this zip into F:\school-website, merging folders. It adds:
    app/Core/...            (base abstractions + tenancy)
    app/Models/Tenant.php, TenantDomain.php, ActivityLog.php
    app/Providers/CoreServiceProvider.php
    database/migrations/2025_01_01_00000{1,2,3}_*.php
    tests/Feature/*, tests/Fixtures/Note.php
    docs/phase-01-foundation.md

## 3. Register the service provider
Edit bootstrap/providers.php and add the class to the array:
    return [
        App\Providers\AppServiceProvider::class,
        App\Providers\CoreServiceProvider::class,   // <-- add
    ];

## 4. Register the tenant middleware
Edit bootstrap/app.php, inside ->withMiddleware(function (Middleware $middleware) {...}):
    $middleware->web(append: [\App\Core\Tenancy\Middleware\ResolveTenant::class]);
    $middleware->api(append: [\App\Core\Tenancy\Middleware\ResolveTenant::class]);

## 5. Configure the database (.env)
Create the database first (phpMyAdmin or CLI): CREATE DATABASE school_website;
Then set in .env:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=school_website
    DB_USERNAME=root
    DB_PASSWORD=
For a friction-free first boot, leave cache/queue on the simple drivers:
    CACHE_STORE=database
    QUEUE_CONNECTION=database
(Redis is the production target per the blueprint; switch once Redis is running.)

## 6. Migrate
    php artisan migrate
If you chose the database queue/cache drivers above, also run:
    php artisan make:queue-table   (only if not already present, older setups)
    php artisan migrate

## 7. Run the tests (proves the gate)
    php artisan test
Expected: tenant isolation + auto tenant_id + activity-on-write all green.

## 8. Serve
    php artisan serve
Visit http://127.0.0.1:8000

## Phase 1 approval gate
- migrations run clean on a fresh DB
- a test proves tenant A cannot read tenant B's rows
- tenant_id is auto-stamped on create
- an activity_logs row is written on every model write
- this design is captured in docs/phase-01-foundation.md
