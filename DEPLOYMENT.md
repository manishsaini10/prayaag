# Prayaag CMS — Portable Production Deployment Guide (Hostinger / Linux)

This guide documents the **portable, automated production deployment system** for the Prayaag School CMS.

---

## 🧭 1. Dynamic Path Portability & `.deploy-config`

The deployment system is **100% path-portable** and contains **no hard-coded project directory paths**.

### How Dynamic Path Resolution Works:
1. **Dynamic Project Root Detection:**
   ```bash
   PROJECT_ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
   ```
   All core paths are derived relative to this detected root:
   * `ARTISAN="$PROJECT_ROOT/artisan"`
   * `PUBLIC_DIR="$PROJECT_ROOT/public"`
   * `BUILD_DIR="$PUBLIC_DIR/build"`
   * `STORAGE_DIR="$PROJECT_ROOT/storage"`
   * `BOOTSTRAP_CACHE="$PROJECT_ROOT/bootstrap/cache"`

2. **Configurable Web Root via `.deploy-config`:**
   The public web directory (`public_html`) is configured in a single file at the root:
   ```ini
   # .deploy-config
   WEB_ROOT="/home/u919095325/domains/lightgray-buffalo-350334.hostingersite.com/public_html"
   ```
   * If your domain or folder structure changes in the future, you **only** need to edit `WEB_ROOT` in `.deploy-config`. No other script or hook needs to be modified.

3. **Strict Path Validation:**
   Before copying any files or applying updates, `deploy.sh` verifies:
   * `PROJECT_ROOT/artisan` exists.
   * `WEB_ROOT` exists and is writable.
   * `PROJECT_ROOT/public/build/manifest.json` exists.
   * If `WEB_ROOT` does not exist, the deployment stops safely with an error to prevent copying files to an incorrect location.

---

## ⚡ 2. Routine Deployment Workflow

### Option A: Run `deploy.sh` directly
From anywhere inside your project directory on the server:
```bash
./deploy.sh
```

### Option B: Automatic Deployment via Git Post-Merge Hook
To automatically trigger `deploy.sh` whenever you run `git pull origin main`, install the portable Git hook once on the server:
```bash
cp .git-hooks/post-merge .git/hooks/post-merge
chmod +x .git/hooks/post-merge
```
After this 1-time step, simply running `git pull origin main` will automatically execute all deployment tasks!

---

## 🛠️ 3. First-Time Server Setup (1-Time Setup)

```bash
# 1. Clone the repository into any directory of your choice
cd /home/u919095325
git clone https://github.com/manishsaini10/prayaag.git prayaag
cd prayaag

# 2. Configure .env
cp .env.example .env
nano .env

# 3. Configure .deploy-config (set your public_html path)
cp .deploy-config.example .deploy-config
nano .deploy-config

# 4. Make deploy.sh executable and run initial deployment
chmod +x deploy.sh
./deploy.sh

# 5. (Optional) Enable automatic post-merge deployment hook
cp .git-hooks/post-merge .git/hooks/post-merge
chmod +x .git/hooks/post-merge
```

---

## 🌐 4. Hostinger `public_html/index.php` Setup

Ensure `public_html/index.php` references the Laravel application root (e.g. `/home/u919095325/prayaag`):

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

---

## 🔍 5. What `deploy.sh` Does (10-Step Execution)

| Step | Action | Description |
|---|---|---|
| **[1/10]** | **Environment & Path Validation** | Detects `$PROJECT_ROOT`, validates `$ARTISAN`, loads `.deploy-config`, and checks `$WEB_ROOT` existence and writability. |
| **[2/10]** | **Git Pull & Pre-Backup** | Checks uncommitted changes, saves pre-deploy snapshot in `$STORAGE_DIR/backups/updates/`, and pulls `origin main`. |
| **[3/10]** | **Directory & Permissions** | Ensures `$BOOTSTRAP_CACHE` and all `$STORAGE_DIR/framework/*` directories exist with `ug+rwX` / `775` permissions. |
| **[4/10]** | **Composer** | Runs `composer install --no-dev --optimize-autoloader` and `composer dump-autoload -o`. |
| **[5/10]** | **Database Migrations** | Runs `php artisan migrate --force` safely without data deletion. |
| **[6/10]** | **Clear Caches** | Flushes old compiled views, configs, and routes (`optimize:clear`). |
| **[7/10]** | **Rebuild Caches** | Rebuilds `config:cache`, `view:cache`, and `route:cache`. |
| **[8/10]** | **Vite & Public Sync** | Validates `manifest.json` and syncs compiled assets (`build/`, `css/`, `js/`, `site.css`, `admin.css`) to `$WEB_ROOT`. |
| **[9/10]** | **Health Check** | Runs `php artisan about` and tests directory writability. |
| **[10/10]** | **Completion Banner** | Prints structured deployment report with commit hash and status. |

---

## 🛡️ 6. Fail-Safe Error Handling & Rollback

* **`set -eo pipefail` + `trap ERR`**: If any command fails (e.g. database error), deployment halts immediately.
* **Pre-Deploy Snapshot**: Every deployment automatically archives a snapshot to:
  `storage/backups/updates/pre-deploy-YYYYMMDD_HHMMSS.tar.gz`
* **To Rollback**:
  ```bash
  tar -xzf storage/backups/updates/pre-deploy-YYYYMMDD_HHMMSS.tar.gz
  php artisan optimize:clear
  php artisan config:cache
  php artisan view:cache
  ```

---

## 🎨 7. Local Vite Frontend Builds (No Node on Server)

Because Node.js/npm is not installed on Hostinger shared hosting:
1. Build assets locally:
   ```bash
   npm run build
   ```
2. Commit and push `public/build`:
   ```bash
   git add public/build
   git commit -m "build: compile frontend assets"
   git push origin main
   ```
3. `deploy.sh` automatically verifies `manifest.json` and syncs `public/build/` to `public_html/build/`.
