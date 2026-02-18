#!/bin/bash

# Configuration
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BIN_DIR="$PROJECT_ROOT/bin"
DATA_DIR="$PROJECT_ROOT/data"
SRC_DIR="$PROJECT_ROOT/src"

# Colors
GREEN='\033[0;32m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting God Stack Installation...${NC}"

# Ensure directories exist
mkdir -p "$BIN_DIR" "$DATA_DIR/mysql" "$DATA_DIR/storage" "$BIN_DIR/lib"

# 1. Install FrankenPHP
if [ ! -f "$BIN_DIR/frankenphp" ]; then
    echo "Downloading FrankenPHP..."
    curl -L https://github.com/dunglas/frankenphp/releases/latest/download/frankenphp-linux-x86_64 -o "$BIN_DIR/frankenphp"
    chmod +x "$BIN_DIR/frankenphp"
    echo "FrankenPHP installed."
else
    echo "FrankenPHP already installed."
fi

# 1.1 Install Node.js (Portable) for MCP/Frontend
if [ ! -d "$BIN_DIR/node" ]; then
    echo "Downloading Node.js (Portable)..."
    curl -L https://nodejs.org/dist/v20.11.1/node-v20.11.1-linux-x64.tar.xz -o node.tar.xz
    tar -xJf node.tar.xz -C "$BIN_DIR"
    mv "$BIN_DIR/node-v20.11.1-linux-x64" "$BIN_DIR/node"
    rm node.tar.xz
    echo "Node.js installed."
else
    echo "Node.js already installed."
fi

# 2. Install Composer
if [ ! -f "$BIN_DIR/composer.phar" ]; then
    echo "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir="$BIN_DIR" --filename=composer.phar
    chmod +x "$BIN_DIR/composer.phar"
    echo "Composer installed."
else
    echo "Composer already installed."
fi

# Create bin/composer wrapper
cat <<EOF > "$BIN_DIR/composer"
#!/bin/bash
PROJECT_ROOT="\$(cd "\$(dirname "\${BASH_SOURCE[0]}")/.." && pwd)"
cd "\$PROJECT_ROOT/src"
exec "\$PROJECT_ROOT/bin/php" "\$PROJECT_ROOT/bin/composer.phar" "\$@"
EOF
chmod +x "$BIN_DIR/composer"

# Create bin/artisan wrapper
cat <<EOF > "$BIN_DIR/artisan"
#!/bin/bash
PROJECT_ROOT="\$(cd "\$(dirname "\${BASH_SOURCE[0]}")/.." && pwd)"
cd "\$PROJECT_ROOT/src"
exec "\$PROJECT_ROOT/bin/php" artisan "\$@"
EOF
chmod +x "$BIN_DIR/artisan"

# 3. Download/Extract MariaDB
if [ ! -d "$BIN_DIR/mariadb" ]; then
    echo "Downloading MariaDB (Portable)..."
    # Using specific version 11.4.3
    # Fallback to archive link if needed
    MARIADB_URL="https://archive.mariadb.org/mariadb-11.4.3/bintar-linux-systemd-x86_64/mariadb-11.4.3-linux-systemd-x86_64.tar.gz"
    
    curl -L "$MARIADB_URL" -o mariadb.tar.gz
    
    echo "Extracting MariaDB..."
    tar -xzf mariadb.tar.gz -C "$BIN_DIR"
    mv "$BIN_DIR/mariadb-11.4.3-linux-systemd-x86_64" "$BIN_DIR/mariadb"
    rm mariadb.tar.gz
    echo "MariaDB installed."
else
    echo "MariaDB already installed."
fi

# 4. Library Hack (for libncurses 5)
# Check if we need to create symlinks
TARGET_LIB="$BIN_DIR/lib/libncurses.so.5"
TARGET_TINFO="$BIN_DIR/lib/libtinfo.so.5"

if [ ! -f "$TARGET_LIB" ] || [ ! -f "$TARGET_TINFO" ]; then
    echo "Checking for libncurses compatibility..."
    
    # Try to find libncurses.so.6
    SYS_LIB=$(find /usr/lib -name "libncurses.so.6" 2>/dev/null | head -n 1)
    if [ -n "$SYS_LIB" ] && [ ! -f "$TARGET_LIB" ]; then
         echo "Symlinking $SYS_LIB to $TARGET_LIB"
         ln -s "$SYS_LIB" "$TARGET_LIB"
    fi

    
    if [ ! -f "$TARGET_TINFO" ]; then
        # Find system libtinfo.so.6
        SYS_TINFO=$(find /usr/lib -name "libtinfo.so.6" 2>/dev/null | head -n 1)
        if [ -n "$SYS_TINFO" ]; then
             echo "Symlinking $SYS_TINFO to $TARGET_TINFO"
             ln -s "$SYS_TINFO" "$TARGET_TINFO"
        fi
    fi
fi

# 5. Project Setup (Composer)
if [ -d "$SRC_DIR" ]; then
    echo "Installing PHP Dependencies..."
    # Ensure our wrapper exists (created by install or manually, but let's recreate just in case)
    if [ ! -f "$BIN_DIR/php" ]; then
        cat <<EOF > "$BIN_DIR/php"
#!/bin/bash
# Wrapper to use FrankenPHP as standard PHP CLI
params=()
while [[ \$# -gt 0 ]]; do
  case "\$1" in
    -d) shift; if [[ \$# -gt 0 ]]; then shift; fi ;;
    -d*) shift ;;
    *) params+=("\$1"); shift ;;
  esac
done
exec "\$(dirname "\$0")/frankenphp" php-cli "\${params[@]}"
EOF
        chmod +x "$BIN_DIR/php"
    fi

    # Use our PHP wrapper
    export PHP_BINARY="$BIN_DIR/php"
    
    cd "$SRC_DIR"
    if [ ! -f .env ]; then
        cp .env.example .env
        echo "Created .env file."
        "$PHP_BINARY" artisan key:generate
    fi

    # Auto-configure .env using sed to avoid duplicates
    update_env() {
        local key=$1
        local value=$2
        if grep -q "^${key}=" .env; then
            sed -i "s|^${key}=.*|${key}=${value}|" .env
        else
            echo "${key}=${value}" >> .env
        fi
    }

    SOCK_PATH=$(readlink -f "$DATA_DIR/mysql/mysql.sock")
    update_env "DB_CONNECTION" "mariadb"
    update_env "DB_SOCKET" "$SOCK_PATH"
    update_env "DB_HOST" ""
    update_env "DB_PORT" ""
    update_env "DB_DATABASE" "laravel"
    update_env "DB_USERNAME" "$(whoami)"
    update_env "DB_PASSWORD" ""

    # PHP Dependencies
    "$BIN_DIR/composer" install
    
    # Node.js Dependencies
    echo "Installing Node.js Dependencies..."
    export PATH="$BIN_DIR/node/bin:$PATH"
    npm install
    npm run build

    # Initial Database Setup (Seeding the scenario)
    echo "Initializing MariaDB for first time..."
    MARIADB_DIR="$BIN_DIR/mariadb"
    MYSQL_DATA="$DATA_DIR/mysql"
    MYSQL_SOCKET="$SOCK_PATH"
    MYSQL_PID="$MYSQL_DATA/mariadb.pid"

    mkdir -p "$MYSQL_DATA"
    if [ -z "$(ls -A "$MYSQL_DATA")" ]; then
        "$MARIADB_DIR/scripts/mariadb-install-db" --user=$(whoami) --datadir="$MYSQL_DATA" --basedir="$MARIADB_DIR" > /dev/null
    fi

    # Start temp MariaDB
    "$MARIADB_DIR/bin/mariadbd" --no-defaults --datadir="$MYSQL_DATA" --socket="$MYSQL_SOCKET" --pid-file="$MYSQL_PID" --skip-networking --default-storage-engine=InnoDB &
    TEMP_DB_PID=$!

    # Wait for ready
    echo "Waiting for MariaDB to initialize..."
    for i in {1..30}; do
        if [ -S "$MYSQL_SOCKET" ]; then break; fi
        sleep 1
    done

    # Create DB, Migrate and Seed
    "$MARIADB_DIR/bin/mariadb" --socket="$MYSQL_SOCKET" -u root -e "CREATE DATABASE IF NOT EXISTS laravel;"
    "$PHP_BINARY" artisan migrate:fresh --seed --force

    # Shutdown temp MariaDB
    kill $TEMP_DB_PID
    wait $TEMP_DB_PID 2>/dev/null
    
    echo -e "${GREEN}Dependencies installed and Database seeded.${NC}"
fi

echo -e "${GREEN}Installation Complete! You can now run ./bin/start.sh${NC}"
