# Phase 13 — Remaining Content Modules + Analytics

Completes every remaining seedable module in one pass, so a single
`migrate:fresh --seed` populates the whole CMS.

## Tables (10)
- downloads (tenant): title, category, description, file, file_type, file_size,
  sort_order, is_published.
- testimonials (tenant): author, role, quote, photo, sort_order, is_published.
- achievements (tenant): title, year, description, icon, sort_order,
  is_published.
- galleries (tenant): title, slug, description, is_published.
- gallery_images: gallery_id, image, caption, sort_order (child model).
- sliders (tenant): title, location, is_published.
- slides: slider_id, image, heading, subheading, link_url, link_label,
  sort_order (child model).
- academic_calendar (tenant): title, type (holiday|term|exam|event), starts_on,
  ends_on, description — folds in the blueprint's separate Holidays module.
- subscribers (tenant): email, name, status, subscribed_at, unsubscribed_at,
  source; unique (tenant, email).
- page_views: tenant_id, page_id, path, referrer, ip_hash, user_agent,
  viewed_at — append-only, no timestamps/soft-deletes.

## Image storage choice
Display modules (slides, gallery images, testimonial photos, downloads) use a
plain string `image`/`file` column (path or URL) rather than a media FK. This
keeps seeding trivial and lets admins paste a URL or a library path. The page
builder's ImageWidget and job résumés still use the media library proper.

## Models
- Tenant-scoped (BaseModel + BelongsToTenant): Download, Testimonial,
  Achievement, Gallery, Slider, AcademicCalendarEntry, Subscriber — each with a
  published()/active() scope where relevant.
- Child models (plain Model + HasUlids, reached via parent): GalleryImage,
  Slide.
- PageView: plain Model + HasUlids, timestamps disabled (append-only).

## Widgets (registered in CoreServiceProvider)
- testimonials, downloads (content)
- slider, gallery (media)
- newsletter (forms) — posts to /subscribe

## Newsletter signup
- POST /subscribe -> SubscriberController@store (honeypot + throttle:10,1).
  Idempotent via updateOrCreate on (tenant, email): a repeat signup re-activates
  rather than erroring on the unique index.

## Analytics (first-party, lightweight)
- PageViewRecorder records one row per public page view; called from
  PageController inside rescue() so it can NEVER break rendering. IPs are stored
  only as a salted SHA-256 hash. Note: this adds one INSERT per public request —
  for high traffic, move it to a queued job or sampled recording later.

## Seeding
Phase13Seeder adds downloads, testimonials, achievements, a Campus Life gallery
(4 images), a homepage slider (2 slides), academic-calendar entries, and
subscribers. DatabaseSeeder now runs Phases 2-6, 10, 11, 12, 13.

## Gate (Phase 13 exit criteria)
- modules isolated per tenant
- gallery returns images in sort order
- public subscribe stores under the tenant; repeat signup is idempotent;
  honeypot drops bots
- slider and testimonials widgets render seeded data

## NOT in this phase (and why)
- Visual editor SPA — a front-end project, not seedable module code.
- Live Chat / Analytics provider integrations — need your third-party keys.
- SEO sitemap.xml / robots.txt generation — needs a running app to emit.
- Phases 15-17 (Optimization, Security Audit, Reusability Validation) —
  verification activities that require profiling/pen-testing a running instance.
