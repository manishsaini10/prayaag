# Phase 5 — Visual Page Builder & Theme Builder (backend backbone)

This phase implements the data architecture and server-side engines that the
visual builder stands on. The drag-and-drop editor UI with live preview is the
NEXT increment (front-end); it is intentionally not in this phase.

## Tables
- page_layouts (tenant): named layout templates.
- pages (tenant): title, slug, layout_id, status, seo (json, inline for now —
  migrates to the dedicated SEO module later). Unique slug per tenant.
- page_sections -> page_rows -> page_columns -> page_widgets: the builder tree.
  Nested tables are reached through the (tenant-scoped) page via FKs and cascade
  on delete, so they need no tenant_id of their own.
- page_widget_settings: optional normalized key/value store. The canonical
  settings store is page_widgets.settings (json).
- theme_components (tenant): headers, footers, sidebars, menus, global blocks.
  `content` holds a builder tree; `is_default` marks the site-wide one.

## Engines
- WidgetRegistry (singleton): widget types register here. Defaults
  (heading, text, image, button, html) are registered in
  CoreServiceProvider::boot. New/plugin widgets implement the Widget contract
  and register the same way — no core change. This satisfies "no hardcoded
  widgets / installable without modifying core".
- PageRenderer (singleton): walks DB tree -> section/row/column -> widget and
  produces HTML entirely in PHP. renderCached()/forget() give per-tenant render
  caching. Blade (added later) stays a thin renderer; no logic in templates.
- Theme settings reuse the Phase 3 Settings Engine (theme_* keys) instead of a
  duplicate table.

## Widget contract
App\Core\Builder\Contracts\Widget — type(), label(), category(),
defaultSettings(), render(array $settings, array $context = []): string.
$context is the dynamic-data-binding hook (e.g. a Notices widget pulling the
latest records once that module exists).

## Security
HtmlWidget sanitizes server-side: strips script/style/iframe/object/embed/
link/meta/base blocks, inline on* handlers, and javascript: URLs. Full
sandboxing (CSP / iframe isolation) belongs to the editor/front-end layer.

## Explicitly deferred to later increments
- The visual editor (left panel widgets/sections, center live preview, right
  panel style + responsive + data-binding controls), no-refresh updates.
- Responsive engine controls, template import/export, mega-menu UI.
- Blade component renderers and front-end theme assets.

## Gate (Phase 5 exit criteria)
- migrations run clean (8 tables)
- the registry renders a registered widget
- the renderer outputs the full section/row/column/widget tree
- pages are isolated per tenant
- the HTML widget strips scripts
