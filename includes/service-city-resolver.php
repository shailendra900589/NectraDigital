<?php
/**
 * Resolve service × city landing pages from DB slug or URL pattern.
 */
require_once __DIR__ . '/seo-data.php';
require_once __DIR__ . '/text-utils.php';
require_once __DIR__ . '/growth/helpers.php';
require_once __DIR__ . '/service-content.php';

function ge_city_from_slug(string $citySlug): ?array
{
    $cities = get_cities_data();
    if (isset($cities[$citySlug])) {
        return $cities[$citySlug];
    }

    if (is_file(__DIR__ . '/db.local.php') && file_exists(__DIR__ . '/growth/bootstrap.php')) {
        require_once __DIR__ . '/growth/bootstrap.php';
        if (function_exists('ge_is_ready') && ge_is_ready()) {
            $row = \Growth\Models\City::findBySlug($citySlug);
            if ($row) {
                return [
                    'name' => $row['name'],
                    'state' => $row['state'] ?? '',
                    'is_hq' => !empty($row['is_hq']),
                    'city_description' => $row['city_description'] ?? '',
                ];
            }
        }
    }

    return null;
}

function ge_service_base_data(string $serviceSlug, ?array $geServiceRecord = null): ?array
{
    $services = get_services_data();
    if (isset($services[$serviceSlug])) {
        return $services[$serviceSlug];
    }

    if ($geServiceRecord === null && is_file(__DIR__ . '/db.local.php') && file_exists(__DIR__ . '/growth/bootstrap.php')) {
        require_once __DIR__ . '/growth/bootstrap.php';
        if (function_exists('ge_is_ready') && ge_is_ready()) {
            $geServiceRecord = \Growth\Models\Service::findBySlug($serviceSlug);
        }
    }

    if ($geServiceRecord) {
        return ge_minimal_service_from_record($geServiceRecord);
    }

    return null;
}

function ge_service_slug_from_url_prefix(string $prefix): ?string
{
    foreach (get_services_data() as $slug => $data) {
        if (ge_static_service_url_prefix($slug) === $prefix) {
            return $slug;
        }
    }

    if (is_file(__DIR__ . '/db.local.php') && file_exists(__DIR__ . '/growth/bootstrap.php')) {
        require_once __DIR__ . '/growth/bootstrap.php';
        if (function_exists('ge_is_ready') && ge_is_ready()) {
            $db = ge_conn();
            $stmt = $db->prepare('SELECT slug FROM ge_services WHERE url_prefix = ? AND status = ? LIMIT 1');
            $active = 'active';
            $stmt->bind_param('ss', $prefix, $active);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if ($row) {
                return $row['slug'];
            }
        }
    }

    return null;
}

function ge_parse_service_city_slug(string $slug): ?array
{
    if (!preg_match('/^(.+)-company-in-([a-z][a-z0-9-]*)$/', $slug, $m)) {
        return null;
    }

    $serviceSlug = ge_service_slug_from_url_prefix($m[1]);
    $citySlug = $m[2];
    $city = ge_city_from_slug($citySlug);

    if (!$serviceSlug || !$city) {
        return null;
    }

    return [
        'service_slug' => $serviceSlug,
        'city_slug' => $citySlug,
        'city' => $city,
        'landing_page' => null,
    ];
}

function ge_dedupe_faqs(array $faqs): array
{
    $seen = [];
    $out = [];
    foreach ($faqs as $faq) {
        if (empty($faq['q'])) {
            continue;
        }
        $key = strtolower(trim($faq['q']));
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $out[] = $faq;
    }
    return $out;
}

function ge_city_faqs_for_service(array $service, array $city, string $citySlug): array
{
    $cityName = $city['name'];
    $state = $city['state'] ?? '';
    $silo = $service['silo'] ?? $service['h1'];
    $isHq = !empty($city['is_hq']);

    $local = [
        [
            'q' => "Why choose Nectra Digital for {$silo} in {$cityName}?",
            'a' => "We combine deep {$cityName} market knowledge with proven {$silo} execution — local keyword targeting, competitive analysis in {$state}, and dedicated account management for businesses in {$cityName}.",
        ],
        [
            'q' => "How much does {$silo} cost in {$cityName}?",
            'a' => "Packages for {$cityName} businesses typically start from ₹15,000/month depending on scope and competition. We provide a free audit and custom proposal for {$cityName} clients.",
        ],
        [
            'q' => "Do you have a team in {$cityName}?",
            'a' => $isHq
                ? "Yes — {$cityName} is our global headquarters. We serve clients across {$state} and India with on-site and remote delivery."
                : "We serve {$cityName} with dedicated remote account managers, video consultations, and on-site meetings when required. HQ: Lucknow, pan-India operations.",
        ],
        [
            'q' => "How fast can you start a {$silo} project in {$cityName}?",
            'a' => "We can kick off within 48 hours of agreement. Discovery, audit, and strategy for {$cityName} accounts typically completes within one week.",
        ],
    ];

    return array_merge($local, $service['faqs'] ?? []);
}

function ge_resolve_service_city_page(string $slug): ?array
{
    $slug = preg_replace('/[^a-z0-9-]/', '', strtolower($slug));
    if ($slug === '') {
        return null;
    }

    $landing = null;
    $serviceSlug = null;
    $citySlug = null;
    $city = null;
    $geServiceRecord = null;

    if (is_file(__DIR__ . '/db.local.php') && file_exists(__DIR__ . '/growth/bootstrap.php')) {
        require_once __DIR__ . '/growth/bootstrap.php';
        if (function_exists('ge_is_ready') && ge_is_ready()) {
            $landing = \Growth\Models\LandingPage::findBySlug($slug);
            if ($landing) {
                $geServiceRecord = \Growth\Models\Service::find((int)$landing['service_id']);
                $serviceSlug = $geServiceRecord['slug'] ?? ge_service_slug_from_url_prefix($landing['url_prefix'] ?? '');
                $citySlug = $landing['city_slug'] ?? null;
                if (!$citySlug && !empty($landing['city_name'])) {
                    foreach (get_cities_data() as $cs => $c) {
                        if ($c['name'] === $landing['city_name']) {
                            $citySlug = $cs;
                            break;
                        }
                    }
                }
                if (!$citySlug && !empty($landing['city_id'])) {
                    $cityRow = \Growth\Models\City::find((int)$landing['city_id']);
                    if ($cityRow) {
                        $citySlug = $cityRow['slug'];
                    }
                }
            }
        }
    }

    if (!$serviceSlug || !$citySlug) {
        $parsed = ge_parse_service_city_slug($slug);
        if (!$parsed && !$landing) {
            return null;
        }
        if ($parsed) {
            $serviceSlug = $parsed['service_slug'];
            $citySlug = $parsed['city_slug'];
            $city = $parsed['city'];
            if (!$landing) {
                $landing = $parsed['landing_page'];
            }
        }
    }

    if (!$serviceSlug || !$citySlug) {
        return null;
    }

    if (!$geServiceRecord && is_file(__DIR__ . '/db.local.php') && file_exists(__DIR__ . '/growth/bootstrap.php')) {
        if (!function_exists('ge_is_ready')) {
            require_once __DIR__ . '/growth/bootstrap.php';
        }
        if (function_exists('ge_is_ready') && ge_is_ready()) {
            $geServiceRecord = \Growth\Models\Service::findBySlug($serviceSlug);
        }
    }

    $serviceBase = ge_service_base_data($serviceSlug, $geServiceRecord);
    if (!$serviceBase) {
        return null;
    }

    if (!$city) {
        $city = ge_city_from_slug($citySlug);
        if (!$city) {
            return null;
        }
    }

    $service = get_service_extended($serviceSlug, $serviceBase);
    $cityName = $city['name'];
    $cityState = $city['state'] ?? '';
    $silo = $service['silo'] ?? $service['h1'];

    $h1 = $landing['h1'] ?? "Best {$silo} Company in {$cityName}";
    $h2 = $landing['h2'] ?? "Professional {$silo} Services in {$cityState}";
    $pageTitle = $landing['meta_title'] ?? "Best {$silo} in {$cityName} | Nectra Digital";
    $pageDesc = $landing['meta_description'] ?? "Top {$silo} company in {$cityName}, {$cityState}. Expert services, free consultation, proven ROI. Contact Nectra Digital.";
    $intro = $service['intro'] . " Serving businesses in {$cityName}, {$cityState} with local market expertise and measurable results.";
    $overviewExtra = '';
    if (!empty($landing['content'])) {
        $overviewExtra = $landing['content'];
    } elseif (!empty($city['city_description'])) {
        $overviewExtra = '<p>Based in India with strong delivery for the <strong>' . htmlspecialchars($cityName) . '</strong> market — ' . htmlspecialchars($city['city_description']) . '</p>';
    }

    $faqs = ge_city_faqs_for_service($service, $city, $citySlug);
    if (!empty($landing['faq_json'])) {
        $dbFaqs = ge_json_decode($landing['faq_json']);
        if (!empty($dbFaqs)) {
            $faqs = array_merge($dbFaqs, $faqs);
        }
    }
    $faqs = ge_dedupe_faqs($faqs);

    return [
        'service_slug' => $serviceSlug,
        'service' => $service,
        'city_slug' => $citySlug,
        'city_name' => $cityName,
        'city_state' => $cityState,
        'city' => $city,
        'landing_page' => $landing,
        'is_city_page' => true,
        'localized_h1' => nectra_decode_entities($h1),
        'localized_h2' => nectra_decode_entities($h2),
        'localized_intro' => nectra_decode_entities($intro),
        'localized_overview_extra' => $overviewExtra,
        'localized_faqs' => $faqs,
        'page_title' => nectra_decode_entities($pageTitle),
        'page_desc' => nectra_decode_entities($pageDesc),
        'quick_answer' => nectra_decode_entities($landing['quick_answer'] ?? $intro),
    ];
}
