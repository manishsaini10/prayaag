# Phase 9 — Authentication

Hand-rolled session auth (no Sanctum/Breeze). Gives a login flow so a human can
sign in as the seeded admin and reach the Phase 8 API and the admin dashboard.

## Routes (web)
- GET  /login   -> AuthController@showLogin (name: login)
- POST /login   -> AuthController@login
- POST /logout  -> AuthController@logout (name: logout)
- GET  /admin   -> DashboardController@index  [auth]  (name: admin.dashboard)
These are registered before the public `/{slug}` catch-all, which also excludes
up/login/logout/admin.

## Tenant-aware by construction
Auth::attempt queries the User model, whose global BelongsToTenant scope is
active because ResolveTenant ran on the web group. So credentials only match a
user in the tenant resolved from the request host — a user cannot log in on
another tenant's domain. This is verified by a test.

## Why this matters for what's registered earlier
The `auth` middleware (used by /admin and the entire Phase 8 API) needs a
`login` named route to redirect guests to. Before this phase that route didn't
exist; now it does, so unauthenticated web access redirects cleanly instead of
erroring.

## Demo login
After `php artisan migrate:fresh --seed` and `php artisan serve`, visit
http://127.0.0.1:8000/login and sign in with the Phase 2 seed account:
    admin@school.test / password
You'll land on the placeholder /admin dashboard. The visual page builder mounts
there in a later phase.

## Gate (Phase 9 exit criteria)
- the login screen renders
- a valid user logs in and reaches /admin
- a bad password fails with errors
- a user from another tenant cannot log in on this host
- guests are redirected from /admin; logout works
