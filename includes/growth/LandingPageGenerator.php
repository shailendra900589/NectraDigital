<?php
namespace Growth;

use Growth\Engines\AeoEngine;
use Growth\Engines\ContentEngine;
use Growth\Engines\GeoEngine;
use Growth\Engines\InternalLinkEngine;
use Growth\Engines\KeywordEngine;
use Growth\Engines\SchemaEngine;
use Growth\Models\GenerationJob;
use Growth\Models\Industry;
use Growth\Models\IndexingQueue;
use Growth\Models\Keyword;
use Growth\Models\LandingPage;
use Growth\Models\Service;
use Growth\Models\City;

class LandingPageGenerator
{
    public static function generateOne(int $serviceId, int $cityId, int $industryId = 0, bool $regenerate = false): array
    {
        $service = Service::find($serviceId);
        $city = City::find($cityId);
        $industry = ($industryId > 0 && ge_table_exists('ge_industries')) ? Industry::find($industryId) : null;

        if (!$service || !$city) {
            return ['success' => false, 'error' => 'Service or city not found'];
        }
        if ($industryId > 0 && !$industry) {
            return ['success' => false, 'error' => 'Industry not found'];
        }

        if (!$regenerate && LandingPage::exists($serviceId, $cityId, $industryId)) {
            return ['success' => false, 'error' => 'Page already exists', 'skipped' => true];
        }

        $content = ContentEngine::generateContent($service, $city, $industry);
        $ctx = $content['ctx'];
        $geo = GeoEngine::generate($service, $city, $ctx);
        $faqs = AeoEngine::generateFaqs($service, $city, $ctx);
        $paa = AeoEngine::generatePaa($service, $city, $ctx);
        $voice = AeoEngine::generateVoiceAnswer($service, $city, $ctx);
        $keywords = KeywordEngine::generateForPage($service, $city, $industry);
        $links = InternalLinkEngine::generate($service, $city, $ctx, $industry);

        $slugExtra = $industry ? ['industry_slug' => $industry['slug']] : [];
        $slug = ge_build_landing_slug($service['url_prefix'], $city['slug'], $slugExtra);
        $urlPath = '/' . $slug;
        $pageType = $industry ? 'service_city_industry' : 'service_city';

        $pageData = [
            'service_id' => $serviceId,
            'city_id' => $cityId,
            'industry_id' => $industryId,
            'page_type' => $pageType,
            'slug' => $slug,
            'url_path' => $urlPath,
            'meta_title' => ge_trim_seo_title($content['metaTitle']),
            'meta_description' => $content['metaDesc'],
            'h1' => $content['h1'],
            'h2' => $content['h2'],
            'h3' => $content['h3'] ?? null,
            'content' => $content['content'],
            'quick_answer' => $geo['quick_answer'],
            'key_takeaways' => $geo['key_takeaways'],
            'summary' => $geo['summary'],
            'expert_insight' => $geo['expert_insight'],
            'faq_json' => ge_json_encode($faqs),
            'paa_json' => ge_json_encode($paa),
            'voice_answer' => $voice,
            'keywords_json' => ge_json_encode(KeywordEngine::metaKeywordList($service, $city, $industry, 20)),
            'internal_links_json' => ge_json_encode($links),
            'cta_json' => ge_json_encode(ge_default_ctas()),
            'content_hash' => ge_hash_content($content['content'] . $geo['quick_answer'] . $slug . ($industryId ?: '')),
            'status' => 'published',
        ];

        $schema = SchemaEngine::forLandingPage(array_merge($pageData, [
            'service_name' => $service['name'],
            'city_name' => $city['name'],
            'industry_name' => $industry['name'] ?? null,
            'state' => $city['state'],
            'country' => $city['country'],
            'schema_type' => $service['schema_type'],
        ]), $faqs);
        $pageData['schema_json'] = ge_json_encode($schema);

        $pageId = LandingPage::upsert($pageData);
        self::syncKeywords($pageId, $serviceId, $cityId, $industryId, $keywords);

        if (ge_setting('auto_index_queue', '1') === '1') {
            IndexingQueue::enqueue(SITE_URL . $urlPath, $pageId);
        }

        return ['success' => true, 'id' => $pageId, 'slug' => $slug];
    }

    private static function syncKeywords(int $pageId, int $serviceId, int $cityId, int $industryId, array $keywords): void
    {
        $db = ge_conn();
        $db->query("DELETE FROM ge_keyword_mappings WHERE landing_page_id = " . (int)$pageId);

        // Limit DB keyword rows per page (meta tag list is separate, capped at 20)
        $keywords = array_slice($keywords, 0, 20);

        $pos = 0;
        foreach ($keywords as $kw) {
            $keywordId = self::ensureKeyword($kw, $serviceId, $cityId, $industryId, $pageId);
            $isPrimary = ($kw['keyword_type'] === 'primary' && $pos < 3) ? 1 : 0;
            $stmt = $db->prepare("INSERT IGNORE INTO ge_keyword_mappings (landing_page_id, keyword_id, is_primary, position) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('iiii', $pageId, $keywordId, $isPrimary, $pos);
            $stmt->execute();
            $pos++;
        }
    }

    private static function ensureKeyword(array $kw, int $serviceId, int $cityId, int $industryId, int $pageId): int
    {
        $db = ge_conn();
        $hasInd = ge_table_exists('ge_industries') && self::keywordsHasIndustryColumn();
        if ($hasInd) {
            $stmt = $db->prepare("SELECT id FROM ge_keywords WHERE keyword = ? AND service_id = ? AND city_id = ? AND industry_id = ? LIMIT 1");
            $stmt->bind_param('siii', $kw['keyword'], $serviceId, $cityId, $industryId);
        } else {
            $stmt = $db->prepare("SELECT id FROM ge_keywords WHERE keyword = ? AND service_id = ? AND city_id = ? LIMIT 1");
            $stmt->bind_param('sii', $kw['keyword'], $serviceId, $cityId);
        }
        $stmt->execute();
        if ($row = $stmt->get_result()->fetch_assoc()) {
            return (int)$row['id'];
        }

        $payload = [
            'keyword' => $kw['keyword'],
            'keyword_type' => $kw['keyword_type'],
            'service_id' => $serviceId,
            'city_id' => $cityId,
            'is_auto_generated' => 1,
        ];
        if ($hasInd) {
            $payload['industry_id'] = $industryId;
            $payload['landing_page_id'] = $pageId;
        }
        return Keyword::create($payload);
    }

    private static function keywordsHasIndustryColumn(): bool
    {
        static $v = null;
        if ($v !== null) return $v;
        $r = ge_conn()->query("SHOW COLUMNS FROM ge_keywords LIKE 'industry_id'");
        $v = $r && $r->num_rows > 0;
        return $v;
    }

    public static function generateBulk(array $serviceIds, array $cityIds, array $industryIds = [0], bool $regenerate = false): array
    {
        if (empty($industryIds)) {
            $industryIds = [0];
        }

        $total = count($serviceIds) * count($cityIds) * count($industryIds);
        $jobId = GenerationJob::create('bulk', $total);
        GenerationJob::start($jobId);

        $processed = 0;
        $failed = 0;
        $skipped = 0;
        $created = [];
        $batchSize = (int)ge_setting('batch_size', 50);
        $isCli = php_sapi_name() === 'cli';

        if ($isCli) {
            echo "Processing {$total} page combinations...\n";
        }

        $done = 0;
        foreach ($serviceIds as $sid) {
            foreach ($cityIds as $cid) {
                foreach ($industryIds as $iid) {
                    $done++;
                    $result = self::generateOne((int)$sid, (int)$cid, (int)$iid, $regenerate);
                    if ($result['success'] ?? false) {
                        $processed++;
                        $created[] = $result['slug'];
                    } elseif (!empty($result['skipped'])) {
                        $processed++;
                        $skipped++;
                    } else {
                        $failed++;
                        if ($isCli) {
                            echo "FAIL [{$done}/{$total}]: " . ($result['error'] ?? 'unknown') . "\n";
                        }
                    }

                    if ($isCli && ($done % 10 === 0 || $done === $total)) {
                        echo "Progress: {$done}/{$total} (created: " . ($processed - $skipped) . ", skipped: {$skipped}, failed: {$failed})\n";
                        if (function_exists('ob_flush')) {
                            @ob_flush();
                        }
                        flush();
                    }

                    if ($done % $batchSize === 0) {
                        GenerationJob::progress($jobId, $processed, $failed);
                    }
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
            'skipped' => $skipped,
            'slugs' => array_slice($created, 0, 10),
        ];
    }

    public static function generateFullMatrix(bool $includeIndustries = false, bool $regenerate = false): array
    {
        $services = Service::all(true);
        $cities = City::all(true);
        $serviceIds = array_column($services, 'id');
        $cityIds = array_column($cities, 'id');

        $industryIds = [0];
        if ($includeIndustries && ge_table_exists('ge_industries')) {
            $industryIds = array_merge([0], array_column(Industry::all(true), 'id'));
        }

        return self::generateBulk($serviceIds, $cityIds, $industryIds, $regenerate);
    }

    /** Generate only missing service × city pages (skips existing). */
    public static function generateMissing(bool $includeIndustries = false): array
    {
        return self::generateFullMatrix($includeIndustries, false);
    }

    /** Auto-generate all city pages for one service (e.g. after admin create). */
    public static function generateForService(int $serviceId, bool $regenerate = false): array
    {
        $cityIds = array_column(City::all(true), 'id');
        return self::generateBulk([$serviceId], $cityIds, [0], $regenerate);
    }

    /** Auto-generate all service pages for one city (e.g. after admin create). */
    public static function generateForCity(int $cityId, bool $regenerate = false): array
    {
        $serviceIds = array_column(Service::all(true), 'id');
        return self::generateBulk($serviceIds, [$cityId], [0], $regenerate);
    }

    public static function queueBatch(array $serviceIds, array $cityIds, array $industryIds, int $jobId = 0): int
    {
        if (!ge_table_exists('ge_generation_queue')) {
            return 0;
        }
        $db = ge_conn();
        $count = 0;
        $stmt = $db->prepare("INSERT INTO ge_generation_queue (job_id, service_id, city_id, industry_id, status) VALUES (?, ?, ?, ?, 'pending')");
        foreach ($serviceIds as $sid) {
            foreach ($cityIds as $cid) {
                foreach ($industryIds as $iid) {
                    $stmt->bind_param('iiii', $jobId, $sid, $cid, $iid);
                    $stmt->execute();
                    $count++;
                }
            }
        }
        return $count;
    }

    public static function processQueue(int $limit = 50): array
    {
        if (!ge_table_exists('ge_generation_queue')) {
            return ['processed' => 0];
        }
        $db = ge_conn();
        $res = $db->query("SELECT * FROM ge_generation_queue WHERE status = 'pending' ORDER BY id ASC LIMIT " . (int)$limit);
        $done = 0;
        while ($row = $res->fetch_assoc()) {
            $db->query("UPDATE ge_generation_queue SET status = 'processing' WHERE id = " . (int)$row['id']);
            $result = self::generateOne((int)$row['service_id'], (int)$row['city_id'], (int)$row['industry_id']);
            if ($result['success'] ?? false) {
                $slug = $db->real_escape_string($result['slug']);
                $db->query("UPDATE ge_generation_queue SET status = 'done', result_slug = '$slug', processed_at = NOW() WHERE id = " . (int)$row['id']);
            } elseif (!empty($result['skipped'])) {
                $db->query("UPDATE ge_generation_queue SET status = 'skipped', processed_at = NOW() WHERE id = " . (int)$row['id']);
            } else {
                $err = $db->real_escape_string($result['error'] ?? 'Unknown error');
                $db->query("UPDATE ge_generation_queue SET status = 'failed', error_message = '$err', processed_at = NOW() WHERE id = " . (int)$row['id']);
            }
            $done++;
        }
        return ['processed' => $done];
    }
}
