<?php
/**
 * Unified page router — Service×City → full template, industry landings, blog posts
 */

$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'])) : '';

if ($slug === '') {
    header('Location: /404.php');
    exit;
}

// Fast path: blog posts — skip Growth Engine bootstrap (lighter for Bing/Google crawlers).
require_once __DIR__ . '/includes/db.php';
$blogStmt = $conn->prepare('SELECT id FROM blog_posts WHERE slug = ? LIMIT 1');
if ($blogStmt) {
    $blogStmt->bind_param('s', $slug);
    $blogStmt->execute();
    $blogHit = $blogStmt->get_result();
    if ($blogHit && $blogHit->num_rows > 0) {
        $_GET['slug'] = $slug;
        require __DIR__ . '/post.php';
        exit;
    }
}

require_once __DIR__ . '/includes/growth/bootstrap.php';
require_once __DIR__ . '/includes/service-city-resolver.php';

use Growth\Models\LandingPage;

$serviceCityCtx = ge_resolve_service_city_page($slug);
if ($serviceCityCtx) {
    extract($serviceCityCtx, EXTR_SKIP);
    require __DIR__ . '/includes/service-template.php';
    exit;
}

if (ge_is_ready()) {
    $page = LandingPage::findBySlug($slug);
    if ($page) {
        $industryId = (int)($page['industry_id'] ?? 0);
        $pageType = $page['page_type'] ?? 'service_city';
        if ($industryId === 0 && $pageType !== 'service_city_industry') {
            $retryCtx = ge_resolve_service_city_page($slug);
            if ($retryCtx) {
                extract($retryCtx, EXTR_SKIP);
                require __DIR__ . '/includes/service-template.php';
                exit;
            }
        }
        require __DIR__ . '/landing.php';
        exit;
    }
}

require __DIR__ . '/post.php';
