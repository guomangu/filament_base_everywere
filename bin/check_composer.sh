#!/bin/bash
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
COMPOSER_BIN="$PROJECT_ROOT/bin/composer-bin"
if [ ! -f "$COMPOSER_BIN" ]; then
    # If we renamed the phar to composer-bin in install.sh, use it.
    # Otherwise check if 'composer' is the phar in bin
    if file "$PROJECT_ROOT/bin/composer" | grep -q "PHP script"; then
        COMPOSER_BIN="$PROJECT_ROOT/bin/composer"
    else
        # Fallback if composer command is not the phar but this script itself (avoid loop)
        echo "Error: Cannot find composer.phar"
        exit 1
    fi
fi

# We need to be careful not to execute this script recursively if we name it 'composer'
# So we will rename the actual phar to composer.phar in install.sh
# For now, let's assume bin/composer is the PHAR and we make bin/composer-wrapper
# Actually, better to just use 'php composer.phar' pattern or similar.

# Let's check what bin/composer IS right now.
