<?php
/**
 * Growth admin bootstrap — load once per request (auth + DB + config).
 */
if (defined('GE_ADMIN_INIT')) {
    return;
}
define('GE_ADMIN_INIT', true);

require_once __DIR__ . '/../../includes/growth/bootstrap.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}

// Surface fatal errors in growth admin (helps debug production 500s)
if (php_sapi_name() !== 'cli') {
    register_shutdown_function(function () {
        $err = error_get_last();
        if (!$err || !in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            return;
        }
        if (headers_sent()) {
            echo "\n\nGrowth Admin fatal: {$err['message']} in {$err['file']}:{$err['line']}\n";
            return;
        }
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html><body style="background:#111;color:#f66;font-family:monospace;padding:2rem">';
        echo '<h2>Growth Admin Error</h2>';
        echo '<p>' . htmlspecialchars($err['message']) . '</p>';
        echo '<p><small>' . htmlspecialchars($err['file']) . ':' . (int)$err['line'] . '</small></p>';
        echo '<p><a href="../dashboard.php?page=home" style="color:#0ff">← Back to NECTRAOS Dashboard</a></p>';
        echo '</body></html>';
    });
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
