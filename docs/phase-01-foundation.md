# Phase 1 — CMS Foundation

## Decisions locked
- Tenancy: row-level, single database. `tenant_id` on tenant-owned tables,
  enforced by a global Eloquent scope (TenantScope).
- Primary keys: ULID everywhere (non-enumerable, time-sortable).
- Database: MySQL / MariaDB.
- Tenant resolution: by request host via the `tenant_domains` table
  (ResolveTenant middleware on the web + api groups).

## Layers
HTTP -> middleware (ResolveTenant) -> presentation (Livewire/controller/API)
-> Service -> Repository -> Eloquent model -> DB. Business logic lives in
services only; presentation never touches Eloquent directly.

Public read pipeline (built in later phases): DB -> Resource Engine ->
Block Engine -> Theme Engine -> Blade -> HTML.

## Base abstractions
- BaseModel: ULID + soft deletes + activity recording. Extended by every model.
- BelongsToTenant: opt-in trait for tenant-owned models. Auto-stamps tenant_id
  and applies TenantScope. NOT used by Tenant / TenantDomain.
- RepositoryInterface + BaseRepository: all data access; guarantees scoping.
- BaseService: business logic; the only layer presentation calls.
- ResourceInterface + ResourceRegistry: CONTRACT ONLY in Phase 1. The Resource
  Engine itself is a later phase. Resource definitions are PHP classes (like
  Filament/Nova); only content & config are database-driven.
- ApiResponse: single { success, data, meta } / { success, message, errors }
  envelope for all API + AJAX responses.

## Tables
tenants(id ulid, name, slug unique, status, settings json, trial_ends_at, ts, sd)
tenant_domains(id ulid, tenant_id fk, domain unique, is_primary, ts, sd)
activity_logs(id ulid, tenant_id nullable, log_name, description,
  subject morphs, causer morphs nullable, properties json, created_at)

`causer` is nullable + polymorphic so Phase 2 users slot in with no rewrite.

## Deferred (do NOT build in Phase 1)
users/roles/permissions (P2), settings (P3), media (P4), themes (P5),
menus (P6), pages (P7), content modules (P8+).

## Notes
- When no tenant is resolved (console, seeders) the scope is skipped, so
  artisan/seeders see all tenants. Set the tenant explicitly in jobs/commands
  that must be tenant-aware: app(TenantManager::class)->set($tenant).
- The audit trail is hand-rolled (RecordsActivity + ActivityLog). It can be
  swapped for spatie/laravel-activitylog later; the table shape is compatible.
