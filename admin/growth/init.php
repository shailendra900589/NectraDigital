<?php
/**
 * Growth admin bootstrap — load once per request (auth + DB + config).
 */
if (defined('GE_ADMIN_INIT')) {
    return;
}
define('GE_ADMIN_INIT', true);

function ge_admin_fatal(string $message, string $file = '', int $line = 0): void
{
    if (php_sapi_name() === 'cli') {
        fwrite(STDERR, "Growth Admin fatal: $message ($file:$line)\n");
        exit(1);
    }
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }
    echo '<!DOCTYPE html><html><body style="background:#111;color:#f66;font-family:monospace;padding:2rem">';
    echo '<h2>Growth Admin Error</h2>';
    echo '<p>' . htmlspecialchars($message) . '</p>';
    if ($file) {
        echo '<p><small>' . htmlspecialchars($file) . ':' . $line . '</small></p>';
    }
    echo '<p><a href="../dashboard.php?page=home" style="color:#0ff">← NECTRAOS Dashboard</a></p>';
    echo '</body></html>';
    exit;
}

try {
    require_once __DIR__ . '/../../includes/growth/bootstrap.php';
} catch (Throwable $e) {
    ge_admin_fatal('Bootstrap failed: ' . $e->getMessage(), $e->getFile(), $e->getLine());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}

if (php_sapi_name() !== 'cli') {
    register_shutdown_function(function () {
        $err = error_get_last();
        if (!$err || !in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            return;
        }
        ge_admin_fatal($err['message'], $err['file'], $err['line']);
    });
}

function ge_admin_layout(): void
{
    require_once __DIR__ . '/includes/layout.php';
}

function ge_admin_require_ready(): bool
{
    return function_exists('ge_is_ready') && ge_is_ready();
}

function ge_admin_safe(callable $fn, $default = null)
{
    try {
        return $fn();
    } catch (Throwable $e) {
        error_log('Growth admin: ' . $e->getMessage());
        return $default;
    }
}
