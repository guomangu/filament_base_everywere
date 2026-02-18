#!/bin/bash

# Configuration
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BIN_DIR="$PROJECT_ROOT/bin"
DATA_DIR="$PROJECT_ROOT/data"
SRC_DIR="$PROJECT_ROOT/src"

MARIADB_DIR="$BIN_DIR/mariadb"
MYSQL_DATA="$DATA_DIR/mysql"
MYSQL_SOCKET="$MYSQL_DATA/mysql.sock"
MYSQL_PID="$MYSQL_DATA/mariadb.pid"

# Check for binaries
if [ ! -f "$MARIADB_DIR/bin/mariadbd" ] || [ ! -f "$BIN_DIR/frankenphp" ] || [ ! -f "$SRC_DIR/.env" ]; then
    echo "Environment not initialized. Running installer..."
    "$BIN_DIR/install.sh"
fi

# Cleanup previous instances
if [ -f "$MYSQL_PID" ]; then
    PID=$(cat "$MYSQL_PID")
    if ps -p $PID > /dev/null; then
        echo "Stopping previous MariaDB instance (PID: $PID)..."
        kill $PID
        sleep 2
    fi
    rm -f "$MYSQL_PID"
fi

# Kill any stray processes from this specific project
pkill -f "$PROJECT_ROOT/bin/frankenphp" || true
# Ensure directories exist
mkdir -p "$MYSQL_DATA"


# Add local libraries to path for MariaDB CLI
export LD_LIBRARY_PATH="$BIN_DIR/lib:$LD_LIBRARY_PATH"

# Initialize MariaDB if data directory is empty
if [ -z "$(ls -A "$MYSQL_DATA")" ]; then
    echo "Initializing MariaDB..."
    "$MARIADB_DIR/scripts/mariadb-install-db" --user=$(whoami) --datadir="$MYSQL_DATA" --basedir="$MARIADB_DIR" > /dev/null
fi

# Start MariaDB
echo "Starting MariaDB..."
"$MARIADB_DIR/bin/mariadbd" --no-defaults --datadir="$MYSQL_DATA" --socket="$MYSQL_SOCKET" --pid-file="$MYSQL_PID" --skip-networking --default-storage-engine=InnoDB &
MARIADB_PID=$!

# Wait for MariaDB to be ready
echo "Waiting for MariaDB..."
for i in {1..30}; do
    if [ -S "$MYSQL_SOCKET" ]; then
        break
    fi
    sleep 1
done

if [ ! -S "$MYSQL_SOCKET" ]; then
    echo "Error: MariaDB failed to start."
    exit 1
fi

echo "MariaDB started."

# Ensure database exists
echo "Ensuring database exists..."
"$MARIADB_DIR/bin/mariadb" --socket="$MYSQL_SOCKET" -u root -e "CREATE DATABASE IF NOT EXISTS laravel;"

# Run Migrations automatically to keep DB sync with code
echo "Updating database schema if needed..."
"$BIN_DIR/artisan" migrate --force

# Start Reverb (Background) - Disabled until configured
# echo "Starting Reverb..."
# cd "$SRC_DIR"
# "$BIN_DIR/php" artisan reverb:start &
# REVERB_PID=$!

# Start FrankenPHP
echo "Starting FrankenPHP..."
cd "$SRC_DIR"
# Use FrankenPHP to serve the app
# Note: FrankenPHP 'php-server' command or just 'run' command depending on config.
# For Laravel, usually: frankenphp run --config Caddyfile
# But for simple serve:
"$BIN_DIR/frankenphp" php-server --listen :8000 --root "$SRC_DIR/public" &
FRANKEN_PID=$!

# Trap cleanup
cleanup() {
    echo "Stopping services..."
    kill $MARIADB_PID $REVERB_PID $FRANKEN_PID
    wait
    echo "Done."
}
trap cleanup SIGINT SIGTERM

echo "God Stack is running on http://localhost:8000"
wait
