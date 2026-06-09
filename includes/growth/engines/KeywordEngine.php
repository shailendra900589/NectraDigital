<?php
namespace Growth\Engines;

class KeywordEngine
{
    public static function generateForPage(array $service, array $city): array
    {
        $sn = $service['name'];
        $cn = $city['name'];
        $prefix = $service['url_prefix'] ?? ge_slugify($service['name']);

        $patterns = [
            'primary' => [
                "{sn} {cn}",
                "Best {sn} {cn}",
                "{sn} Company {cn}",
                "{sn} Agency {cn}",
                "{sn} Services {cn}",
            ],
            'commercial' => [
                "Hire {sn} {cn}",
                "Top {sn} Company in {cn}",
                "Affordable {sn} {cn}",
                "{sn} Packages {cn}",
            ],
            'transactional' => [
                "{sn} Quote {cn}",
                "Get {sn} in {cn}",
                "{sn} Pricing {cn}",
            ],
            'local' => [
                "{sn} near me {cn}",
                "Local {sn} {cn}",
                "{sn} {cn} {state}",
            ],
            'informational' => [
                "What is {sn}",
                "How to choose {sn} in {cn}",
                "Benefits of {sn} for {cn} businesses",
            ],
            'lsi' => [
                "Digital solutions {cn}",
                "Business growth {cn}",
                "Online marketing {cn}",
                "Professional {prefix} services",
            ],
            'secondary' => [
                "{sn} experts {cn}",
                "Certified {sn} {cn}",
                "Trusted {sn} provider {cn}",
            ],
        ];

        $keywords = [];
        $state = $city['state'] ?? $city['country'] ?? 'India';

        foreach ($patterns as $type => $templates) {
            foreach ($templates as $tpl) {
                $kw = ge_replace_tokens($tpl, [
                    'sn' => $sn,
                    'cn' => $cn,
                    'state' => $state,
                    'prefix' => str_replace('-', ' ', $prefix),
                ]);
                $keywords[] = ['keyword' => $kw, 'keyword_type' => $type];
            }
        }

        if (!empty($service['keywords_template'])) {
            foreach (explode(',', $service['keywords_template']) as $extra) {
                $extra = trim($extra);
                if ($extra) {
                    $keywords[] = [
                        'keyword' => ge_replace_tokens($extra, ['city' => $cn, 'service' => $sn, 'state' => $state]),
                        'keyword_type' => 'secondary',
                    ];
                }
            }
        }

        return $keywords;
    }
}
