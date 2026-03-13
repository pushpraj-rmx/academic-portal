#!/usr/bin/env bash
set -euo pipefail

# Academic Portal – deployment script
# Run from project root. Ensure .env exists and APP_KEY is set before running.

echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Building frontend assets..."
pnpm install --frozen-lockfile 2>/dev/null || pnpm install
pnpm run build

echo "Running migrations..."
php artisan migrate --force

echo "Caching config, routes, views, events and linking storage..."
composer run deploy

# Fix permissions for shared hosting (POSIX-safe; run from project root)
echo "Fixing file permissions for shared hosting..."
if ! [ -f composer.json ] || ! [ -f artisan ]; then
    echo "  Error: Run this script from the project root (where composer.json and artisan exist)."
    exit 1
fi
echo "  Setting directories to 755 (excluding .git, node_modules, vendor)..."
find . -path ./.git -prune -o -path ./node_modules -prune -o -path ./vendor -prune -o -type d -exec chmod 755 {} \;
echo "  Setting files to 644 (excluding .git, node_modules, vendor)..."
find . -path ./.git -prune -o -path ./node_modules -prune -o -path ./vendor -prune -o -type f -exec chmod 644 {} \;
echo "  Restoring executable permission for scripts/..."
find ./scripts -type f -exec chmod 755 {} \; 2>/dev/null || true
echo "  Restoring executable permission for node_modules/.bin/..."
find ./node_modules/.bin -type f -exec chmod 755 {} \; 2>/dev/null || true
echo "  Restoring executable permission for *.sh files..."
find . -path ./.git -prune -o -path ./node_modules -prune -o -path ./vendor -prune -o -name "*.sh" -type f -exec chmod 755 {} \;
echo "  Permissions updated."

echo "Deploy complete."
