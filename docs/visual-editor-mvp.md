# Visual Editor — MVP

A working slice of the drag-light page editor. Not the full Elementor-class
tool, but a real, usable builder that reads and writes the same tree the public
renderer uses.

## What it does
- Lists pages at /admin/pages with an Edit link each.
- /admin/pages/{page}/edit loads the editor shell + the widget palette (every
  registered widget: type, label, category, default settings).
- Loads the page's current tree as JSON, renders an editable canvas of
  Sections -> Rows -> Columns -> Widgets.
- Add / delete / reorder (up-down) at every level; set column width (1-12).
- Per-widget settings form generated from the widget's default settings
  (checkbox for booleans, number for numbers, JSON textarea for arrays/objects,
  text otherwise).
- Save writes the whole tree back via PageTreeService::sync (one transaction),
  then busts the render cache. Preview opens the live public page.

## Why web routes, not the Phase 8 API
The /api routes need token/Sanctum auth that isn't set up. The editor instead
uses session-authenticated web routes under /admin (cookie + CSRF), so it works
in a browser today with no extra auth wiring. Same PageTreeService underneath,
so the API and the editor stay consistent.

## Architecture
- EditorController: index (picker), edit (shell), tree (GET JSON), save
  (PUT -> sync + cache forget). Page is route-model-bound and tenant-scoped, so
  a cross-tenant page id 404s.
- resources/views/admin/editor.blade.php: data injected via Blade; the app
  itself is vanilla JS inside @verbatim (no build step, no framework). State is
  a plain JS object mirroring the sync tree shape exactly.

## Deliberately minimal (clear next steps)
- Reordering: widgets support drag-and-drop (a drag handle per widget; columns
  and widgets are drop targets, so you can reorder within a column or move a
  widget to another column). Up/down buttons remain for sections/rows/columns.
  DnD only mutates the in-memory tree and re-renders, so Save and Live Preview
  are unaffected.
- Live preview IS available: the "Live Preview" toggle renders the current
  (unsaved) tree in an isolated iframe via POST /admin/pages/{page}/preview
  (server-side, reusing the widget registry through PageRenderer::renderTree),
  refreshed on every edit. It does not persist anything.
- Settings toggles reset on structural re-render.
- Permission gating is auth-only; add can:pages.edit once the permission names
  are confirmed against the seeder.
These are additive refinements; the data contract (sync tree) won't change.

## Gate (exit criteria)
- editor requires authentication
- tree endpoint returns the structure as JSON
- save replaces the tree and the change shows in the rendered HTML
- a cross-tenant page id is not editable (404)
