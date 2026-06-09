<?php
namespace Growth\Engines;

class IntentKeywordEngine
{
    private static ?array $catalog = null;

    public static function catalog(): array
    {
        if (self::$catalog === null) {
            require_once __DIR__ . '/../../seo-keywords-intent.php';
            self::$catalog = get_intent_keyword_catalog();
        }
        return self::$catalog;
    }

    /** Keywords for static service pages (national, no city). */
    public static function forStaticPage(string $serviceSlug, ?string $existingKeys = null): string
    {
        $cat = self::catalog()[$serviceSlug] ?? [];
        $merged = array_merge(
            $cat['primary'] ?? [],
            $cat['commercial'] ?? [],
            get_global_intent_keywords()
        );
        if ($existingKeys) {
            foreach (explode(',', $existingKeys) as $k) {
                $k = trim($k);
                if ($k) {
                    $merged[] = $k;
                }
            }
        }
        return self::toMetaString($merged, 20);
    }

    /** Full keyword list for programmatic landing page (typed for DB). */
    public static function forLandingPage(array $service, array $city, ?array $industry = null): array
    {
        $slug = $service['slug'] ?? '';
        $cn = $city['name'];
        $state = $city['state'] ?? 'India';
        $sn = $service['name'];
        $cat = self::catalog()[$slug] ?? [];

        $keywords = [];

        foreach ($cat['primary'] ?? [] as $kw) {
            $keywords[] = ['keyword' => self::localize($kw, $cn, $state), 'keyword_type' => 'primary'];
        }
        foreach ($cat['commercial'] ?? [] as $kw) {
            $keywords[] = ['keyword' => self::localize($kw, $cn, $state), 'keyword_type' => 'commercial'];
        }
        foreach ($cat['local'] ?? [] as $tpl) {
            $keywords[] = ['keyword' => self::applyCity($tpl, $cn, $state), 'keyword_type' => 'local'];
        }

        // Highest-intent local commercial patterns
        $highIntent = [
            "best {$sn} company in {$cn}",
            "top {$sn} agency {$cn}",
            "{$sn} company {$cn}",
            "{$sn} services {$cn}",
            "hire {$sn} {$cn}",
            "{$sn} near me {$cn}",
            "affordable {$sn} {$cn}",
            "{$sn} pricing {$cn}",
            "{$sn} quote {$cn}",
            "professional {$sn} {$cn} {$state}",
            "#1 {$sn} company {$cn}",
            "trusted {$sn} provider {$cn}",
        ];
        foreach ($highIntent as $kw) {
            $keywords[] = ['keyword' => $kw, 'keyword_type' => 'transactional'];
        }

        if ($industry) {
            $in = $industry['name'];
            $keywords[] = ['keyword' => "{$sn} for {$in} in {$cn}", 'keyword_type' => 'primary'];
            $keywords[] = ['keyword' => "best {$sn} for {$in} {$cn}", 'keyword_type' => 'commercial'];
            $keywords[] = ['keyword' => "{$in} {$sn} company {$cn}", 'keyword_type' => 'local'];
        }

        return self::dedupeKeywords($keywords);
    }

    /** Top primary keyword for meta title optimization. */
    public static function primaryPhrase(array $service, array $city, ?array $industry = null): string
    {
        $sn = $service['name'];
        $cn = $city['name'];
        if ($industry) {
            return "Best {$sn} for {$industry['name']} in {$cn}";
        }
        $slug = $service['slug'] ?? '';
        $cat = self::catalog()[$slug] ?? [];
        if (!empty($cat['local'][0])) {
            return self::applyCity($cat['local'][0], $cn, $city['state'] ?? 'India');
        }
        return "Best {$sn} Company in {$cn}";
    }

    /** Meta keywords string (top N by intent priority). */
    public static function metaKeywords(array $service, array $city, ?array $industry = null, int $limit = 15): string
    {
        $all = self::forLandingPage($service, $city, $industry);
        $order = ['primary', 'transactional', 'commercial', 'local', 'secondary', 'long_tail', 'informational', 'lsi', 'semantic'];
        usort($all, function ($a, $b) use ($order) {
            $pa = array_search($a['keyword_type'], $order, true);
            $pb = array_search($b['keyword_type'], $order, true);
            return ($pa === false ? 99 : $pa) <=> ($pb === false ? 99 : $pb);
        });
        return self::toMetaString(array_column($all, 'keyword'), $limit);
    }

    public static function optimizeMetaTitle(string $title, string $primaryPhrase, string $brand = 'Nectra Digital'): string
    {
        if (!function_exists('ge_trim_seo_title')) {
            require_once __DIR__ . '/../helpers.php';
        }
        $title = trim($title);
        if ($title === '' || stripos($title, $primaryPhrase) === false) {
            $title = $primaryPhrase . ' | ' . $brand;
        }
        return ge_trim_seo_title($title, $brand, 60);
    }

    public static function optimizeMetaDescription(string $desc, array $service, array $city, ?array $industry = null): string
    {
        if (!function_exists('ge_trim_seo_description')) {
            require_once __DIR__ . '/../helpers.php';
        }
        $cn = $city['name'];
        $state = $city['state'] ?? 'India';
        $sn = $service['name'];

        if (mb_strlen(trim(strip_tags($desc))) >= 100 && stripos($desc, $cn) !== false) {
            return ge_trim_seo_description($desc, 120, 160);
        }

        $in = $industry ? " for {$industry['name']}" : '';
        $desc = "Expert {$sn}{$in} in {$cn}, {$state}. Search engine optimization & digital marketing by Nectra Digital. Free audit, 200+ projects.";
        return ge_trim_seo_description($desc, 120, 160);
    }

    private static function localize(string $kw, string $city, string $state): string
    {
        if (str_contains($kw, '{city}')) {
            return self::applyCity($kw, $city, $state);
        }
        return "{$kw} {$city}";
    }

    private static function applyCity(string $tpl, string $city, string $state): string
    {
        return str_replace(['{city}', '{state}'], [$city, $state], $tpl);
    }

    private static function dedupeKeywords(array $keywords): array
    {
        $seen = [];
        $out = [];
        foreach ($keywords as $row) {
            $k = mb_strtolower(trim($row['keyword']));
            if ($k === '' || isset($seen[$k])) {
                continue;
            }
            $seen[$k] = true;
            $out[] = $row;
        }
        return $out;
    }

    private static function toMetaString(array $keywords, int $limit): string
    {
        $unique = [];
        foreach ($keywords as $k) {
            $k = trim(is_array($k) ? ($k['keyword'] ?? '') : $k);
            if ($k === '') {
                continue;
            }
            $lk = mb_strtolower($k);
            if (!isset($unique[$lk])) {
                $unique[$lk] = $k;
            }
        }
        return implode(', ', array_slice(array_values($unique), 0, $limit));
    }
}
