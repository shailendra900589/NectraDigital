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

function nectra_fix_mojibake(?string $text): string
{
    if ($text === null || $text === '') {
        return '';
    }

    $text = nectra_decode_entities($text);

    $map = [
        'â€™' => "'",
        'â€˜' => "'",
        'â€œ' => '"',
        'â€' => '"',
        'â€“' => '–',
        'â€”' => '—',
        'â€¦' => '…',
        'Letâ' => "Let's",
        'donâ€™t' => "don't",
        'Donâ€™t' => "Don't",
        'wonâ€™t' => "won't",
        'itâ€™s' => "it's",
        'Itâ€™s' => "It's",
        'Indiax' => 'India',
        'indiax' => 'India',
    ];
    $text = str_replace(array_keys($map), array_values($map), $text);

    if (preg_match('/â|Ã|ðŸ|Letâ/u', $text)) {
        $attempt = @mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
        if (is_string($attempt) && $attempt !== '' && mb_check_encoding($attempt, 'UTF-8')) {
            $text = $attempt;
        }
    }

    $text = preg_replace('/Letâ[\x00-\xFF]{0,3}s/u', "Let's", $text) ?? $text;

    return trim($text);
}

function nectra_display_text(?string $text): string
{
    return htmlspecialchars(nectra_fix_mojibake($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitize_db_text(?string $data): string
{
    return stripslashes(nectra_fix_mojibake(trim((string) $data)));
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
