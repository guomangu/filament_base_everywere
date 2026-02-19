#!/bin/bash

# ==============================================================================
# God Stack Universal Starter for Debian 13 VPS
# ==============================================================================

# Configuration
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BIN_DIR="$PROJECT_ROOT/bin"
DATA_DIR="$PROJECT_ROOT/data"
SRC_DIR="$PROJECT_ROOT/src"
LOG_DIR="$DATA_DIR/logs"

MARIADB_DIR="$BIN_DIR/mariadb"
MYSQL_DATA="$DATA_DIR/mysql"
MYSQL_SOCKET="$MYSQL_DATA/mysql.sock"
MYSQL_PID="$MYSQL_DATA/mariadb.pid"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Check if installed
if [ ! -f "$BIN_DIR/frankenphp" ] || [ ! -d "$MARIADB_DIR" ]; then
    echo -e "${YELLOW}Environment not detected. Running installer...${NC}"
    "$BIN_DIR/install.sh"
fi

# Cleanup stale processes
echo -e "${YELLOW}Cleaning up stale processes...${NC}"
if [ -f "$MYSQL_PID" ]; then
    OLD_PID=$(cat "$MYSQL_PID")
    if ps -p $OLD_PID > /dev/null; then
        kill $OLD_PID
        sleep 2
    fi
    rm -f "$MYSQL_PID"
fi
pkill -f "$BIN_DIR/frankenphp" || true

# Start MariaDB
# Start MariaDB
echo -e "${GREEN}Starting MariaDB...${NC}"
export LD_LIBRARY_PATH="$BIN_DIR/lib:$LD_LIBRARY_PATH"

DB_USER_FLAG=""
if [ "$(id -u)" = "0" ]; then
    # If running as root, force MariaDB to run as the owner of this script
    SCRIPT_OWNER=$(stat -c '%U' "${BASH_SOURCE[0]}")
    DB_USER_FLAG="--user=$SCRIPT_OWNER"
    echo "Running MariaDB as user: $SCRIPT_OWNER"
fi

"$MARIADB_DIR/bin/mariadbd" --no-defaults --datadir="$MYSQL_DATA" --socket="$MYSQL_SOCKET" --pid-file="$MYSQL_PID" --skip-networking --default-storage-engine=InnoDB $DB_USER_FLAG >> "$LOG_DIR/mariadb.log" 2>&1 &
MARIADB_PID=$!

# Wait for MariaDB
for i in {1..30}; do
    if [ -S "$MYSQL_SOCKET" ]; then break; fi
    if [ -S "$MYSQL_SOCKET" ]; then break; fi
    sleep 1
done

# Ensure DB_HOST is localhost for socket connection (prevent TCP fallback)
cd "$SRC_DIR"
if grep -q "^DB_HOST=$" .env || ! grep -q "^DB_HOST=localhost" .env; then
    echo -e "${YELLOW}Enforcing DB_HOST=localhost for socket connection...${NC}"
    sed -i "s|^DB_HOST=.*|DB_HOST=localhost|" .env
    "$BIN_DIR/artisan" config:clear
fi

if [ ! -S "$MYSQL_SOCKET" ]; then
    echo -e "${RED}Error: MariaDB failed to start. Check $LOG_DIR/mariadb.log${NC}"
    exit 1
fi

# Sync Schema
# Sync Schema & Clear Config
echo -e "${GREEN}Syncing database schema and clearing config...${NC}"
"$BIN_DIR/artisan" config:clear
"$BIN_DIR/artisan" migrate --force

# Start FrankenPHP
PORT=${1:-80}
echo -e "${GREEN}Starting FrankenPHP on port $PORT...${NC}"

# If port 80 is requested, we might need to stop the system service
if [ "$PORT" = "80" ]; then
    if systemctl is-active --quiet frankenphp; then
        echo -e "${YELLOW}System FrankenPHP service detected on port 80. Stopping it...${NC}"
        sudo systemctl stop frankenphp || echo -e "${RED}Warning: Could not stop system frankenphp. Port conflict likely.${NC}"
    fi
    
    # Check if we can bind to port 80 (need root or setcap)
    if [ "$(id -u)" != "0" ]; then
        echo -e "${YELLOW}Note: Binding to port 80 usually requires root. If this fails, try: sudo ./bin/start.sh${NC}"
    fi
fi

cd "$SRC_DIR"
# Use setcap or sudo for port 80 if necessary, but here we just try
"$BIN_DIR/frankenphp" php-server --listen ":$PORT" --root "$SRC_DIR/public" >> "$LOG_DIR/frankenphp.log" 2>&1 &
FRANKEN_PID=$!

# Trap for clean shutdown
cleanup() {
    echo -e "\n${YELLOW}Stopping services...${NC}"
    kill $FRANKEN_PID $MARIADB_PID 2>/dev/null || true
    wait 2>/dev/null
    echo -e "${GREEN}Shutdown complete.${NC}"
}
trap cleanup SIGINT SIGTERM

echo -e "${GREEN}God Stack is running at http://$(curl -s ifconfig.me):$PORT${NC}"
echo -e "${YELLOW}Press Ctrl+C to stop.${NC}"
wait
