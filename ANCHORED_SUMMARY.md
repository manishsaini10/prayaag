# Anchored Summary — School Website CMS

## Goal
- Complete the full AI Chatbot Enterprise Platform including dynamic pre-chat form builder, campaign/webhook/analytics admin views, enterprise module admin UIs, and migrate the `/post-testimonial` page into the CMS page builder.

## Constraints & Preferences
- No Livewire or blade-ui-kit packages
- Embed script must work on any external site via single `<script>` tag
- Per-provider API keys stored in `settings_data['ai']["{$provider}_key"]` instead of single `api_key`
- CSRF excluded for `chatbot/widget/*` in `bootstrap/app.php`
- Form fields must be fully dynamic (admin creates fields, marks required/optional, drag-reorder)
- Form submission data saves as JSON, legacy flat fields remain for backward compat
- `/post-testimonial` must be migrated from standalone Blade view to CMS page builder with same content/design

## Progress
### Done
- **Dynamic Pre-Chat Form Builder** — complete backend-managed system (migration, model, controller, CRUD views, API endpoint, JS runtime, embed fallback, 11 tests)
- **Enterprise Module Admin Views** — campaigns, webhooks, analytics, reports, funnel analytics views all created
- **AnalyticsController** updated with realtime chart data and report generation
- **Webhook dispatch service** (`WebhookDispatcher`) + trigger from enquiry/chatbot event
- **Mail notification system** (`MailNotification` Mailable + email template)
- **API token management** (`ApiTokenController` + model + view)
- **TOTP-based 2FA** — pure PHP HMAC-SHA1, setup/enable/disable/challenge, Google Charts QR code
- **Funnel analytics** (`FunnelAnalytics` service + dashboard view)
- **Fixed pre-chat form not showing** — `showScreen()` always renders form (removed localStorage check)
- **Fixed embed form submission bugs** — legacy fallback fields got `name` attributes; removed broken `firstFieldValue` check; button disable on submit
- **Fixed `QrCode` class not found** — replaced with Google Charts QR API URL
- **Migrated `/post-testimonial` to CMS page builder**:
  - `TestimonialPageWidget.php` created — renders `<x-testimonial-page />` via `Blade::render()`
  - `testimonial-page.blade.php` component with hero, brand showcase, form
  - Registered in `CoreServiceProvider.php` under forms category
  - Removed explicit route from `routes/web.php` + removed from catch-all exclusion regex
  - `TestimonialPageSeeder.php` creates published CMS page with widget
  - **Added widget settings** — hero_accent_text, hero_heading, hero_description, show_rating_cards, rating_value/label, verified_value/label, show_guide, guide_title, guide_steps, background_style (dropdown), form_title, form_description, form_button_text, consent_text
  - **TestimonialPageWidget now passes settings** to component via `Blade::render()` data array
  - **testimonial-page and testimonial-form components** updated to use `@props` and consume settings
- **Fixed failing ParentTestimonialsTest** — replaced `route('testimonials.create')` with direct `/post-testimonial` URL; added CMS page creation (`Page::firstOrCreate` + `PageTreeService::sync`) in test since `RefreshDatabase` wipes state
- **Created BreadcrumbWidget** — `app/Core/Builder/Widgets/BreadcrumbWidget.php`:
  - **15 settings**: style (simple/gradient/modern/minimal/with-image/with-video), separator (chevron/slash/dot/arrow), home_text, show_home, show_current_page, alignment, background_color, text_color, accent_color, overlay_opacity, padding_y, background_image, background_video, show_mobile, **min_height** (60px–300px dropdown), **max_width** (full/7xl/6xl/5xl/4xl/3xl/2xl dropdown), **width_style** (full/box dropdown)
  - **Auto-detects current page** from `$context['page_slug']` (passed by `PageRenderer`) or falls back to `request()->path()` + DB lookup
  - **6 visual styles** in `breadcrumb.blade.php` component
  - **Image/video background** with overlay, YouTube/Vimeo/mp4 support, fallback logic
  - **Fixed padding** — uses Tailwind class `{{ $padding }}` instead of undefined CSS variable `var(--padding)`
  - **Fixed `array_merge($this->defaultSettings(), $settings)`** in `render()` so all keys are guaranteed even if not user-saved
  - **17 tests** in `BreadcrumbWidgetTest.php` covering all styles, separators, alignment, mobile, image, video, fallback, height, width, real page via `PageRenderer`, width_style box
- **Updated `PageRenderer.php`** — added `$renderContext` property set in `render(Page $page)` and passed to `renderWidget()` → `WidgetRegistry::render()` → widget `render()` method; editor `renderTree()` passes empty context
- **`width_style` added to BreadcrumbWidget** — `full` (default) and `box` options; `$widthClass` applies `container mx-auto` when box + optional `max-w-{value}`; applied to all 6 style navs; tests updated
- **`database/deploy.sql` generated** (~1863 KB) — complete MySQL dump with 129 tables (schema + seed data) via SQLite-to-MySQL conversion; exported using PRAGMA-based column info with proper ULID→VARCHAR(26), datetime→TIMESTAMP, tinyint→TINYINT(1) mapping
- **`database/deploy.sh` generated** — full deployment script for Hostinger terminal (creates DB, installs Composer/npm, runs Laravel setup, sets permissions, configures cron)
- **Git commit + push** — commit `f588388` pushed to `main` on GitHub
- **Read all 65 migration files** — complete schema audit of the rewritten migration set (~130+ tables total)
- **Generated `database/deploy_fixed.sql`** — 8127 lines, 2.02 MB, 130 tables with proper MySQL types (CHAR(26) ULIDs, TINYINT(1) booleans, JSON, TIMESTAMP, AUTO_INCREMENT PKs)
- **Generated `database/validation_report.md`** — comprehensive schema validation covering all 130 tables, FKs, indexes, data types
- **Generated `database/schema_dependency_graph.md`** — FK dependency chain analysis with 8 depth levels

### Completed
- (none)

### Blocked
- (none)

## Key Decisions
- **Form fields stored in dedicated table** (`chatbot_form_fields`) rather than as JSON in settings
- **`form_data` stored as JSON on `chatbot_leads`** — legacy `name`, `email`, `phone`, `admission_class` columns still populated from matching `field_key` values
- **Widget falls back to hardcoded form** when no dynamic fields configured or API unavailable
- **Always show pre-chat form** — `showScreen()` no longer checks localStorage
- **`QrCode` replaced with Google Charts API** — zero-dependency solution
- **`post-testimonial` migrated to CMS page builder** — content in `TestimonialPageWidget` + Blade component; route via catch-all `PageController`
- **`PageRenderer::renderContext`** — context (page_id, slug, title, url, seo) stored as class property and passed through rendering chain, so widgets like `BreadcrumbWidget` can auto-detect the current page without duplicating DB queries
- **`array_merge($this->defaultSettings(), $settings)`** in widget `render()` — ensures all default keys are present before use, preventing bugs when settings are saved partially
- **Laravel migrations = source of truth** for database schema — SQL dump must be regenerated from migrations if conflicts exist
- **`database/deploy.sql` generated programmatically** using PRAGMA-based column info from SQLite (not manual SQL) to ensure accuracy
- **SQL dump should target one-click import** on Hostinger/phpMyAdmin/MySQL CLI with zero errors
- **Entire migration set rewritten** — old files (2025_06_19_* etc.) no longer exist; replaced with clean 2025_01_*/2026_* structure across 65 files (~130+ tables)

## Next Steps
1. Diagnose `/post-testimonial` rendering performance (10s test time) — compare benchmark (0.89s page render) vs test overhead (RefreshDatabase + PageTreeService::sync)

## Critical Context
- **185+ tests pass** — all tests green in test environment
- **Settings cache** cleared via `php artisan cache:forget chatbot:settings`
- **CSRF excluded** for `chatbot/widget/*` in `bootstrap/app.php` line 31
- **CORS middleware** allows all origins, methods, and common headers — applied to chatbot widget routes
- **Admin sidebar** fully populated with links for API Tokens, 2FA, Campaigns, Webhooks, Form Builder, Analytics, Funnel Analytics
- **65 migration files** define the intended schema — must be used as source of truth for `deploy_fixed.sql`
- **SQLite database** at `database/database.sqlite` (4.3 MB) contains all seed data to be extracted
- **Project uses ULID primary keys** (CHAR 26) for most user-facing tables, BIGINT AUTO_INCREMENT for system tables (cache, jobs, academic calendar)
- **Breadcrumb widget** — tested and passes all 17 tests including width_style box
- **Migration set restructured** — fully rewritten with clean, well-organized timestamps (2025_01_* through 2026_07_15)

## Relevant Files
- `database/migrations/` — 65 migration files defining the complete schema (SOURCE OF TRUTH)
- `database/deploy.sql` — currently generated MySQL dump (1863 KB, 129 tables) — TO BE REPLACED
- `database/deploy.sh` — deployment shell script for Hostinger terminal
- `ANCHORED_SUMMARY.md` — this file
