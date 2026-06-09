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
        'en'    => ['label' => 'English',   'native' => 'English',    'flag' => '🇬🇧', 'code' => 'GB', 'hreflang' => 'en-IN'],
        'hi'    => ['label' => 'Hindi',     'native' => 'हिन्दी',      'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'hi-IN'],
        'bn'    => ['label' => 'Bengali',   'native' => 'বাংলা',       'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'bn-IN'],
        'ta'    => ['label' => 'Tamil',     'native' => 'தமிழ்',       'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'ta-IN'],
        'te'    => ['label' => 'Telugu',    'native' => 'తెలుగు',      'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'te-IN'],
        'mr'    => ['label' => 'Marathi',   'native' => 'मराठी',       'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'mr-IN'],
        'gu'    => ['label' => 'Gujarati',  'native' => 'ગુજરાતી',     'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'gu-IN'],
        'kn'    => ['label' => 'Kannada',   'native' => 'ಕನ್ನಡ',       'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'kn-IN'],
        'ml'    => ['label' => 'Malayalam', 'native' => 'മലയാളം',      'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'ml-IN'],
        'pa'    => ['label' => 'Punjabi',   'native' => 'ਪੰਜਾਬੀ',      'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'pa-IN'],
        'or'    => ['label' => 'Odia',      'native' => 'ଓଡ଼ିଆ',       'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'or-IN'],
        'as'    => ['label' => 'Assamese',  'native' => 'অসমীয়া',     'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'as-IN'],
        'ur'    => ['label' => 'Urdu',      'native' => 'اردو',        'flag' => '🇮🇳', 'code' => 'IN', 'hreflang' => 'ur-IN', 'rtl' => true],
        'ar'    => ['label' => 'Arabic',    'native' => 'العربية',     'flag' => '🇸🇦', 'code' => 'SA', 'hreflang' => 'ar', 'rtl' => true],
        'fr'    => ['label' => 'French',    'native' => 'Français',   'flag' => '🇫🇷', 'code' => 'FR', 'hreflang' => 'fr'],
        'de'    => ['label' => 'German',    'native' => 'Deutsch',    'flag' => '🇩🇪', 'code' => 'DE', 'hreflang' => 'de'],
        'es'    => ['label' => 'Spanish',   'native' => 'Español',    'flag' => '🇪🇸', 'code' => 'ES', 'hreflang' => 'es'],
        'zh-CN' => ['label' => 'Chinese',   'native' => '中文',        'flag' => '🇨🇳', 'code' => 'CN', 'hreflang' => 'zh-CN'],
        'ja'    => ['label' => 'Japanese',  'native' => '日本語',      'flag' => '🇯🇵', 'code' => 'JP', 'hreflang' => 'ja'],
        'ko'    => ['label' => 'Korean',    'native' => '한국어',       'flag' => '🇰🇷', 'code' => 'KR', 'hreflang' => 'ko'],
    ];
}

function nectra_translate_language_codes(): string
{
    return implode(',', array_keys(nectra_supported_languages()));
}

function nectra_is_rtl_lang(string $lang): bool
{
    $supported = nectra_supported_languages();
    return !empty($supported[$lang]['rtl']);
}

/** Apply ?lang= from URL to cookies before HTML output (full-page Google Translate). */
function nectra_handle_lang_request(): void
{
    if (php_sapi_name() === 'cli' || headers_sent()) {
        return;
    }

    $supported = nectra_supported_languages();
    $lang = isset($_GET['lang']) ? trim((string)$_GET['lang']) : '';

    if ($lang === '' || !isset($supported[$lang])) {
        return;
    }

    $expires = time() + 365 * 86400;
    $cookieOpts = [
        'expires'  => $expires,
        'path'     => '/',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => false,
        'samesite' => 'Lax',
    ];

    setcookie('nectra_lang', $lang, $cookieOpts);
    $_COOKIE['nectra_lang'] = $lang;

    if ($lang === 'en') {
        setcookie('googtrans', '', ['expires' => time() - 3600, 'path' => '/']);
        unset($_COOKIE['googtrans']);
    } else {
        setcookie('googtrans', '/en/' . $lang, $cookieOpts);
        $_COOKIE['googtrans'] = '/en/' . $lang;
    }
}

function nectra_get_user_lang(): string
{
    $supported = nectra_supported_languages();

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
        if (isset($supported[$code])) {
            return $code;
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

/** Build localized URL (?lang=) for hreflang / IndexNow / sitemap. */
function nectra_lang_url(string $baseUrl, string $langCode): string
{
    $baseUrl = rtrim($baseUrl, '/');
    if ($langCode === 'en') {
        return $baseUrl;
    }
    $parts = parse_url($baseUrl);
    $path = $parts['path'] ?? '';
    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
    }
    $query['lang'] = $langCode;
    $qs = http_build_query($query);
    $scheme = $parts['scheme'] ?? 'https';
    $host = $parts['host'] ?? '';
    $port = isset($parts['port']) ? ':' . $parts['port'] : '';
    return $scheme . '://' . $host . $port . $path . '?' . $qs;
}

function nectra_language_url_variants(string $url): array
{
    if (nectra_skip_i18n_url($url)) {
        return [$url];
    }

    $urls = [];
    foreach (nectra_supported_languages() as $code => $meta) {
        $urls[] = nectra_lang_url($url, $code);
    }
    return array_values(array_unique($urls));
}

function nectra_expand_urls_for_languages(array $urls): array
{
    $expanded = [];
    foreach ($urls as $url) {
        $expanded = array_merge($expanded, nectra_language_url_variants($url));
    }
    return array_values(array_unique($expanded));
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

function nectra_output_hreflang_tags(string $canonicalUrl): void
{
    $supported = nectra_supported_languages();
    $base = rtrim($canonicalUrl, '/');

    echo '<link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($base) . '" />' . "\n";

    foreach ($supported as $code => $meta) {
        $hl = $meta['hreflang'] ?? $code;
        $href = nectra_lang_url($base, $code);
        echo '<link rel="alternate" hreflang="' . htmlspecialchars($hl) . '" href="' . htmlspecialchars($href) . '" />' . "\n";
    }
}

function nectra_i18n_config_js(): array
{
    $lang = nectra_get_user_lang();
    return [
        'cookieName'    => 'nectra_lang',
        'defaultLang'   => 'en',
        'currentLang'   => $lang,
        'googtrans'     => $lang !== 'en' ? '/en/' . $lang : '',
        'apiUrl'        => (defined('SITE_URL') ? SITE_URL : '') . '/api/translate',
        'apiEnabled'    => nectra_translate_api_enabled(),
        'languages'     => nectra_supported_languages(),
        'includedCodes' => nectra_translate_language_codes(),
    ];
}

nectra_handle_lang_request();
