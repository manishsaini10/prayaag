# RESTORE Instructions

## To restore this snapshot:

### 1. Restore files
```bash
cd /path/to/project
robocopy restore-points/2026-07-08/001_baseline/app app /E
robocopy restore-points/2026-07-08/001_baseline/routes routes /E
robocopy restore-points/2026-07-08/001_baseline/config config /E
robocopy restore-points/2026-07-08/001_baseline/public public /E
robocopy restore-points/2026-07-08/001_baseline/resources/views resources/views /E
copy restore-points/2026-07-08/001_baseline/bootstrap/app.php bootstrap/app.php
copy restore-points/2026-07-08/001_baseline/bootstrap/providers.php bootstrap/providers.php
copy restore-points/2026-07-08/001_baseline/.env .env
copy restore-points/2026-07-08/001_baseline/composer.json composer.json
copy restore-points/2026-07-08/001_baseline/composer.lock composer.lock
```

### 2. Restore database
```bash
php artisan migrate:fresh --seed
```

### 3. Clear caches
```bash
php artisan cache:clear
php artisan view:clear
```

### 4. Verify
```bash
php artisan serve
# Visit: http://127.0.0.1:8000
```
