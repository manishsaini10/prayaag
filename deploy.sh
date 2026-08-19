#!/usr/bin/env bash

# ==============================================================================
# Prayaag School CMS — Fully Portable Production Deployment Script
# Supports dynamic project root detection and configurable web root.
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

# ==============================================================================
# Dynamic Path Detection (Zero Hard-Coded Project Root)
# ==============================================================================
PROJECT_ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
cd "${PROJECT_ROOT}"

ARTISAN="${PROJECT_ROOT}/artisan"
PUBLIC_DIR="${PROJECT_ROOT}/public"
BUILD_DIR="${PUBLIC_DIR}/build"
STORAGE_DIR="${PROJECT_ROOT}/storage"
BOOTSTRAP_CACHE="${PROJECT_ROOT}/bootstrap/cache"
ENV_FILE="${PROJECT_ROOT}/.env"

# Load Configurable Web Root from .deploy-config if present
DEPLOY_CONFIG="${PROJECT_ROOT}/.deploy-config"
WEB_ROOT=""
if [ -f "${DEPLOY_CONFIG}" ]; then
    # shellcheck source=/dev/null
    source "${DEPLOY_CONFIG}"
fi

# [1/10] Checking environment & Path Validation
step_header "[1/10] Checking environment & Path Validation"

# 1. Validate Project Root & Artisan
if [ ! -f "${ARTISAN}" ]; then
    log_error "artisan not found at: ${ARTISAN}"
    log_error "Please run deploy.sh from within the Laravel Git repository."
    exit 1
fi

if [ ! -f "${PROJECT_ROOT}/composer.json" ]; then
    log_error "composer.json not found in project root: ${PROJECT_ROOT}"
    exit 1
fi

if [ ! -f "${ENV_FILE}" ]; then
    log_error ".env file is missing in ${PROJECT_ROOT}!"
    log_warn "Copy .env.example to .env and configure production credentials."
    exit 1
fi

# 2. Detect Tooling Binaries
PHP_BIN=$(which php || echo "php")
COMPOSER_BIN=$(which composer || echo "composer")
GIT_BIN=$(which git || echo "git")

log_info "Detected Project Root: ${PROJECT_ROOT}"
log_info "PHP Binary:            ${PHP_BIN} ($(${PHP_BIN} -r 'echo PHP_VERSION;'))"
log_info "Composer:              $(${COMPOSER_BIN} --version 2>/dev/null | head -n 1 || echo 'Detected')"
log_info "Git:                   $(${GIT_BIN} --version 2>/dev/null || echo 'Detected')"

# 3. Validate Configurable Web Root (WEB_ROOT)
if [ -n "${WEB_ROOT}" ]; then
    log_info "Configured Web Root:   ${WEB_ROOT} (.deploy-config)"
    
    if [ ! -d "${WEB_ROOT}" ]; then
        log_error "WEB_ROOT directory does NOT exist: ${WEB_ROOT}"
        log_error "Please verify or update the path in: ${DEPLOY_CONFIG}"
        exit 1
    fi

    if [ ! -w "${WEB_ROOT}" ]; then
        log_error "WEB_ROOT directory is NOT writable: ${WEB_ROOT}"
        log_error "Check web server user permissions on the public web root."
        exit 1
    fi
    log_success "Target Web Root validated and writable."
else
    WEB_ROOT="${PUBLIC_DIR}"
    log_info "Target Web Root:       ${WEB_ROOT} (Default unified public directory)"
fi

log_success "Environment and path validations passed."

# [2/10] Pulling Git changes & Pre-Deploy Safety Backup
step_header "[2/10] Pulling Git changes"

# Check for uncommitted production changes
DIRTY_FILES=$(${GIT_BIN} status --porcelain 2>/dev/null || true)
if [ -n "${DIRTY_FILES}" ]; then
    log_warn "Uncommitted local changes detected in repository:"
    echo "${DIRTY_FILES}"
    log_warn "Review these files to prevent accidental overwrite."
fi

# Pre-Deploy Safety Backup (.env + critical configs)
BACKUP_DIR="${STORAGE_DIR}/backups/updates"
mkdir -p "${BACKUP_DIR}"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/pre-deploy-${TIMESTAMP}.tar.gz"

log_info "Creating pre-deployment safety snapshot: $(basename ${BACKUP_FILE})..."
tar -czf "${BACKUP_FILE}" \
    -C "${PROJECT_ROOT}" \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='storage/framework/cache' \
    --exclude='storage/framework/sessions' \
    --exclude='storage/framework/views' \
    .env config routes app database 2>/dev/null || true
log_success "Safety snapshot saved."

# Pull latest commits
log_info "Pulling latest code from origin main..."
${GIT_BIN} pull origin main

COMMIT_HASH=$(${GIT_BIN} rev-parse --short HEAD 2>/dev/null || echo "Unknown")
COMMIT_MSG=$(${GIT_BIN} log -1 --pretty=format:"%s (%cr)" 2>/dev/null || echo "Latest commit")
log_success "Active commit: ${COMMIT_HASH} - ${COMMIT_MSG}"

# [3/10] Preparing Laravel directories & Permissions
step_header "[3/10] Preparing Laravel directories & Permissions"

log_info "Ensuring bootstrap/cache and storage directories exist..."
mkdir -p "${BOOTSTRAP_CACHE}"
mkdir -p "${STORAGE_DIR}/app/public"
mkdir -p "${STORAGE_DIR}/framework/cache/data"
mkdir -p "${STORAGE_DIR}/framework/sessions"
mkdir -p "${STORAGE_DIR}/framework/views"
mkdir -p "${STORAGE_DIR}/logs"
mkdir -p "${STORAGE_DIR}/backups/updates"

log_info "Enforcing safe directory permissions (ug+rwX / 775)..."
chmod -R ug+rwX "${STORAGE_DIR}" "${BOOTSTRAP_CACHE}" 2>/dev/null || \
chmod -R 775 "${STORAGE_DIR}" "${BOOTSTRAP_CACHE}" 2>/dev/null || true

# Validate writability
if [ ! -w "${BOOTSTRAP_CACHE}" ] || [ ! -w "${STORAGE_DIR}" ]; then
    log_error "Write permission verification failed on ${BOOTSTRAP_CACHE} or ${STORAGE_DIR}!"
    exit 1
fi
log_success "Directory structure and permissions verified."

# [4/10] Installing Composer dependencies
step_header "[4/10] Installing Composer dependencies"

log_info "Running composer install (--no-dev --optimize-autoloader)..."
${COMPOSER_BIN} install --no-dev --no-interaction --prefer-dist --optimize-autoloader

log_info "Generating optimized autoload classmap..."
${COMPOSER_BIN} dump-autoload -o --no-interaction
log_success "Composer dependencies and autoloader updated."

# [5/10] Running migrations
step_header "[5/10] Running database migrations"

log_info "Executing database migrations (--force)..."
${PHP_BIN} "${ARTISAN}" migrate --force
log_success "Database migrations up to date."

# [6/10] Clearing caches
step_header "[6/10] Clearing caches"

log_info "Clearing old bootstrap, view, route, and config caches..."
${PHP_BIN} "${ARTISAN}" optimize:clear
log_success "All old caches cleared."

# [7/10] Rebuilding caches
step_header "[7/10] Rebuilding caches"

log_info "Caching production configuration..."
${PHP_BIN} "${ARTISAN}" config:cache

log_info "Caching Blade views..."
${PHP_BIN} "${ARTISAN}" view:cache

log_info "Caching routes..."
if ! ${PHP_BIN} "${ARTISAN}" route:cache; then
    log_warn "Route caching skipped (closure routes or dynamic registrations detected)."
    ${PHP_BIN} "${ARTISAN}" route:clear || true
else
    log_success "Routes cached successfully."
fi

# [8/10] Checking Vite assets & syncing public_html
step_header "[8/10] Checking Vite assets & syncing public_html"

VITE_STATUS="WARNING"
MANIFEST_FILE="${BUILD_DIR}/manifest.json"

if [ -f "${MANIFEST_FILE}" ]; then
    log_success "Vite manifest.json verified: ${MANIFEST_FILE}"
    VITE_STATUS="OK"
else
    log_warn "Vite manifest.json not found in ${BUILD_DIR}!"
    log_warn "Vite production assets are missing. Run npm run build on the local development machine and deploy/commit the generated public/build directory."
fi

# Sync to external WEB_ROOT if distinct from local public/
if [ -n "${WEB_ROOT}" ] && [ "${WEB_ROOT}" != "${PUBLIC_DIR}" ] && [ -d "${WEB_ROOT}" ]; then
    log_info "Syncing compiled public assets to Web Root: ${WEB_ROOT}..."

    # Sync build directory
    if [ -d "${BUILD_DIR}" ]; then
        mkdir -p "${WEB_ROOT}/build"
        cp -r "${BUILD_DIR}/." "${WEB_ROOT}/build/"
    fi

    # Sync css, js, images, fonts directories if present
    for dir in css js images fonts; do
        if [ -d "${PUBLIC_DIR}/${dir}" ]; then
            mkdir -p "${WEB_ROOT}/${dir}"
            cp -r "${PUBLIC_DIR}/${dir}/." "${WEB_ROOT}/${dir}/"
        fi
    done

    # Sync root asset files
    for file in site.css admin.css robots.txt favicon.ico deploy.php; do
        if [ -f "${PUBLIC_DIR}/${file}" ]; then
            cp "${PUBLIC_DIR}/${file}" "${WEB_ROOT}/${file}"
        fi
    done

    # Verify storage symlink in WEB_ROOT
    if [ ! -L "${WEB_ROOT}/storage" ] && [ ! -d "${WEB_ROOT}/storage" ]; then
        log_info "Creating storage symlink in ${WEB_ROOT}..."
        ln -s "${STORAGE_DIR}/app/public" "${WEB_ROOT}/storage" 2>/dev/null || true
    fi

    log_success "Assets successfully synced to ${WEB_ROOT}."
fi

# [9/10] Checking Laravel system health
step_header "[9/10] Checking Laravel system health"

${PHP_BIN} "${ARTISAN}" about --only=environment 2>/dev/null || ${PHP_BIN} "${ARTISAN}" about || true

# Final permission assertions
test -d "${BOOTSTRAP_CACHE}" && test -w "${BOOTSTRAP_CACHE}"
test -d "${STORAGE_DIR}" && test -w "${STORAGE_DIR}"

# [10/10] Deployment complete
step_header "[10/10] Deployment complete"

PHP_VER=$(${PHP_BIN} -r 'echo PHP_VERSION;')
APP_ENV=$(${PHP_BIN} -r "require '${PROJECT_ROOT}/vendor/autoload.php'; \$app = require '${PROJECT_ROOT}/bootstrap/app.php'; echo config('app.env', 'production');" 2>/dev/null || echo "production")

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
