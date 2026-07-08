# Phase 3 — Settings Engine

## Tables
- setting_groups: global catalog of settings sections (General, SEO, Contact).
  id ulid, name, slug unique, description, sort_order, timestamps, soft deletes.
- settings: per-tenant key/value. id ulid, tenant_id (FK), group_id (FK,
  nullable), key, value (longText), type, timestamps, soft deletes.
  Unique per (tenant_id, key).

## Engine
- SettingsManager (singleton, app(SettingsManager::class)) is tenant-aware:
  it reads the current tenant from TenantManager at call time.
- Reads come from a single cached map per tenant (cache key settings:{tenantId});
  any write flushes that key. On SQLite local dev the cache store is the
  database; switching to Redis later needs no code change.
- Values are stored as text + a `type` column and cast on read:
  string | boolean | integer | float | json. set() infers the type when
  not given.
- Facade: \App\Core\Settings\Settings::get('site_name') — usable by full
  class name, no alias registration required.

## Usage
    app(SettingsManager::class)->set('site_name', 'My School', 'string', 'general');
    app(SettingsManager::class)->get('site_name', 'Default');
    \App\Core\Settings\Settings::get('maintenance_mode'); // returns bool

## Seeding
Phase3Seeder seeds the three groups and starter values for the Demo School
tenant. DatabaseSeeder now calls Phase2Seeder then Phase3Seeder, so
`php artisan migrate:fresh --seed` builds the whole local state.

## Gate (Phase 3 exit criteria)
- migrations run clean (setting_groups + settings)
- values round-trip with correct type casting
- settings are isolated per tenant
- the cached map invalidates on write
