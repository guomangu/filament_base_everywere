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

# 2. Install Composer
if [ ! -f "$BIN_DIR/composer" ]; then
    echo "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir="$BIN_DIR" --filename=composer
    chmod +x "$BIN_DIR/composer"
    echo "Composer installed."
else
    echo "Composer already installed."
fi

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
        echo "Created .env file. Please configure database path if needed."
        # Auto-configure socket path
        SOCK_PATH=$(readlink -f "$DATA_DIR/mysql/mysql.sock")
        echo "DB_SOCKET=$SOCK_PATH" >> .env
        echo "DB_CONNECTION=mariadb" >> .env
        echo "DB_HOST=" >> .env
        echo "DB_PORT=" >> .env
        echo "DB_DATABASE=laravel" >> .env
        echo "DB_USERNAME=$(whoami)" >> .env
        echo "DB_PASSWORD=" >> .env
        
        "$PHP_BINARY" artisan key:generate
    fi
    
    "$PHP_BINARY" "$BIN_DIR/composer" install
    
    echo -e "${GREEN}Dependencies installed.${NC}"
fi

echo -e "${GREEN}Installation Complete! You can now run ./bin/start.sh${NC}"
