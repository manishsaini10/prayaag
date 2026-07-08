# Phase 2 — Users & Permissions

## Tables
- users (replaced Laravel default): ulid id, tenant_id (indexed ulid), name,
  email, password, soft deletes. Email is unique PER TENANT, not globally.
  No DB-level FK on tenant_id (migration runs before the tenants table and
  SQLite cannot add the constraint later); integrity is enforced at the app
  layer by BelongsToTenant.
- permissions: global ability catalog (slug unique). NOT tenant-scoped.
- roles: per-tenant (tenant_id FK to tenants). slug unique per tenant.
- role_user / permission_role: ULID pivot tables.

## Model conventions
- User extends Authenticatable but wears the same traits as BaseModel
  (HasUlids, SoftDeletes, RecordsActivity) plus BelongsToTenant + HasRoles.
- Role extends BaseModel + BelongsToTenant. Permission extends BaseModel
  (global, no tenant scope).

## RBAC
- A user's effective permissions = union of its roles' permissions.
- CoreServiceProvider registers a Gate::before: super-admins pass every
  ability; everyone else passes an ability if they hold the matching
  permission slug. So $user->can('pages.create') just works.
- Route guard: ->middleware('permission:pages.create') via EnsurePermission.

## Wiring (already applied in bootstrap/)
- bootstrap/providers.php registers App\Providers\CoreServiceProvider.
- bootstrap/app.php appends ResolveTenant to the web + api groups and
  aliases 'permission' => EnsurePermission.

## Local bootstrap (Phase2Seeder)
Creates: Demo School tenant + 127.0.0.1/localhost domains, the permission
catalog, super-admin + editor roles, and admin@school.test / password.
Login works on `php artisan serve` because ResolveTenant matches the
127.0.0.1 host to the seeded tenant.

## Database
Currently SQLite (default scaffold). The blueprint's MySQL/MariaDB target is
a single .env change (uncomment the DB_* block) whenever you want it; all
Phase 1/2 migrations run on either.

## Gate (Phase 2 exit criteria)
- migrations run clean (users + permissions + roles + 2 pivots)
- a user inherits its roles' permissions
- super-admin passes any gate
- a user without a permission is denied
