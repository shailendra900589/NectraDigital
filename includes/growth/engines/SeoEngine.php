<?php
namespace Growth\Engines;

class SeoEngine
{
    public static function metaTags(array $page): array
    {
        $url = SITE_URL . '/' . ltrim($page['slug'], '/');
        $image = !empty($page['service_image'])
            ? (strpos($page['service_image'], 'http') === 0 ? $page['service_image'] : SITE_URL . '/' . ltrim($page['service_image'], '/'))
            : SITE_URL . '/assets/images/logo.png';

        return [
            'title' => $page['meta_title'],
            'description' => $page['meta_description'],
            'canonical' => $url,
            'og' => [
                'type' => 'website',
                'title' => $page['meta_title'],
                'description' => $page['meta_description'],
                'url' => $url,
                'image' => $image,
                'site_name' => SITE_NAME,
                'locale' => 'en_IN',
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $page['meta_title'],
                'description' => $page['meta_description'],
                'image' => $image,
            ],
            'keywords' => implode(', ', ge_json_decode($page['keywords_json'] ?? '[]')),
        ];
    }
}
