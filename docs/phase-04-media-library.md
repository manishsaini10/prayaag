# Phase 4 — Media Library

## Tables
- media_folders: tenant-scoped folder tree. id ulid, tenant_id (FK),
  parent_id (self FK, nullable), name, slug, path (slash-joined slugs),
  timestamps, soft deletes. Unique per (tenant_id, parent_id, slug).
- media: tenant-scoped file records. id ulid, tenant_id (FK), folder_id
  (FK nullable), disk, path, filename, original_name, mime_type, extension,
  size, width, height, alt, title, timestamps, soft deletes.

## Engine — MediaManager (app(MediaManager::class))
- store(UploadedFile, ?MediaFolder): saves the binary to the `public` disk
  under media/{tenantId}/{folderPath}/{ulid}.{ext}, extracts image
  dimensions, and creates the metadata row (tenant_id auto-stamped).
- url(Media), delete(Media): standard helpers over the storage disk.
- toWebp(Media): optional WebP conversion using native GD (imagewebp).
  Returns null if GD is unavailable, so it degrades gracefully — no extra
  Composer package required. Add intervention/image later for resizing.

## Model features
- Media::search($term) and Media::ofType('image') query scopes for the
  library's search and filters.
- MediaFolder maintains `path` on create and exposes parent/children/media.

## Serving files
Media uses the `public` disk. Run once so files are web-accessible:
    php artisan storage:link

## Seeding
Phase4Seeder creates Images / Documents / Galleries root folders for the
Demo School tenant. DatabaseSeeder now runs Phase2 → Phase3 → Phase4.

## Gate (Phase 4 exit criteria)
- migrations run clean (media_folders + media)
- storing an image creates a record with correct dimensions and stored file
- media is isolated per tenant
- (optional) toWebp produces a webp when GD is present
