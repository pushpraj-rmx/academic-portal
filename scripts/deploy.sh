#!/usr/bin/env bash
set -euo pipefail

# Academic Portal – deployment script
# Run from project root. Ensure .env exists and APP_KEY is set before running.
# Produces an uploadable zip ready for shared web hosting.

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT_DIR"

if ! [ -f composer.json ] || ! [ -f artisan ]; then
    echo "Error: Run this script from the project root (where composer.json and artisan exist)."
    exit 1
fi

echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Building frontend assets..."
pnpm install --frozen-lockfile 2>/dev/null || pnpm install
pnpm run build
if [[ ! -f public/build/manifest.json ]]; then
    echo "Error: Frontend build failed (public/build/manifest.json not found)."
    exit 1
fi
echo "  Build output: public/build/"

echo "Clearing Laravel caches (do not ship cached paths to another machine)..."
php artisan optimize:clear

# Fix permissions for shared hosting (POSIX-safe)
echo "Fixing file permissions for shared hosting..."
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

# Create uploadable release zip (outside project dir so it is not included in the archive)
APP_NAME="academic-portal"
TIMESTAMP="$(date +%Y%m%d-%H%M)"
OUTPUT_ZIP="${APP_NAME}-${TIMESTAMP}.zip"
OUTPUT_PATH="${ROOT_DIR}/../${OUTPUT_ZIP}"

echo "Ensuring Laravel runtime directories exist (so they are in the zip)..."
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/logs
mkdir -p bootstrap/cache

echo ""
echo "Creating release zip: $OUTPUT_PATH"

zip -r "$OUTPUT_PATH" . \
  -x ".git/*" \
  -x ".env" \
  -x ".env.*" \
  -x "node_modules/*" \
  -x "tests/*" \
  -x "docs/*" \
  -x ".cursor/*" \
  -x ".idea/*" \
  -x ".vscode/*" \
  -x ".fleet/*" \
  -x ".nova/*" \
  -x ".zed/*" \
  -x "bootstrap/cache/*.php" \
  -x "storage/logs/*" \
  -x "storage/framework/cache/data/*" \
  -x "storage/framework/sessions/*" \
  -x "storage/framework/views/*" \
  -x ".phpunit.cache/*" \
  -x "*.phpunit.result.cache" \
  -x "*.log" \
  -x "phpunit.xml" \
  -x "phpunit.xml.dist" \
  -x "pest.xml" \
  -x "public/hot" \
  -x ".DS_Store" \
  -x "Thumbs.db" \
  -x "AGENTS.md" \
  -x "*.zip"

zip "$OUTPUT_PATH" .env.example 2>/dev/null || true

# Include .gitkeep so extracted zip has these dirs (they are excluded above by the * patterns)
zip -u "$OUTPUT_PATH" \
  storage/framework/views/.gitkeep \
  storage/framework/cache/.gitkeep \
  storage/framework/cache/data/.gitkeep \
  storage/framework/sessions/.gitkeep \
  storage/logs/.gitkeep \
  bootstrap/cache/.gitkeep \
  2>/dev/null || true

echo ""
echo "Deploy complete. Release zip ready for upload:"
echo "  $OUTPUT_PATH"
echo ""
echo "Next steps for shared hosting:"
echo "  1. Upload the zip to your server"
echo "  2. Extract (e.g. above public_html or in a private directory)"
echo "  3. Point document root to the 'public' folder, or move public/* to public_html"
echo "  4. Copy .env.example to .env and set APP_KEY, DB_*, etc."
echo "  5. Run: php artisan storage:link (if not already linked)"
echo "  6. Run: php artisan migrate --force"
echo "  7. Ensure storage and bootstrap/cache are writable (e.g. chmod -R 775)"
echo ""
