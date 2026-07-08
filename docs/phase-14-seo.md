# Phase 14 — SEO Discoverability (sitemap + robots)

Pure-code SEO surface, generated live from the database. No new tables.

## Routes / controller
- GET /sitemap.xml -> SitemapController@sitemap
  - Tenant-scoped; lists published pages only, newest first.
  - `home` maps to `/`; other slugs to `/{slug}`. Absolute URLs via url().
  - lastmod from each page's updated_at (Atom format).
  - Content-Type: application/xml.
- GET /robots.txt -> SitemapController@robots
  - Allows all, points crawlers at /sitemap.xml.

Both are registered before the catch-all; their dots already exclude them from
the page catch-all regex, so no slug collision.

## Why live generation
New/edited/unpublished pages are reflected immediately with no build or cache
step — the sitemap always matches the published set for that tenant.

## Per-page meta tags (not done here)
Pages already store an `seo` JSON blob (title/description/og) from Phase 5. To
emit it, the public layout needs a few meta lines fed from PageController. That
touches the layout that PageRenderingTest covers, so it's left as a small,
deliberate follow-up rather than folded in silently.

## Gate (Phase 14 exit criteria)
- sitemap lists published pages, excludes drafts
- sitemap is tenant-scoped (only the resolved host's tenant)
- robots.txt references the sitemap

## What remains after this
Only two things, both outside "authorable pure-code modules":
1. The visual editor SPA — a front-end project (an MVP slice is the next
   buildable chunk).
2. Phases 15-17 (Optimization, Security Audit, Reusability Validation) —
   verification work that requires a running, seeded instance.
Plus integrations that need YOUR keys: Live Chat provider, external Analytics
(GA), and any third-party SEO services.
