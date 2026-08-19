# Prayaag CMS — Production Deployment & Database Migration Guide

This guide documents the **25-step automated, production-safe deployment system** for the Prayaag School CMS running on Hostinger Shared Hosting (PHP 8.3 / Laravel 12).

---

## 🗄️ 1. Database & Automatic Migration Policy

Every deployment automatically executes:
```bash
php artisan migrate --force
```

### 🛑 Production Database Safety Rules:
1. **NEVER Run Destructive Commands:**
   The deployment engine will **never** run:
   * `php artisan migrate:fresh`
   * `php artisan migrate:refresh`
   * `php artisan db:wipe`
   * `php artisan migrate:reset`
2. **Idempotent Migration Execution:**
   Laravel's `migrations` table is used to track applied migrations. If there are 5 new migrations, only the pending 5 will run in exact chronological order. If none are pending, it continues smoothly.
3. **Migration Failure Safety:**
   If `php artisan migrate --force` fails:
   * Deployment halts immediately.
   * Production cache rebuilding is aborted.
   * Full error details are recorded in `storage/logs/deployment.log`.
   * The database state is preserved without destructive rollbacks.
4. **Pre-Migration Database Snapshot:**
   Before running any pending migrations, a timestamped SQL snapshot is safely saved to:
   `storage/backups/database/db-backup-YYYYMMDD_HHMMSS.sql`
   *(This directory is located outside `public_html` and is strictly excluded by `.gitignore`).*

---

## 📜 2. Best Practice: Creating Future Migrations

Once a migration is deployed to production:
> ⚠️ **NEVER modify or edit an existing migration file that has already executed in production.**

To make future database schema changes (e.g. adding a column or modifying a table), always create a **NEW** migration file:

```bash
# Example: Adding a column to popups table
php artisan make:migration add_target_url_to_popups_table --table=popups
```

Then commit and push the new migration file to Git:
```bash
git add database/migrations/*_add_target_url_to_popups_table.php
git commit -m "feat: add target_url to popups"
git push origin main
```

When deployed on the server, `deploy.sh` will automatically detect the new migration and run only that specific migration safely!

---

## 🧭 3. Dynamic Path Portability & `.deploy-config`

The deployment system is **100% path-portable** with zero hardcoded project root paths.

### A. Dynamic Project Root:
```bash
PROJECT_ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
```
All paths (`artisan`, `public/`, `storage/`, `bootstrap/cache/`) are derived relative to this root.

### B. Configurable Web Root ([`.deploy-config`](file:///f:/prayaag-laravel/prayaag/.deploy-config)):
```ini
# .deploy-config
WEB_ROOT="/home/u919095325/domains/lightgray-buffalo-350334.hostingersite.com/public_html"
```
If your public web directory changes in the future, **only update `WEB_ROOT` in `.deploy-config`**.

---

## ⚡ 4. The Exact 25-Step Deployment Pipeline

| Step | Action | Description |
|---|---|---|
| **1/25** | **Git Pull** | Pulls latest commits from `origin main`. |
| **2/25** | **Detect Project Root** | Dynamically resolves repository root via `git rev-parse --show-toplevel`. |
| **3/25** | **Load Configuration** | Loads `WEB_ROOT` from `.deploy-config`. |
| **4/25** | **Validate Laravel** | Verifies `artisan`, `composer.json`, and `.env`. |
| **5/25** | **Validate Environment** | Verifies PHP 8.3 binary, Composer, and `WEB_ROOT` writability. |
| **6/25** | **Validate Vite Manifest** | Checks `public/build/manifest.json`. |
| **7/25** | **Create Directories** | Creates `bootstrap/cache`, `storage/framework/*`, `storage/logs`, `storage/backups/database`. |
| **8/25** | **Fix Safe Permissions** | Enforces `ug+rwX` / `775` permissions on storage and bootstrap/cache. |
| **9/25** | **Composer Install** | Runs `composer install --no-dev --optimize-autoloader`. |
| **10/25** | **Composer Autoload** | Runs `composer dump-autoload -o`. |
| **11/25** | **Verify DB Connection** | Confirms active MySQL PDO connection. |
| **12/25** | **Pre-Migration Status** | Displays `php artisan migrate:status` before migrating. |
| **13/25** | **Database Backup** | Creates pre-migration database snapshot in `storage/backups/database/`. |
| **14/25** | **Run Migrations** | Executes `php artisan migrate --force`. |
| **15/25** | **Post-Migration Status** | Verifies all migrations are marked as "Ran". |
| **16/25** | **Sync Public Files** | Copies `css/`, `js/`, `images/`, `site.css`, `admin.css`, `robots.txt` to `WEB_ROOT`. |
| **17/25** | **Sync Vite Build** | Copies `public/build/` to `WEB_ROOT/build/`. |
| **18/25** | **Verify Manifest Assets** | Confirms all compiled assets referenced in manifest exist. |
| **19/25** | **Storage Link** | Executes `php artisan storage:link` and verifies symlink in `WEB_ROOT/storage`. |
| **20/25** | **Clear Caches** | Clears old bootstrap, route, view, and config caches (`optimize:clear`). |
| **21/25** | **Cache Config** | Generates production `config:cache`. |
| **22/25** | **Cache Routes** | Generates production `route:cache`. |
| **23/25** | **Cache Views** | Precompiles Blade templates (`view:cache`). |
| **24/25** | **Health Check** | Executes `php artisan about` and validates directory writability. |
| **25/25** | **Audit Log & Summary** | Writes success record to `storage/logs/deployment.log` and displays summary. |

---

## 🪵 5. Deployment Audit Logs & Error Tracking

All deployment events and any migration errors are logged with timestamps in:
```bash
tail -n 50 storage/logs/deployment.log
```

---

## 🔁 6. Routine Deployment Commands

### Via SSH:
```bash
cd /home/u919095325/prayaag
./deploy.sh
```

### Automatic Post-Merge Hook (1-Time Setup):
```bash
cp .git-hooks/post-merge .git/hooks/post-merge
chmod +x .git/hooks/post-merge
```
*(Now running `git pull origin main` automatically executes the full 25-step deployment!)*
