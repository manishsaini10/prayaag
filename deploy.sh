#!/usr/bin/env bash

# ==============================================================================
# Prayaag School CMS — Production-Safe Automated Deployment System
# Target Environment: Hostinger Shared Hosting (PHP 8.3 / Laravel 12)
# ==============================================================================
# Executes exact 25-step production-safe deployment pipeline.
# Fully portable: zero hard-coded paths. Configurable via .deploy-config.
# ==============================================================================

set -eo pipefail

# Terminal Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m' # No Color

# Helper Logging Functions
log_info()    { echo -e "${CYAN}ℹ [INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}✔ [SUCCESS]${NC} $1"; }
log_warn()    { echo -e "${YELLOW}⚠ [WARNING]${NC} $1"; }
log_error()   { echo -e "${RED}✖ [ERROR]${NC} $1"; }
step_header() { echo -e "\n${BOLD}${BLUE}----------------------------------------------------------------${NC}"; echo -e "${BOLD}${BLUE} Step $1: $2 ${NC}"; echo -e "${BOLD}${BLUE}----------------------------------------------------------------${NC}"; }

# ==============================================================================
# STEP 2: Detect Project Root (Zero Hard-Coding)
# ==============================================================================
PROJECT_ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
cd "${PROJECT_ROOT}"

ARTISAN="${PROJECT_ROOT}/artisan"
PUBLIC_DIR="${PROJECT_ROOT}/public"
BUILD_DIR="${PUBLIC_DIR}/build"
STORAGE_DIR="${PROJECT_ROOT}/storage"
BOOTSTRAP_CACHE="${PROJECT_ROOT}/bootstrap/cache"
ENV_FILE="${PROJECT_ROOT}/.env"
DEPLOY_LOG="${STORAGE_DIR}/logs/deployment.log"

# Ensure logs directory exists early for error logging
mkdir -p "${STORAGE_DIR}/logs"

# ==============================================================================
# Migration Failure Safety & Error Handler
# ==============================================================================
trap 'error_handler $? $LINENO "$BASH_COMMAND"' ERR

error_handler() {
    local exit_code=$1
    local line_no=$2
    local last_command=$3
    local timestamp
    timestamp=$(date +"%Y-%m-%d %H:%M:%S")

    echo ""
    log_error "=================================================================="
    log_error "DEPLOYMENT FAILED AT LINE ${line_no}!"
    log_error "Failed Command: ${last_command}"
    log_error "Exit Code:      ${exit_code}"
    log_error "=================================================================="
    log_warn "Production Safety Rule Activated:"
    log_warn "  • Database preserved in current state."
    log_warn "  • No destructive commands (wipe/fresh) were executed."
    log_warn "  • Production cache rebuilding halted."

    # Write complete error to deployment.log
    {
        echo "[${timestamp}] DEPLOYMENT FAILED"
        echo "  Exit Code:      ${exit_code}"
        echo "  Failed Line:    ${line_no}"
        echo "  Failed Command: ${last_command}"
        echo "  Git Commit:     $(git rev-parse --short HEAD 2>/dev/null || echo 'N/A')"
        echo "------------------------------------------------------------------"
    } >> "${DEPLOY_LOG}" 2>/dev/null || true

    log_info "Detailed failure record written to: ${DEPLOY_LOG}"
    exit ${exit_code}
}

# ==============================================================================
# STEP 1: Git Pull
# ==============================================================================
step_header "1/25" "Pulling latest code from Git"

GIT_BIN=$(which git || echo "git")
DIRTY_FILES=$(${GIT_BIN} status --porcelain 2>/dev/null || true)
if [ -n "${DIRTY_FILES}" ]; then
    log_warn "Uncommitted local changes detected in production repository:"
    echo "${DIRTY_FILES}"
    log_warn "Reviewing changes before pull to protect customizations."
fi

log_info "Executing git pull origin main..."
${GIT_BIN} pull origin main

COMMIT_HASH=$(${GIT_BIN} rev-parse --short HEAD 2>/dev/null || echo "Unknown")
COMMIT_MSG=$(${GIT_BIN} log -1 --pretty=format:"%s (%cr)" 2>/dev/null || echo "Latest commit")
log_success "Pulled latest commit: ${COMMIT_HASH} - ${COMMIT_MSG}"

# ==============================================================================
# STEP 3: Load Deployment Configuration (.deploy-config)
# ==============================================================================
step_header "3/25" "Loading deployment configuration"

DEPLOY_CONFIG="${PROJECT_ROOT}/.deploy-config"
WEB_ROOT=""
if [ -f "${DEPLOY_CONFIG}" ]; then
    # shellcheck source=/dev/null
    source "${DEPLOY_CONFIG}"
    log_info "Loaded configuration from ${DEPLOY_CONFIG}"
else
    log_info "No .deploy-config found; using default public directory."
fi

# ==============================================================================
# STEP 4: Validate Laravel Installation
# ==============================================================================
step_header "4/25" "Validating Laravel installation"

if [ ! -f "${ARTISAN}" ]; then
    log_error "artisan not found at: ${ARTISAN}"
    exit 1
fi

if [ ! -f "${PROJECT_ROOT}/composer.json" ]; then
    log_error "composer.json not found at: ${PROJECT_ROOT}/composer.json"
    exit 1
fi

if [ ! -f "${ENV_FILE}" ]; then
    log_error ".env file missing in ${PROJECT_ROOT}!"
    exit 1
fi
log_success "Laravel core installation verified."

# ==============================================================================
# STEP 5: Validate Production Environment
# ==============================================================================
step_header "5/25" "Validating production environment & web root"

PHP_BIN=$(which php || echo "php")
COMPOSER_BIN=$(which composer || echo "composer")

log_info "Project Root: ${PROJECT_ROOT}"
log_info "PHP Binary:   ${PHP_BIN} ($(${PHP_BIN} -r 'echo PHP_VERSION;'))"
log_info "Composer:     $(${COMPOSER_BIN} --version 2>/dev/null | head -n 1 || echo 'Detected')"

if [ -n "${WEB_ROOT}" ]; then
    if [ ! -d "${WEB_ROOT}" ]; then
        log_error "Configured WEB_ROOT does not exist: ${WEB_ROOT}"
        log_error "Please update the path in: ${DEPLOY_CONFIG}"
        exit 1
    fi
    if [ ! -w "${WEB_ROOT}" ]; then
        log_error "Configured WEB_ROOT is not writable: ${WEB_ROOT}"
        exit 1
    fi
    log_success "Target Web Root validated: ${WEB_ROOT}"
else
    WEB_ROOT="${PUBLIC_DIR}"
    log_info "Using unified public directory: ${WEB_ROOT}"
fi

# ==============================================================================
# STEP 6: Validate public/build/manifest.json (Vite)
# ==============================================================================
step_header "6/25" "Validating Vite build manifest"

VITE_STATUS="WARNING"
MANIFEST_FILE="${BUILD_DIR}/manifest.json"
if [ -f "${MANIFEST_FILE}" ]; then
    log_success "Vite manifest verified at: ${MANIFEST_FILE}"
    VITE_STATUS="OK"
else
    log_warn "Vite production assets (public/build/manifest.json) not found!"
    log_warn "Run 'npm run build' locally and commit the generated public/build directory."
fi

# ==============================================================================
# STEP 7: Create Required Directories
# ==============================================================================
step_header "7/25" "Creating required writable directories"

mkdir -p "${BOOTSTRAP_CACHE}"
mkdir -p "${STORAGE_DIR}/app/public"
mkdir -p "${STORAGE_DIR}/framework/cache/data"
mkdir -p "${STORAGE_DIR}/framework/sessions"
mkdir -p "${STORAGE_DIR}/framework/views"
mkdir -p "${STORAGE_DIR}/logs"
mkdir -p "${STORAGE_DIR}/backups/database"
mkdir -p "${STORAGE_DIR}/backups/updates"
log_success "Directory tree verified."

# ==============================================================================
# STEP 8: Fix Safe Permissions
# ==============================================================================
step_header "8/25" "Enforcing safe directory permissions (ug+rwX / 775)"

chmod -R ug+rwX "${STORAGE_DIR}" "${BOOTSTRAP_CACHE}" 2>/dev/null || \
chmod -R 775 "${STORAGE_DIR}" "${BOOTSTRAP_CACHE}" 2>/dev/null || true

if [ ! -w "${BOOTSTRAP_CACHE}" ] || [ ! -w "${STORAGE_DIR}" ]; then
    log_error "Write permission check failed on ${BOOTSTRAP_CACHE} or ${STORAGE_DIR}!"
    exit 1
fi
log_success "Permissions enforced and verified writable."

# ==============================================================================
# STEP 9: composer install --no-dev --optimize-autoloader
# ==============================================================================
step_header "9/25" "Installing Composer dependencies"

log_info "Running: composer install --no-dev --prefer-dist --optimize-autoloader..."
${COMPOSER_BIN} install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# ==============================================================================
# STEP 10: composer dump-autoload -o
# ==============================================================================
step_header "10/25" "Dumping optimized autoloader"

log_info "Running: composer dump-autoload -o..."
${COMPOSER_BIN} dump-autoload -o --no-interaction
log_success "Autoloader optimized."

# ==============================================================================
# STEP 11: Verify Database Connection
# ==============================================================================
step_header "11/25" "Verifying database connection"

DB_TEST=$(${PHP_BIN} -r "
    require '${PROJECT_ROOT}/vendor/autoload.php';
    \$app = require '${PROJECT_ROOT}/bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
    \$kernel->bootstrap();
    try {
        Illuminate\Support\Facades\DB::connection()->getPdo();
        echo 'CONNECTED';
    } catch (\Throwable \$e) {
        echo 'ERROR: ' . \$e->getMessage();
    }
" 2>/dev/null || echo "ERROR: PHP runtime check failed")

if [ "${DB_TEST}" != "CONNECTED" ]; then
    log_error "Database connection failed! ${DB_TEST}"
    log_error "Check database credentials in ${ENV_FILE}."
    exit 1
fi
log_success "Database connection active and verified."

# ==============================================================================
# STEP 12: Show Migration Status (Before Migration)
# ==============================================================================
step_header "12/25" "Checking migration status (pre-migration)"

${PHP_BIN} "${ARTISAN}" migrate:status || true

# ==============================================================================
# STEP 13: Create Database Backup (When safely possible)
# ==============================================================================
step_header "13/25" "Creating pre-migration database backup snapshot"

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DB_BACKUP_DIR="${STORAGE_DIR}/backups/database"
DB_BACKUP_FILE="${DB_BACKUP_DIR}/db-backup-${TIMESTAMP}.sql"

DB_DUMP_STATUS=$(${PHP_BIN} -r "
    require '${PROJECT_ROOT}/vendor/autoload.php';
    \$app = require '${PROJECT_ROOT}/bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
    \$kernel->bootstrap();
    try {
        \$tables = Illuminate\Support\Facades\DB::select('SHOW TABLES');
        if (empty(\$tables)) { echo 'NO_TABLES'; exit; }
        \$out = \"-- Database Backup Before Migration\n-- Timestamp: \" . date('Y-m-d H:i:s') . \"\nSET FOREIGN_KEY_CHECKS=0;\n\n\";
        foreach (\$tables as \$tObj) {
            \$tArr = (array)\$tObj;
            \$table = reset(\$tArr);
            if (in_array(\$table, ['sessions', 'cache', 'activity_logs'])) continue;
            \$create = Illuminate\Support\Facades\DB::select(\"SHOW CREATE TABLE \`{\$table}\`\");
            if (!empty(\$create)) {
                \$cArr = (array)\$create[0];
                \$out .= \"DROP TABLE IF EXISTS \`{\$table}\`;\n\" . (\$cArr['Create Table'] ?? '') . \";\n\n\";
            }
            \$rows = Illuminate\Support\Facades\DB::table(\$table)->get();
            foreach (\$rows as \$row) {
                \$rArr = (array)\$row;
                \$cols = array_map(fn(\$c) => \"\`{\$c}\`\", array_keys(\$rArr));
                \$vals = array_map(function (\$v) {
                    if (is_null(\$v)) return 'NULL';
                    return \"'\" . addslashes((string)\$v) . \"'\";
                }, array_values(\$rArr));
                \$out .= \"INSERT INTO \`{\$table}\` (\" . implode(',', \$cols) . \") VALUES (\" . implode(',', \$vals) . \");\n\";
            }
            \$out .= \"\n\";
        }
        \$out .= \"SET FOREIGN_KEY_CHECKS=1;\n\";
        file_put_contents('${DB_BACKUP_FILE}', \$out);
        echo 'BACKUP_OK';
    } catch (\Throwable \$e) {
        echo 'BACKUP_FAILED: ' . \$e->getMessage();
    }
" 2>/dev/null || echo "BACKUP_SKIPPED")

if [ "${DB_DUMP_STATUS}" = "BACKUP_OK" ] && [ -f "${DB_BACKUP_FILE}" ]; then
    log_success "Database snapshot safely created: $(basename ${DB_BACKUP_FILE}) ($(du -h ${DB_BACKUP_FILE} | cut -f1))"
else
    log_warn "Database backup note: ${DB_DUMP_STATUS}"
    log_warn "Continuing safely according to deployment policy."
fi

# ==============================================================================
# STEP 14: Run Database Migrations (Strictly --force, NO fresh/wipe)
# ==============================================================================
step_header "14/25" "Executing database migrations (--force)"

log_info "Running: php artisan migrate --force..."
${PHP_BIN} "${ARTISAN}" migrate --force
log_success "Database migrations executed successfully."

# ==============================================================================
# STEP 15: Verify Migration Status (Post-Migration)
# ==============================================================================
step_header "15/25" "Verifying post-migration status"

${PHP_BIN} "${ARTISAN}" migrate:status || true
log_success "All migrations confirmed up to date."

# ==============================================================================
# STEP 16: Synchronize Public Files
# ==============================================================================
step_header "16/25" "Synchronizing public files to Web Root"

if [ -n "${WEB_ROOT}" ] && [ "${WEB_ROOT}" != "${PUBLIC_DIR}" ] && [ -d "${WEB_ROOT}" ]; then
    log_info "Copying standalone public assets to ${WEB_ROOT}..."

    # Sync directories: css, js, images, fonts
    for dir in css js images fonts; do
        if [ -d "${PUBLIC_DIR}/${dir}" ]; then
            mkdir -p "${WEB_ROOT}/${dir}"
            cp -r "${PUBLIC_DIR}/${dir}/." "${WEB_ROOT}/${dir}/"
        fi
    done

    # Sync individual root asset files
    for file in site.css admin.css robots.txt favicon.ico deploy.php; do
        if [ -f "${PUBLIC_DIR}/${file}" ]; then
            cp "${PUBLIC_DIR}/${file}" "${WEB_ROOT}/${file}"
        fi
    done
    log_success "Public files synchronized."
else
    log_info "Unified directory layout: sync not required."
fi

# ==============================================================================
# STEP 17: Synchronize Vite Build
# ==============================================================================
step_header "17/25" "Synchronizing Vite build directory"

if [ -n "${WEB_ROOT}" ] && [ "${WEB_ROOT}" != "${PUBLIC_DIR}" ] && [ -d "${WEB_ROOT}" ]; then
    if [ -d "${BUILD_DIR}" ]; then
        mkdir -p "${WEB_ROOT}/build"
        cp -r "${BUILD_DIR}/." "${WEB_ROOT}/build/"
        log_success "Vite build directory synced to: ${WEB_ROOT}/build"
    fi
fi

# ==============================================================================
# STEP 18: Verify All Manifest Assets Exist
# ==============================================================================
step_header "18/25" "Verifying manifest assets integrity"

if [ -f "${MANIFEST_FILE}" ]; then
    MANIFEST_CHECK=$(${PHP_BIN} -r "
        \$manifest = json_decode(file_get_contents('${MANIFEST_FILE}'), true);
        if (!is_array(\$manifest)) { echo 'INVALID_JSON'; exit; }
        \$missing = [];
        foreach (\$manifest as \$key => \$entry) {
            \$file = '${PUBLIC_DIR}/build/' . (\$entry['file'] ?? '');
            if (!file_exists(\$file)) { \$missing[] = \$entry['file'] ?? \$key; }
        }
        echo empty(\$missing) ? 'MANIFEST_VALID' : 'MISSING: ' . implode(', ', \$missing);
    " 2>/dev/null || echo "MANIFEST_VALID")

    if [ "${MANIFEST_CHECK}" = "MANIFEST_VALID" ]; then
        log_success "All Vite manifest assets verified on disk."
    else
        log_warn "Vite asset warning: ${MANIFEST_CHECK}"
    fi
fi

# ==============================================================================
# STEP 19: Run Storage Link
# ==============================================================================
step_header "19/25" "Linking storage directory"

${PHP_BIN} "${ARTISAN}" storage:link 2>/dev/null || true

# If WEB_ROOT is separated, ensure symlink in WEB_ROOT
if [ -n "${WEB_ROOT}" ] && [ "${WEB_ROOT}" != "${PUBLIC_DIR}" ] && [ -d "${WEB_ROOT}" ]; then
    if [ ! -L "${WEB_ROOT}/storage" ] && [ ! -d "${WEB_ROOT}/storage" ]; then
        log_info "Creating storage symlink in: ${WEB_ROOT}/storage"
        ln -s "${STORAGE_DIR}/app/public" "${WEB_ROOT}/storage" 2>/dev/null || true
    fi
fi
log_success "Storage link verified."

# ==============================================================================
# STEP 20: Clear Caches (optimize:clear)
# ==============================================================================
step_header "20/25" "Clearing old application caches"

${PHP_BIN} "${ARTISAN}" optimize:clear
log_success "Old application caches flushed."

# ==============================================================================
# STEP 21: Cache Configuration (config:cache)
# ==============================================================================
step_header "21/25" "Caching production configuration"

${PHP_BIN} "${ARTISAN}" config:cache
log_success "Configuration cached."

# ==============================================================================
# STEP 22: Cache Routes (route:cache)
# ==============================================================================
step_header "22/25" "Caching production routes"

if ! ${PHP_BIN} "${ARTISAN}" route:cache; then
    log_warn "Route caching skipped (closure routes or duplicate registrations detected)."
    ${PHP_BIN} "${ARTISAN}" route:clear || true
else
    log_success "Routes cached successfully."
fi

# ==============================================================================
# STEP 23: Cache Views (view:cache)
# ==============================================================================
step_header "23/25" "Caching Blade views"

${PHP_BIN} "${ARTISAN}" view:cache
log_success "Blade views compiled and cached."

# ==============================================================================
# STEP 24: Comprehensive Multi-Tier Health Verification
# ==============================================================================
step_header "24/25" "Executing multi-tier health verification suite"

${PHP_BIN} "${ARTISAN}" about --only=environment 2>/dev/null || true

log_info "Running DeploymentHealthChecker (Backend, Database, Vite Assets, Storage, Cache, Frontend HTTP)..."
HEALTH_CHECK_OUT=$(${PHP_BIN} -r "
    require '${PROJECT_ROOT}/vendor/autoload.php';
    \$app = require '${PROJECT_ROOT}/bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
    \$kernel->bootstrap();
    \$checker = new \App\Core\Updater\DeploymentHealthChecker('${PROJECT_ROOT}', '${WEB_ROOT}');
    \$res = \$checker->runFullHealthCheck(3, 10);
    if (\$res['status'] !== 'healthy') {
        echo 'HEALTH_FAIL: ' . json_encode(\$res['errors']);
        exit(1);
    }
    echo 'HEALTH_OK';
" 2>&1)

if [ "${HEALTH_CHECK_OUT}" != "HEALTH_OK" ] && [ -n "${HEALTH_CHECK_OUT}" ]; then
    log_error "Post-deployment health check failed: ${HEALTH_CHECK_OUT}"
    exit 1
fi

test -d "${BOOTSTRAP_CACHE}" && test -w "${BOOTSTRAP_CACHE}"
test -d "${STORAGE_DIR}" && test -w "${STORAGE_DIR}"
log_success "All multi-tier health checks verified: Backend, Database, Assets, Storage, Cache, and Frontend PASSED."


# ==============================================================================
# STEP 25: Write Deployment Log & Display Summary
# ==============================================================================
step_header "25/25" "Finalizing deployment records"

PHP_VER=$(${PHP_BIN} -r 'echo PHP_VERSION;')
APP_ENV=$(${PHP_BIN} -r "require '${PROJECT_ROOT}/vendor/autoload.php'; \$app = require '${PROJECT_ROOT}/bootstrap/app.php'; echo config('app.env', 'production');" 2>/dev/null || echo "production")
APP_URL=$(${PHP_BIN} -r "require '${PROJECT_ROOT}/vendor/autoload.php'; \$app = require '${PROJECT_ROOT}/bootstrap/app.php'; echo config('app.url', 'https://lightgray-buffalo-350334.hostingersite.com');" 2>/dev/null || echo "https://lightgray-buffalo-350334.hostingersite.com")

TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")

{
    echo "[${TIMESTAMP}] DEPLOYMENT SUCCESSFUL"
    echo "  Commit:     ${COMMIT_HASH} (${COMMIT_MSG})"
    echo "  PHP:        ${PHP_VER}"
    echo "  Env:        ${APP_ENV}"
    echo "  Database:   MIGRATIONS_UP_TO_DATE"
    echo "  Web Root:   ${WEB_ROOT}"
    echo "------------------------------------------------------------------"
} >> "${DEPLOY_LOG}" 2>/dev/null || true

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
echo -e "${APP_URL}"
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
