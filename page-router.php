<?php
/**
 * Unified page router — Growth landing pages → Blog posts
 */
require_once __DIR__ . '/includes/growth/bootstrap.php';

$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'])) : '';

if ($slug === '') {
    header('Location: /404.php');
    exit;
}

if (ge_is_ready()) {
    $page = \Growth\Models\LandingPage::findBySlug($slug);
    if ($page) {
        require __DIR__ . '/landing.php';
        exit;
    }
}

require __DIR__ . '/post.php';
