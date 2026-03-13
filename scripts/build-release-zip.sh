#!/usr/bin/env bash
set -euo pipefail

# Laravel Shared Hosting Release Builder
# Produces a production-ready zip with vendor + compiled assets.

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT_DIR"

APP_NAME="academic-portal"
TIMESTAMP="$(date +%Y%m%d-%H%M)"
OUTPUT_ZIP="${APP_NAME}-${TIMESTAMP}.zip"
OUTPUT_PATH="${ROOT_DIR}/../${OUTPUT_ZIP}"

BUILD_DEPS=1

usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --skip-build    Skip composer/npm build (use existing vendor/build)"
    echo "  -h, --help      Show help"
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --skip-build) BUILD_DEPS=0 ;;
        -h|--help) usage; exit 0 ;;
        *) echo "Unknown option: $1"; usage; exit 1 ;;
    esac
    shift
done

echo "Project root: $ROOT_DIR"

# ------------------------------------------------
# Install production dependencies
# ------------------------------------------------

if [[ "$BUILD_DEPS" -eq 1 ]]; then
    echo ""
    echo "Installing production PHP dependencies..."
    composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction

    echo ""
    echo "Installing frontend dependencies..."
    pnpm install --frozen-lockfile 2>/dev/null || pnpm install

    echo ""
    echo "Building frontend assets..."
    pnpm run build
fi

# ------------------------------------------------
# Verify Vite build exists
# ------------------------------------------------

if [[ ! -f public/build/manifest.json ]]; then
    echo ""
    echo "ERROR: Vite build missing."
    echo "Expected: public/build/manifest.json"
    echo "Run: pnpm run build"
    exit 1
fi

# ------------------------------------------------
# Ensure Laravel writable directories exist
# ------------------------------------------------

echo ""
echo "Preparing storage directories..."

mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# ------------------------------------------------
# Optimize Laravel for production
# ------------------------------------------------

echo ""
echo "Caching Laravel configuration..."

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# ------------------------------------------------
# Create release archive
# ------------------------------------------------

echo ""
echo "Creating release archive: $OUTPUT_PATH"

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
-x "node_modules/*" \
-x "public/hot" \
-x ".DS_Store" \
-x "Thumbs.db" \
-x "AGENTS.md"

# Re-add env template
zip "$OUTPUT_PATH" .env.example 2>/dev/null || true

echo ""
echo "Release created:"
echo "  $OUTPUT_PATH"
echo ""

echo "Deployment steps:"
echo "1. Upload the zip to your server"
echo "2. Extract above public_html"
echo "3. Move contents of /public -> /public_html"
echo "4. Copy .env.example to .env and configure DB + APP_KEY"
echo "5. Run from project root: ./scripts/deploy.sh (installs deps, migrates, caches, fixes permissions)"
echo ""
echo "Done."
