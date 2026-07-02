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

        ['section' => 'Leads & CRM'],
        ['id' => 'leads', 'url' => 'dashboard:leads', 'icon' => 'fa-inbox', 'label' => 'Contact Leads'],
        ['id' => 'hire_requests', 'url' => 'dashboard:hire_requests', 'icon' => 'fa-user-tie', 'label' => 'Hire Requests'],
        ['id' => 'crm_leads', 'url' => 'growth:leads.php', 'icon' => 'fa-address-book', 'label' => 'CRM Pipeline'],

        ['section' => 'Content'],
        ['id' => 'blog', 'url' => 'dashboard:blog', 'icon' => 'fa-pen-to-square', 'label' => 'Blog Posts'],
        ['id' => 'comments', 'url' => 'dashboard:comments', 'icon' => 'fa-comments', 'label' => 'Comments'],
        ['id' => 'case-studies', 'url' => 'growth:case-studies.php', 'icon' => 'fa-trophy', 'label' => 'Case Studies'],
        ['id' => 'authors', 'url' => 'growth:authors.php', 'icon' => 'fa-user-edit', 'label' => 'Authors'],
        ['id' => 'knowledge', 'url' => 'growth:knowledge-base.php', 'icon' => 'fa-book', 'label' => 'Knowledge Base'],

        ['section' => 'Programmatic SEO'],
        ['id' => 'services', 'url' => 'growth:services.php', 'icon' => 'fa-cogs', 'label' => 'Services'],
        ['id' => 'cities', 'url' => 'dashboard:cities', 'icon' => 'fa-map-marker-alt', 'label' => 'Cities'],
        ['id' => 'industries', 'url' => 'growth:industries.php', 'icon' => 'fa-industry', 'label' => 'Industries'],
        ['id' => 'keywords', 'url' => 'growth:keywords.php', 'icon' => 'fa-key', 'label' => 'Keywords'],
        ['id' => 'landing', 'url' => 'growth:landing-pages.php', 'icon' => 'fa-file-alt', 'label' => 'Landing Pages'],
        ['id' => 'generate', 'url' => 'growth:generate.php', 'icon' => 'fa-wand-magic-sparkles', 'label' => 'Generate Pages'],

        ['section' => 'SEO & Indexing'],
        ['id' => 'indexing', 'url' => 'growth:indexing.php', 'icon' => 'fa-search-plus', 'label' => 'Auto Indexing'],
        ['id' => 'export', 'url' => 'root:export-urls.php?type=all', 'icon' => 'fa-download', 'label' => 'Export URLs'],
        ['id' => 'competitor', 'url' => 'growth:competitor.php', 'icon' => 'fa-crosshairs', 'label' => 'Competitor Intel'],

        ['section' => 'Monetization & HR'],
        ['id' => 'ads', 'url' => 'dashboard:ads', 'icon' => 'fa-rectangle-ad', 'label' => 'Ad Manager'],
        ['id' => 'careers', 'url' => 'dashboard:careers', 'icon' => 'fa-briefcase', 'label' => 'Careers'],

        ['section' => 'Platform'],
        ['id' => 'tools', 'url' => 'growth:tools.php', 'icon' => 'fa-toolbox', 'label' => 'Tools'],
        ['id' => 'analytics', 'url' => 'growth:analytics.php', 'icon' => 'fa-chart-line', 'label' => 'Analytics'],
        ['id' => 'settings', 'url' => 'growth:settings.php', 'icon' => 'fa-sliders', 'label' => 'Settings'],
    ];
}
