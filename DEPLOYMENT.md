# Prayaag CMS — Production Deployment Guide (Hostinger Shared Hosting)

This guide documents the automated, production-safe deployment system for the Prayaag School CMS running on Hostinger Shared Hosting (PHP 8.3 / Laravel 12).

---

## 🏗️ 1. Hostinger Architecture & Directory Layout

On Hostinger shared hosting, the Laravel application core is stored safely **outside** the public web directory, protecting your `.env`, database configuration, and application code:

```
/home/u919095325/
├── prayaag/                                                          # 🔒 Core Laravel Root
│   ├── app/
│   ├── bootstrap/
│   │   ├── app.php
│   │   └── cache/                                                    # (Auto-repaired: 775)
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── storage/                                                      # (Auto-repaired: 775)
│   │   ├── app/public/
│   │   ├── backups/updates/                                          # (Pre-deploy safety snapshots)
│   │   ├── framework/{cache,sessions,views}
│   │   └── logs/
│   ├── vendor/
│   ├── .env                                                          # (Preserved - Never overwritten)
│   ├── artisan
│   ├── composer.json
│   └── deploy.sh                                                     # ⚡ Production Deployment Engine
│
└── domains/lightgray-buffalo-350334.hostingersite.com/
    └── public_html/                                                  # 🌐 Public Web Root
        ├── index.php                                                 # (Bootstraps /home/u919095325/prayaag)
        ├── .htaccess
        ├── build/                                                    # (Synced Vite CSS/JS assets)
        │   ├── assets/
        │   └── manifest.json
        ├── site.css
        ├── admin.css
        └── storage -> /home/u919095325/prayaag/storage/app/public     # (Symlink)
```

---

## ⚡ 2. Normal Routine Deployment (Single Command)

Whenever you make changes, push them to GitHub from your local machine:
```bash
# On your local computer:
npm run build          # If frontend CSS/JS changed
git add .
git commit -m "feat: your new feature"
git push origin main
```

Then on the Hostinger server via SSH, run the deployment script:
```bash
cd /home/u919095325/prayaag
./deploy.sh
```

---

## 🛠️ 3. First-Time Server Setup (1-Time Setup)

If setting up a fresh server or new domain on Hostinger:

### Step 1: Clone Repository
```bash
cd /home/u919095325
git clone https://github.com/manishsaini10/prayaag.git prayaag
cd /home/u919095325/prayaag
```

### Step 2: Configure Environment (`.env`)
```bash
cp .env.example .env
nano .env
```
*Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://lightgray-buffalo-350334.hostingersite.com`, and your Hostinger MySQL database credentials.*

### Step 3: Configure `public_html/index.php`
Ensure `/home/u919095325/domains/lightgray-buffalo-350334.hostingersite.com/public_html/index.php` points to the real application root:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Maintenance mode check
if (file_exists($maintenance = '/home/u919095325/prayaag/storage/framework/maintenance.php')) {
    require $maintenance;
}

// 2. Composer Autoloader
require '/home/u919095325/prayaag/vendor/autoload.php';

// 3. Bootstrap Laravel Application
/** @var Application $app */
$app = require_once '/home/u919095325/prayaag/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

### Step 4: Make `deploy.sh` Executable & Run Initial Deployment
```bash
chmod +x /home/u919095325/prayaag/deploy.sh
/home/u919095325/prayaag/deploy.sh
```

---

## 🔍 4. What `deploy.sh` Does (10-Step Breakdown)

| Step | Action | Description |
|---|---|---|
| **[1/10]** | **Environment Check** | Verifies `artisan`, `composer.json`, `.env`, PHP 8.3 binary, Composer, and targets. |
| **[2/10]** | **Git Pull & Pre-Backup** | Checks for uncommitted changes, takes a timestamped safety backup of `.env` & configs, and pulls `origin main`. |
| **[3/10]** | **Directory & Permissions** | Ensures `bootstrap/cache` and all `storage/framework/*` subdirectories exist and applies safe `ug+rwX` / `775` permissions. |
| **[4/10]** | **Composer Install** | Executes `composer install --no-dev --optimize-autoloader` and dumps optimized autoload classmap. |
| **[5/10]** | **Safe Migrations** | Runs `php artisan migrate --force` (never uses destructive `--fresh` or `db:wipe`). |
| **[6/10]** | **Clear Old Caches** | Flushes previous config, route, and view caches (`optimize:clear`). |
| **[7/10]** | **Rebuild Caches** | Generates production `config:cache`, `view:cache`, and `route:cache`. |
| **[8/10]** | **Vite & Public Sync** | Validates `manifest.json` and syncs compiled assets (`build/`, `css/`, `js/`, `images/`, `site.css`, `admin.css`) to `public_html`. |
| **[9/10]** | **Health Verification** | Executes `php artisan about` and tests directory read/write access. |
| **[10/10]** | **Summary Banner** | Displays clean green deployment summary report with commit hash and status. |

---

## 🛡️ 5. Fail-Safe Behavior & Error Handling

* **`set -eo pipefail` + `trap ERR`**: If any command fails (e.g. migration error, syntax issue), the script stops execution immediately.
* **Non-Destructive**: It **never** runs `git reset --hard`, `git clean -fd`, `migrate:fresh`, or deletes user uploads.
* **Pre-Deploy Snapshot**: Before touching git, a timestamped snapshot is archived to:
  `storage/backups/updates/pre-deploy-YYYYMMDD_HHMMSS.tar.gz`

---

## ↩️ 6. How to Rollback

### Method A: Via Web Admin Panel
Go to **[https://lightgray-buffalo-350334.hostingersite.com/admin/updates](https://lightgray-buffalo-350334.hostingersite.com/admin/updates)** and click **↩ Rollback** next to any previous version.

### Method B: Via SSH Command Line
```bash
cd /home/u919095325/prayaag
# Restore specific pre-deploy snapshot
tar -xzf storage/backups/updates/pre-deploy-YYYYMMDD_HHMMSS.tar.gz
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

---

## 🪵 7. How to Check Laravel Logs

To inspect live production errors:
```bash
tail -n 100 /home/u919095325/prayaag/storage/logs/laravel.log
```

Or view recent errors formatted in terminal:
```bash
cd /home/u919095325/prayaag
php artisan pail
```

---

## 🎨 8. Local Vite Frontend Builds (No Node on Server)

Because Hostinger shared hosting accounts do not have Node.js/npm installed:
1. Always build frontend assets on your local machine:
   ```bash
   npm run build
   ```
2. Commit the generated `public/build` directory to Git:
   ```bash
   git add public/build
   git commit -m "build: compile frontend assets"
   git push origin main
   ```
3. When `deploy.sh` runs on the server, it automatically syncs `public/build` to `public_html/build` without needing Node on Hostinger!
