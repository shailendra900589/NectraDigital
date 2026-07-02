<?php
/**
 * Text normalization — fixes &amp; and other HTML entities showing literally sitewide.
 */

if (defined('NECTRA_TEXT_UTILS_LOADED')) {
    return;
}
define('NECTRA_TEXT_UTILS_LOADED', true);

require_once __DIR__ . '/crawler_access.php';

function nectra_decode_entities(?string $text, int $maxPasses = 6): string
{
    if ($text === null || $text === '') {
        return '';
    }
    $prev = null;
    $current = $text;
    $passes = 0;
    while ($current !== $prev && $passes < $maxPasses) {
        $prev = $current;
        $current = html_entity_decode($current, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $passes++;
    }
    return $current;
}

function nectra_display_text(?string $text): string
{
    return htmlspecialchars(nectra_decode_entities($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitize_db_text(?string $data): string
{
    return stripslashes(nectra_decode_entities(trim((string) $data)));
}

function nectra_preferred_host(): string
{
    if (defined('SITE_URL')) {
        $host = parse_url(SITE_URL, PHP_URL_HOST);
        if ($host) {
            return $host;
        }
    }
    return 'www.nectradigital.com';
}

function nectra_is_https_request(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    $proto = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '');
    return $proto === 'https' || strtolower($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '') === 'on';
}

/** 301 to https://www.nectradigital.com when Hostinger/CDN bypasses .htaccess. */
function nectra_enforce_preferred_host(): void
{
    if (php_sapi_name() === 'cli' || headers_sent()) {
        return;
    }

    $preferred = strtolower(nectra_preferred_host());
    $host = strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''));

    if ($host === $preferred && nectra_is_https_request()) {
        return;
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $parts = parse_url($uri);
    $path = $parts['path'] ?? '/';
    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
        unset($query['lang']);
    }
    $qs = $query ? '?' . http_build_query($query) : '';

    header('Location: https://' . $preferred . $path . $qs, true, 301);
    exit;
}
