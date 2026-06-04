#!/usr/bin/env bash
# HistoStains — One-time local dev setup script (run inside WSL)
# Usage: bash setup-dev.sh

set -e
cd "$(dirname "$0")"

echo "==> Installing PHP SQLite extension..."
sudo apt-get install -y php8.3-sqlite3

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding admin user..."
php artisan db:seed --class=AdminSeeder

echo "==> Creating storage symlink..."
php artisan storage:link

echo ""
echo "✓ Setup complete."
echo ""
echo "  Admin login:  admin@histostains.local / admin1234"
echo ""
echo "  Start dev server:"
echo "    Terminal 1: php artisan serve"
echo "    Terminal 2: npm run dev"
echo ""
echo "  App URL: http://localhost:8000"
