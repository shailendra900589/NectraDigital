<?php
namespace Growth;

use Growth\Engines\AeoEngine;
use Growth\Engines\ContentEngine;
use Growth\Engines\GeoEngine;
use Growth\Engines\InternalLinkEngine;
use Growth\Engines\KeywordEngine;
use Growth\Engines\SchemaEngine;
use Growth\Models\GenerationJob;
use Growth\Models\IndexingQueue;
use Growth\Models\Keyword;
use Growth\Models\LandingPage;
use Growth\Models\Service;
use Growth\Models\City;

class LandingPageGenerator
{
    public static function generateOne(int $serviceId, int $cityId, bool $regenerate = false): array
    {
        $service = Service::find($serviceId);
        $city = City::find($cityId);

        if (!$service || !$city) {
            return ['success' => false, 'error' => 'Service or city not found'];
        }

        if (!$regenerate && LandingPage::exists($serviceId, $cityId)) {
            return ['success' => false, 'error' => 'Page already exists', 'skipped' => true];
        }

        $content = ContentEngine::generateContent($service, $city);
        $ctx = $content['ctx'];
        $geo = GeoEngine::generate($service, $city, $ctx);
        $faqs = AeoEngine::generateFaqs($service, $city, $ctx);
        $paa = AeoEngine::generatePaa($service, $city, $ctx);
        $voice = AeoEngine::generateVoiceAnswer($service, $city, $ctx);
        $keywords = KeywordEngine::generateForPage($service, $city);
        $links = InternalLinkEngine::generate($service, $city, $ctx);

        $slug = ge_build_landing_slug($service['url_prefix'], $city['slug']);
        $urlPath = '/' . $slug;

        $pageData = [
            'service_id' => $serviceId,
            'city_id' => $cityId,
            'slug' => $slug,
            'url_path' => $urlPath,
            'meta_title' => mb_substr($content['metaTitle'], 0, 255),
            'meta_description' => $content['metaDesc'],
            'h1' => $content['h1'],
            'h2' => $content['h2'],
            'content' => $content['content'],
            'quick_answer' => $geo['quick_answer'],
            'key_takeaways' => $geo['key_takeaways'],
            'summary' => $geo['summary'],
            'expert_insight' => $geo['expert_insight'],
            'faq_json' => ge_json_encode($faqs),
            'paa_json' => ge_json_encode($paa),
            'voice_answer' => $voice,
            'keywords_json' => ge_json_encode(array_column($keywords, 'keyword')),
            'internal_links_json' => ge_json_encode($links),
            'content_hash' => ge_hash_content($content['content'] . $geo['quick_answer'] . $slug),
            'status' => 'published',
        ];

        $schema = SchemaEngine::forLandingPage(array_merge($pageData, [
            'service_name' => $service['name'],
            'city_name' => $city['name'],
            'state' => $city['state'],
            'country' => $city['country'],
            'schema_type' => $service['schema_type'],
        ]), $faqs);
        $pageData['schema_json'] = ge_json_encode($schema);

        $pageId = LandingPage::upsert($pageData);
        self::syncKeywords($pageId, $serviceId, $cityId, $keywords);

        if (ge_setting('auto_index_queue', '1') === '1') {
            IndexingQueue::enqueue(SITE_URL . $urlPath, $pageId);
        }

        return ['success' => true, 'id' => $pageId, 'slug' => $slug];
    }

    private static function syncKeywords(int $pageId, int $serviceId, int $cityId, array $keywords): void
    {
        $db = ge_conn();
        $db->query("DELETE FROM ge_keyword_mappings WHERE landing_page_id = " . (int)$pageId);

        $pos = 0;
        foreach ($keywords as $kw) {
            $keywordId = self::ensureKeyword($kw, $serviceId, $cityId);
            $isPrimary = ($kw['keyword_type'] === 'primary' && $pos < 3) ? 1 : 0;
            $stmt = $db->prepare("INSERT IGNORE INTO ge_keyword_mappings (landing_page_id, keyword_id, is_primary, position) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('iiii', $pageId, $keywordId, $isPrimary, $pos);
            $stmt->execute();
            $pos++;
        }
    }

    private static function ensureKeyword(array $kw, int $serviceId, int $cityId): int
    {
        $db = ge_conn();
        $stmt = $db->prepare("SELECT id FROM ge_keywords WHERE keyword = ? AND service_id = ? AND city_id = ? LIMIT 1");
        $stmt->bind_param('sii', $kw['keyword'], $serviceId, $cityId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            return (int)$row['id'];
        }
        return Keyword::create([
            'keyword' => $kw['keyword'],
            'keyword_type' => $kw['keyword_type'],
            'service_id' => $serviceId,
            'city_id' => $cityId,
            'is_auto_generated' => 1,
        ]);
    }

    public static function generateBulk(array $serviceIds, array $cityIds, bool $regenerate = false): array
    {
        $total = count($serviceIds) * count($cityIds);
        $jobId = GenerationJob::create('bulk', $total);
        GenerationJob::start($jobId);

        $processed = 0;
        $failed = 0;
        $created = [];
        $batchSize = (int)ge_setting('batch_size', 50);

        foreach ($serviceIds as $sid) {
            foreach ($cityIds as $cid) {
                $result = self::generateOne((int)$sid, (int)$cid, $regenerate);
                if ($result['success'] ?? false) {
                    $processed++;
                    $created[] = $result['slug'];
                } elseif (!empty($result['skipped'])) {
                    $processed++;
                } else {
                    $failed++;
                }

                if (($processed + $failed) % $batchSize === 0) {
                    GenerationJob::progress($jobId, $processed, $failed);
                }
            }
        }

        GenerationJob::progress($jobId, $processed, $failed);
        GenerationJob::complete($jobId, $failed > 0 && $processed === 0 ? 'failed' : 'completed');

        return [
            'job_id' => $jobId,
            'total' => $total,
            'processed' => $processed,
            'failed' => $failed,
            'slugs' => array_slice($created, 0, 10),
        ];
    }

    public static function generateFullMatrix(bool $regenerate = false): array
    {
        $services = Service::all(true);
        $cities = City::all(true);
        $serviceIds = array_column($services, 'id');
        $cityIds = array_column($cities, 'id');

        $jobId = GenerationJob::create('full_matrix', count($serviceIds) * count($cityIds));
        return self::generateBulk($serviceIds, $cityIds, $regenerate);
    }
}
