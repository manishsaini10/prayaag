#!/bin/bash
# ============================================================
# Prayaag School CMS - Production Deployment Script
# ============================================================
# Run this on your Hostinger server after Git pull.
# Usage: bash database/deploy.sh
# ============================================================
set -euo pipefail

echo "=== Prayaag School CMS Deployment ==="
echo ""

# 1. Create .env if not exists
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
    echo ">>> PLEASE EDIT .env with your production values:"
    echo "    APP_ENV=production"
    echo "    APP_DEBUG=false"
    echo "    APP_URL=https://yourdomain.com"
    echo "    DB_CONNECTION=mysql"
    echo "    DB_HOST=127.0.0.1"
    echo "    DB_DATABASE=your_db_name"
    echo "    DB_USERNAME=your_db_user"
    echo "    DB_PASSWORD=your_db_pass"
    echo ""
    read -p "Press Enter after editing .env, or Ctrl+C to abort..."
fi

# 2. Create MySQL database (if not exists)
if command -v mysql &> /dev/null; then
    echo "Creating database if not exists..."
    source <(grep -E '^DB_(DATABASE|USERNAME|PASSWORD|HOST)' .env | sed 's/ //g')
    mysql -h"${DB_HOST:-127.0.0.1}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" \
        -e "CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
fi

# 3. Import SQL dump (if available and DB is empty)
if [ -f database/deploy.sql ]; then
    echo "Found database/deploy.sql — do you want to import it? (y/n)"
    read answer
    if [ "$answer" = "y" ]; then
        echo "Importing database/deploy.sql..."
        source <(grep -E '^DB_(DATABASE|USERNAME|PASSWORD|HOST)' .env | sed 's/ //g')
        mysql -h"${DB_HOST:-127.0.0.1}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" < database/deploy.sql
        echo "Import complete!"
    fi
fi

# 4. Install PHP dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# 5. Install & build frontend
if [ -f package.json ]; then
    echo "Building frontend assets..."
    npm ci --production
    npm run build
fi

# 6. Laravel setup
echo "Running Laravel setup..."
php artisan key:generate --force
php artisan storage:link --force

# 7. Run migrations (if SQL dump wasn't imported)
echo "Running migrations..."
php artisan migrate --force

# 8. Seed database
echo "Running database seeders..."
php artisan db:seed --force

# 9. Cache for production
echo "Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 10. Set permissions
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/uploads

echo ""
echo "=== Deployment Complete! ==="
echo "Admin login: /admin"
echo "Default credentials (if seeded): admin@school.test / password"
echo ""
echo "IMPORTANT: Set up a cron job:"
echo "  * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "For queue: nohup php artisan queue:work --daemon &"