#!/bin/bash

# ==============================================================================
# God Stack Universal Installer for Debian 13 VPS
# ==============================================================================
set -e

# Configuration
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BIN_DIR="$PROJECT_ROOT/bin"
DATA_DIR="$PROJECT_ROOT/data"
SRC_DIR="$PROJECT_ROOT/src"
LOG_DIR="$DATA_DIR/logs"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}====================================================${NC}"
echo -e "${GREEN}   Starting Universal God Stack Installation...    ${NC}"
echo -e "${GREEN}====================================================${NC}"

# 0. System Checks & Permissions
echo -e "${YELLOW}[1/6] Checking system and permissions...${NC}"

# Check for required system tools
for cmd in curl tar xz git; do
    if ! command -v $cmd &> /dev/null; then
        echo -e "${RED}Error: $cmd is not installed. Please run: sudo apt update && sudo apt install -y $cmd${NC}"
        exit 1
    fi
done

# Fix home directory permissions if strict (typical on some VPS)
USER_HOME="/home/$(whoami)"
if [ -d "$USER_HOME" ]; then
    CURRENT_PERMS=$(stat -c %a "$USER_HOME")
    if [ "$CURRENT_PERMS" != "755" ]; then
        echo "Updating home directory permissions to 755 for web server access..."
        chmod 755 "$USER_HOME"
    fi
fi

# Ensure directories exist
mkdir -p "$BIN_DIR" "$DATA_DIR/mysql" "$DATA_DIR/storage" "$BIN_DIR/lib" "$LOG_DIR" "$BIN_DIR/.core"

# 1. Download Portable Binaries
echo -e "${YELLOW}[2/6] Downloading portable binaries...${NC}"

# FrankenPHP
if [ ! -f "$BIN_DIR/frankenphp" ]; then
    echo "Downloading FrankenPHP..."
    curl -L https://github.com/dunglas/frankenphp/releases/latest/download/frankenphp-linux-x86_64 -o "$BIN_DIR/frankenphp"
    chmod +x "$BIN_DIR/frankenphp"
fi

# Node.js (v20 LTS)
if [ ! -d "$BIN_DIR/node" ]; then
    echo "Downloading Node.js (Portable)..."
    NODE_VERSION="v20.11.1"
    curl -L "https://nodejs.org/dist/$NODE_VERSION/node-$NODE_VERSION-linux-x64.tar.xz" -o node.tar.xz
    tar -xJf node.tar.xz -C "$BIN_DIR"
    mv "$BIN_DIR/node-$NODE_VERSION-linux-x64" "$BIN_DIR/node"
    rm node.tar.xz
fi

# Composer
if [ ! -f "$BIN_DIR/composer.phar" ]; then
    echo "Installing Composer..."
    curl -sS https://getcomposer.org/installer | "$BIN_DIR/frankenphp" php-cli -- --install-dir="$BIN_DIR" --filename=composer.phar
    chmod +x "$BIN_DIR/composer.phar"
fi

# MariaDB (Binary Tarball)
if [ ! -d "$BIN_DIR/mariadb" ]; then
    echo "Downloading MariaDB (Portable)..."
    MARIA_VER="11.4.3"
    MARIADB_URL="https://archive.mariadb.org/mariadb-$MARIA_VER/bintar-linux-systemd-x86_64/mariadb-$MARIA_VER-linux-systemd-x86_64.tar.gz"
    curl -L "$MARIADB_URL" -o mariadb.tar.gz
    tar -xzf mariadb.tar.gz -C "$BIN_DIR"
    mv "$BIN_DIR/mariadb-$MARIA_VER-linux-systemd-x86_64" "$BIN_DIR/mariadb"
    rm mariadb.tar.gz
fi

# PHP Wrapper (Smart - Filters flags only for Artisan)
cat <<EOF > "$BIN_DIR/php"
#!/bin/bash
REAL_SCRIPT=\$(readlink -f "\$0" 2>/dev/null || echo "\$0")
BIN_DIR_PATH=\$(dirname "\$REAL_SCRIPT")
PROJECT_ROOT=\$(cd "\$BIN_DIR_PATH/.." && pwd)

# Check if running 'artisan' to conditionally filter flags
IS_ARTISAN=0
for arg in "\$@"; do
  if [[ "\$arg" == "artisan" ]] || [[ "\$arg" == *"artisan" ]]; then
    IS_ARTISAN=1
    break
  fi
done

params=()
if [ "\$IS_ARTISAN" -eq 1 ]; then
  # Filter -d args for Artisan (FrankenPHP chokes on them)
  while [[ \$# -gt 0 ]]; do
    case "\$1" in
      -d) shift; if [[ \$# -gt 0 ]]; then shift; fi ;;
      -d*) shift ;;
      *) params+=("\$1"); shift ;;
    esac
  done
else
  # Pass everything for Composer/Others
  params=("\$@")
fi

exec "\$PROJECT_ROOT/bin/frankenphp" php-cli "\${params[@]}"
EOF
chmod +x "$BIN_DIR/php"

# Symlink standard PHP for PATH (so 'php -v' works)
ln -sf "$BIN_DIR/php" "$BIN_DIR/.core/php"

# Composer Wrapper
cat <<EOF > "$BIN_DIR/composer"
#!/bin/bash
PROJECT_ROOT="\$(cd "\$(dirname "\${BASH_SOURCE[0]}")" && while [ ! -d bin ] && [ "\$PWD" != "/" ]; do cd ..; done && pwd)"
cd "\$PROJECT_ROOT/src"
exec "\$PROJECT_ROOT/bin/php" "\$PROJECT_ROOT/bin/composer.phar" "\$@"
EOF
chmod +x "$BIN_DIR/composer"

# Artisan Wrapper
cat <<EOF > "$BIN_DIR/artisan"
#!/bin/bash
PROJECT_ROOT="\$(cd "\$(dirname "\${BASH_SOURCE[0]}")" && while [ ! -d bin ] && [ "\$PWD" != "/" ]; do cd ..; done && pwd)"
cd "\$PROJECT_ROOT/src"
exec "\$PROJECT_ROOT/bin/php" artisan "\$@"
EOF
chmod +x "$BIN_DIR/artisan"

# 3. Application Setup
echo -e "${YELLOW}[4/6] Setting up application...${NC}"
cd "$SRC_DIR"

if [ ! -f .env ]; then
    cp .env.example .env
    echo "Created .env from example."
fi

# Configure .env (Database Paths)
SOCK_PATH=$(readlink -f "$DATA_DIR/mysql/mysql.sock")
sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=mariadb|" .env
sed -i "s|^DB_SOCKET=.*|DB_SOCKET=$SOCK_PATH|" .env
sed -i "s|^DB_HOST=.*|DB_HOST=|" .env
sed -i "s|^DB_PORT=.*|DB_PORT=|" .env
sed -i "s|^DB_DATABASE=.*|DB_DATABASE=laravel|" .env
sed -i "s|^DB_USERNAME=.*|DB_USERNAME=$(whoami)|" .env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=|" .env

# Add only core binaries to PATH during install to avoid artisan wrapper conflicts
ln -sf "$BIN_DIR/node/bin/node" "$BIN_DIR/.core/node"
ln -sf "$BIN_DIR/node/bin/npm" "$BIN_DIR/.core/npm"
export PATH="$BIN_DIR/.core:$PATH"
export PHP="$BIN_DIR/php"

# PHP Dependencies
"$BIN_DIR/composer" install --no-interaction --prefer-dist

# App Key
if ! grep -q "^APP_KEY=base64:" .env || [ -z "$(grep "^APP_KEY=" .env | cut -d'=' -f2)" ]; then
    "$BIN_DIR/artisan" key:generate --force
fi

# Node Dependencies
npm install && npm run build

# 4. Database Initialization
echo -e "${YELLOW}[5/6] Initializing database...${NC}"
MARIADB_DIR="$BIN_DIR/mariadb"
MYSQL_DATA="$DATA_DIR/mysql"
MYSQL_PID="$MYSQL_DATA/mariadb.pid"

if [ -z "$(ls -A "$MYSQL_DATA")" ]; then
    "$MARIADB_DIR/scripts/mariadb-install-db" --user=$(whoami) --datadir="$MYSQL_DATA" --basedir="$MARIADB_DIR" --auth-root-authentication-method=normal
fi

# Start temporary MariaDB for seeding
export LD_LIBRARY_PATH="$BIN_DIR/lib:$LD_LIBRARY_PATH"
"$MARIADB_DIR/bin/mariadbd" --no-defaults --datadir="$MYSQL_DATA" --socket="$SOCK_PATH" --pid-file="$MYSQL_PID" --skip-networking --default-storage-engine=InnoDB &
TEMP_DB_PID=$!

# Wait for ready
for i in {1..30}; do
    if [ -S "$SOCK_PATH" ]; then break; fi
    sleep 1
done

if [ ! -S "$SOCK_PATH" ]; then
    echo -e "${RED}MariaDB failed to start during installation.${NC}"
    kill $TEMP_DB_PID || true
    exit 1
fi

"$MARIADB_DIR/bin/mariadb" --socket="$SOCK_PATH" -u root -e "CREATE DATABASE IF NOT EXISTS laravel;"
"$BIN_DIR/artisan" migrate:fresh --seed --force

# Shutdown temp DB
kill $TEMP_DB_PID
wait $TEMP_DB_PID 2>/dev/null || true

# 5. Final Permissions
echo -e "${YELLOW}[6/6] Finalizing permissions...${NC}"
chmod -R 775 storage bootstrap/cache
"$BIN_DIR/artisan" config:clear

echo -e "${GREEN}====================================================${NC}"
echo -e "${GREEN}   Installation Complete! Use ./bin/start.sh       ${NC}"
echo -e "${GREEN}====================================================${NC}"
