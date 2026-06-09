<?php
namespace Growth\Engines;

class KeywordEngine
{
    public static function generateForPage(array $service, array $city, ?array $industry = null): array
    {
        $sn = $service['name'];
        $cn = $city['name'];
        $in = $industry['name'] ?? '';
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
            'long_tail' => [
                "Best {sn} company for small business in {cn}",
                "Affordable {sn} agency {cn} {state}",
                "Top rated {sn} services near {cn}",
            ],
            'semantic' => [
                "Digital growth partner {cn}",
                "Marketing automation {cn}",
                "Online visibility {state}",
            ],
        ];

        if ($in) {
            $patterns['primary'][] = "{sn} for {in} in {cn}";
            $patterns['commercial'][] = "{sn} {in} company {cn}";
            $patterns['local'][] = "{in} {sn} {cn}";
            $patterns['long_tail'][] = "Best {sn} for {in} businesses in {cn}";
            $patterns['semantic'][] = "{in} digital marketing {cn}";
        }

        $keywords = [];
        $state = $city['state'] ?? $city['country'] ?? 'India';

        foreach ($patterns as $type => $templates) {
            foreach ($templates as $tpl) {
                $kw = ge_replace_tokens($tpl, [
                    'sn' => $sn,
                    'cn' => $cn,
                    'in' => $in,
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
