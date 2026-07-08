# Optimization Pass (Static)

Static review for query/runtime hot spots. Actual profiling (query counts,
timings, memory) needs a running, seeded instance and is listed as run-gated.

---

## FIXED in this pass

### Added index for the analytics aggregation
`/admin/analytics` groups page_views by path. Added `index(tenant_id, path)` to
the page_views migration so the top-pages query is covered rather than scanning.

---

## OPEN — recommended

### 1. [IMPORTANT — correctness] renderCached() vs dynamic widgets
`PageRenderer::renderCached()` caches the whole page's HTML with
`rememberForever`. Pages containing DYNAMIC widgets — latest_posts,
notice_board, upcoming_events, job_listings, testimonials, downloads, slider,
gallery, contact_form, newsletter — would be frozen at first render and serve
stale content (and a stale CSRF token in the form widgets).
The live path (`PageController`) currently calls `render()`, NOT `renderCached()`,
so there is no staleness today — but the cached method is a trap waiting to be
used.
**Recommend:** add `Widget::isDynamic(): bool` (default false; true on the
dynamic widgets), have `renderCached()` bypass the cache when a page contains any
dynamic widget, and call `forget()` on relevant content writes. Until then,
document `renderCached()` as "static pages only".

### 2. [MEDIUM] Per-widget queries (N+1 across widgets)
Each dynamic widget runs its own query at render. A content-heavy page with many
dynamic widgets issues one query per widget. Fine at small scale; for heavy
pages consider a per-request cache or batching common lookups.

### 3. [MEDIUM] Header/menu rebuilt every request
`ThemeRenderer::header()` builds the primary menu tree on each request. Eager-
load menu items and/or cache the rendered header per tenant (invalidate on menu
change).

### 4. [MEDIUM] One INSERT per public page view
`PageViewRecorder` writes a row on every public request (inside rescue(), so it
can't break rendering). At traffic, move to a queued job or sample (e.g. 1 in N),
or batch-flush.

---

## Verified OK
- `PageRenderer::render()` eager-loads `sections.rows.columns.widgets` — no N+1
  walking the page tree.
- Index coverage is solid: posts (tenant,status,published_at), events/notices
  (tenant,starts_at), enquiries (tenant,type / tenant,status), job_listings
  (tenant,status), subscribers unique (tenant,email), downloads/testimonials/
  achievements (tenant,is_published), page_views (tenant,viewed_at / page_id /
  path).
- ULID PKs are indexed primaries; FK columns are constrained (indexed).

## Run-gated (needs profiling)
- Real query counts per page (Laravel Debugbar / Telescope).
- Cache hit/miss behaviour and the dynamic-widget cache decision under load.
- Slow-query log review against seeded data volumes.
- Asset/payload sizes from the front end.
