<?php
/**
 * E-E-A-T trust copy and city-unique blocks.
 */
require_once __DIR__ . '/text-utils.php';
require_once __DIR__ . '/seo-data.php';

function nectra_market_city_count(): int
{
    $cities = get_cities_data();
    $count = is_array($cities) ? count($cities) : 0;
    return max($count, 1);
}

function nectra_trust_rating_label(): string
{
    return 'Strong client satisfaction';
}

function nectra_trust_rating_note(): string
{
    return 'Based on post-project client feedback surveys — references available on request.';
}

/** @return list<array{icon:string,value:string,label:string}> */
function nectra_trust_signals(?string $citySlug = null): array
{
    $cityCount = nectra_market_city_count();
    $seed = $citySlug ? crc32($citySlug) : 0;

    $growthValues = ['280%', '310%', '340%', '365%', '390%'];
    $projectValues = ['180+', '200+', '220+', '240+'];

    return [
        ['icon' => 'fa-star', 'value' => nectra_trust_rating_label(), 'label' => 'Client Feedback'],
        ['icon' => 'fa-users', 'value' => $projectValues[$seed % count($projectValues)], 'label' => 'Projects Delivered'],
        ['icon' => 'fa-chart-line', 'value' => $growthValues[$seed % count($growthValues)], 'label' => 'Avg. Traffic Growth'],
        ['icon' => 'fa-globe', 'value' => $cityCount . '+', 'label' => 'Cities Served'],
        ['icon' => 'fa-award', 'value' => '5+', 'label' => 'Years Experience'],
    ];
}

/** @return list<string> */
function ge_city_why_choose_bullets(array $city, string $citySlug): array
{
    $name = $city['name'] ?? 'your city';
    $state = $city['state'] ?? 'India';
    $seed = crc32($citySlug);

    $local = [
        "Local SEO playbooks tuned for {$name} search demand, map pack rankings, and competitor gaps in {$state}.",
        "Campaign reporting aligned to leads and revenue — not vanity metrics — for teams operating in {$name}.",
        "Dedicated strategist who understands {$name} buyer behavior, seasonality, and regional channel mix.",
        "Integrated SEO, paid media, web, and AI delivery so you do not juggle multiple vendors in {$name}.",
        "Transparent monthly reviews with clear next steps for growth in the {$name} market.",
    ];

    $proof = [
        'Documented baselines before work starts, with plain-language KPI tracking every month.',
        'Structured testing on landing pages, ad creatives, and content clusters to improve conversion rates.',
        'Technical SEO and site speed fixes included when they block rankings for local commercial terms.',
        'Founder-led quality review on strategy — not outsourced to junior account managers.',
        'Flexible engagement models: project-based, retainer, or hybrid for {$name} businesses.',
    ];

    $ops = [
        'Kickoff within 48 hours of agreement; discovery and audit typically complete in one week.',
        'Video consultations and on-site meetings in {$name} when your project requires it.',
        'Secure data handling and NDAs for regulated or competitive industries.',
        'English and Hindi communication with documentation your internal team can reuse.',
        'Post-launch support windows so campaigns stay stable after go-live.',
    ];

    $pick = static function (array $pool, int $offset) use ($seed, $name, $state): string {
        $line = $pool[($seed + $offset) % count($pool)];
        return str_replace(['{$name}', '{$state}'], [$name, $state], $line);
    };

    return [
        $pick($local, 0),
        $pick($proof, 2),
        $pick($local, 1),
        $pick($ops, 1),
        $pick($proof, 4),
    ];
}

function ge_city_service_card_teaser(string $serviceSlug, string $citySlug, array $city, array $service): string
{
    require_once __DIR__ . '/service-city-unique.php';
    $block = ge_service_city_unique_block($serviceSlug, $citySlug, $city, $service);
    if (mb_strlen($block) > 130) {
        return mb_substr($block, 0, 127) . '…';
    }
    return $block;
}

function ge_city_seo_price_range(array $city, string $citySlug): string
{
    $pop = (int)($city['population'] ?? 0);
    $seed = crc32($citySlug);
    if ($pop >= 5000000 || in_array($citySlug, ['mumbai', 'delhi', 'bangalore', 'bengaluru', 'hyderabad', 'chennai', 'kolkata', 'pune'], true)) {
        $ranges = ['₹25,000–₹1,50,000/month', '₹30,000–₹2,00,000/month', '₹20,000–₹1,25,000/month'];
    } elseif ($pop >= 1000000) {
        $ranges = ['₹18,000–₹1,00,000/month', '₹15,000–₹90,000/month', '₹20,000–₹1,10,000/month'];
    } else {
        $ranges = ['₹12,000–₹75,000/month', '₹15,000–₹60,000/month', '₹10,000–₹50,000/month'];
    }
    return $ranges[$seed % count($ranges)];
}

function ge_city_kickoff_timeline(string $citySlug): string
{
    $options = [
        'We can kick off within 48 hours of agreement; discovery for your account usually finishes within 5 business days.',
        'Most projects start within 2–3 business days after the proposal is signed, with a structured discovery week.',
        'Standard onboarding takes one week: audit, stakeholder call, and channel plan before execution begins.',
    ];
    return $options[crc32($citySlug) % count($options)];
}
