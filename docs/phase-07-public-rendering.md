# Phase 7 — Public Rendering & Routing

This phase makes the stack visible in a browser. No new tables — it's the
front-end serving layer over Phases 1–6.

## Flow
1. ResolveTenant middleware maps the request host to a tenant (via
   tenant_domains).
2. PageController resolves a published Page by slug within that tenant
   (404 if no tenant resolved for the host).
3. PageRenderer turns the page's section/row/column/widget tree into HTML.
4. ThemeRenderer builds the header (site name from Settings + primary menu
   from the Menu Builder) and footer.
5. The thin cms.layout Blade view echoes header + content + footer and injects
   theme settings (primary color, font) as CSS variables. No logic in Blade.

## Files
- app/Core/Theme/ThemeRenderer.php — dynamic header/footer (singleton).
- app/Http/Controllers/Cms/PageController.php — home() and show($slug).
- resources/views/cms/layout.blade.php — thin renderer + base styles.
- routes/web.php — `/` -> home, `/{slug}` -> show (excludes /up).

## Try it
    php artisan migrate:fresh --seed
    php artisan serve
    # open http://127.0.0.1:8000  (seeded tenant is bound to 127.0.0.1)
You should see the seeded Home page: "Welcome to Demo School", the intro text,
the Primary menu in the header, and the footer.

## Gate (Phase 7 exit criteria)
- a published page renders end-to-end over HTTP (header + widgets + footer)
- an unknown host (no tenant) returns 404 — tenant isolation holds at the edge
