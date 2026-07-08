# Phase 10 — Content Modules + Dynamic Data Binding

The content layer that makes the builder useful for a real site, plus the
widgets that bind content into pages automatically.

## Tables
- categories (tenant, self-nesting), tags (tenant), posts (tenant; category_id,
  status, published_at, featured_image), post_tag (pivot), notices (tenant;
  starts_at/ends_at/is_pinned), events (tenant; starts_at/ends_at/location).

## Models & scopes
- Post: category(), tags(), scopePublished() (status=published AND
  published_at null or <= now).
- Notice: scopeActive() (within starts/ends window or null).
- Event: scopeUpcoming() (starts_at >= now).
- Category (parent/children/posts), Tag (posts) — all tenant-scoped.

## Dynamic data binding (the keystone)
Three widgets query their module directly at render time, so a page shows live
content with zero manual updates — the blueprint's "Latest Notices widget →
loads automatically":
- latest_posts   -> Post::published()   (setting: limit)
- notice_board   -> Notice::active()    (pinned first; setting: limit)
- upcoming_events-> Event::upcoming()    (soonest first; setting: limit)
Because the models are tenant-scoped, a widget on tenant A's page only ever
shows tenant A's content. Registered in CoreServiceProvider alongside the
static widgets.

## Seeding
Phase10Seeder adds a News category, 3 published posts, 2 notices, and 2 events
for the Demo School tenant. DatabaseSeeder now runs Phases 2-6 then 10.

## Gate (Phase 10 exit criteria)
- published scope excludes drafts and future posts
- content is isolated per tenant
- post/tag relationship works
- the latest_posts widget renders real published data
