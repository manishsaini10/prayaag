# Phase 6 — Menu Builder

## Tables
- menus (tenant): name, slug, location (primary | footer | mobile ...).
  Unique slug per tenant.
- menu_items: menu_id (FK), parent_id (self FK, nullable — supports nested /
  mega menus), label, type (url | page | custom), url, page_id (FK pages,
  nullable), target, sort_order, settings (json: icon, css class, mega config).

## Models
- Menu (BaseModel + BelongsToTenant): items(), rootItems(), scopeLocation(),
  and tree() which returns the nested item structure (each item gets a
  `children` relation).
- MenuItem (lightweight, reached via its menu): parent/children/menu/page
  relations and resolveUrl() — returns the linked page's slug path when
  type=page, otherwise the explicit URL.

## Engine
- MenuManager (app(MenuManager::class)): location($loc) returns the tenant's
  menu for a location; tree($loc) returns its nested item tree. Used by the
  theme builder's header/footer to render dynamic menus.

## Seeding
Phase6Seeder creates a Primary menu (Home/About/Admissions/Contact) for the
Demo School tenant, linking Home to the seeded Home page. DatabaseSeeder now
runs Phases 2 → 6.

## Gate (Phase 6 exit criteria)
- migrations run clean (menus + menu_items)
- a menu builds a correct nested tree
- a page-linked item resolves to the page's URL
- menus are isolated per tenant
