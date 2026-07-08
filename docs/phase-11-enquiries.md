# Phase 11 — Enquiries & Contact Capture

Consolidates the blueprint's leads / enquiries / contacts into one typed store
and adds public submission + a drop-in form widget.

## Table
- enquiries (tenant): type (contact|admission|enquiry), name, email, phone,
  subject, message, source (originating page), status (new|read|archived),
  meta (json). Soft-deletable.

## Submission flow
- POST /enquiries -> EnquiryController@store, on the web group.
  - Tenant resolved from host; row created in that tenant's scope.
  - Honeypot: a hidden `website` field; if filled, the request is silently
    dropped (looks successful to the bot).
  - Route throttle: 10/min per client.
  - On success: back() with a flashed `enquiry_sent` so the form shows a notice.

## Widget
- contact_form (category: forms) renders a CSRF-protected form posting to
  /enquiries, with a honeypot and the current page as `source`. Token/session
  reads are guarded with rescue() so the widget is safe to render anywhere
  (including direct tests). Settings: heading, button, success, type.

## Seeding
Phase11Seeder builds a published /contact page containing a heading + the
contact_form widget, so the capture flow works end-to-end immediately.
DatabaseSeeder now runs Phases 2-6, 10, 11.

## Deferred
The admin inbox UI for reading/triaging enquiries is part of the admin app
(alongside the visual editor); the model exposes ofType() and unread() scopes
ready for it.

## Gate (Phase 11 exit criteria)
- a public submission is stored under the right tenant
- the honeypot drops bots
- validation requires email + message
- enquiries are isolated per tenant
- the contact_form widget renders a valid form
