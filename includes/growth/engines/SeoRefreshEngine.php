<?php
namespace Growth\Engines;

use Growth\Models\City;
use Growth\Models\Industry;
use Growth\Models\LandingPage;
use Growth\Models\Service;

/** Batch refresh meta titles, descriptions, and keywords without full content regen. */
class SeoRefreshEngine
{
    public static function refreshAll(bool $reindex = true): array
    {
        if (!ge_is_ready()) {
            return ['updated' => 0, 'error' => 'Not ready'];
        }

        $db = ge_conn();
        $res = $db->query("SELECT id, service_id, city_id, industry_id FROM ge_landing_pages WHERE status = 'published'");
        $updated = 0;
        $urls = [];

        while ($row = $res->fetch_assoc()) {
            $r = self::refreshOne((int)$row['id'], (int)$row['service_id'], (int)$row['city_id'], (int)$row['industry_id']);
            if ($r) {
                $updated++;
                if (!empty($r['url'])) {
                    $urls[] = $r['url'];
                }
            }
        }

        if ($reindex && !empty($urls)) {
            DiscoveryEngine::signalUrls(array_slice($urls, 0, 100));
            if (count($urls) > 100) {
                IndexingEngine::queueAllPending(500, true);
            }
        }

        return ['updated' => $updated, 'urls_signaled' => min(count($urls), 100)];
    }

    public static function refreshOne(int $pageId, int $serviceId, int $cityId, int $industryId): ?array
    {
        $service = Service::find($serviceId);
        $city = City::find($cityId);
        $industry = ($industryId > 0 && ge_table_exists('ge_industries')) ? Industry::find($industryId) : null;
        if (!$service || !$city) {
            return null;
        }

        $page = LandingPage::find($pageId);
        if (!$page) {
            return null;
        }

        $metaTitle = IntentKeywordEngine::optimizeMetaTitle(
            $page['meta_title'] ?? IntentKeywordEngine::primaryPhrase($service, $city, $industry),
            IntentKeywordEngine::primaryPhrase($service, $city, $industry)
        );
        $metaDesc = IntentKeywordEngine::optimizeMetaDescription(
            $page['meta_description'] ?? '',
            $service,
            $city,
            $industry
        );
        $keywords = KeywordEngine::metaKeywordList($service, $city, $industry, 20);

        LandingPage::update($pageId, array_merge($page, [
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
            'keywords_json' => ge_json_encode($keywords),
        ]));

        return ['id' => $pageId, 'url' => SITE_URL . '/' . $page['slug']];
    }
}
