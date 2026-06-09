<?php
namespace Growth\Engines;

class SchemaEngine
{
    public static function forLandingPage(array $page, array $faqs): array
    {
        $url = SITE_URL . '/' . $page['slug'];
        $founder = ge_setting('founder_name', 'Ravindra Kumar Chauhan');

        $graphs = [];

        $graphs[] = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => SITE_URL . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $page['service_name'], 'item' => SITE_URL . '/services'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $page['city_name'], 'item' => $url],
            ],
        ];

        $graphs[] = [
            '@type' => $page['schema_type'] ?? 'Service',
            'name' => $page['h1'],
            'description' => $page['meta_description'],
            'url' => $url,
            'provider' => [
                '@type' => 'Organization',
                'name' => SITE_NAME,
                'url' => SITE_URL,
                'founder' => ['@type' => 'Person', 'name' => $founder],
            ],
            'areaServed' => [
                '@type' => 'City',
                'name' => $page['city_name'],
                'containedInPlace' => ['@type' => 'State', 'name' => $page['state'] ?? 'India'],
            ],
        ];

        $graphs[] = [
            '@type' => 'LocalBusiness',
            'name' => SITE_NAME . ' — ' . $page['city_name'],
            'description' => $page['meta_description'],
            'url' => $url,
            'email' => 'contact@nectradigital.com',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $page['city_name'],
                'addressRegion' => $page['state'] ?? '',
                'addressCountry' => $page['country'] ?? 'IN',
            ],
            'priceRange' => '₹₹₹',
        ];

        if (!empty($faqs)) {
            $graphs[] = [
                '@type' => 'FAQPage',
                'mainEntity' => array_map(fn($f) => [
                    '@type' => 'Question',
                    'name' => $f['q'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
                ], $faqs),
            ];
        }

        return ['@context' => 'https://schema.org', '@graph' => $graphs];
    }

    public static function output(array $schema): void
    {
        echo '<script type="application/ld+json">' .
            json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
            '</script>';
    }
}
