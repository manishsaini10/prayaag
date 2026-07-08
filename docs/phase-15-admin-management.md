# Phase 15 — Admin Management Layer

The read/management side for everything the public site captures. Closes the
gap between "data is being collected" and "an admin can actually see it."

## Shared shell
- resources/views/admin/layout.blade.php — sidebar nav (Dashboard, Pages,
  Enquiries, Applications, Subscribers, Analytics) + log out. Management views
  extend it. (The existing dashboard view is left as-is.)

## Inboxes (InboxController, all tenant-scoped, auth-gated)
- /admin/enquiries — received contact/admission/enquiry submissions with type,
  name, email (mailto), message excerpt, status.
- /admin/applications — job applications with applicant, position (joined from
  the listing), résumé link, status.
- /admin/subscribers — newsletter list with email, name, source, status.

All read the models directly; the tenant global scope keeps each tenant to its
own data.

## Analytics (AnalyticsController)
- /admin/analytics — total views, last-7-day views, and top 25 pages by path,
  aggregated from page_views (filtered by tenant explicitly, since PageView is a
  plain append-only model). First-party only, no third-party trackers.

## Deliberately read-only (next steps)
Status changes (mark enquiry read, advance an application, unsubscribe) are
writes left for a follow-up — the views expose the data; mutations are a small
addition on top. Résumé links use the media path directly; a signed-URL
download route pairs with moving résumés to a private disk (flagged in Phase 12).

## Gate (exit criteria)
- inboxes require authentication
- the enquiries inbox is tenant-scoped (no cross-tenant leakage)
- applications list shows the joined position
- analytics summarizes views and renders top paths
