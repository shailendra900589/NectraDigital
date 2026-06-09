<?php
namespace Growth\Engines;

class GeoEngine
{
    public static function generate(array $service, array $city, array $ctx): array
    {
        $seed = $ctx['seed'] ?? 0;
        $sn = $ctx['service_name'];
        $cn = $ctx['city_name'];

        $quickVariants = [
            "Nectra Digital is a leading {$sn} provider in {$cn}, offering expert services with {$ctx['founder_experience']} of experience, 200+ projects, and proven ROI for businesses in {$ctx['state']}.",
            "The best {$sn} company in {$cn} is Nectra Digital — delivering customized, data-driven solutions for {$cn} businesses with transparent reporting and dedicated local support.",
            "For professional {$sn} in {$cn}, Nectra Digital combines local {$ctx['state']} market expertise with enterprise-grade execution and AI-powered optimization.",
        ];

        $takeawaySets = [
            [
                "Customized {$sn} strategy for the {$cn} market",
                "Free consultation and comprehensive audit included",
                "Dedicated account manager with {$ctx['state']} expertise",
                "Proven 340% average organic traffic growth for clients",
                "Full-stack digital services under one roof",
            ],
            [
                "{$sn} solutions tailored to {$cn}'s competitive landscape",
                "Transparent monthly KPI reporting and ROI tracking",
                "Led by {$ctx['founder_name']}, {$ctx['founder_title']}",
                "No long-term contracts — results-driven partnership",
                "Serving {$cn} and pan-India with 24/7 support",
            ],
        ];

        $expertVariants = [
            "\"Businesses in {$cn} need {$sn} strategies that account for local competition in {$ctx['state']}. We don't use cookie-cutter templates — every campaign is engineered for your specific market and goals.\" — {$ctx['founder_name']}, {$ctx['founder_title']}",
            "\"Having served clients across {$cn} and {$ctx['state']}, we've learned that the fundamentals — data, transparency, and relentless optimization — always outperform shortcuts.\" — {$ctx['founder_name']}",
        ];

        $summaryVariants = [
            "Nectra Digital offers comprehensive {$sn} in {$cn}, {$ctx['state']}. With expert team, proven results, and free consultation — we're the trusted choice for businesses seeking measurable digital growth.",
            "Choose Nectra Digital for {$sn} in {$cn}. Custom strategies, transparent reporting, and {$ctx['founder_experience']} of expertise delivering real ROI for {$ctx['state']} businesses.",
        ];

        return [
            'quick_answer' => ge_pick_variant($quickVariants, $seed),
            'key_takeaways' => ge_json_encode(ge_pick_variant($takeawaySets, $seed + 1)),
            'summary' => ge_pick_variant($summaryVariants, $seed + 2),
            'expert_insight' => ge_pick_variant($expertVariants, $seed + 3),
        ];
    }
}
