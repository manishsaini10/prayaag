# 🎓 Prayaag School CMS — Complete System Implementation Documentation

**Project:** Prayaag School Content Management System & ERP  
**Core Framework:** Laravel 12.x | PHP 8.3.x | MySQL 8.0+ / MariaDB 10.11+  
**Hosting Environment:** Hostinger Shared Hosting (Linux)  
**Live Production URL:** `https://lightgray-buffalo-350334.hostingersite.com`  
**GitHub Repository:** `https://github.com/manishsaini10/prayaag.git` (`main` branch)  
**Last Updated:** August 19, 2026

---

## 📑 Table of Contents

1. [Executive Architecture Overview](#1-executive-architecture-overview)
2. [Upload Center & Media File Management](#2-upload-center--media-file-management)
3. [CMS Auto-Updater, Version Tracking & Rollback Engine](#3-cms-auto-updater-version-tracking--rollback-engine)
4. [Production-Safe 25-Step Linux Deployment System (`deploy.sh`)](#4-production-safe-25-step-linux-deployment-system-deploysh)
5. [Database Architecture & Migration Safety](#5-database-architecture--migration-safety)
6. [Email Infrastructure & Multi-Provider Engine](#6-email-infrastructure--multi-provider-engine)
7. [AI Chatbot, Live Chat & CRM Integration](#7-ai-chatbot-live-chat--crm-integration)
8. [Video Testimonials Suite & Widget Builder](#8-video-testimonials-suite--widget-builder)
9. [Admissions, Leads & Form Automation](#9-admissions-leads--form-automation)
10. [Security, Bot Protection & GDPR Compliance](#10-security-bot-protection--gdpr-compliance)
11. [Performance Optimization & High-Speed Caching](#11-performance-optimization--high-speed-caching)
12. [Complete Inventory of Created Files & Endpoints](#12-complete-inventory-of-created-files--endpoints)

---

## 🏗️ 1. Executive Architecture Overview

The system is architected to operate efficiently on Hostinger shared hosting using a **split directory structure** to maximize security and prevent exposure of core application files:

```
/home/u919095325/
├── prayaag/                                                          # 🔒 Laravel Core (Outside Public Root)
│   ├── app/                                                          # Application logic, Models, Controllers
│   ├── bootstrap/
│   │   ├── app.php                                                   # Application bootstrap & middleware
│   │   └── cache/                                                    # Auto-managed compiled cache (775)
│   ├── config/
│   │   └── cms.php                                                   # Release versioning & build metadata
│   ├── database/migrations/                                          # Version-controlled migrations
│   ├── routes/                                                       # web.php, api.php, chatbot.php, console.php
│   ├── storage/                                                      # Framework storage, logs & snapshots
│   │   ├── backups/database/                                         # Automated SQL snapshots
│   │   ├── backups/updates/                                          # Pre-deploy .tar.gz / .zip snapshots
│   │   └── logs/deployment.log                                       # Audit trail of deployments
│   ├── .deploy-config                                                # Portable deployment configuration
│   ├── .git-hooks/post-merge                                         # Zero-touch Git deployment hook
│   └── deploy.sh                                                     # 25-Step deployment engine
│
└── domains/lightgray-buffalo-350334.hostingersite.com/
    └── public_html/                                                  # 🌐 Public Web Root (Only web assets)
        ├── index.php                                                 # Entry point pointing to /home/u919095325/prayaag
        ├── build/                                                    # Compiled Vite CSS/JS assets & manifest.json
        ├── site.css / admin.css                                      # Theme stylesheets
        └── storage -> /home/u919095325/prayaag/storage/app/public     # Symlink for public uploads
```

---

## 📂 2. Upload Center & Media File Management

**Location:** `http://127.0.0.1:8000/admin/upload` & `/admin/m/media`

### Implemented Features:
1. **Drag-and-Drop File Upload:** Direct multi-file upload zone supporting images, PDFs, docs up to 20 MB each.
2. **File Deletion Action:**
   * Hovering over any media tile reveals the **Copy Link** and **Trash (Delete)** buttons.
   * **Client Confirmation:** Interactive confirmation prompt (`onsubmit="return confirm('Are you sure you want to permanently delete this file?')"`) prevents accidental file loss.
   * **Physical & Database Purge:** Deletes the actual file from disk (`Storage::disk('public')`) and deletes the corresponding database record in `media` table.
3. **Responsive Visual Grid:** Displays file previews, MIME badges, dimensions, and human-readable file sizes (`B`, `KB`, `MB`).

---

## 🔄 3. CMS Auto-Updater, Version Tracking & Rollback Engine

**Location:** `http://127.0.0.1:8000/admin/updates`

### Implemented Features:
1. **Dynamic Version & Release Tracking (`config/cms.php`):**
   * Eliminates the `"Unknown / Git not initialized"` issue on servers without `.git`.
   * Automatically tracks `version` (e.g., `v1.3.1`), `build` (short commit hash), and `released_at` timestamp.
   * Automatically bumps patch versions on deployment (`1.3.1` ➔ `1.3.2`).
2. **Remote GitHub Release Detection:**
   * Periodically checks the GitHub API repository for newer commits on `main`.
   * When an update is available, an animated glowing badge **`⚡ New Update Available!`** pulses in the admin navigation header bar.
3. **Mandatory Pre-Update Dual Backup:**
   * Before updating, automatically bundles all core application files (`app/`, `config/`, `resources/`, `routes/`, `database/`) AND generates a complete MySQL `database_snapshot.sql` into `storage/backups/updates/backup-v{VERSION}-{TIMESTAMP}.zip`.
   * **Strict Safety Abort:** If the backup cannot be written to disk, update execution halts immediately.
4. **1-Click Rollback Action:**
   * Stored restore points are listed with a **`↩ Rollback`** button to instantly restore previous code and database state.
5. **Zero-Touch Webhook Deployment:**
   * Endpoint `POST /api/deploy/webhook?token=...` allows automatic deployment upon GitHub webhooks.

---

## ⚡ 4. Production-Safe 25-Step Linux Deployment System (`deploy.sh`)

**File:** [`deploy.sh`](file:///f:/prayaag-laravel/prayaag/deploy.sh) (Executable Mode `755`)  
**Config:** [`.deploy-config`](file:///f:/prayaag-laravel/prayaag/.deploy-config)

### The 25-Step Deployment Pipeline:

```
[Step 1/25]  git pull origin main
[Step 2/25]  Detect project root dynamically (git rev-parse --show-toplevel)
[Step 3/25]  Load deployment configuration (.deploy-config)
[Step 4/25]  Validate Laravel installation (artisan, composer.json, .env)
[Step 5/25]  Validate production environment & WEB_ROOT writability
[Step 6/25]  Validate public/build/manifest.json (Vite)
[Step 7/25]  Create required directories (bootstrap/cache, storage/framework/*, logs, backups)
[Step 8/25]  Fix safe permissions (ug+rwX / 775)
[Step 9/25]  composer install --no-dev --optimize-autoloader
[Step 10/25] composer dump-autoload -o
[Step 11/25] Verify active database connection (MySQL PDO test)
[Step 12/25] Show pre-migration status (php artisan migrate:status)
[Step 13/25] Create database backup snapshot (storage/backups/database/)
[Step 14/25] Run database migrations (php artisan migrate --force)
[Step 15/25] Verify post-migration status (php artisan migrate:status)
[Step 16/25] Synchronize public files (css, js, images, site.css, admin.css) to WEB_ROOT
[Step 17/25] Synchronize Vite build directory to WEB_ROOT/build
[Step 18/25] Verify all manifest assets exist on disk
[Step 19/25] Run php artisan storage:link & verify WEB_ROOT/storage
[Step 20/25] Run php artisan optimize:clear
[Step 21/25] Run php artisan config:cache
[Step 22/25] Run php artisan route:cache
[Step 23/25] Run php artisan view:cache
[Step 24/25] Final application verification (php artisan about)
[Step 25/25] Write deployment record to storage/logs/deployment.log & output summary report
```

### Path Portability:
* **Zero Hard-Coded Paths:** Moving the application root does not break deployment.
* **Single Configuration Point:** `WEB_ROOT` is read from `.deploy-config`.
* **Git Hook Support:** `.git-hooks/post-merge` enables automated execution on every `git pull`.

---

## 🗄️ 5. Database Architecture & Migration Safety

### Production Database Safety Principles:
1. **Never Run Destructive Commands:** `migrate:fresh`, `migrate:refresh`, `db:wipe`, and `migrate:reset` are strictly prohibited.
2. **Idempotency:** Migration state is driven solely by Laravel's `migrations` table.
3. **Migration Failure Trap:** If a migration fails, deployment halts, cache rebuilding is prevented, error details are saved in `storage/logs/deployment.log`, and the database is preserved intact.
4. **Hostinger / MariaDB Compatibility:**
   * Resolved Windows UTF-16 BOM export corruption.
   * Fixed `#1005 Foreign Key Constraint` errors with unconditional `SET FOREIGN_KEY_CHECKS = 0;`.
   * Standardized ULID primary keys for Spatie RBAC tables (`model_has_roles`, `model_has_permissions`).

---

## 📧 6. Email Infrastructure & Multi-Provider Engine

**Location:** `/admin/settings/email-providers` & `/admin/settings/email-templates`

### Implemented Features:
1. **Multi-Provider Failover Architecture:**
   * Native support for Hostinger / cPanel SMTP, Zoho Mail, Brevo (Sendinblue), and Custom SMTP.
   * Dynamic fallback priority chain (if Primary SMTP fails, auto-switches to Secondary).
2. **Dynamic Email Template Engine:**
   * Visual template editor with subject line customization.
   * 1-Click placeholder chips (`{name}`, `{email}`, `{job_title}`, `{reference_id}`).
   * Real-time Test Email Sender modal.
3. **Full Audit Logging (`/admin/email-logs`):**
   * Records every outbound email with recipient, template, status (`delivered`, `failed`), and error stack trace.
   * 1-Click Resend action for failed emails.
4. **Newsletter Module (`/admin/newsletter`):**
   * Double opt-in confirmation tokens.
   * Single-click unsubscribe token verification.
   * Bulk campaign dispatcher queued with throttling.

---

## 🤖 7. AI Chatbot, Live Chat & CRM Integration

**Location:** `/admin/chatbot` & `/chatbot/widget`

### Implemented Features:
1. **Embeddable Widget (`/chatbot/embed.js`):**
   * Cross-origin CORS-enabled runtime.
   * Real-time visitor identification and session tracking.
2. **Pre-Chat Form Builder (`/admin/chatbot/form-fields`):**
   * Drag-and-drop form field ordering.
   * Captures student/parent inquiries before chat initialization.
3. **Knowledge Base & Assistant (`/admin/chatbot/assistant`):**
   * Document upload for AI context.
   * Canned responses with shortcut suggestions.
4. **Enterprise Features:**
   * Departments & agent assignments.
   * Support tickets, CRM contacts & deals pipeline.
   * Webhook notifications & delivery logs.
   * Real-time analytics and sentiment reporting.

---

## 🎥 8. Video Testimonials Suite & Widget Builder

**Location:** `/admin/video-testimonials` & `/video-testimonials`

### Implemented Features:
1. **Public Submission Portal:**
   * Video upload with client-side validation & MIME type verification.
   * Student/Parent consent checkboxes.
2. **Admin Moderation Console:**
   * Pending approval queue, tag categorization, video preview modal.
3. **Multi-Layout Frontend Widgets:**
   * Carousel slider, Masonry wall, Story bubble avatars, Video spotlight modal, and Reel sliders.
4. **Analytics & View Tracking:**
   * Play counter, completion percentage, and impression logs.

---

## 📝 9. Admissions, Leads & Form Automation

**Location:** `/admin/leads`, `/admin/enquiries`, `/admin/forms`

### Implemented Features:
1. **Multi-Step Admission Forms:**
   * Student details, parent credentials, previous schooling, and document attachments.
2. **Admin Lead Management:**
   * Status pipeline (`new`, `contacted`, `enrolled`, `rejected`).
   * Lead PDF export with school branding.
   * Instant admin notification alerts via top header bell icon.

---

## 🛡️ 10. Security, Bot Protection & GDPR Compliance

### Implemented Features:
1. **Bot Protection:**
   * Honeypot input fields on public forms (`career`, `enquiries`, `admissions`).
   * Google reCAPTCHA v3 score-based validation.
2. **Rate Limiting:**
   * Strict IP-based throttles on public endpoints (`5 requests/minute`, `20 requests/day`).
3. **Two-Factor Authentication (2FA):**
   * Google Authenticator / TOTP QR code setup under `/2fa/setup`.
4. **Data Retention & Privacy (`config/privacy.php`):**
   * Automatic pseudonymization and purging of applicant personal data after 24 months.
5. **Malware Protection:**
   * Binary file signature inspection (`MimeVerifier`) preventing spoofed file extensions.

---

## 🚀 11. Performance Optimization & High-Speed Caching

### Implemented Features:
1. **Smart Cache Layer:**
   * Multi-level caching for Mess Menus, Featured Testimonials, Open Job Listings, and Dynamic Menus.
   * Automated cache invalidation on Model `saved` and `deleted` events.
2. **Asset Optimization:**
   * Self-contained Vite asset pipeline with fallback to standalone `site.css` and `admin.css`.
   * Zero external uncompiled CSS dependency issues.

---

## 📋 12. Complete Inventory of Created Files & Endpoints

### Key Deployment & Updater Files:
* [`deploy.sh`](file:///f:/prayaag-laravel/prayaag/deploy.sh) — 25-Step production deployment engine.
* [`.deploy-config`](file:///f:/prayaag-laravel/prayaag/.deploy-config) — Portable Web Root path definition.
* [`.deploy-config.example`](file:///f:/prayaag-laravel/prayaag/.deploy-config.example) — Template configuration.
* [`.git-hooks/post-merge`](file:///f:/prayaag-laravel/prayaag/.git-hooks/post-merge) — Automated Git pull hook.
* [`DEPLOYMENT.md`](file:///f:/prayaag-laravel/prayaag/DEPLOYMENT.md) — Comprehensive deployment operations manual.
* [`app/Core/Updater/AutoDeployerService.php`](file:///f:/prayaag-laravel/prayaag/app/Core/Updater/AutoDeployerService.php) — Dynamic path detector, GitHub commit inspector & deployer.
* [`app/Core/Updater/UpdateManager.php`](file:///f:/prayaag-laravel/prayaag/app/Core/Updater/UpdateManager.php) — ZIP package validator, backup generator & rollback engine.
* [`app/Http/Controllers/Admin/UpdateController.php`](file:///f:/prayaag-laravel/prayaag/app/Http/Controllers/Admin/UpdateController.php) — Controller for `/admin/updates`.
* [`resources/views/admin/updates/index.blade.php`](file:///f:/prayaag-laravel/prayaag/resources/views/admin/updates/index.blade.php) — Updates dashboard & deployment console.

### Key Media & Controller Files:
* [`app/Http/Controllers/Admin/UploadController.php`](file:///f:/prayaag-laravel/prayaag/app/Http/Controllers/Admin/UploadController.php) — Upload store & `destroy()` method.
* [`resources/views/admin/upload/index.blade.php`](file:///f:/prayaag-laravel/prayaag/resources/views/admin/upload/index.blade.php) — Upload Center UI with delete confirmation overlay.

### Key Route Endpoints:
* `GET  /admin/updates` — Updates & Deployment Dashboard
* `POST /admin/updates/git-pull` — 1-Click Backup & Git Auto-Deploy
* `POST /admin/updates/backup` — Create manual full snapshot
* `POST /admin/updates/rollback/{id}` — 1-Click Rollback
* `POST /api/deploy/webhook` — Zero-touch GitHub webhook receiver
* `GET  /admin/upload` — Upload Center UI
* `POST /admin/upload` — Store uploaded media
* `DELETE /admin/upload/{id}` — Delete media file and database record

---

*Documentation compiled and verified. All systems operational and committed to Git.*
