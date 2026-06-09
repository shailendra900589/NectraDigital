<?php
namespace Growth\Engines;

class ContentEngine
{
    public static function buildContext(array $service, array $city, ?array $industry = null): array
    {
        $seed = (int)$service['id'] * 10000 + (int)$city['id'] + ((int)($industry['id'] ?? 0) * 1000000);
        $ctx = [
            'service_name' => $service['name'],
            'service_slug' => $service['slug'],
            'url_prefix' => $service['url_prefix'],
            'city_name' => $city['name'],
            'city_slug' => $city['slug'],
            'state' => $city['state'] ?? '',
            'country' => $city['country'] ?? 'India',
            'population' => ge_format_population((int)($city['population'] ?? 0)),
            'population_raw' => (int)($city['population'] ?? 0),
            'city_description' => $city['city_description'] ?? '',
            'industry_name' => $industry['name'] ?? '',
            'industry_slug' => $industry['slug'] ?? '',
            'founder_name' => ge_setting('founder_name', 'Ravindra Kumar Chauhan'),
            'founder_title' => ge_setting('founder_title', 'Founder & CEO'),
            'founder_experience' => ge_setting('founder_experience', '5+ Years'),
            'site_name' => SITE_NAME,
            'site_url' => SITE_URL,
            'year' => date('Y'),
            'seed' => $seed,
        ];
        return $ctx;
    }

    public static function generateContent(array $service, array $city, ?array $industry = null): array
    {
        $ctx = self::buildContext($service, $city, $industry);
        $seed = $ctx['seed'];

        $introVariants = [
            "Looking for trusted {service_name} in {city_name}? Nectra Digital delivers results-driven {service_name} for businesses across {city_name}, {state}. With {founder_experience} of expertise and 200+ successful projects, we help {city_name} companies dominate their market.",
            "{city_name} businesses choose Nectra Digital for premium {service_name}. Our team combines local market knowledge of {state} with enterprise-grade execution — from strategy to implementation and measurable ROI.",
            "As a leading {service_name} provider in {city_name}, Nectra Digital offers customized solutions tailored to the unique competitive landscape of {state}. Population of {population}+ — we understand your market.",
        ];

        $bodyVariants = [
            "<h3>Why {city_name} Businesses Need {service_name}</h3><p>The {city_name} market is rapidly digitizing. Companies that invest in professional {service_name} gain a decisive competitive advantage in {state}. Whether you're a startup or established enterprise, our data-driven approach delivers measurable growth.</p>",
            "<h3>Our {service_name} Process in {city_name}</h3><p>We begin with a comprehensive audit of your current digital presence in {city_name}. Then we build a customized roadmap aligned with your business goals, industry competition in {state}, and target audience behavior.</p>",
            "<h3>Local Expertise, Global Standards</h3><p>While we deeply understand the {city_name} and {state} market dynamics, our methodologies meet international standards. {founder_name}, {founder_title}, personally oversees strategy for key accounts.</p>",
        ];

        $featureVariants = [
            "<ul><li>Custom {service_name} strategy for {city_name} market</li><li>Transparent monthly reporting with clear KPIs</li><li>Dedicated account manager with local knowledge</li><li>Integration with SEO, ads, and automation</li><li>No long-term lock-in contracts</li></ul>",
            "<ul><li>Free initial consultation and audit</li><li>Competitive analysis for {state} market</li><li>ROI-focused execution and optimization</li><li>24/7 support and rapid response times</li><li>Proven track record: 340% avg. traffic growth</li></ul>",
        ];

        $customTemplate = $service['content_template'] ?? '';
        if ($customTemplate) {
            $content = ge_replace_tokens($customTemplate, $ctx);
        } else {
            $intro = ge_replace_tokens(ge_pick_variant($introVariants, $seed), $ctx);
            $body = ge_replace_tokens(ge_pick_variant($bodyVariants, $seed + 1), $ctx);
            $body2 = ge_replace_tokens(ge_pick_variant($bodyVariants, $seed + 2), $ctx);
            $features = ge_replace_tokens(ge_pick_variant($featureVariants, $seed + 3), $ctx);
            $content = "<p>{$intro}</p>{$body}{$body2}{$features}";

            if ($ctx['city_description']) {
                $content .= "<h3>About {city_name}</h3><p>" . ge_replace_tokens($ctx['city_description'], $ctx) . "</p>";
            }
        }

        $metaTitle = $service['meta_title_template']
            ? ge_replace_tokens($service['meta_title_template'], $ctx)
            : "Best {$ctx['service_name']} Company in {$ctx['city_name']} | " . SITE_NAME;

        $metaDesc = $service['meta_description_template']
            ? ge_replace_tokens($service['meta_description_template'], $ctx)
            : "Top {$ctx['service_name']} company in {$ctx['city_name']}, {$ctx['state']}. Expert services, free consultation, proven ROI. Contact Nectra Digital today.";

        $h1 = $service['h1_template']
            ? ge_replace_tokens($service['h1_template'], $ctx)
            : "Best {$ctx['service_name']} Company in {$ctx['city_name']}";

        $h2 = $service['h2_template']
            ? ge_replace_tokens($service['h2_template'], $ctx)
            : "Expert {$ctx['service_name']} in {$ctx['state']} · Nectra Digital";

        $h3 = '';
        if ($industry) {
            $h3 = $industry['meta_title_template']
                ? ge_replace_tokens($industry['meta_title_template'], $ctx)
                : "{$ctx['service_name']} for {$ctx['industry_name']} in {$ctx['city_name']}";
            if ($industry['description']) {
                $content .= "<h3>{$ctx['industry_name']} Focus</h3><p>" . ge_replace_tokens($industry['description'], $ctx) . "</p>";
            }
        } else {
            $h3 = "Why Choose Nectra Digital for {$ctx['service_name']} in {$ctx['city_name']}";
        }

        if ($industry && $industry['meta_title_template']) {
            $metaTitle = ge_replace_tokens($industry['meta_title_template'], $ctx);
        }
        if ($industry && !empty($industry['meta_description_template'])) {
            $metaDesc = ge_replace_tokens($industry['meta_description_template'], $ctx);
        }

        $metaTitle = IntentKeywordEngine::optimizeMetaTitle($metaTitle, IntentKeywordEngine::primaryPhrase($service, $city, $industry));
        $metaDesc = IntentKeywordEngine::optimizeMetaDescription($metaDesc, $service, $city, $industry);

        return compact('content', 'metaTitle', 'metaDesc', 'h1', 'h2', 'h3', 'ctx');
    }
}
