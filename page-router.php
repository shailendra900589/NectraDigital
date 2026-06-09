<?php
/**
 * Unified page router — Service×City → full template, industry landings, blog posts
 */
require_once __DIR__ . '/includes/growth/bootstrap.php';
require_once __DIR__ . '/includes/service-city-resolver.php';

use Growth\Models\LandingPage;

$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'])) : '';

if ($slug === '') {
    header('Location: /404.php');
    exit;
}

$serviceCityCtx = ge_resolve_service_city_page($slug);
if ($serviceCityCtx) {
    extract($serviceCityCtx, EXTR_SKIP);
    require __DIR__ . '/includes/service-template.php';
    exit;
}

if (ge_is_ready()) {
    $page = LandingPage::findBySlug($slug);
    if ($page) {
        require __DIR__ . '/landing.php';
        exit;
    }
}

require __DIR__ . '/post.php';
