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

echo "Deploy complete."
