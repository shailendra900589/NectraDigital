<?php
namespace Growth\Engines;

class KeywordEngine
{
    public static function generateForPage(array $service, array $city, ?array $industry = null): array
    {
        // High-intent catalog first (primary source)
        $intent = IntentKeywordEngine::forLandingPage($service, $city, $industry);

        $sn = $service['name'];
        $cn = $city['name'];
        $in = $industry['name'] ?? '';
        $prefix = $service['url_prefix'] ?? ge_slugify($service['name']);
        $state = $city['state'] ?? $city['country'] ?? 'India';

        $patterns = [
            'secondary' => [
                "{sn} experts {cn}",
                "Certified {sn} {cn}",
                "Trusted {sn} provider {cn}",
            ],
            'long_tail' => [
                "Best {sn} company for small business in {cn}",
                "Affordable {sn} agency {cn} {state}",
                "Top rated {sn} services near {cn}",
            ],
            'informational' => [
                "How to choose {sn} in {cn}",
                "Benefits of {sn} for {cn} businesses",
            ],
            'lsi' => [
                "Digital solutions {cn}",
                "Business growth {cn}",
                "Online marketing {cn}",
            ],
        ];

        if ($in) {
            $patterns['long_tail'][] = "Best {sn} for {in} businesses in {cn}";
        }

        $extra = [];
        foreach ($patterns as $type => $templates) {
            foreach ($templates as $tpl) {
                $extra[] = [
                    'keyword' => ge_replace_tokens($tpl, [
                        'sn' => $sn, 'cn' => $cn, 'in' => $in, 'state' => $state,
                        'prefix' => str_replace('-', ' ', $prefix),
                    ]),
                    'keyword_type' => $type,
                ];
            }
        }

        if (!empty($service['keywords_template'])) {
            foreach (explode(',', $service['keywords_template']) as $raw) {
                $raw = trim($raw);
                if ($raw) {
                    $extra[] = [
                        'keyword' => ge_replace_tokens($raw, ['city' => $cn, 'service' => $sn, 'state' => $state]),
                        'keyword_type' => 'secondary',
                    ];
                }
            }
        }

        return self::mergeDedupe($intent, $extra);
    }

    /** Top keywords for meta tag storage on landing page. */
    public static function metaKeywordList(array $service, array $city, ?array $industry = null, int $limit = 20): array
    {
        $all = self::generateForPage($service, $city, $industry);
        $priority = ['primary', 'transactional', 'commercial', 'local', 'secondary', 'long_tail', 'informational', 'lsi', 'semantic'];
        usort($all, function ($a, $b) use ($priority) {
            $pa = array_search($a['keyword_type'], $priority, true);
            $pb = array_search($b['keyword_type'], $priority, true);
            return ($pa === false ? 99 : $pa) <=> ($pb === false ? 99 : $pb);
        });
        $out = [];
        $seen = [];
        foreach ($all as $row) {
            $k = mb_strtolower(trim($row['keyword']));
            if ($k === '' || isset($seen[$k])) {
                continue;
            }
            $seen[$k] = true;
            $out[] = $row['keyword'];
            if (count($out) >= $limit) {
                break;
            }
        }
        return $out;
    }

    private static function mergeDedupe(array $a, array $b): array
    {
        $seen = [];
        $out = [];
        foreach (array_merge($a, $b) as $row) {
            $k = mb_strtolower(trim($row['keyword'] ?? ''));
            if ($k === '' || isset($seen[$k])) {
                continue;
            }
            $seen[$k] = true;
            $out[] = $row;
        }
        return $out;
    }
}
