#!/usr/bin/env bash
# HistoStains — Production release script
# Run as www-data (or any user with write access to APP_DIR):
#   sudo -u www-data bash deploy/release.sh
#
# First-ever deploy:
#   sudo -u www-data bash deploy/release.sh --first
#
# Options:
#   --first    Seed the DB, generate keys, skip maintenance mode
#   --branch   Git branch to deploy (default: main)
#   --no-build Skip npm build (use when assets were built in CI and uploaded)

set -euo pipefail

APP_DIR="/var/www/histostains"
BRANCH="main"
FIRST_DEPLOY=false
SKIP_BUILD=false

for arg in "$@"; do
    case "$arg" in
        --first)     FIRST_DEPLOY=true  ;;
        --no-build)  SKIP_BUILD=true    ;;
        --branch=*)  BRANCH="${arg#*=}" ;;
    esac
done

log()  { echo -e "\n\033[1;36m==> $*\033[0m"; }
ok()   { echo -e "\033[1;32m    ✓ $*\033[0m"; }
fail() { echo -e "\033[1;31m    ✗ $*\033[0m"; exit 1; }

cd "${APP_DIR}"

# ── 1. Pull latest code ───────────────────────────────────────────────────
log "Pulling ${BRANCH}"
git fetch --all
git checkout "${BRANCH}"
git reset --hard "origin/${BRANCH}"
ok "Code up to date"

# ── 2. PHP dependencies ───────────────────────────────────────────────────
log "Installing PHP dependencies"
composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist
ok "Composer done"

# ── 3. Node / JS build (skip with --no-build if CI uploads build/ already) ─
if [[ "${SKIP_BUILD}" == "false" ]]; then
    log "Building frontend assets"
    npm ci --omit=dev
    npm run build
    ok "Vite build done"
else
    ok "Skipping npm build (--no-build)"
fi

# ── 4. First-deploy only ──────────────────────────────────────────────────
if [[ "${FIRST_DEPLOY}" == "true" ]]; then
    log "First-deploy setup"

    [[ -f .env ]] || fail ".env not found — copy deploy/env.production.example to .env and fill in values"

    php artisan key:generate --force
    ok "App key set"

    php artisan webpush:vapid
    ok "VAPID keys generated"

    php artisan storage:link
    ok "Storage link created"

    php artisan migrate --force
    ok "Migrations ran"

    php artisan db:seed --class=AdminSeeder
    ok "Admin seeded  (admin@histostains.local / admin1234 — change immediately)"

else
    # ── 5. Rolling deploy ─────────────────────────────────────────────────

    log "Entering maintenance mode"
    php artisan down --render="errors::503" --retry=15

    log "Running migrations"
    php artisan migrate --force
    ok "Migrations ran"
fi

# ── 6. Cache warm-up ──────────────────────────────────────────────────────
log "Caching config / routes / views / events"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
ok "Caches warmed"

# ── 7. Restart queue workers ──────────────────────────────────────────────
log "Restarting queue workers"
php artisan queue:restart          # signals workers to stop after current job
supervisorctl restart histostains-worker:* 2>/dev/null || true
ok "Queue workers restarted"

# ── 8. Leave maintenance mode ─────────────────────────────────────────────
if [[ "${FIRST_DEPLOY}" == "false" ]]; then
    php artisan up
    ok "App is live"
fi

# ── 9. Reload PHP-FPM (picks up any .env or INI changes) ──────────────────
log "Reloading PHP-FPM"
systemctl reload php8.3-fpm 2>/dev/null || true
ok "PHP-FPM reloaded"

echo ""
echo -e "\033[1;32m✓ Deploy complete — $(git log -1 --format='%h %s')\033[0m"
