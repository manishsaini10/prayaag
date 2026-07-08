# Phase 1 — Full Project Audit

**Project:** `F:\school-website` — Laravel 12 (PHP 8.2+), single-site enterprise CMS (post de-tenancy).
**DB:** SQLite (`database/database.sqlite`); tests on `:memory:`. ULID PKs, SoftDeletes, audit trail throughout.
**Audit basis:** every file in the subsystems below was read directly. Findings reflect the **current, post-de-tenancy** state.

> ⚠️ **Meta-finding (highest priority): the codebase has never been executed.** No `migrate`, no `seed`, no `test` run has ever happened across either build phase. Everything below is statically sound, but *nothing has a runtime baseline*. The single most valuable action is `migrate:fresh --seed && php artisan test` **before** any new module work.

---

## A. Subsystem inventory & health

### 1. Routes (`routes/web.php`, `routes/api.php`) — ✅ healthy
- Public: `/`, `/{slug}` (negative-lookahead regex excludes `up`, `login`, `logout`, `admin`, `enquiries`, `jobs`, `subscribe`), `/sitemap.xml`, `/robots.txt`, and POST `/enquiries`, `/jobs/apply`, `/subscribe` (each `throttle:10,1` + honeypot).
- Auth: `/login` GET/POST (`throttle:5,1`), `/logout`.
- Admin (`auth` group): dashboard, page editor (index/edit/tree GET+PUT/preview), inboxes (enquiries/applications/subscribers + write actions + gated résumé), analytics.
- API (`auth` + per-route `permission:`): pages CRUD + tree sync, widgets catalogue.
- **No tenant/host routing remains.** Catch-all is correctly ordered last.

### 2. Controllers — ✅ healthy
Auth, Dashboard, Editor, Inbox, Analytics (Admin); Page, PageApi, WidgetApi, Sitemap, Subscriber, Enquiry, JobApplication (Cms). All single-site, all permission/honeypot/throttle-guarded where relevant. `ApiResponse` envelope is used consistently on the API.

### 3. Models (~37) — ✅ healthy
`BaseModel` (ULID + SoftDeletes + `RecordsActivity`, `$guarded=['id']`); builder/child models are plain `Model` + `HasUlids` reached via parent. RBAC via `HasRoles`. All `tenant_id` and `BelongsToTenant` usage removed. Scopes (`published`, `open`, `active`, `upcoming`, `location`) are pure status/date logic.

### 4. Migrations (~40 app tables + framework) — ✅ healthy
users, activity_logs, permissions, roles, role_user, permission_role, setting_groups, settings, media_folders, media, page_layouts, pages, page_sections/rows/columns/widgets/widget_settings, theme_components, menus, menu_items, categories, tags, posts, post_tag, notices, events, enquiries, job_listings, job_applications, downloads, testimonials, achievements, galleries, gallery_images, sliders, slides, academic_calendar, subscribers, page_views. `tenants`/`tenant_domains` migrations neutralized to no-ops. All composite tenant keys collapsed to single-column uniques/indexes (see `DETENANCY_REFACTOR.md`).

### 5. Seeders — ✅ healthy
`DatabaseSeeder` → Phase2/3/4/5/6/10/11/12/13. Produces admin user (`admin@school.test` / `password`), RBAC catalog, settings, media folders, published Home + Contact pages with real widgets, primary menu, and demo data for every dynamic widget. `RestaurantDemoSeeder` retired (no-op, unreferenced). Idempotent via `firstOrCreate` on the new single-column keys.

### 6. Middleware — ✅ healthy
`EnsurePermission` (`permission:` alias → 403 unless `$user->can()`). `ResolveTenant` neutralized to pass-through and removed from the HTTP groups.

### 7. Services — ✅ healthy
`SettingsManager` (typed, single cached map, `rememberForever` + `flush`), `MediaManager` (upload, dimensions, folders, configurable disk, optional WebP), `MenuManager` (`location()`/`tree()`), `ThemeRenderer` (header/footer from settings + primary menu), `BaseRepository`/`BaseService` (clean abstractions), `PageViewRecorder` (hashed IP), `ApiResponse`.

### 8. Widgets (15) — ✅ healthy
Static: heading, text, image, button, html (script-stripped). Dynamic: latest_posts, notice_board, upcoming_events, job_listings, testimonials, downloads. Media: slider, gallery. Forms: contact_form, newsletter — **both correctly emit `_token` (CSRF) + honeypot + rescue-guarded session reads**. `isDynamic()` drives cache bypass.

### 9. Builder — ✅ healthy
`WidgetRegistry` (singleton, plugin-style `register()`), `AbstractWidget` (defaults/escaping/`isDynamic`), `PageTreeService` (full-tree replace in one `DB::transaction`, FK-cascade cleanup), `PageRenderer` (DB tree → HTML, `renderTree()` for live preview, `renderCached()` with dynamic-widget bypass).

### 10. Dashboard — ⚠️ placeholder (by design)
`DashboardController` returns a static "Admin Dashboard" shell. No metrics/widgets yet — intended mount point for the visual editor.

### 11. Authentication — ✅ healthy
Session login (`Auth::attempt`), `throttle:5,1`, session regenerate on login + invalidate on logout, redirect-if-authenticated. RBAC enforced via `Gate::before` (super-admin bypass → permission check → fall through).

### 12. Menu system — ✅ healthy
Nested `menu_items` (self parent), `type` page|url, `resolveUrl()` resolves linked-page slug or explicit URL, location-based lookup, `tree()` ordering.

### 13. Settings system — ✅ healthy (one doc nit)
Typed key/value with groups; cached map. **Nit:** docblock references "the Settings facade" — no such facade/alias exists; access is via `app(SettingsManager::class)` / injection only.

### 14. Media system — ✅ healthy (one consistency nit)
Upload with metadata, folder paths, private-disk support (résumés), live `url()`. **Nit:** `delete()` hard-deletes the file but the `Media` row only soft-deletes (`BaseModel`), so a restored soft-deleted record points at a missing file. **`toWebp()` exists but is never called** (optimization implemented, not wired).

### 15. Page Builder (admin) — ✅ healthy
Editor MVP: page picker, palette from registry, JSON tree GET, validated atomic save (then cache-bust), live preview without persistence, draft/publish via `status`.

### 16. SEO system — ✅ healthy
Live `sitemap.xml` (published only, `home`→`/`, `lastmod`), `robots.txt` pointing at it, per-page meta (`description`, `keywords`) and Open Graph (`og:title` always, `og:description`/`og:image` when set) in `cms/layout`.

---

## B. Findings (issues & cleanup) — all minor, none blocking

| # | Severity | Subsystem | Finding |
|---|----------|-----------|---------|
| 1 | **High** | All | Never executed — no `migrate/seed/test` baseline. Run first. |
| 2 | Medium | Page Builder | `renderCached()` is fully built (with dynamic bypass) but **`PageController` calls `render()`, not `renderCached()`** — the public site never uses the page cache. One-line wire-up + verify form-page bypass. |
| 3 | Low | Schema | `page_widget_settings` table + `PageWidget::settingRows()` are **unused** — widget settings live in the JSON `settings` column. Dead table; drop or adopt. |
| 4 | Low | Media | `toWebp()` never called; `delete()` file/row soft-delete mismatch (see §14). |
| 5 | Low | Settings | Non-existent "Settings facade" referenced in docblock. |
| 6 | Low | Resources | `ResourceRegistry` is bound but nothing implements `ResourceInterface` — aspirational scaffolding for a future Resource Engine. |
| 7 | Info | Cleanup | 11 orphaned tenancy files still need manual `rm` (see `DETENANCY_REFACTOR.md` §2) — they're no-ops, harmless, but not yet deleted. |

**Security posture (verified, post-refactor):** RBAC + per-route permissions; login & form throttling; honeypots; CSRF tokens present in form widgets; HtmlWidget script-stripping; private-disk résumés behind a gated route; hashed analytics IPs; reserved-path-safe slug routing. No tenant-isolation gaps because there is no longer a tenant boundary.

---

## C. Gap inventory — the 27 "enterprise modules" (prompt Steps 4–11)

None of these exist yet; here is what's actually present vs. missing, so the build order is informed by reality rather than the list:

| Module | Status | Nearest existing foundation |
|--------|--------|------------------------------|
| Form Builder | ❌ Missing | Only hardcoded `contact_form`/`newsletter` widgets + `enquiries` table |
| Workflow Engine | ❌ Missing | — |
| Notification Engine | ❌ Missing | — |
| Email Queue | ⚠️ Partial | `jobs` queue tables exist; no Mailables/Notifications wired |
| API Builder + Tokens | ⚠️ Partial | Session-auth admin API exists; **no token auth/Sanctum** |
| Plugin System | ⚠️ Partial | `WidgetRegistry` + `ResourceRegistry` are hooks; no loader/manifest/lifecycle |
| Module Generator | ❌ Missing | — |
| Theme Marketplace | ⚠️ Partial | `theme_components` table + header/footer defaults; no marketplace/install |
| Health Monitor | ❌ Missing | — |
| Revision / Versioning | ❌ Missing | `activity_logs` captures changes but no restore/diff |
| Approval Workflow | ❌ Missing | `status` fields exist on content; no approval state machine |
| Dashboard Builder | ❌ Missing | Static placeholder dashboard |
| Global Search / Filters / Bulk Actions | ❌ Missing | — |
| Import / Export | ❌ Missing | — |
| Scheduler | ❌ Missing | Default console kernel; no scheduled commands |
| Activity / Audit Center | ⚠️ Partial | `RecordsActivity` → `activity_logs` works; **no admin UI to view it** |

---

## D. Recommended next steps (in order)

1. **Delete the 11 orphaned tenancy files** (`DETENANCY_REFACTOR.md` §2) + `composer dump-autoload`.
2. **Run `php artisan migrate:fresh --seed && php artisan storage:link && php artisan test`.** Get to green. This is the missing baseline.
3. **Quick wins from §B** (low-risk, high-clarity): wire `renderCached()` into `PageController`; decide `page_widget_settings` (drop vs. adopt); fix the doc/facade nit; resolve the media soft-delete/file mismatch.
4. **Then build the gap modules one at a time**, each with its own migration + feature tests + a real run. Suggested launch-value order: **Audit Center UI** (data already captured) → **Global Search + Bulk Actions** → **Revision/Versioning** → **Import/Export** → **API tokens (Sanctum)** → Form Builder → the rest. Plugin System / Marketplace / Module Generator are platform-scale and should come after the launch-critical set.

---

*Companion docs: `DETENANCY_REFACTOR.md` (what changed and why), this file (current state + gaps).*
