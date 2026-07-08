# Pendings Closed

Deferred items from the audit and phase docs that were implemented in code in
the "complete all pendings" pass. Each ships with coverage in
`tests/Feature/PendingItemsTest.php`.

## Security
- **Résumés moved off the public disk (audit finding #5, HIGH).**
  JobApplicationController now stores résumés on the private `local` disk.
  Download is via a new gated, tenant-scoped route
  `GET /admin/applications/{application}/resume` (auth required) that streams
  the file. MediaManager::store gained an optional `$disk` argument.
- **Mass-assignment hardening (audit finding #6, MEDIUM).**
  `PageView`, `GalleryImage`, `Slide` now declare explicit `$fillable` instead
  of `$guarded = []`.

## Admin write-actions (read-only inboxes gap)
- Enquiries: mark read / archive — `POST /admin/enquiries/{enquiry}/status`.
- Job applications: set status (new/reviewing/rejected/hired) —
  `POST /admin/applications/{application}/status`.
- Subscribers: unsubscribe — `POST /admin/subscribers/{subscriber}/unsubscribe`.
  All tenant-scoped via route-model binding (safe now that tenant resolution
  runs before binding), CSRF-protected, with controls wired into the views.

## Optimization / correctness
- **Dynamic-widget cache bypass (optimization finding #1).**
  Added `Widget::isDynamic()` (default false; true on all 10 dynamic widgets —
  latest_posts, notice_board, upcoming_events, job_listings, testimonials,
  downloads, slider, gallery, contact_form, newsletter).
  `PageRenderer::renderCached()` now bypasses the cache entirely for any page
  containing a dynamic widget, so live content and form CSRF tokens are never
  served stale.

## SEO
- **Per-page meta tags.** PageController passes the page's `seo` blob to the
  layout, which now emits `<meta name="description">`, `keywords`, and
  `og:title` / `og:description` / `og:image` when present.

---

## Still genuinely pending (cannot be closed by writing code)
1. **Running the suite** — `migrate:fresh --seed && php artisan test`. Requires
   shell access to the project; this is the operator's step. Nothing here has
   executed yet, including these changes.
2. **Phase 17 — Reusability Validation.** By definition a running exercise:
   stand up a second site type on the same engine and confirm it generalizes.
3. **Key-dependent integrations** — Live Chat, external Analytics (GA). Need
   third-party credentials.
4. **Editor polish** — true drag-and-drop and in-canvas live preview (the MVP
   uses up/down ordering and a settings form).
5. **Remaining audit items** — password reset / email verification / 2FA;
   `composer audit` for dependency CVEs (needs installed deps); live
   penetration testing of the hardened HTML widget and tenant isolation.
