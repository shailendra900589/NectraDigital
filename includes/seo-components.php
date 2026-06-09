<?php
/**
 * Reusable SEO components for Nectra Digital
 */
require_once __DIR__ . '/seo-data.php';

if (!function_exists('nectra_display_text')) {
    require_once __DIR__ . '/text-utils.php';
}

function render_breadcrumbs($items) {
    echo '<nav aria-label="breadcrumb" class="mb-4"><ol class="breadcrumb bg-transparent p-0 m-0 small">';
    foreach ($items as $i => $item) {
        $is_last = ($i === count($items) - 1);
        if ($is_last) {
            echo '<li class="breadcrumb-item active text-neon" aria-current="page">' . nectra_display_text($item['name']) . '</li>';
        } else {
            echo '<li class="breadcrumb-item"><a href="' . htmlspecialchars($item['url']) . '" class="text-white-50 text-decoration-none">' . nectra_display_text($item['name']) . '</a></li>';
        }
    }
    echo '</ol></nav>';
}

function render_faq_section($faqs, $title = 'Frequently Asked Questions') {
    if (empty($faqs)) return;
    echo '<section class="py-5 border-top border-secondary"><div class="container py-4">';
    echo '<h2 class="text-white h3 mb-4">' . htmlspecialchars($title) . '</h2>';
    echo '<div class="accordion accordion-flush" id="faqAccordion">';
    foreach ($faqs as $i => $faq) {
        $id = 'faq' . $i;
        echo '<div class="accordion-item bg-transparent border-secondary">';
        echo '<h3 class="accordion-header"><button class="accordion-button ' . ($i > 0 ? 'collapsed' : '') . ' bg-dark text-white" type="button" data-bs-toggle="collapse" data-bs-target="#' . $id . '">' . htmlspecialchars($faq['q']) . '</button></h3>';
        echo '<div id="' . $id . '" class="accordion-collapse collapse ' . ($i === 0 ? 'show' : '') . '" data-bs-parent="#faqAccordion">';
        echo '<div class="accordion-body text-white-50">' . htmlspecialchars($faq['a']) . '</div></div></div>';
    }
    echo '</div></div></section>';
    output_faq_schema($faqs);
}

function render_cta_blocks($variant = 'full') {
    $ctas = [
        ['label' => 'Get Free SEO Audit', 'url' => '/contact?service=SEO+Audit', 'icon' => 'fa-search'],
        ['label' => 'Book Free Consultation', 'url' => '/contact?service=Consultation', 'icon' => 'fa-calendar-check'],
        ['label' => 'Request Proposal', 'url' => '/contact?service=Proposal', 'icon' => 'fa-file-alt'],
        ['label' => 'Talk To Expert', 'url' => '/contact?service=Expert+Call', 'icon' => 'fa-headset'],
        ['label' => 'Schedule Strategy Call', 'url' => '/contact?service=Strategy+Call', 'icon' => 'fa-phone'],
    ];
    echo '<section class="py-5 bg-darker border-top border-secondary"><div class="container py-3">';
    echo '<div class="text-center mb-4"><h2 class="text-white h4">Ready to <span class="text-neon">Grow?</span></h2>';
    echo '<p class="text-white-50 small">Choose how you\'d like to connect with our team.</p></div>';
    echo '<div class="row g-3 justify-content-center">';
    $show = ($variant === 'full') ? $ctas : array_slice($ctas, 0, 3);
    foreach ($show as $cta) {
        echo '<div class="col-md-6 col-lg-4 col-xl">';
        echo '<a href="' . $cta['url'] . '" class="d-block p-3 border border-secondary rounded bg-glass text-decoration-none text-center hover-effect h-100">';
        echo '<i class="fas ' . $cta['icon'] . ' text-neon fa-lg mb-2"></i>';
        echo '<span class="d-block text-white small fw-bold">' . $cta['label'] . '</span></a></div>';
    }
    echo '</div></div></section>';
}

function render_founder_section($compact = false) {
    echo '<section class="py-5 ' . ($compact ? '' : 'bg-darker border-top border-secondary') . '"><div class="container py-4">';
    echo '<div class="row align-items-center">';
    echo '<div class="col-lg-4 mb-4 mb-lg-0 text-center">';
    echo '<div class="mx-auto rounded-circle border border-neon d-flex align-items-center justify-content-center bg-dark" style="width:180px;height:180px;">';
    echo '<i class="fas fa-user-tie fa-4x text-neon opacity-75"></i></div></div>';
    echo '<div class="col-lg-8">';
    echo '<h6 class="text-neon text-uppercase mb-2" style="letter-spacing:2px;">Founder & Leadership</h6>';
    echo '<h2 class="text-white h3 mb-3">' . FOUNDER_NAME . '</h2>';
    echo '<p class="text-neon small text-uppercase mb-3">' . FOUNDER_TITLE . ' · ' . FOUNDER_EXPERIENCE . ' Experience</p>';
    echo '<p class="text-white-50">' . FOUNDER_NAME . ' founded Nectra Digital with a mission to bridge complex technology and measurable business growth. With expertise spanning SEO, digital marketing, AI automation, and software development, he leads a team dedicated to delivering ROI-driven digital solutions for businesses across India and globally.</p>';
    echo '<div class="d-flex flex-wrap gap-2 mb-3">';
    foreach (FOUNDER_EXPERTISE as $skill) {
        echo '<span class="badge bg-dark border border-secondary text-white-50">' . $skill . '</span>';
    }
    echo '</div>';
    echo '<a href="' . FOUNDER_LINKEDIN . '" target="_blank" rel="noopener noreferrer" class="btn btn-outline-light btn-sm"><i class="fab fa-linkedin me-2"></i>Connect on LinkedIn</a>';
    echo ' <a href="/about" class="btn btn-outline-secondary btn-sm ms-2">Full Bio</a>';
    echo '</div></div></div></section>';
}

function render_author_bio($compact = true) {
    echo '<div class="p-4 border border-secondary rounded bg-glass mt-5" itemscope itemtype="https://schema.org/Person">';
    echo '<div class="d-flex align-items-start gap-3">';
    echo '<div class="flex-shrink-0 rounded-circle border border-neon d-flex align-items-center justify-content-center bg-dark" style="width:60px;height:60px;"><i class="fas fa-user-tie text-neon"></i></div>';
    echo '<div><h4 class="text-white h6 mb-1" itemprop="name">' . FOUNDER_NAME . '</h4>';
    echo '<p class="text-neon small mb-2" itemprop="jobTitle">' . FOUNDER_TITLE . '</p>';
    echo '<p class="text-white-50 small mb-2" itemprop="description">SEO expert and digital strategist with ' . FOUNDER_EXPERIENCE . ' of experience. Author verified by Nectra Digital editorial team.</p>';
    echo '<a href="' . FOUNDER_LINKEDIN . '" target="_blank" rel="noopener" class="text-neon small" itemprop="sameAs"><i class="fab fa-linkedin"></i> LinkedIn</a>';
    echo ' · <a href="/editorial-guidelines" class="text-white-50 small">Editorial Guidelines</a></div></div></div>';
}

function render_geo_blocks($title, $content_excerpt = '') {
    $sentences = preg_split('/(?<=[.!?])\s+/', strip_tags($content_excerpt), 3);
    $quick = isset($sentences[0]) ? $sentences[0] : 'This article provides expert insights on ' . $title . ' from Nectra Digital, a leading SEO and digital marketing agency in India.';
    
    echo '<div class="geo-blocks mb-5">';
    echo '<div class="p-4 border border-neon rounded bg-glass mb-4"><h2 class="text-neon h6 text-uppercase mb-2"><i class="fas fa-bolt me-2"></i>Quick Answer</h2>';
    echo '<p class="text-white mb-0">' . htmlspecialchars($quick) . '</p></div>';
    
    echo '<div class="p-4 border border-secondary rounded bg-dark mb-4"><h2 class="text-white h6 mb-3"><i class="fas fa-list-check text-neon me-2"></i>Key Takeaways</h2><ul class="text-white-50 small mb-0">';
    echo '<li>Actionable strategies backed by 5+ years of industry experience</li>';
    echo '<li>Data-driven approach from Nectra Digital SEO experts</li>';
    echo '<li>Applicable for businesses in India and global markets</li>';
    echo '<li>Updated for 2026 best practices and algorithm changes</li></ul></div>';
    
    echo '<div class="p-4 border border-secondary rounded bg-glass mb-4"><h2 class="text-white h6 mb-2"><i class="fas fa-lightbulb text-neon me-2"></i>Expert Insight</h2>';
    echo '<p class="text-white-50 small mb-2"><strong class="text-white">' . FOUNDER_NAME . '</strong>, ' . FOUNDER_TITLE . ':</p>';
    echo '<p class="text-white-50 small fst-italic mb-0">"The strategies outlined here reflect what we implement daily for clients achieving 340%+ organic growth. Focus on fundamentals first — technical SEO, content authority, and user experience — before chasing trends."</p></div>';
    echo '</div>';
}

function render_geo_summary($title) {
    echo '<div class="p-4 border-start border-neon border-3 bg-glass mt-5"><h2 class="text-white h5 mb-3">Summary</h2>';
    echo '<p class="text-white-50 small mb-0">This guide on ' . htmlspecialchars($title) . ' provides comprehensive, expert-verified information from Nectra Digital. For personalized strategy and implementation, <a href="/contact" class="text-neon">book a free consultation</a> with our team.</p></div>';
}

function render_internal_links_service($related_slugs) {
    $services = get_services_data();
    echo '<section class="py-4 border-top border-secondary"><div class="container"><h3 class="text-white h6 mb-3">Related Services</h3><div class="row g-2">';
    foreach ($related_slugs as $slug) {
        if (!isset($services[$slug])) continue;
        $s = $services[$slug];
        echo '<div class="col-md-6 col-lg-4"><a href="/' . $slug . '" class="d-block p-3 border border-secondary rounded text-decoration-none hover-effect">';
        echo '<i class="fas ' . $s['icon'] . ' text-neon me-2"></i><span class="text-white small">' . htmlspecialchars($s['h1']) . '</span></a></div>';
    }
    echo '<div class="col-md-6 col-lg-4"><a href="/insights" class="d-block p-3 border border-secondary rounded text-decoration-none hover-effect">';
    echo '<i class="fas fa-newspaper text-neon me-2"></i><span class="text-white small">Latest Intel & Blog</span></a></div>';
    echo '<div class="col-md-6 col-lg-4"><a href="/contact" class="d-block p-3 border border-secondary rounded text-decoration-none hover-effect">';
    echo '<i class="fas fa-envelope text-neon me-2"></i><span class="text-white small">Contact Our Team</span></a></div>';
    echo '</div></div></section>';
}

function render_post_internal_links($conn, $post, $category) {
    $services = get_services_data();
    $links = [];
    
    $cat_map = [
        'SEO' => ['seo-services', 'technical-seo-services', 'local-seo-services'],
        'Marketing' => ['digital-marketing-services', 'ppc-management'],
        'AI' => ['ai-automation-services', 'ai-chatbot-development'],
        'Web' => ['web-development-services', 'ecommerce-development'],
        'Tech' => ['software-development-services', 'mobile-app-development'],
    ];
    
    $matched = false;
    foreach ($cat_map as $cat => $slugs) {
        if (stripos($category, $cat) !== false) {
            foreach ($slugs as $slug) {
                if (isset($services[$slug])) $links[] = ['url' => '/' . $slug, 'title' => $services[$slug]['h1']];
            }
            $matched = true;
            break;
        }
    }
    if (!$matched) {
        $links[] = ['url' => '/seo-services', 'title' => 'SEO Services India'];
        $links[] = ['url' => '/digital-marketing-services', 'title' => 'Digital Marketing Agency'];
        $links[] = ['url' => '/ai-automation-services', 'title' => 'AI Automation Services'];
    }
    
    $links[] = ['url' => '/services', 'title' => 'All Services'];
    $links[] = ['url' => '/contact', 'title' => 'Contact Us'];
    $links[] = ['url' => '/about', 'title' => 'About Nectra Digital'];
    $links[] = ['url' => '/hire-experts', 'title' => 'Hire Experts'];
    
    if ($conn) {
        $pid = $post['id'];
        $stmt = $conn->prepare("SELECT slug, title FROM blog_posts WHERE id != ? AND category = ? ORDER BY created_at DESC LIMIT 4");
        $stmt->bind_param("is", $pid, $category);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $links[] = ['url' => '/' . $row['slug'], 'title' => nectra_decode_entities($row['title'])];
        }
    }
    
    $cities = array_slice(array_keys(get_cities_data()), 0, 3);
    foreach ($cities as $city) {
        $links[] = ['url' => '/digital-agency-' . $city, 'title' => 'Digital Agency ' . ucfirst($city)];
    }
    
    echo '<section class="py-4 mt-4 border-top border-secondary"><h3 class="text-white h6 mb-3"><i class="fas fa-link text-neon me-2"></i>Related Resources</h3><div class="row g-2">';
    foreach (array_slice($links, 0, 12) as $link) {
        echo '<div class="col-md-6"><a href="' . $link['url'] . '" class="text-neon small text-decoration-none"><i class="fas fa-angle-right me-1"></i>' . nectra_display_text($link['title']) . '</a></div>';
    }
    echo '</div></section>';
}

function render_trust_signals() {
    echo '<section class="py-4 border-top border-bottom border-secondary bg-dark"><div class="container"><div class="row g-4 text-center">';
    $signals = [
        ['icon' => 'fa-star', 'value' => '4.9/5', 'label' => 'Client Rating'],
        ['icon' => 'fa-users', 'value' => '200+', 'label' => 'Projects Delivered'],
        ['icon' => 'fa-chart-line', 'value' => '340%', 'label' => 'Avg. Traffic Growth'],
        ['icon' => 'fa-globe', 'value' => '15+', 'label' => 'Cities Served'],
        ['icon' => 'fa-award', 'value' => '5+', 'label' => 'Years Experience'],
    ];
    foreach ($signals as $s) {
        echo '<div class="col"><i class="fas ' . $s['icon'] . ' text-neon fa-lg mb-2"></i>';
        echo '<div class="text-white fw-bold">' . $s['value'] . '</div>';
        echo '<small class="text-white-50">' . $s['label'] . '</small></div>';
    }
    echo '</div></div></section>';
}

function render_usp_section() {
    echo '<section class="py-5"><div class="container py-4"><div class="text-center mb-5">';
    echo '<h6 class="text-neon text-uppercase mb-2">Why Nectra Digital</h6>';
    echo '<h2 class="text-white">Your <span class="text-neon">Competitive Edge</span></h2></div><div class="row g-4">';
    $usps = [
        ['icon' => 'fa-search', 'title' => 'SEO-First Architecture', 'desc' => 'Every website, app, and campaign is built with search engine optimization at its core — not as an afterthought.'],
        ['icon' => 'fa-robot', 'title' => 'AI-Powered Automation', 'desc' => 'Reduce operational costs by 70% with custom AI agents, chatbots, and intelligent workflow automation.'],
        ['icon' => 'fa-chart-bar', 'title' => 'ROI-Driven Results', 'desc' => 'We measure success in revenue, leads, and conversions — not vanity metrics. Transparent reporting every month.'],
        ['icon' => 'fa-shield-alt', 'title' => 'Enterprise-Grade Security', 'desc' => '256-bit encryption, secure coding practices, and compliance with GDPR, CCPA, and global data standards.'],
        ['icon' => 'fa-users-cog', 'title' => 'Dedicated Expert Teams', 'desc' => 'Work directly with senior SEO strategists, developers, and marketers — not junior account managers.'],
        ['icon' => 'fa-globe-asia', 'title' => 'Pan-India + Global Reach', 'desc' => 'Headquartered in Lucknow with active operations across 20+ Indian cities and international clients worldwide.'],
    ];
    foreach ($usps as $u) {
        echo '<div class="col-md-6 col-lg-4"><div class="p-4 border border-secondary rounded bg-glass h-100">';
        echo '<i class="fas ' . $u['icon'] . ' text-neon fa-2x mb-3"></i>';
        echo '<h3 class="text-white h6">' . $u['title'] . '</h3>';
        echo '<p class="text-white-50 small mb-0">' . $u['desc'] . '</p></div></div>';
    }
    echo '</div></div></section>';
}

function render_service_schema($slug, $service) {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service['h1'],
        'description' => $service['intro'],
        'provider' => ['@id' => SITE_URL . '/#organization'],
        'areaServed' => ['@type' => 'Country', 'name' => 'India'],
        'url' => SITE_URL . '/' . $slug,
        'serviceType' => $service['silo'],
        'offers' => [
            '@type' => 'Offer',
            'availability' => 'https://schema.org/InStock',
            'priceCurrency' => 'INR',
            'url' => SITE_URL . '/contact?service=' . urlencode($service['h1']),
        ],
    ];
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';

    if (!empty($service['faqs'])) {
        $faqItems = array_map(function ($f) {
            return [
                '@type' => 'Question',
                'name' => $f['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
            ];
        }, $service['faqs']);
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqItems,
        ];
        echo '<script type="application/ld+json">' . json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}

function render_local_business_schema($city_data, $city_slug) {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'Nectra Digital — ' . $city_data['name'],
        'description' => 'SEO, digital marketing, AI automation, and web development services in ' . $city_data['name'] . ', ' . $city_data['state'] . '.',
        'url' => SITE_URL . '/digital-agency-' . $city_slug,
        'email' => 'contact@nectradigital.com',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $city_data['name'],
            'addressRegion' => $city_data['state'],
            'addressCountry' => 'IN'
        ],
        'parentOrganization' => ['@id' => SITE_URL . '/#organization'],
        'priceRange' => '₹₹₹',
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday'],
            'opens' => '09:00',
            'closes' => '18:00'
        ]
    ];
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
}

function ge_service_city_landing_url(string $service_slug, string $city_slug, ?string $url_prefix = null): string
{
    if (!function_exists('ge_static_service_url_prefix')) {
        require_once __DIR__ . '/growth/helpers.php';
    }

    static $prefixByService = [];
    static $publishedByService = [];

    if ($url_prefix === null) {
        if (!isset($prefixByService[$service_slug])) {
            $prefixByService[$service_slug] = ge_static_service_url_prefix($service_slug);
            try {
                if (is_file(__DIR__ . '/db.local.php') && file_exists(__DIR__ . '/growth/bootstrap.php')) {
                    require_once __DIR__ . '/growth/bootstrap.php';
                    if (function_exists('ge_is_ready') && ge_is_ready()) {
                        $geService = \Growth\Models\Service::findBySlug($service_slug);
                        if ($geService) {
                            $prefixByService[$service_slug] = $geService['url_prefix'];
                            $publishedByService[$service_slug] = \Growth\Models\LandingPage::citySlugMapByService((int)$geService['id']);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Static slug fallback below
            }
        }

        $url_prefix = $prefixByService[$service_slug];
        if (!empty($publishedByService[$service_slug][$city_slug])) {
            return '/' . $publishedByService[$service_slug][$city_slug];
        }
    }

    if (function_exists('ge_build_landing_slug') && function_exists('ge_is_ready') && ge_is_ready()) {
        return '/' . ge_build_landing_slug($url_prefix, $city_slug);
    }

    return '/' . ge_slugify($url_prefix . '-company-in-' . $city_slug);
}

function render_service_city_links(string $service_slug, array $service, ?string $current_city_slug = null): void
{
    if (!function_exists('ge_static_service_url_prefix')) {
        require_once __DIR__ . '/growth/helpers.php';
    }

    $cities = get_cities_data();
    if (empty($cities)) {
        return;
    }

    $label = $service['silo'] ?? ($service['h1'] ?? 'Services');
    $urlPrefix = ge_static_service_url_prefix($service_slug);
    $published = [];

    try {
        if (is_file(__DIR__ . '/db.local.php') && file_exists(__DIR__ . '/growth/bootstrap.php')) {
            require_once __DIR__ . '/growth/bootstrap.php';
            if (function_exists('ge_is_ready') && ge_is_ready()) {
                $geService = \Growth\Models\Service::findBySlug($service_slug);
                if ($geService) {
                    $urlPrefix = $geService['url_prefix'];
                    $published = \Growth\Models\LandingPage::citySlugMapByService((int)$geService['id']);
                }
            }
        }
    } catch (\Throwable $e) {
        $published = [];
    }

    $buildSlug = function (string $citySlug) use ($urlPrefix, $published) {
        if (isset($published[$citySlug])) {
            return $published[$citySlug];
        }
        if (function_exists('ge_build_landing_slug') && function_exists('ge_is_ready') && ge_is_ready()) {
            return ge_build_landing_slug($urlPrefix, $citySlug);
        }
        return ge_slugify($urlPrefix . '-company-in-' . $citySlug);
    };

    echo '<section class="py-5 bg-darker border-top border-secondary">';
    echo '<div class="container py-2">';
    echo '<h2 class="text-white h4 mb-2">' . htmlspecialchars($label) . ' in <span class="text-neon">All Cities</span></h2>';
    echo '<p class="text-white-50 small mb-4">City-specific ' . htmlspecialchars(strtolower($label)) . ' pages across India — local expertise, national standards.</p>';
    echo '<div class="row g-2">';

    foreach ($cities as $citySlug => $city) {
        $slug = $buildSlug($citySlug);
        $href = '/' . ltrim($slug, '/');
        $isCurrent = ($current_city_slug !== null && $citySlug === $current_city_slug);
        $cardClass = 'd-block p-2 border rounded text-decoration-none hover-effect text-center h-100 ' . ($isCurrent ? 'border-neon bg-glass' : 'border-secondary');
        echo '<div class="col-6 col-md-4 col-lg-3 col-xl-2">';
        echo '<a href="' . htmlspecialchars($href) . '" class="' . $cardClass . '"' . ($isCurrent ? ' aria-current="page"' : '') . '>';
        echo '<span class="' . ($isCurrent ? 'text-neon' : 'text-white') . ' small fw-semibold d-block">' . htmlspecialchars($city['name']) . '</span>';
        echo '<span class="text-white-50" style="font-size:0.7rem;">' . htmlspecialchars($city['state']) . '</span>';
        echo '</a></div>';
    }

    echo '</div></div></section>';
}
