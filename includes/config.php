<?php
if (defined('NECTRA_CONFIG_LOADED')) {
    return;
}
define('NECTRA_CONFIG_LOADED', true);

require_once __DIR__ . '/text-utils.php';

$isLocalDev = nectra_is_local_environment();

// Production secrets (Hostinger). Skipped on local so SITE_URL is never forced to the live domain.
if (!$isLocalDev && is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

if (php_sapi_name() !== 'cli') {
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    if (!$isLocalDev) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
    header('Referrer-Policy: no-referrer-when-downgrade');
}

if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE && !nectra_is_search_bot()) {
    session_start();
}
if (php_sapi_name() !== 'cli' && !nectra_is_search_bot() && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Nectra Digital');
}
if (!defined('SITE_URL')) {
    if ($isLocalDev && isset($_SERVER['HTTP_HOST'])) {
        define('SITE_URL', nectra_compute_site_url());
    } else {
        define('SITE_URL', 'https://www.nectradigital.com');
    }
}

if (php_sapi_name() !== 'cli' && !$isLocalDev) {
    nectra_enforce_preferred_host();
}

if (php_sapi_name() !== 'cli' && nectra_site_base_path() !== '') {
    ob_start('nectra_prefix_root_relative_urls');
}

if (php_sapi_name() !== 'cli') {
    error_reporting(0);
}
