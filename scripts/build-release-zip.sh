#!/usr/bin/env bash
set -euo pipefail

# Academic Portal – build a zip for shared web hosting
# Run from project root. Produces a zip with vendor + built assets, no .env or dev files.

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT_DIR"

RELEASE_NAME="academic-portal"
TIMESTAMP="$(date +%Y%m%d-%H%M)"
OUTPUT_ZIP="${RELEASE_NAME}-${TIMESTAMP}.zip"
# Output next to project root (parent directory)
OUTPUT_PATH="${ROOT_DIR}/../${OUTPUT_ZIP}"

BUILD_DEPS="${BUILD_DEPS:-1}"

usage() {
    echo "Usage: $0 [OPTIONS]"
    echo "  Builds a zip suitable for shared hosting (no .git, .env, node_modules, tests)."
    echo ""
    echo "Options:"
    echo "  --skip-build    Skip composer install and pnpm build (use existing vendor/ and public/build/)"
    echo "  -h, --help      Show this help"
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --skip-build) BUILD_DEPS=0 ;;
        -h|--help) usage; exit 0 ;;
        *) echo "Unknown option: $1"; usage; exit 1 ;;
    esac
    shift
done

if [[ "$BUILD_DEPS" -eq 1 ]]; then
    echo "Installing PHP dependencies (production)..."
    composer install --no-dev --optimize-autoloader --no-interaction

    echo "Building frontend assets..."
    pnpm install --frozen-lockfile 2>/dev/null || pnpm install
    pnpm run build
    echo ""
fi

echo "Creating release zip: $OUTPUT_PATH"

zip -r "$OUTPUT_PATH" . \
    -x ".git/*" \
    -x ".env" \
    -x ".env.backup" \
    -x ".env.production" \
    -x ".env.*" \
    -x "node_modules/*" \
    -x "package-lock.json" \
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
    -x "storage/*.key" \
    -x "storage/pail/*" \
    -x ".phpunit.cache/*" \
    -x "*.phpunit.result.cache" \
    -x "*.log" \
    -x "tests/*" \
    -x "public/hot" \
    -x "public/storage" \
    -x ".DS_Store" \
    -x "Thumbs.db" \
    -x "AGENTS.md" \
    -x "Homestead.json" \
    -x "Homestead.yaml" \
    -x "auth.json" \
    -x "phpunit.xml" \
    -x "phpunit.xml.dist" \
    -x "pest.xml" \
    -x "docs/*" \
    -x "extra/*" \
    -x "jhjsda/*"

# Re-add .env.example so host can copy to .env
zip "$OUTPUT_PATH" .env.example 2>/dev/null || true

echo ""
echo "Done. Upload this file to your shared host:"
echo "  $OUTPUT_PATH"
echo ""
echo "On the server: extract, set document root to the 'public' folder, copy .env.example to .env,"
echo "set APP_KEY (php artisan key:generate) and DB_*, then run:"
echo "  php artisan storage:link"
echo "  php artisan config:cache && php artisan route:cache && php artisan view:cache"
