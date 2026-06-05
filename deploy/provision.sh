#!/usr/bin/env bash
# HistoStains — One-time server provisioning
# Tested on: Ubuntu 24.04 LTS (Noble)
#
# Usage (run as root on a fresh droplet):
#   bash provision.sh <domain> <db_password> [db_name]
#
# Example:
#   bash provision.sh histostains.example.com "s3cr3t_pass" histostains

set -euo pipefail

DOMAIN="${1:?First argument must be the app domain (e.g. histostains.example.com)}"
DB_PASSWORD="${2:?Second argument must be the MySQL password for the app user}"
DB_NAME="${3:-histostains}"
APP_DIR="/var/www/histostains"
DEPLOY_DIR="$(dirname "$(realpath "$0")")"

log() { echo -e "\n\033[1;36m==> $*\033[0m"; }
die() { echo -e "\033[1;31mERROR: $*\033[0m" >&2; exit 1; }

[[ "$(id -u)" -eq 0 ]] || die "Run as root (sudo bash provision.sh ...)"

# ── 1. System updates ──────────────────────────────────────────────────────
log "System updates"
apt-get update -qq
DEBIAN_FRONTEND=noninteractive apt-get upgrade -yq

# ── 2. Core packages ───────────────────────────────────────────────────────
log "Installing core packages"
DEBIAN_FRONTEND=noninteractive apt-get install -yq \
    nginx \
    mysql-server \
    software-properties-common \
    certbot \
    python3-certbot-nginx \
    git \
    curl \
    wget \
    unzip \
    supervisor \
    ufw \
    fail2ban

# ── 3. PHP 8.3 via Ondřej PPA ─────────────────────────────────────────────
log "Installing PHP 8.3"
add-apt-repository ppa:ondrej/php -y
apt-get update -qq
DEBIAN_FRONTEND=noninteractive apt-get install -yq \
    php8.3-fpm \
    php8.3-cli \
    php8.3-mysql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-zip \
    php8.3-gd \
    php8.3-intl \
    php8.3-bcmath \
    php8.3-opcache

# ── 4. Composer ───────────────────────────────────────────────────────────
log "Installing Composer 2"
if ! command -v composer &>/dev/null; then
    curl -sS https://getcomposer.org/installer \
        | php -- --install-dir=/usr/local/bin --filename=composer --2
fi
composer --version

# ── 5. Node.js 20 LTS ─────────────────────────────────────────────────────
log "Installing Node.js 20 LTS"
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
DEBIAN_FRONTEND=noninteractive apt-get install -yq nodejs

# ── 6. MySQL: database + app user ─────────────────────────────────────────
log "Configuring MySQL"
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\`
          CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_NAME}'@'localhost'
          IDENTIFIED BY '${DB_PASSWORD}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_NAME}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# ── 7. App directory + permissions ────────────────────────────────────────
log "Setting up app directory"
mkdir -p "${APP_DIR}"
chown www-data:www-data "${APP_DIR}"

# ── 8. Swap file (prevents OOM on 1–2 GB droplets) ────────────────────────
if [[ ! -f /swapfile ]]; then
    log "Creating 2 GB swap file"
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
fi

# ── 9. Nginx ──────────────────────────────────────────────────────────────
log "Configuring Nginx"
sed "s/histostains.example.com/${DOMAIN}/g" \
    "${DEPLOY_DIR}/nginx.conf" \
    > /etc/nginx/sites-available/histostains

ln -sf /etc/nginx/sites-available/histostains /etc/nginx/sites-enabled/histostains
rm -f /etc/nginx/sites-enabled/default
nginx -t

# ── 10. PHP-FPM pool ──────────────────────────────────────────────────────
log "Configuring PHP-FPM pool"
cp "${DEPLOY_DIR}/php-fpm.conf" /etc/php/8.3/fpm/pool.d/histostains.conf
# Disable the default www pool to avoid resource contention
mv /etc/php/8.3/fpm/pool.d/www.conf /etc/php/8.3/fpm/pool.d/www.conf.disabled 2>/dev/null || true

# ── 11. Supervisor ────────────────────────────────────────────────────────
log "Configuring Supervisor"
cp "${DEPLOY_DIR}/supervisor.conf" /etc/supervisor/conf.d/histostains.conf

# ── 12. Scheduler cron ────────────────────────────────────────────────────
log "Installing scheduler cron"
CRON_LINE="* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1"
(crontab -u www-data -l 2>/dev/null | grep -v "artisan schedule:run" || true
 echo "${CRON_LINE}") | crontab -u www-data -

# ── 13. Firewall ──────────────────────────────────────────────────────────
log "Configuring firewall"
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# ── 14. Fail2ban ──────────────────────────────────────────────────────────
log "Enabling Fail2ban"
systemctl enable fail2ban
systemctl start  fail2ban

# ── 15. Start services ────────────────────────────────────────────────────
log "Starting services"
systemctl enable  nginx mysql php8.3-fpm supervisor
systemctl restart nginx mysql php8.3-fpm supervisor
supervisorctl reread
supervisorctl update

# ── 16. SSL via Certbot ───────────────────────────────────────────────────
log "Obtaining SSL certificate"
certbot --nginx \
    --non-interactive \
    --agree-tos \
    --redirect \
    --domain "${DOMAIN}" \
    --email  "admin@${DOMAIN}"

# ── 17. Auto-renew cron (Certbot installs its own timer, this is a fallback)
(crontab -l 2>/dev/null | grep -v "certbot renew" || true
 echo "0 3 * * * certbot renew --quiet") | crontab -

log "Provisioning complete."
echo ""
echo "  MySQL DB   : ${DB_NAME}"
echo "  MySQL user : ${DB_NAME}@localhost"
echo "  App dir    : ${APP_DIR}"
echo ""
echo "  Next: clone the repo into ${APP_DIR}, copy .env, then:"
echo "        bash ${DEPLOY_DIR}/release.sh --first"
