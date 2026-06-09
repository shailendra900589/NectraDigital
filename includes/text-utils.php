<?php
/**
 * Text normalization — fixes &amp; and other HTML entities showing literally sitewide.
 */

if (defined('NECTRA_TEXT_UTILS_LOADED')) {
    return;
}
define('NECTRA_TEXT_UTILS_LOADED', true);

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
