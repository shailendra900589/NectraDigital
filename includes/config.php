<?php
if (defined('NECTRA_CONFIG_LOADED')) {
    return;
}
define('NECTRA_CONFIG_LOADED', true);

// Local overrides (Hostinger: copy config.local.php.example → config.local.php)
if (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

if (php_sapi_name() !== 'cli') {
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    header('Referrer-Policy: no-referrer-when-downgrade');
}

if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (php_sapi_name() !== 'cli' && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Nectra Digital');
}
if (!defined('SITE_URL')) {
    define('SITE_URL', 'https://www.nectradigital.com');
}

if (php_sapi_name() !== 'cli') {
    error_reporting(0);
}
