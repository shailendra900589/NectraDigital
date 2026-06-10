<?php
/**
 * AdSense-safe rendering — one loader script, deferred push per slot.
 */
if (defined('NECTRA_AD_ENGINE_LOADED')) {
    return;
}
define('NECTRA_AD_ENGINE_LOADED', true);

function nectra_adsense_clients(): array
{
    global $nectra_adsense_clients;
    if (!isset($nectra_adsense_clients) || !is_array($nectra_adsense_clients)) {
        $nectra_adsense_clients = [];
    }
    return $nectra_adsense_clients;
}

function nectra_adsense_register_client(string $code): void
{
    if (preg_match('/data-ad-client="(ca-pub-[^"]+)"/i', $code, $m)) {
        $clients = &nectra_adsense_clients();
        $clients[$m[1]] = true;
    }
}

function nectra_is_adsense_markup(string $code): bool
{
    return stripos($code, 'adsbygoogle') !== false || stripos($code, 'googlesyndication') !== false;
}

/** Strip duplicate AdSense script tags; keep ins markup only. */
function nectra_prepare_adsense_markup(string $code): string
{
    $code = trim($code);
    if ($code === '') {
        return '';
    }

    nectra_adsense_register_client($code);

    $clean = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $code);
    $clean = trim($clean);
    if ($clean === '') {
        return '';
    }

    if (stripos($clean, 'adsbygoogle') !== false && stripos($clean, 'data-nectra-adsense') === false) {
        $clean = preg_replace('/<ins\b/i', '<ins data-nectra-adsense="1"', $clean, 1);
    }

    return $clean;
}

function nectra_prepare_ad_code_html(string $code): string
{
    if (nectra_is_adsense_markup($code)) {
        return nectra_prepare_adsense_markup($code);
    }
    return trim($code);
}

function nectra_output_adsense_loader(): void
{
    $clients = array_keys(nectra_adsense_clients());
    if (empty($clients)) {
        return;
    }

    $client = $clients[0];
    echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='
        . htmlspecialchars($client, ENT_QUOTES)
        . '" crossorigin="anonymous" id="nectra-adsense-loader"></script>' . "\n";
}

function nectra_output_ad_scripts(): void
{
    nectra_output_adsense_loader();
    $base = defined('SITE_URL') ? SITE_URL : '';
    echo '<script src="' . htmlspecialchars($base, ENT_QUOTES) . '/assets/js/nectra-ads.js?v=2" defer></script>' . "\n";
}
