<?php
/**
 * Navigation structure — maps labels to existing indexed URLs.
 */
if (defined('NECTRA_NAV_DATA_LOADED')) {
    return;
}
define('NECTRA_NAV_DATA_LOADED', true);

function nectra_nav_path(string $path): string
{
    return function_exists('nectra_web_path') ? nectra_web_path($path) : $path;
}

/** Canonical path for active nav highlighting (no query string). */
function nectra_nav_current_path(): string
{
    if (!function_exists('nectra_uri_path_for_canonical')) {
        require_once __DIR__ . '/text-utils.php';
    }
    $requestUri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    return rtrim(nectra_uri_path_for_canonical($requestUri), '/') ?: '/';
}

function nectra_nav_is_current(string $path): bool
{
    $path = rtrim($path, '/') ?: '/';
    $current = nectra_nav_current_path();
    if ($path === $current) {
        return true;
    }
    if ($path === '/services' && $current !== '/' && str_ends_with($current, '-services')) {
        return true;
    }
    return false;
}

function nectra_nav_link_class(string $path, string $extra = ''): string
{
    $classes = ['nd-nav-link'];
    if ($extra !== '') {
        $classes[] = $extra;
    }
    if (nectra_nav_is_current($path)) {
        $classes[] = 'nd-nav-link--current';
        $classes[] = 'active';
    }
    return implode(' ', $classes);
}

function nectra_nav_link_aria(string $path): string
{
    return nectra_nav_is_current($path) ? ' aria-current="page"' : '';
}

/** Four-column mega menu (development-first). */
function nectra_mega_menu_columns(): array
{
    return [
        [
            'title' => 'Software Engineering',
            'icon' => 'fa-code',
            'items' => [
                ['title' => 'Custom Software Development', 'desc' => 'Tailored business apps, portals and workflows.', 'url' => '/software-development-services'],
                ['title' => 'SaaS Development', 'desc' => 'Multi-tenant products from MVP to scale.', 'url' => '/software-development-services'],
                ['title' => 'Enterprise Software', 'desc' => 'Secure systems for complex operations.', 'url' => '/software-development-services'],
                ['title' => 'Web Application Development', 'desc' => 'Dynamic platforms and customer portals.', 'url' => '/web-development-services'],
                ['title' => 'API Development & Integration', 'desc' => 'Connect CRMs, payments and third-party tools.', 'url' => '/software-development-services'],
                ['title' => 'Software Modernization', 'desc' => 'Refactor legacy systems for performance.', 'url' => '/software-development-services'],
            ],
        ],
        [
            'title' => 'Web Development',
            'icon' => 'fa-globe',
            'items' => [
                ['title' => 'Website Design & Development', 'desc' => 'Corporate and marketing sites engineered to convert.', 'url' => '/web-development-services'],
                ['title' => 'Corporate Websites', 'desc' => 'Brand-led, fast, accessible company websites.', 'url' => '/web-development-services'],
                ['title' => 'E-commerce Development', 'desc' => 'Shopify, WooCommerce and custom storefronts.', 'url' => '/ecommerce-development'],
                ['title' => 'Landing Page Development', 'desc' => 'Campaign and product launch pages.', 'url' => '/web-development-services'],
                ['title' => 'CMS Development', 'desc' => 'WordPress and headless CMS implementations.', 'url' => '/web-development-services'],
                ['title' => 'Website Maintenance', 'desc' => 'Updates, security and performance care.', 'url' => '/web-development-services'],
            ],
        ],
        [
            'title' => 'Mobile & Digital Products',
            'icon' => 'fa-mobile-alt',
            'items' => [
                ['title' => 'Mobile App Development', 'desc' => 'Android, iOS and cross-platform apps.', 'url' => '/mobile-app-development'],
                ['title' => 'Android App Development', 'desc' => 'Native and hybrid Android experiences.', 'url' => '/mobile-app-development'],
                ['title' => 'iOS App Development', 'desc' => 'Swift and cross-platform iOS delivery.', 'url' => '/mobile-app-development'],
                ['title' => 'Cross-Platform Apps', 'desc' => 'React Native and Flutter builds.', 'url' => '/mobile-app-development'],
                ['title' => 'UI/UX Design', 'desc' => 'Wireframes, design systems and prototypes.', 'url' => '/web-development-services'],
                ['title' => 'Product Engineering', 'desc' => 'End-to-end product design and delivery.', 'url' => '/software-development-services'],
            ],
        ],
        [
            'title' => 'AI, Automation & Growth',
            'icon' => 'fa-robot',
            'items' => [
                ['title' => 'AI & Automation', 'desc' => 'Workflow automation and intelligent agents.', 'url' => '/ai-automation-services'],
                ['title' => 'AI Chatbots', 'desc' => 'Website and support conversational AI.', 'url' => '/ai-chatbot-development'],
                ['title' => 'WhatsApp Automation', 'desc' => 'Business API bots for sales and support.', 'url' => '/whatsapp-ai-bot-development'],
                ['title' => 'SEO Services', 'desc' => 'Organic visibility after your product is live.', 'url' => '/seo-services'],
                ['title' => 'Digital Marketing', 'desc' => 'Acquisition and measurable growth.', 'url' => '/digital-marketing-services'],
            ],
        ],
    ];
}

function nectra_solutions_nav_items(): array
{
    return [
        ['title' => 'Startup Solutions', 'desc' => 'MVPs, web apps and mobile products for early-stage teams.', 'anchor' => 'startups'],
        ['title' => 'SME Digital Transformation', 'desc' => 'Websites, software and automation for growing businesses.', 'anchor' => 'sme'],
        ['title' => 'Enterprise Software Solutions', 'desc' => 'Scalable platforms and integration-heavy systems.', 'anchor' => 'enterprise'],
        ['title' => 'SaaS Product Development', 'desc' => 'From architecture to launch and iteration.', 'anchor' => 'saas'],
        ['title' => 'E-commerce Solutions', 'desc' => 'Storefronts, checkout and operational integrations.', 'anchor' => 'ecommerce'],
        ['title' => 'Business Automation', 'desc' => 'AI, CRM sync and workflow design.', 'anchor' => 'automation'],
    ];
}
