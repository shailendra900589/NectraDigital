<?php
/**
 * Multilingual support — Google Translate widget + Cloud Translation API proxy.
 */
if (defined('NECTRA_I18N_LOADED')) {
    return;
}
define('NECTRA_I18N_LOADED', true);

function nectra_supported_languages(): array
{
    return [
        'en'    => ['label' => 'English',   'native' => 'English',    'flag' => '🇬🇧', 'code' => 'GB', 'google' => 'en',    'hreflang' => 'en-IN'],
        'hi'    => ['label' => 'Hindi',     'native' => 'हिन्दी',      'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'hi',    'hreflang' => 'hi-IN'],
        'bn'    => ['label' => 'Bengali',   'native' => 'বাংলা',       'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'bn',    'hreflang' => 'bn-IN'],
        'ta'    => ['label' => 'Tamil',     'native' => 'தமிழ்',       'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'ta',    'hreflang' => 'ta-IN'],
        'te'    => ['label' => 'Telugu',    'native' => 'తెలుగు',      'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'te',    'hreflang' => 'te-IN'],
        'mr'    => ['label' => 'Marathi',   'native' => 'मराठी',       'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'mr',    'hreflang' => 'mr-IN'],
        'gu'    => ['label' => 'Gujarati',  'native' => 'ગુજરાતી',     'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'gu',    'hreflang' => 'gu-IN'],
        'kn'    => ['label' => 'Kannada',   'native' => 'ಕನ್ನಡ',       'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'kn',    'hreflang' => 'kn-IN'],
        'ml'    => ['label' => 'Malayalam', 'native' => 'മലയാളം',      'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'ml',    'hreflang' => 'ml-IN'],
        'pa'    => ['label' => 'Punjabi',   'native' => 'ਪੰਜਾਬੀ',      'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'pa',    'hreflang' => 'pa-IN'],
        'or'    => ['label' => 'Odia',      'native' => 'ଓଡ଼ିଆ',       'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'or',    'hreflang' => 'or-IN'],
        'as'    => ['label' => 'Assamese',  'native' => 'অসমীয়া',     'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'as',    'hreflang' => 'as-IN'],
        'ur'    => ['label' => 'Urdu',      'native' => 'اردو',        'flag' => '🇮🇳', 'code' => 'IN', 'google' => 'ur',    'hreflang' => 'ur-IN', 'rtl' => true],
        'ar'    => ['label' => 'Arabic',    'native' => 'العربية',     'flag' => '🇸🇦', 'code' => 'SA', 'google' => 'ar',    'hreflang' => 'ar', 'rtl' => true],
        'fr'    => ['label' => 'French',    'native' => 'Français',   'flag' => '🇫🇷', 'code' => 'FR', 'google' => 'fr',    'hreflang' => 'fr'],
        'de'    => ['label' => 'German',    'native' => 'Deutsch',    'flag' => '🇩🇪', 'code' => 'DE', 'google' => 'de',    'hreflang' => 'de'],
        'es'    => ['label' => 'Spanish',   'native' => 'Español',    'flag' => '🇪🇸', 'code' => 'ES', 'google' => 'es',    'hreflang' => 'es'],
        'zh-CN' => ['label' => 'Chinese',   'native' => '中文',        'flag' => '🇨🇳', 'code' => 'CN', 'google' => 'zh-CN', 'hreflang' => 'zh-CN'],
        'ja'    => ['label' => 'Japanese',  'native' => '日本語',      'flag' => '🇯🇵', 'code' => 'JP', 'google' => 'ja',    'hreflang' => 'ja'],
        'ko'    => ['label' => 'Korean',    'native' => '한국어',       'flag' => '🇰🇷', 'code' => 'KR', 'google' => 'ko',    'hreflang' => 'ko'],
    ];
}

function nectra_google_lang_code(string $lang): string
{
    $supported = nectra_supported_languages();
    if (!isset($supported[$lang])) {
        return $lang;
    }
    return $supported[$lang]['google'] ?? $lang;
}

function nectra_lang_from_google_code(string $googleCode): string
{
    foreach (nectra_supported_languages() as $code => $meta) {
        if (($meta['google'] ?? $code) === $googleCode) {
            return $code;
        }
    }
    return isset(nectra_supported_languages()[$googleCode]) ? $googleCode : 'en';
}

function nectra_cookie_domain(): string
{
    $host = parse_url(defined('SITE_URL') ? SITE_URL : '', PHP_URL_HOST);
    if (!$host) {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    }
    $root = preg_replace('/^www\./i', '', $host);
    return '.' . $root;
}

function nectra_google_language_codes(): string
{
    $codes = [];
    foreach (nectra_supported_languages() as $meta) {
        $codes[] = $meta['google'] ?? 'en';
    }
    return implode(',', array_unique($codes));
}

function nectra_translate_language_codes(): string
{
    return nectra_google_language_codes();
}

function nectra_is_rtl_lang(string $lang): bool
{
    $supported = nectra_supported_languages();
    return !empty($supported[$lang]['rtl']);
}

/** Clear Google Translate cookies (all domain variants). */
function nectra_clear_googtrans_cookies(): void
{
    if (php_sapi_name() === 'cli' || headers_sent()) {
        return;
    }
    $past = time() - 3600;
    $domain = nectra_cookie_domain();
    setcookie('googtrans', '', ['expires' => $past, 'path' => '/']);
    setcookie('googtrans', '', ['expires' => $past, 'path' => '/', 'domain' => $domain]);
    unset($_COOKIE['googtrans']);
}

/** Remove ?lang= from a URL (canonical / sitemap / IndexNow must never include it). */
function nectra_strip_lang_from_url(string $url): string
{
    $parts = parse_url($url);
    if ($parts === false) {
        return rtrim($url, '/');
    }

    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
        unset($query['lang']);
    }

    $scheme = $parts['scheme'] ?? 'https';
    $host = $parts['host'] ?? '';
    $port = isset($parts['port']) ? ':' . $parts['port'] : '';
    $path = $parts['path'] ?? '';
    $qs = $query ? '?' . http_build_query($query) : '';

    return rtrim($scheme . '://' . $host . $port . $path . $qs, '/');
}

/** 301 redirect to the same path without ?lang= (prevents GSC alternate-canonical noise). */
function nectra_redirect_without_lang_param(int $status = 301): void
{
    if (headers_sent()) {
        return;
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $parts = parse_url($uri);
    $path = $parts['path'] ?? '/';
    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
    }
    unset($query['lang']);
    $qs = $query ? '?' . http_build_query($query) : '';
    $target = $path . $qs;

    header('Location: ' . $target, true, $status);
    exit;
}

/** Apply ?lang= from URL to cookies, then redirect to clean canonical URL. */
function nectra_handle_lang_request(): void
{
    if (php_sapi_name() === 'cli' || headers_sent() || !isset($_GET['lang'])) {
        return;
    }

    if (!function_exists('nectra_is_search_bot')) {
        require_once __DIR__ . '/text-utils.php';
    }

    $supported = nectra_supported_languages();
    $lang = trim((string)$_GET['lang']);
    $isBot = nectra_is_search_bot();

    if (!$isBot && $lang !== '' && isset($supported[$lang])) {
        nectra_clear_googtrans_cookies();

        $expires = time() + 365 * 86400;
        $domain = nectra_cookie_domain();
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $cookieOpts = [
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => false,
            'samesite' => 'Lax',
        ];

        setcookie('nectra_lang', $lang, $cookieOpts);
        $_COOKIE['nectra_lang'] = $lang;

        if ($lang === 'en') {
            nectra_clear_googtrans_cookies();
        } else {
            $googleCode = nectra_google_lang_code($lang);
            $googVal = '/en/' . $googleCode;
            setcookie('googtrans', $googVal, $cookieOpts);
            setcookie('googtrans', $googVal, array_merge($cookieOpts, ['domain' => $domain]));
            $_COOKIE['googtrans'] = $googVal;
        }
    }

    nectra_redirect_without_lang_param();
}

function nectra_get_user_lang(): string
{
    if (!function_exists('nectra_is_search_bot')) {
        require_once __DIR__ . '/text-utils.php';
    }
    $supported = nectra_supported_languages();

    if (nectra_is_search_bot()) {
        return 'en';
    }

    if (!empty($_GET['lang']) && isset($supported[$_GET['lang']])) {
        return (string)$_GET['lang'];
    }

    $cookie = $_COOKIE['nectra_lang'] ?? '';
    if (isset($supported[$cookie])) {
        return $cookie;
    }

    if (!empty($_COOKIE['googtrans'])) {
        $parts = explode('/', trim($_COOKIE['googtrans'], '/'));
        $code = end($parts);
        $mapped = nectra_lang_from_google_code((string)$code);
        if (isset($supported[$mapped])) {
            return $mapped;
        }
    }

    return 'en';
}

function nectra_html_lang(): string
{
    $lang = nectra_get_user_lang();
    if ($lang === 'zh-CN') {
        return 'zh-Hans';
    }
    return str_replace('_', '-', explode('-', $lang)[0]);
}

function nectra_html_dir(): string
{
    return nectra_is_rtl_lang(nectra_get_user_lang()) ? 'rtl' : 'ltr';
}

/**
 * Legacy helper — translation uses cookies + Google Translate, not indexable ?lang= URLs.
 * Always returns the clean canonical URL without language query params.
 */
function nectra_lang_url(string $baseUrl, string $langCode): string
{
    return nectra_strip_lang_from_url($baseUrl);
}

function nectra_language_url_variants(string $url): array
{
    return [nectra_strip_lang_from_url($url)];
}

function nectra_expand_urls_for_languages(array $urls): array
{
    $clean = [];
    foreach ($urls as $url) {
        $clean[] = nectra_strip_lang_from_url($url);
    }
    return array_values(array_unique($clean));
}

function nectra_skip_i18n_url(string $url): bool
{
    return (bool)preg_match(
        '#/(api/|admin/|assets/|cron/|database/|tools/.*\.(xml|txt|json)|rss\.xml|atom\.xml|discover-feed\.xml|news-sitemap\.xml|sitemap\.xml|llms\.txt)#i',
        $url
    );
}

function nectra_translate_api_enabled(): bool
{
    return defined('GOOGLE_TRANSLATE_API_KEY') && GOOGLE_TRANSLATE_API_KEY !== '';
}

function nectra_translate_cache_dir(): string
{
    $dir = dirname(__DIR__) . '/storage/cache/translations';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir;
}

/**
 * Google Cloud Translation API v2 with file cache.
 *
 * @param string|string[] $text
 * @return string|string[]|null
 */
function nectra_translate_text($text, string $target, string $source = 'en')
{
    if (!nectra_translate_api_enabled()) {
        return null;
    }
    if ($target === $source || ($target === 'en' && $source === 'en')) {
        return $text;
    }

    $batch = is_array($text);
    $items = $batch ? $text : [$text];
    $results = [];
    $toFetch = [];
    $indexMap = [];

    foreach ($items as $i => $item) {
        $item = trim((string)$item);
        if ($item === '') {
            $results[$i] = '';
            continue;
        }
        $cacheKey = md5($source . '|' . $target . '|' . $item);
        $cacheFile = nectra_translate_cache_dir() . '/' . $cacheKey . '.txt';
        if (is_file($cacheFile)) {
            $results[$i] = file_get_contents($cacheFile);
            continue;
        }
        $toFetch[] = $item;
        $indexMap[count($toFetch) - 1] = ['i' => $i, 'cache' => $cacheFile];
    }

    if (!empty($toFetch)) {
        $translated = nectra_google_translate_request($toFetch, $target, $source);
        if ($translated === null) {
            return null;
        }
        foreach ($translated as $j => $line) {
            $meta = $indexMap[$j];
            $results[$meta['i']] = $line;
            @file_put_contents($meta['cache'], $line, LOCK_EX);
        }
    }

    ksort($results);
    $ordered = array_values($results);
    return $batch ? $ordered : ($ordered[0] ?? null);
}

function nectra_google_translate_request(array $texts, string $target, string $source): ?array
{
    $key = GOOGLE_TRANSLATE_API_KEY;
    $url = 'https://translation.googleapis.com/language/translate/v2?key=' . urlencode($key);

    $payload = json_encode([
        'q'      => array_values($texts),
        'target' => $target,
        'source' => $source,
        'format' => 'text',
    ], JSON_UNESCAPED_UNICODE);

    $ctx = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => $payload,
            'timeout' => 15,
        ],
    ]);

    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        return null;
    }

    $json = json_decode($raw, true);
    if (empty($json['data']['translations'])) {
        return null;
    }

    $out = [];
    foreach ($json['data']['translations'] as $row) {
        $out[] = $row['translatedText'] ?? '';
    }
    return $out;
}

/** Intentionally empty — Google Translate widget URLs must not use hreflang (causes GSC alternate-canonical reports). */
function nectra_output_hreflang_tags(string $canonicalUrl): void
{
}

function nectra_i18n_config_js(): array
{
    $lang = nectra_get_user_lang();
    $googleCode = nectra_google_lang_code($lang);
    return [
        'cookieName'    => 'nectra_lang',
        'defaultLang'   => 'en',
        'currentLang'   => $lang,
        'currentGoogle' => $googleCode,
        'googtrans'     => $lang !== 'en' ? '/en/' . $googleCode : '',
        'cookieDomain'  => nectra_cookie_domain(),
        'apiUrl'        => (defined('SITE_URL') ? SITE_URL : '') . '/api/translate',
        'apiEnabled'    => nectra_translate_api_enabled(),
        'languages'     => nectra_supported_languages(),
        'includedCodes' => nectra_google_language_codes(),
    ];
}

nectra_handle_lang_request();
