<?php
namespace Growth\Engines;

use Growth\Models\City;
use Growth\Models\Service;

class CatalogSyncEngine
{
    public static function syncAll(): array
    {
        if (!ge_is_ready()) {
            return ['services' => 0, 'cities' => 0, 'errors' => ['Growth tables not ready']];
        }

        return [
            'services' => self::syncServices(),
            'cities' => self::syncCities(),
            'errors' => [],
        ];
    }

    public static function syncServices(): int
    {
        if (!ge_table_exists('ge_services')) {
            return 0;
        }

        require_once __DIR__ . '/../../seo-data.php';
        $catalog = get_services_data();
        $count = 0;
        $order = 0;

        foreach ($catalog as $slug => $data) {
            self::upsertService($slug, $data, $order++);
            $count++;
        }

        return $count;
    }

    public static function syncCities(): int
    {
        if (!ge_table_exists('ge_cities')) {
            return 0;
        }

        require_once __DIR__ . '/../../seo-data.php';
        $catalog = get_cities_data();
        $count = 0;

        foreach ($catalog as $slug => $data) {
            self::upsertCity($slug, $data);
            $count++;
        }

        return $count;
    }

    public static function syncService(string $slug, ?array $data = null): ?int
    {
        if (!ge_table_exists('ge_services')) {
            return null;
        }

        if ($data === null) {
            require_once __DIR__ . '/../../seo-data.php';
            $catalog = get_services_data();
            if (!isset($catalog[$slug])) {
                return null;
            }
            $data = $catalog[$slug];
        }

        $order = 0;
        foreach (array_keys(get_services_data()) as $i => $key) {
            if ($key === $slug) {
                $order = $i;
                break;
            }
        }

        return self::upsertService($slug, $data, $order);
    }

    private static function upsertService(string $slug, array $data, int $sortOrder): int
    {
        $payload = self::servicePayload($slug, $data, $sortOrder);
        $existing = Service::findBySlug($slug);

        if ($existing) {
            Service::update((int)$existing['id'], $payload);
            return (int)$existing['id'];
        }

        return Service::create($payload);
    }

    private static function upsertCity(string $slug, array $data): int
    {
        $payload = [
            'name' => $data['name'],
            'slug' => $slug,
            'state' => $data['state'] ?? null,
            'country' => 'India',
            'status' => 'active',
        ];

        $existing = City::findBySlug($slug);
        if ($existing) {
            City::update((int)$existing['id'], $payload);
            return (int)$existing['id'];
        }

        return City::create($payload);
    }

    private static function servicePayload(string $slug, array $data, int $sortOrder): array
    {
        $name = ge_static_service_name($slug, $data);
        $urlPrefix = ge_static_service_url_prefix($slug);
        $faqs = [];
        foreach ($data['faqs'] ?? [] as $faq) {
            if (!empty($faq['q']) && !empty($faq['a'])) {
                $faqs[] = ['q' => $faq['q'], 'a' => $faq['a']];
            }
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'url_prefix' => $urlPrefix,
            'meta_title_template' => 'Best {service_name} in {city_name} | Nectra Digital',
            'meta_description_template' => 'Top {service_name} company in {city_name}, {state}. Expert services, free consultation. Contact Nectra Digital.',
            'h1_template' => 'Best {service_name} in {city_name}',
            'h2_template' => 'Professional {service_name} in {state}',
            'content_template' => null,
            'keywords_template' => $data['keywords'] ?? null,
            'faq_template' => ge_json_encode($faqs),
            'schema_type' => 'Service',
            'sort_order' => $sortOrder,
            'status' => 'active',
        ];
    }
}
