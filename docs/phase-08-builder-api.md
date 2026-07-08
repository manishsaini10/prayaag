# Phase 8 — Page Builder API

The API substrate the visual editor's front-end will call. No new tables; this
exposes Phases 5–7 over authenticated, permission-gated HTTP using the Phase 1
ApiResponse envelope.

## Routing
- routes/api.php registered via bootstrap/app.php (withRouting api:), prefix /api.
- ResolveTenant is appended to the api group, so requests resolve a tenant from
  the host exactly like the web group.

## Endpoints (all under 'auth' + a permission)
- GET    /api/widgets            (pages.view)   widget catalogue for the palette
- GET    /api/pages              (pages.view)   paginated list
- POST   /api/pages              (pages.create) create a page
- GET    /api/pages/{id}         (pages.view)   page + full tree
- PUT    /api/pages/{id}         (pages.update) update page meta
- DELETE /api/pages/{id}         (pages.delete) delete
- PUT    /api/pages/{id}/tree    (pages.update) replace the whole builder tree

## Tree sync
PageTreeService.sync() persists an entire sections/rows/columns/widgets payload
in one transaction. It deletes existing sections (FK cascade removes the rest)
and rebuilds from the payload, so the editor can "save the page" atomically and
re-saving never accumulates duplicates.

## Tenant + authorization
- Page lookups use findOrFail within the resolved tenant's scope, so an id from
  another tenant returns 404 — no route-model-binding leakage.
- Per-route 'permission:*' middleware enforces RBAC; super-admins pass via the
  Gate::before from Phase 2.

## Known limitation (next phase)
These routes require an authenticated user, but there is no login flow yet
(Phase 2 built users/RBAC, not auth scaffolding). Tests use actingAs(); browser
use needs the auth phase. Token auth (Sanctum) was intentionally not added to
avoid a dependency before it's needed.

## Gate (Phase 8 exit criteria)
- an admin can list pages and sync a tree
- a user without the permission is 403'd
- re-syncing a tree replaces rather than duplicates it
