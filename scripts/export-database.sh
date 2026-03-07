#!/usr/bin/env bash
# Export the project database using Laravel's config.
# Usage: ./scripts/export-database.sh [output.sql]
# Default output: database/exports/database_YYYY-MM-DD_HHMMSS.sql

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$ROOT_DIR"

OUTPUT_FILE="${1:-}"
if [[ -z "$OUTPUT_FILE" ]]; then
  EXPORT_DIR="$ROOT_DIR/database/exports"
  mkdir -p "$EXPORT_DIR"
  OUTPUT_FILE="$EXPORT_DIR/database_$(date +%Y-%m-%d_%H%M%S).sql"
fi

EXPORT_OUTPUT="$OUTPUT_FILE" php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
\$connection = config('database.default');
\$config = config(\"database.connections.{\$connection}\");
\$driver = \$config['driver'] ?? '';

\$output = getenv('EXPORT_OUTPUT');
if (\$output === false || \$output === '') {
    fwrite(STDERR, 'EXPORT_OUTPUT not set' . PHP_EOL);
    exit(1);
}

if (\$driver === 'mysql' || \$driver === 'mariadb') {
    \$host = \$config['host'] ?? '127.0.0.1';
    \$port = \$config['port'] ?? 3306;
    \$database = \$config['database'];
    \$username = \$config['username'];
    \$password = \$config['password'] ?? '';
    if (\$password !== '') {
        putenv('MYSQL_PWD=' . \$password);
    }
    \$cmd = sprintf(
        'mysqldump -h %s -P %s -u %s %s 2>/dev/null',
        escapeshellarg(\$host),
        escapeshellarg(\$port),
        escapeshellarg(\$username),
        escapeshellarg(\$database)
    );
    passthru(\$cmd . ' > ' . escapeshellarg(\$output));
    if (\$password !== '') {
        putenv('MYSQL_PWD');
    }
    exit(0);
}

if (\$driver === 'sqlite') {
    \$path = \$config['database'];
    if (!is_file(\$path)) {
        fwrite(STDERR, 'SQLite database file not found: ' . \$path . PHP_EOL);
        exit(1);
    }
    passthru('sqlite3 ' . escapeshellarg(\$path) . ' .dump > ' . escapeshellarg(\$output) . ' 2>/dev/null');
    exit(0);
}

fwrite(STDERR, 'Unsupported driver for export: ' . \$driver . PHP_EOL);
exit(1);
"

echo "Database exported to: $OUTPUT_FILE"
