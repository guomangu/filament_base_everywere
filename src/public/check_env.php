<?php
echo "APP_KEY from env(): " . env('APP_KEY', 'NULL_VAL') . "<br>";
echo "APP_KEY from getenv(): " . getenv('APP_KEY') . "<br>";
echo "APP_KEY from \$_ENV: " . ($_ENV['APP_KEY'] ?? 'not set') . "<br>";
echo "CWD: " . getcwd() . "<br>";
echo ".env exists in parent: " . (file_exists(__DIR__ . '/../.env') ? 'yes' : 'no') . "<br>";
if (file_exists(__DIR__ . '/../.env')) {
    echo ".env size: " . filesize(__DIR__ . '/../.env') . "<br>";
    echo ".env readable: " . (is_readable(__DIR__ . '/../.env') ? 'yes' : 'no') . "<br>";
}
echo "Full config('app'): <pre>" . print_r(config('app'), true) . "</pre>";
