# Prayaag CMS — Production Transactional Deployment & Rollback System

**Core Framework:** Laravel 12.x | PHP 8.3 | Hostinger Shared Hosting  
**Architecture:** Transactional Zero-Downtime Deployment with Multi-Tier Health Verification & Auto-Rollback  

---

## 🛡️ 1. Transactional Update Principle

Treat deployment as a **single protected transaction**:

```
PREVIOUS WORKING VERSION
        ↓
1. CREATE VERIFIED RESTORE POINT (App + Public + .env + MySQL Dump + SHA-256 Checksums)
        ↓
2. APPLY GIT UPDATE & COMPOSER
        ↓
3. DATABASE MIGRATIONS (php artisan migrate --force)
        ↓
4. PUBLIC & VITE ASSETS SYNC
        ↓
5. REBUILD RUNTIME CACHES
        ↓
6. MULTI-TIER HEALTH CHECKS
        ↓
   ┌──────────────┴──────────────┐
   ▼                             ▼
[PASS]                        [FAIL]
COMMIT UPDATE &              TRIGGER AUTOMATIC ROLLBACK
RETAIN RESTORE POINT         ↳ Restore Files & Assets
                             ↳ Restore MySQL Snapshot if Migrated
                             ↳ Rebuild Caches
                             ↳ Health Check Restored Version
                             ↳ State: ROLLBACK SUCCESSFUL
```

> **Safety Rule:** The system **NEVER** deletes the only known-good restore point until the new deployment has passed all 6 health checks.

---

## 📦 2. Verified Restore Point Structure

Before any Git update begins, a structured restore point directory is created in:
`storage/backups/updates/backup-DEPLOY-YYYYMMDD-HHMMSS-xxxx/`

```
backup-DEPLOY-20260819-120000-a8f4/
├── metadata.json           # Commit hash, version, build, timestamp, paths
├── database.sql.gz         # Full compressed MySQL database snapshot
├── application.tar.gz      # app/, config/, database/, resources/, routes/, composer.json, composer.lock
├── public.tar.gz           # build/, css/, js/, images/, site.css, admin.css
├── env.backup              # Production .env configuration (non-git)
└── checksums.sha256        # SHA-256 checksums of all backup files
```

### Verification Step:
The deployer checks that all 5 archives exist, are non-empty, and their SHA-256 checksums match **before** running `git pull`. If verification fails, deployment aborts immediately.

---

## 🩺 3. Multi-Tier Health Check Engine (`DeploymentHealthChecker.php`)

After deployment, the system runs 6 separate health verifications:

| Check | Target | Verification Criteria |
|---|---|---|
| **1. Backend Boot** | Laravel Runtime | Verifies Laravel boots without fatal PHP errors, registers service providers, and detects PHP version. |
| **2. Database** | MySQL PDO | Tests connection, verifies critical tables exist (`users`, `settings`, `media`, `pages`), and confirms 0 pending migrations. |
| **3. Storage** | Filesystem | Verifies `bootstrap/cache`, `storage/framework/*`, and `storage/logs` exist and are writable (`775`). |
| **4. Cache** | Cache Driver | Tests cache write, read, and forget cycle. |
| **5. Vite & Assets** | `manifest.json` & Assets | Confirms every CSS and JS asset referenced in manifest exists on disk (>0 bytes) and returns `HTTP 200`. |
| **6. Frontend HTTP** | HTTP Endpoints | Queries `/`, `/login`, and `/admin` over HTTP, asserting `HTTP 200` without unhandled PHP/SQL exceptions. |

### Retry & Timeout Policy:
- Retries up to 3 times (with 2s, 5s backoff) before declaring failure.

---

## ↩ 4. Automated Rollback Engine (`RollbackManager.php`)

If **ANY** critical health check fails:
1. Deployment is marked as `failed`.
2. Automatic rollback is triggered immediately without waiting for admin intervention.
3. `RollbackManager` extracts `application.tar.gz` and `public.tar.gz`, restores `.env`, and restores the MySQL snapshot if database migrations were executed.
4. Caches are cleared and rebuilt.
5. `DeploymentHealthChecker` runs again on the restored version.
6. The UI displays:
   ```
   UPDATE FAILED: <reason>
   AUTOMATIC ROLLBACK SUCCESSFUL: Previous version restored and verified healthy.
   ```

---

## 🔒 5. Concurrency Protection (Lock)

* File: `storage/framework/deployment.lock`
* If another deployment is running, new deployment requests are rejected with `"Another deployment is already in progress"`.
* Auto-expires stale locks older than 5 minutes.
* Automatically released in `finally` / error blocks.

---

## 🚀 6. How to Deploy

### Option A: Web Admin UI (Recommended)
Navigate to `/admin/updates` and click **`⚡ Backup & Apply Update Now`**.

### Option B: SSH Terminal
```bash
cd /home/u919095325/prayaag
./deploy.sh
```

### Option C: Automated Git Post-Merge Hook
```bash
cd /home/u919095325/prayaag
git pull origin main
```
*(Automatically triggers the 25-step deployment pipeline).*
