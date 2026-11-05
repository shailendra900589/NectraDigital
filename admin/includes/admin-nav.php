<?php
/**
 * Unified Nectra Admin navigation — single source for dashboard + growth pages.
 */
function admin_layout_context(): array
{
    static $ctx = null;
    if ($ctx !== null) {
        return $ctx;
    }
    $script = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    $inGrowth = (bool)preg_match('#/admin/growth/#', $script);
    $ctx = [
        'in_growth' => $inGrowth,
        'root'      => $inGrowth ? '../' : '',
        'growth'    => $inGrowth ? '' : 'growth/',
        'assets'    => $inGrowth ? '../../assets/' : '../assets/',
    ];
    return $ctx;
}

function admin_nav_resolve_url(string $target): string
{
    $ctx = admin_layout_context();
    if (str_starts_with($target, 'dashboard:')) {
        return $ctx['root'] . 'dashboard.php?page=' . rawurlencode(substr($target, 10));
    }
    if (str_starts_with($target, 'growth:')) {
        return $ctx['growth'] . substr($target, 7);
    }
    if (str_starts_with($target, 'root:')) {
        return $ctx['root'] . substr($target, 5);
    }
    return $target;
}

function admin_nav_items(): array
{
    return [
        ['section' => 'Overview'],
        ['id' => 'home', 'url' => 'dashboard:home', 'icon' => 'fa-gauge-high', 'label' => 'Dashboard'],

        ['section' => 'Inbox'],
        ['id' => 'leads', 'url' => 'dashboard:leads', 'icon' => 'fa-inbox', 'label' => 'Contact Leads'],
        ['id' => 'hire_requests', 'url' => 'dashboard:hire_requests', 'icon' => 'fa-user-tie', 'label' => 'Hire Requests'],

        ['section' => 'Content'],
        ['id' => 'blog', 'url' => 'dashboard:blog', 'icon' => 'fa-pen-to-square', 'label' => 'Blog Posts'],
        ['id' => 'comments', 'url' => 'dashboard:comments', 'icon' => 'fa-comments', 'label' => 'Comments'],
        ['id' => 'case-studies', 'url' => 'growth:case-studies.php', 'icon' => 'fa-trophy', 'label' => 'Case Studies'],

        ['section' => 'Growth & SEO'],
        ['id' => 'cities', 'url' => 'dashboard:cities', 'icon' => 'fa-map-marker-alt', 'label' => 'Cities'],
        ['id' => 'services', 'url' => 'growth:services.php', 'icon' => 'fa-layer-group', 'label' => 'Services'],
        ['id' => 'landing', 'url' => 'growth:landing-pages.php', 'icon' => 'fa-file-alt', 'label' => 'Landing Pages'],
        ['id' => 'generate', 'url' => 'growth:generate.php', 'icon' => 'fa-wand-magic-sparkles', 'label' => 'Generate Pages'],
        ['id' => 'indexing', 'url' => 'growth:indexing.php', 'icon' => 'fa-search-plus', 'label' => 'Auto Indexing'],

        ['section' => 'Site'],
        ['id' => 'ads', 'url' => 'dashboard:ads', 'icon' => 'fa-rectangle-ad', 'label' => 'Ads'],
        ['id' => 'careers', 'url' => 'dashboard:careers', 'icon' => 'fa-briefcase', 'label' => 'Careers'],
        ['id' => 'settings', 'url' => 'growth:settings.php', 'icon' => 'fa-sliders', 'label' => 'Settings'],
    ];
}
