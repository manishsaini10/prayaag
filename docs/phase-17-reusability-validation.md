# Phase 17 — Reusability Validation

Goal: prove the platform is a genuine engine, not a school site in disguise —
that a completely different vertical runs on it with **zero code changes**.

## What was added — and what wasn't
Added: one data-only seeder (`RestaurantDemoSeeder`) and one automated test
(`ReusabilityValidationTest`).

NOT added (the whole point): no migrations, no models, no controllers, no
routes, no widgets, no Blade changes. The restaurant is built entirely by
inserting rows through the same APIs the school uses.

## The second vertical: "Bella Cucina" (restaurant)
Same engine, different site, via existing pieces only:

| Engine capability        | School uses it for      | Restaurant uses it for          |
|--------------------------|-------------------------|---------------------------------|
| Tenant + domain          | school.test/localhost   | bella.test                      |
| Settings engine          | blue theme              | deep-red theme, serif font      |
| Page builder + widgets   | home/about/contact      | home/menu/gallery/about/reserve |
| `slider` widget          | (available)             | homepage hero (food)            |
| `testimonials` widget    | parent quotes           | diner reviews                   |
| `upcoming_events` widget | school events           | wine tasting, jazz brunch       |
| `gallery` widget         | campus life             | "Our Dishes"                    |
| `downloads` widget       | prospectus/fees         | dinner menu PDF                 |
| `contact_form` widget    | admissions enquiries    | table reservations              |
| Posts / categories       | news                    | seasonal specials               |
| Notices                  | term notices            | "Closed on Mondays"             |
| Menu builder             | school nav              | restaurant nav                  |

If a school and a restaurant both fall out of the same code with only data
changing, the architecture's central claim holds.

## How it's validated
1. **Automated (`ReusabilityValidationTest`)** — builds two tenants (school +
   restaurant) with their own domains and their own data behind an identical
   widget set, then drives real HTTP requests per host and asserts:
   - each host renders only its own tenant's content,
   - the shared widgets (events, testimonials, contact form) bind to the
     correct tenant's data,
   - an unknown host resolves no tenant (404) — fail-closed isolation.
   This runs as part of `php artisan test`.
2. **Manual** — point `bella.test` at `127.0.0.1` in your hosts file, run
   `php artisan migrate:fresh --seed` (now seeds both verticals) and
   `php artisan serve`, then open the school host and `bella.test` and confirm
   two visibly different, fully working sites off one codebase + one database.

## Pass criteria
- `ReusabilityValidationTest` is green.
- Both sites render and are visibly distinct, with correct per-tenant content.
- Building the restaurant required no source changes — only data.

## Honest status
The seeder and test are written; the automated half is confirmed the moment
`php artisan test` runs green. The manual "view two sites" step is the operator
sign-off (needs the app served + a hosts entry) — by design, since Phase 17 is
fundamentally a running exercise.
