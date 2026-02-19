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
echo -e "${GREEN}   God Stack Universal Installer v2.1              ${NC}"
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

# Polyfill missing libraries (specifically libncurses.so.5 for MariaDB on Debian 13)
echo -e "${YELLOW}Configuring Library Polyfills...${NC}"

# Ensure we can use system tools
export PATH=$PATH:/usr/sbin:/sbin

# Precise search order:
# 1. Exact Debian/Ubuntu Multiarch locations (Fastest, Safest)
# 2. ldconfig cache (Reliable if updated)
# 3. Recursive find (Slow, fallback)

POSSIBLE_NCURSES=(
    "/usr/lib/x86_64-linux-gnu/libncurses.so.6"
    "/lib/x86_64-linux-gnu/libncurses.so.6"
    "/usr/lib64/libncurses.so.6"
    "/lib64/libncurses.so.6"
    "/usr/lib/libncurses.so.6"
    "/lib/libncurses.so.6"
)

FOUND_LIB=""

echo "Debug: Checking specific paths..."
for path in "${POSSIBLE_NCURSES[@]}"; do
    if [ -f "$path" ]; then
        FOUND_LIB="$path"
        echo "  -> Found at $path"
        break
    fi
done

if [ -z "$FOUND_LIB" ]; then
    echo "Debug: Checking ldconfig..."
    # nuances: awk prints last field which is the path
    FOUND_LIB=$(ldconfig -p 2>/dev/null | grep "libncurses.so.6" | head -n 1 | awk '{print $NF}')
fi

if [ -z "$FOUND_LIB" ]; then
    echo "Debug: Running broad search (this may take a moment)..."
    FOUND_LIB=$(find /usr/lib /lib /usr/lib64 /lib64 -name "libncurses.so.6*" -print -quit 2>/dev/null)
fi

# Fallback to version 5 if systems actually have it but it's just not in path
if [ -z "$FOUND_LIB" ]; then
    FOUND_LIB=$(find /usr/lib /lib /usr/lib64 /lib64 -name "libncurses.so.5*" -print -quit 2>/dev/null)
fi


if [ -n "$FOUND_LIB" ]; then
    echo "Polyfilling libncurses.so.5 using: $FOUND_LIB"
    ln -sf "$FOUND_LIB" "$BIN_DIR/lib/libncurses.so.5"
    
    # Setup libtinfo.so.5 (often required alongside ncurses)
    # 1. Try to find a sibling tinfo
    TINFO_CANDIDATE=$(echo "$FOUND_LIB" | sed 's/ncurses/tinfo/')
    
    if [ -f "$TINFO_CANDIDATE" ]; then
         echo "  -> Found sibling libtinfo: $TINFO_CANDIDATE"
         ln -sf "$TINFO_CANDIDATE" "$BIN_DIR/lib/libtinfo.so.5"
    else
         # 2. Try to find tinfo via ldconfig
         TINFO_SYS=$(ldconfig -p 2>/dev/null | grep "libtinfo.so.6" | head -n 1 | awk '{print $NF}')
         if [ -n "$TINFO_SYS" ]; then
              echo "  -> Found system libtinfo: $TINFO_SYS"
              ln -sf "$TINFO_SYS" "$BIN_DIR/lib/libtinfo.so.5"
         else
              # 3. Fallback: specific debian path check for tinfo
              if [ -f "/usr/lib/x86_64-linux-gnu/libtinfo.so.6" ]; then
                   ln -sf "/usr/lib/x86_64-linux-gnu/libtinfo.so.6" "$BIN_DIR/lib/libtinfo.so.5"
              else
                   # 4. Last resort: Link ncurses to tinfo (often works as they are bundled)
                   echo "  -> Warning: Could not find libtinfo independently. Aliasing to libncurses."
                   ln -sf "$FOUND_LIB" "$BIN_DIR/lib/libtinfo.so.5"
              fi
         fi
    fi
else
    echo -e "${RED}CRITICAL WARNING: Could not detect libncurses.so.6 or .5 on this system.${NC}"
    echo "Database initialization will likely fail."
    echo "Debug info:"
    uname -a
    echo "ldconfig output for ncurses:"
    ldconfig -p | grep curses || echo "No curses in ldconfig"
fi

echo "Debug: Final contents of logical library path ($BIN_DIR/lib):"
ls -l "$BIN_DIR/lib"

# Try to install libncurses5 if possible and polyfill failed
if [ ! -f "$BIN_DIR/lib/libncurses.so.5" ]; then
    echo "Attempting to install libncurses5 via apt..."
    sudo apt update && sudo apt install -y libncurses5 || echo "libncurses5 package not found, relying on polyfill."
fi

# PHP Wrapper (Smart - Filters flags only for Artisan)
cat <<EOF > "$BIN_DIR/php"
#!/bin/bash
REAL_SCRIPT=\$(readlink -f "\$0" 2>/dev/null || echo "\$0")
BIN_DIR_PATH=\$(dirname "\$REAL_SCRIPT")
PROJECT_ROOT=\$(cd "\$BIN_DIR_PATH/.." && pwd)

# Check if this is an Artisan call
ARGS=("\$@")
ARTISAN_INDEX=-1

for i in "\${!ARGS[@]}"; do
    if [[ "\${ARGS[\$i]}" == "artisan" ]] || [[ "\${ARGS[\$i]}" == *"artisan" ]]; then
        ARTISAN_INDEX=\$i
        break
    fi
done

if [ \$ARTISAN_INDEX -ge 0 ]; then
    # Found artisan! 
    # Discard everything before (flags, composer) AND the artisan arg itself.
    # Use ABSOLUTE path to src/artisan to avoid any ambiguity or "Path empty" errors.
    
    # Get args AFTER artisan matching index
    REST_ARGS=("${ARGS[@]:$((ARTISAN_INDEX + 1))}")
    
    exec "\$PROJECT_ROOT/bin/frankenphp" php-cli "\$PROJECT_ROOT/src/artisan" "\${REST_ARGS[@]}"
else
    # Standard execution (Composer, etc)
    exec "\$PROJECT_ROOT/bin/frankenphp" php-cli "\$@"
fi
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

# Export Library Path GLOBALLY for this section so all mariadb tools see the polyfill
export LD_LIBRARY_PATH="$BIN_DIR/lib:$LD_LIBRARY_PATH"

# Force kill any stale mariadbd processes to avoid file lock errors (error 11)
echo "Ensuring no stale Database processes are running..."
pkill -f "$MARIADB_DIR/bin/mariadbd" || true
sleep 2

if [ -z "$(ls -A "$MYSQL_DATA")" ]; then
    echo "Installing default system tables..."
    "$MARIADB_DIR/scripts/mariadb-install-db" --user=$(whoami) --datadir="$MYSQL_DATA" --basedir="$MARIADB_DIR" --auth-root-authentication-method=normal
fi

# Start temporary MariaDB for seeding
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

    exit 1
fi

echo "Creating 'laravel' database..."
# Try running mariadb client. If it fails, capture debug info.
if ! "$MARIADB_DIR/bin/mariadb" --socket="$SOCK_PATH" -u root -e "CREATE DATABASE IF NOT EXISTS laravel;"; then
    echo -e "${RED}MariaDB Client failed. Debugging libraries...${NC}"
    ldd "$MARIADB_DIR/bin/mariadb"
    export LD_DEBUG=libs
    "$MARIADB_DIR/bin/mariadb" --socket="$SOCK_PATH" -u root -e "CREATE DATABASE IF NOT EXISTS laravel;"
    exit 1
fi

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
