<?php
// Local overrides (Hostinger: copy config.local.php.example → config.local.php)
if (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

// 1. SECURITY HEADERS (skip in CLI)
if (php_sapi_name() !== 'cli') {
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
    header("X-Content-Type-Options: nosniff");
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    header("Referrer-Policy: no-referrer-when-downgrade");
}

// 2. SESSION & CSRF
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (php_sapi_name() !== 'cli' && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3. GLOBAL SETTINGS
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Nectra Digital');
}
if (!defined('SITE_URL')) {
    define('SITE_URL', 'https://www.nectradigital.com');
}

// 4. ERROR HANDLING (production web; CLI scripts set their own)
if (php_sapi_name() !== 'cli') {
    error_reporting(0);
}