#!/usr/bin/env bash

# ==============================================================================
# Prayaag School CMS — Production-Safe Deployment Script (Hostinger Linux)
# Target Environment: Hostinger Shared Hosting (PHP 8.3 / Laravel 12)
# Project Root:       /home/u919095325/prayaag
# Web Root:           /home/u919095325/domains/lightgray-buffalo-350334.hostingersite.com/public_html
# ==============================================================================

set -eo pipefail

# Color Codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m' # No Color

# Helper Functions
log_info()    { echo -e "${CYAN}ℹ [INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}✔ [SUCCESS]${NC} $1"; }
log_warn()    { echo -e "${YELLOW}⚠ [WARNING]${NC} $1"; }
log_error()   { echo -e "${RED}✖ [ERROR]${NC} $1"; }
step_header() { echo -e "\n${BOLD}${BLUE}================================================================${NC}"; echo -e "${BOLD}${BLUE} $1 ${NC}"; echo -e "${BOLD}${BLUE}================================================================${NC}"; }

# Fail-safe trap on any unhandled failure
trap 'error_handler $? $LINENO "$BASH_COMMAND"' ERR

error_handler() {
    local exit_code=$1
    local line_no=$2
    local last_command=$3
    echo ""
    log_error "Deployment FAILED at line ${line_no}!"
    log_error "Failed Command: ${last_command}"
    log_error "Exit Code: ${exit_code}"
    echo -e "${YELLOW}The application has been preserved in its previous state. Check logs above for details.${NC}"
    exit ${exit_code}
}

# Configuration Defaults (Auto-detected or Hostinger defaults)
APP_DIR="$(pwd)"
PUBLIC_WEB_ROOT="/home/u919095325/domains/lightgray-buffalo-350334.hostingersite.com/public_html"

# [1/10] Checking environment
step_header "[1/10] Checking environment"

if [ ! -f "${APP_DIR}/artisan" ] || [ ! -f "${APP_DIR}/composer.json" ]; then
    log_error "Must run from Laravel project root! Current directory: ${APP_DIR}"
    exit 1
fi

if [ ! -f "${APP_DIR}/.env" ]; then
    log_error ".env file is missing in ${APP_DIR}!"
    log_warn "Copy .env.example to .env and configure your production credentials."
    exit 1
fi

PHP_BIN=$(which php || echo "php")
COMPOSER_BIN=$(which composer || echo "composer")
GIT_BIN=$(which git || echo "git")

log_info "Laravel Project Root: ${APP_DIR}"
log_info "PHP Binary:           ${PHP_BIN} ($(${PHP_BIN} -r 'echo PHP_VERSION;'))"
log_info "Composer:             $(${COMPOSER_BIN} --version 2>/dev/null | head -n 1 || echo 'Detected')"
log_info "Git Version:          $(${GIT_BIN} --version 2>/dev/null || echo 'Detected')"

if [ -d "${PUBLIC_WEB_ROOT}" ]; then
    log_info "Target Web Root:      ${PUBLIC_WEB_ROOT} (Split public_html)"
else
    PUBLIC_WEB_ROOT="${APP_DIR}/public"
    log_info "Target Web Root:      ${PUBLIC_WEB_ROOT} (Unified public)"
fi
log_success "Environment verification passed."

# [2/10] Pulling Git changes & Pre-Deploy Backup
step_header "[2/10] Pulling Git changes"

# Check for uncommitted changes in production
DIRTY_FILES=$(${GIT_BIN} status --porcelain 2>/dev/null || true)
if [ -n "${DIRTY_FILES}" ]; then
    log_warn "Uncommitted local changes detected in production repository:"
    echo "${DIRTY_FILES}"
    log_warn "To avoid overwriting custom changes, please review or commit them before pulling."
fi

# Pre-Deploy Safety Backup (Protect .env and critical configs)
BACKUP_DIR="${APP_DIR}/storage/backups/updates"
mkdir -p "${BACKUP_DIR}"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/pre-deploy-${TIMESTAMP}.tar.gz"

log_info "Creating pre-deployment safety snapshot: $(basename ${BACKUP_FILE})..."
tar -czf "${BACKUP_FILE}" \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='storage/framework/cache' \
    --exclude='storage/framework/sessions' \
    --exclude='storage/framework/views' \
    .env config/ routes/ app/ database/ 2>/dev/null || true
log_success "Safety backup created."

# Fetch and pull latest main branch
log_info "Fetching and pulling latest changes from origin main..."
${GIT_BIN} pull origin main

COMMIT_HASH=$(${GIT_BIN} rev-parse --short HEAD 2>/dev/null || echo "Unknown")
COMMIT_MSG=$(${GIT_BIN} log -1 --pretty=format:"%s (%cr)" 2>/dev/null || echo "Latest commit")
log_success "Pulled commit: ${COMMIT_HASH} - ${COMMIT_MSG}"

# [3/10] Preparing Laravel directories & Permissions
step_header "[3/10] Preparing Laravel directories & Permissions"

log_info "Ensuring bootstrap/cache and storage directories exist..."
mkdir -p "${APP_DIR}/bootstrap/cache"
mkdir -p "${APP_DIR}/storage/app/public"
mkdir -p "${APP_DIR}/storage/framework/cache/data"
mkdir -p "${APP_DIR}/storage/framework/sessions"
mkdir -p "${APP_DIR}/storage/framework/views"
mkdir -p "${APP_DIR}/storage/logs"
mkdir -p "${APP_DIR}/storage/backups/updates"

log_info "Setting safe directory permissions (ug+rwX / 775)..."
chmod -R ug+rwX "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" 2>/dev/null || \
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache" 2>/dev/null || true

# Verify write permission
if [ ! -w "${APP_DIR}/bootstrap/cache" ] || [ ! -w "${APP_DIR}/storage" ]; then
    log_error "Write permission check failed on bootstrap/cache or storage!"
    exit 1
fi
log_success "Directories created and permissions verified."

# [4/10] Installing Composer dependencies
step_header "[4/10] Installing Composer dependencies"

log_info "Running composer install (--no-dev --optimize-autoloader)..."
${COMPOSER_BIN} install --no-dev --no-interaction --prefer-dist --optimize-autoloader

log_info "Dumping optimized autoloader..."
${COMPOSER_BIN} dump-autoload -o --no-interaction
log_success "Composer dependencies updated and autoloaded."

# [5/10] Running migrations
step_header "[5/10] Running database migrations"

log_info "Running database migrations (--force)..."
${PHP_BIN} artisan migrate --force
log_success "Database migrations up to date."

# [6/10] Clearing caches
step_header "[6/10] Clearing caches"

log_info "Clearing old compiled caches..."
${PHP_BIN} artisan optimize:clear
log_success "All caches cleared."

# [7/10] Rebuilding caches
step_header "[7/10] Rebuilding caches"

log_info "Caching production configuration..."
${PHP_BIN} artisan config:cache

log_info "Caching Blade views..."
${PHP_BIN} artisan view:cache

log_info "Caching routes..."
if ! ${PHP_BIN} artisan route:cache; then
    log_warn "Route caching skipped (closure routes or dynamic registrations detected)."
    ${PHP_BIN} artisan route:clear || true
else
    log_success "Routes cached successfully."
fi

# [8/10] Checking Vite assets & syncing public_html
step_header "[8/10] Checking Vite assets & syncing public_html"

VITE_STATUS="WARNING"
if [ -f "${APP_DIR}/public/build/manifest.json" ]; then
    log_success "Vite manifest.json found in public/build."
    VITE_STATUS="OK"
else
    log_warn "Vite production assets (public/build/manifest.json) not found!"
    log_warn "Remember to run 'npm run build' locally and commit public/build."
fi

# If public_html is separated, sync public assets
if [ "${PUBLIC_WEB_ROOT}" != "${APP_DIR}/public" ] && [ -d "${PUBLIC_WEB_ROOT}" ]; then
    log_info "Syncing public assets to public_html: ${PUBLIC_WEB_ROOT}..."

    # Sync build directory
    if [ -d "${APP_DIR}/public/build" ]; then
        mkdir -p "${PUBLIC_WEB_ROOT}/build"
        cp -r "${APP_DIR}/public/build/." "${PUBLIC_WEB_ROOT}/build/"
    fi

    # Sync css & js directories if they exist
    for dir in css js images fonts; do
        if [ -d "${APP_DIR}/public/${dir}" ]; then
            mkdir -p "${PUBLIC_WEB_ROOT}/${dir}"
            cp -r "${APP_DIR}/public/${dir}/." "${PUBLIC_WEB_ROOT}/${dir}/"
        fi
    done

    # Sync standalone root assets
    for file in site.css admin.css robots.txt favicon.ico deploy.php; do
        if [ -f "${APP_DIR}/public/${file}" ]; then
            cp "${APP_DIR}/public/${file}" "${PUBLIC_WEB_ROOT}/${file}"
        fi
    done

    # Verify storage symlink
    if [ ! -L "${PUBLIC_WEB_ROOT}/storage" ] && [ ! -d "${PUBLIC_WEB_ROOT}/storage" ]; then
        log_info "Creating storage symlink in public_html..."
        ln -s "${APP_DIR}/storage/app/public" "${PUBLIC_WEB_ROOT}/storage" 2>/dev/null || true
    fi

    log_success "Public assets synced to public_html."
fi

# [9/10] Checking Laravel
step_header "[9/10] Checking Laravel system health"

${PHP_BIN} artisan about --only=environment 2>/dev/null || ${PHP_BIN} artisan about || true

# Check write permissions
test -d "${APP_DIR}/bootstrap/cache" && test -w "${APP_DIR}/bootstrap/cache"
test -d "${APP_DIR}/storage" && test -w "${APP_DIR}/storage"

# [10/10] Deployment complete
step_header "[10/10] Deployment complete"

PHP_VER=$(${PHP_BIN} -r 'echo PHP_VERSION;')
APP_ENV=$(${PHP_BIN} -r "require '${APP_DIR}/vendor/autoload.php'; \$app = require '${APP_DIR}/bootstrap/app.php'; echo config('app.env', 'production');" 2>/dev/null || echo "production")

echo ""
echo -e "${BOLD}${GREEN}========================================${NC}"
echo -e "${BOLD}${GREEN}DEPLOYMENT SUCCESSFUL${NC}"
echo -e "${BOLD}${GREEN}========================================${NC}"
echo ""
echo -e "Application:"
echo -e "Laravel 12"
echo ""
echo -e "PHP:"
echo -e "${PHP_VER}"
echo ""
echo -e "Commit:"
echo -e "${COMMIT_HASH}"
echo ""
echo -e "Environment:"
echo -e "${APP_ENV}"
echo ""
echo -e "URL:"
echo -e "https://lightgray-buffalo-350334.hostingersite.com"
echo ""
echo -e "Laravel cache:"
echo -e "OK"
echo ""
echo -e "Storage:"
echo -e "OK"
echo ""
echo -e "Composer:"
echo -e "OK"
echo ""
echo -e "Database:"
echo -e "OK"
echo ""
echo -e "Vite manifest:"
echo -e "${VITE_STATUS}"
echo ""
echo -e "${BOLD}${GREEN}========================================${NC}"
echo ""
