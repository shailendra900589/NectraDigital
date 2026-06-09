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
        'en'    => ['label' => 'English',   'native' => 'English',    'flag' => '🇬🇧', 'hreflang' => 'en-IN'],
        'hi'    => ['label' => 'Hindi',     'native' => 'हिन्दी',      'flag' => '🇮🇳', 'hreflang' => 'hi-IN'],
        'bn'    => ['label' => 'Bengali',   'native' => 'বাংলা',       'flag' => '🇮🇳', 'hreflang' => 'bn-IN'],
        'ta'    => ['label' => 'Tamil',     'native' => 'தமிழ்',       'flag' => '🇮🇳', 'hreflang' => 'ta-IN'],
        'te'    => ['label' => 'Telugu',    'native' => 'తెలుగు',      'flag' => '🇮🇳', 'hreflang' => 'te-IN'],
        'mr'    => ['label' => 'Marathi',   'native' => 'मराठी',       'flag' => '🇮🇳', 'hreflang' => 'mr-IN'],
        'gu'    => ['label' => 'Gujarati',  'native' => 'ગુજરાતી',     'flag' => '🇮🇳', 'hreflang' => 'gu-IN'],
        'kn'    => ['label' => 'Kannada',   'native' => 'ಕನ್ನಡ',       'flag' => '🇮🇳', 'hreflang' => 'kn-IN'],
        'ml'    => ['label' => 'Malayalam', 'native' => 'മലയാളം',      'flag' => '🇮🇳', 'hreflang' => 'ml-IN'],
        'pa'    => ['label' => 'Punjabi',   'native' => 'ਪੰਜਾਬੀ',      'flag' => '🇮🇳', 'hreflang' => 'pa-IN'],
        'or'    => ['label' => 'Odia',      'native' => 'ଓଡ଼ିଆ',       'flag' => '🇮🇳', 'hreflang' => 'or-IN'],
        'as'    => ['label' => 'Assamese',  'native' => 'অসমীয়া',     'flag' => '🇮🇳', 'hreflang' => 'as-IN'],
        'ur'    => ['label' => 'Urdu',      'native' => 'اردو',        'flag' => '🇮🇳', 'hreflang' => 'ur-IN'],
        'ar'    => ['label' => 'Arabic',    'native' => 'العربية',     'flag' => '🇸🇦', 'hreflang' => 'ar'],
        'fr'    => ['label' => 'French',    'native' => 'Français',   'flag' => '🇫🇷', 'hreflang' => 'fr'],
        'de'    => ['label' => 'German',    'native' => 'Deutsch',    'flag' => '🇩🇪', 'hreflang' => 'de'],
        'es'    => ['label' => 'Spanish',   'native' => 'Español',    'flag' => '🇪🇸', 'hreflang' => 'es'],
        'zh-CN' => ['label' => 'Chinese',   'native' => '中文',        'flag' => '🇨🇳', 'hreflang' => 'zh-CN'],
        'ja'    => ['label' => 'Japanese',  'native' => '日本語',      'flag' => '🇯🇵', 'hreflang' => 'ja'],
        'ko'    => ['label' => 'Korean',    'native' => '한국어',       'flag' => '🇰🇷', 'hreflang' => 'ko'],
    ];
}

function nectra_translate_language_codes(): string
{
    return implode(',', array_keys(nectra_supported_languages()));
}

function nectra_get_user_lang(): string
{
    $supported = nectra_supported_languages();
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
    return $lang === 'zh-CN' ? 'zh-Hans' : str_replace('_', '-', explode('-', $lang)[0]);
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
    if ($target === $source || $target === 'en' && $source === 'en') {
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
    echo '<link rel="alternate" hreflang="en-IN" href="' . htmlspecialchars($base) . '" />' . "\n";
    foreach ($supported as $code => $meta) {
        if ($code === 'en') {
            continue;
        }
        $hl = $meta['hreflang'] ?? $code;
        echo '<link rel="alternate" hreflang="' . htmlspecialchars($hl) . '" href="' . htmlspecialchars($base) . '?lang=' . htmlspecialchars($code) . '" />' . "\n";
    }
}

function nectra_i18n_config_js(): array
{
    return [
        'cookieName'   => 'nectra_lang',
        'defaultLang'  => 'en',
        'currentLang'  => nectra_get_user_lang(),
        'apiUrl'       => (defined('SITE_URL') ? SITE_URL : '') . '/api/translate',
        'apiEnabled'   => nectra_translate_api_enabled(),
        'languages'    => nectra_supported_languages(),
        'includedCodes'=> nectra_translate_language_codes(),
    ];
}
