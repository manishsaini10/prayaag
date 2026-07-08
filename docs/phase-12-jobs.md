# Phase 12 — Job Listings & Applications

A content module (listings) combined with public submission (applications) and
the media library (résumé uploads).

## Naming note
The table is `job_listings`, NOT `jobs` — Laravel's queue already owns the
`jobs` table via its default migration, so a second one would fail. This also
matches the blueprint's "Job Listings" module name.

## Tables
- job_listings (tenant): title, slug, department, location, employment_type
  (full_time|part_time|contract), description, status (open|closed), closes_at.
- job_applications (tenant): job_listing_id, name, email, phone, cover_letter,
  resume_media_id (-> media, nullOnDelete), status (new|reviewing|rejected|
  hired), meta.

## Models & scopes
- JobListing: applications(), scopeOpen() (status=open AND closes_at null or
  >= now).
- JobApplication: jobListing(), resume() (-> Media), scopeForStatus().

## Apply flow
- POST /jobs/apply -> JobApplicationController@store (web group).
  - Honeypot + throttle:10,1.
  - Target listing resolved via JobListing::open()->findOrFail() — a closed,
    expired, or cross-tenant id returns 404.
  - Optional résumé (pdf/doc/docx, <=5 MB) stored through MediaManager; the
    media id is linked on the application.

## Widget
- job_listings (category: content) — dynamic; lists open listings with
  department/location, bound to the current tenant.

## Privacy note
Résumés are currently stored on the `public` disk (same as other media). For
production, applicant documents should live on a private disk with signed-URL
access — a one-line disk change in MediaManager plus a download route. Flagged,
not yet implemented.

## Seeding
Phase12Seeder adds two open listings. DatabaseSeeder now runs Phases 2-6, 10,
11, 12.

## Gate (Phase 12 exit criteria)
- open scope excludes closed and expired listings
- a public application stores and links its listing + résumé, under the tenant
- applying to a closed/cross-tenant listing 404s
- honeypot drops bots
- applications are isolated per tenant
- the job_listings widget renders open listings
