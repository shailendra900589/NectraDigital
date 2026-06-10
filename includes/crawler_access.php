<?php
/**
 * Search-engine crawler detection + IP ranges for Hostinger CDN / IP Manager allowlists.
 */

if (!function_exists('nectra_search_bot_user_agents')) {
    function nectra_search_bot_user_agents(): array
    {
        return [
            'bingbot', 'bingpreview', 'msnbot', 'adidxbot',
            'googlebot', 'google-inspectiontool', 'googlebot-image',
            'adsbot-google', 'mediapartners-google', 'storebot-google',
            'slurp', 'yahoo', 'yahoobot', 'yandexbot', 'duckduckbot',
            'applebot', 'baiduspider', 'facebot', 'facebookexternalhit',
            'twitterbot', 'linkedinbot', 'pinterestbot', 'semrushbot',
            'ahrefsbot', 'petalbot', 'bytespider', 'mj12bot', 'dotbot',
        ];
    }
}

if (!function_exists('nectra_is_search_bot')) {
    function nectra_is_search_bot(): bool
    {
        $ua = strtolower((string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
        if ($ua !== '') {
            foreach (nectra_search_bot_user_agents() as $bot) {
                if (strpos($ua, $bot) !== false) {
                    return true;
                }
            }
        }

        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
        if ($ip !== '' && nectra_is_search_bot_ip($ip)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('nectra_search_bot_ip_prefixes')) {
    /** Known crawler IP prefixes (Microsoft/Bing, Google, Yahoo/Oath, Yandex, etc.) */
    function nectra_search_bot_ip_prefixes(): array
    {
        return [
            '40.77.', '40.78.', '40.79.', '40.80.', '40.88.', '40.89.',
            '52.167.', '13.66.', '13.67.', '13.68.', '13.69.',
            '157.55.', '157.56.', '207.46.', '65.52.', '65.55.',
            '66.249.', '64.233.', '72.14.', '74.125.', '209.85.', '216.58.', '216.239.',
            '66.196.', '68.142.', '72.30.', '98.136.', '98.137.', '98.138.', '98.139.',
            '100.64.', '103.21.', '104.16.', '141.101.', '162.158.', '172.64.', '173.245.',
            '188.114.', '190.93.', '197.234.', '198.41.',
            '5.255.', '87.250.', '93.158.', '95.108.', '100.43.', '213.180.',
            '17.58.', '17.248.',
        ];
    }
}

if (!function_exists('nectra_is_search_bot_ip')) {
    function nectra_is_search_bot_ip(string $ip): bool
    {
        foreach (nectra_search_bot_ip_prefixes() as $prefix) {
            if (strncmp($ip, $prefix, strlen($prefix)) === 0) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('nectra_crawler_access_alert_html')) {
    function nectra_crawler_access_alert_html(): string
    {
        return '<div class="alert alert-warning border-warning mb-4">'
            . '<h5 class="alert-heading mb-2"><i class="fas fa-globe-americas"></i> Bing/Yahoo/Google blocked? Fix Hostinger CDN geo-block</h5>'
            . '<p class="mb-2">If crawlers see <strong>“Your country is not allowed to access this resource”</strong>, this is <strong>Hostinger CDN country blocking</strong> — not your PHP code. Bing/Yahoo crawl from US datacenters and get blocked when only India is allowed.</p>'
            . '<ol class="mb-2 ps-3"><li>hPanel → <strong>Websites</strong> → <strong>Manage</strong> (nectradigital.com)</li>'
            . '<li>Sidebar → <strong>CDN</strong> → open CDN settings (arrow)</li>'
            . '<li><strong>Traffic blocking</strong> → delete ALL country rules (trash icon)</li>'
            . '<li>Turn <strong>OFF</strong> “Allow only specific countries” if enabled</li>'
            . '<li>Security → turn <strong>Bot Protection OFF</strong> (or upgrade VPS to disable)</li>'
            . '<li>Also check <strong>IP Manager</strong> — remove any Block rules for search bots</li></ol>'
            . '<p class="mb-0 small text-muted">After saving, wait 5–10 minutes, then re-test in Bing URL Inspection. Code changes alone cannot override CDN edge geo-blocks.</p>'
            . '</div>';
    }
}
