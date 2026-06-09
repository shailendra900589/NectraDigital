<?php
namespace Growth\Engines;

use Growth\Models\IndexingQueue;

/**
 * Multi-engine URL indexing: IndexNow (Bing/Yandex/DDG), sitemap pings (Google/Bing).
 */
class IndexingEngine
{
    private const INDEXNOW_ENDPOINTS = [
        'indexnow' => 'https://api.indexnow.org/indexnow',
        'bing' => 'https://www.bing.com/indexnow',
        'yandex' => 'https://yandex.com/indexnow',
    ];

    public static function apiKey(): string
    {
        $key = trim((string)ge_setting('indexnow_api_key', ''));
        if ($key === '') {
            $key = trim((string)ge_setting('indexnow_key', ''));
        }
        if ($key === '') {
            $key = self::generateAndStoreKey();
        }
        self::ensureKeyFile($key);
        return $key;
    }

    public static function host(): string
    {
        $host = parse_url(SITE_URL, PHP_URL_HOST);
        return $host ?: 'www.nectradigital.com';
    }

    public static function keyFileUrl(): string
    {
        return SITE_URL . '/' . self::apiKey() . '.txt';
    }

    private static function generateAndStoreKey(): string
    {
        $key = bin2hex(random_bytes(16));
        if (ge_table_exists('ge_settings')) {
            $db = ge_conn();
            $stmt = $db->prepare("INSERT INTO ge_settings (setting_key, setting_value) VALUES ('indexnow_api_key', ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            if ($stmt) {
                $stmt->bind_param('s', $key);
                $stmt->execute();
            }
        }
        return $key;
    }

    public static function siteRoot(): string
    {
        return dirname(__DIR__, 3);
    }

    public static function ensureKeyFile(?string $key = null): bool
    {
        $key = $key ?: self::apiKey();
        $path = self::siteRoot() . '/' . $key . '.txt';
        if (is_file($path)) {
            return true;
        }
        return (bool)file_put_contents($path, $key);
    }

    public static function isEngineEnabled(string $engine): bool
    {
        return ge_setting('index_engine_' . $engine, '1') === '1';
    }

    /** Submit one or many URLs via IndexNow to all enabled endpoints. */
    public static function submitIndexNow(array $urls): array
    {
        $urls = array_values(array_unique(array_filter($urls)));
        if (empty($urls)) {
            return ['ok' => false, 'message' => 'No URLs provided', 'engines' => []];
        }

        $key = self::apiKey();
        $host = self::host();
        $payload = json_encode([
            'host' => $host,
            'key' => $key,
            'keyLocation' => self::keyFileUrl(),
            'urlList' => array_slice($urls, 0, 10000),
        ]);

        $results = [];
        foreach (self::INDEXNOW_ENDPOINTS as $name => $endpoint) {
            if (!self::isEngineEnabled($name)) {
                $results[$name] = ['skipped' => true];
                continue;
            }
            $results[$name] = self::httpPost($endpoint, $payload, ['Content-Type: application/json; charset=utf-8']);
        }

        // DuckDuckGo participates in IndexNow via Bing — log separately for dashboard clarity
        if (self::isEngineEnabled('duckduckgo')) {
            $results['duckduckgo'] = $results['bing'] ?? ['note' => 'Uses IndexNow via Bing network'];
        }

        $ok = false;
        foreach ($results as $r) {
            if (!empty($r['ok'])) {
                $ok = true;
            }
        }

        return ['ok' => $ok, 'urls' => count($urls), 'engines' => $results];
    }

    /** Ping sitemap to Google and Bing webmaster endpoints. */
    public static function pingSitemap(): array
    {
        return self::pingAllSitemaps();
    }

    /** Ping XML sitemaps (Yandex). Google/Bing ping deprecated — use IndexNow for URLs. */
    public static function pingAllSitemaps(): array
    {
        $sitemaps = [
            SITE_URL . '/sitemap.xml',
            SITE_URL . '/news-sitemap.xml',
        ];
        $results = [];
        $indexNow = self::submitIndexNow($sitemaps);

        foreach ($sitemaps as $sitemap) {
            $encoded = urlencode($sitemap);
            if (self::isEngineEnabled('yandex')) {
                $results['yandex_' . basename($sitemap)] = self::httpGet("https://webmaster.yandex.com/ping?sitemap={$encoded}");
            }
        }

        $ok = !empty($indexNow['ok']);
        foreach ($results as $r) {
            if (!empty($r['ok'])) {
                $ok = true;
            }
        }
        return [
            'ok' => $ok,
            'sitemaps' => $sitemaps,
            'indexnow' => $indexNow,
            'engines' => $results,
            'note' => 'Google/Bing sitemap ping deprecated; URLs submitted via IndexNow + submit sitemap in Search Console.',
        ];
    }

    /** Legacy single sitemap ping — delegates to pingAllSitemaps. */
    public static function pingSitemapLegacy(): array
    {
        $sitemap = SITE_URL . '/sitemap.xml';
        $encoded = urlencode($sitemap);
        $results = [];

        if (self::isEngineEnabled('google_sitemap')) {
            $results['google_sitemap'] = self::httpGet("https://www.google.com/ping?sitemap={$encoded}");
        }
        if (self::isEngineEnabled('bing_sitemap')) {
            $results['bing_sitemap'] = self::httpGet("https://www.bing.com/ping?sitemap={$encoded}");
        }

        $ok = false;
        foreach ($results as $r) {
            if (!empty($r['ok'])) {
                $ok = true;
            }
        }
        return ['ok' => $ok, 'sitemap' => $sitemap, 'engines' => $results];
    }

    /** Process pending ge_indexing_queue items with real HTTP submissions. */
    public static function processQueue(int $limit = 50): array
    {
        if (!ge_table_exists('ge_indexing_queue')) {
            return ['processed' => 0, 'failed' => 0, 'message' => 'Indexing queue table missing'];
        }

        $pending = IndexingQueue::pending($limit);
        if (empty($pending)) {
            return ['processed' => 0, 'failed' => 0, 'message' => 'Queue empty'];
        }

        $urls = array_column($pending, 'url');
        $batch = self::submitIndexNow($urls);
        $sitemap = self::pingSitemap();

        $processed = 0;
        $failed = 0;
        $responseLog = json_encode(['indexnow' => $batch, 'sitemap' => $sitemap], JSON_UNESCAPED_SLASHES);

        foreach ($pending as $item) {
            $id = (int)$item['id'];
            $pageId = (int)($item['landing_page_id'] ?? 0);
            if ($batch['ok']) {
                IndexingQueue::markProcessed($id, 'completed', $responseLog);
                if ($pageId > 0) {
                    ge_conn()->query("UPDATE ge_landing_pages SET index_status='submitted', index_submitted_at=NOW() WHERE id={$pageId}");
                }
                $processed++;
            } else {
                IndexingQueue::markProcessed($id, 'failed', $responseLog);
                if ($pageId > 0) {
                    ge_conn()->query("UPDATE ge_landing_pages SET index_status='failed' WHERE id={$pageId}");
                }
                $failed++;
            }
        }

        return [
            'processed' => $processed,
            'failed' => $failed,
            'indexnow' => $batch,
            'sitemap' => $sitemap,
        ];
    }

    /** Queue all pending landing pages and optionally process immediately. */
    public static function queueAllPending(int $limit = 500, bool $processNow = false): array
    {
        if (!ge_table_exists('ge_landing_pages')) {
            return ['queued' => 0, 'processed' => 0];
        }

        $db = ge_conn();
        $res = $db->query("SELECT id, slug FROM ge_landing_pages WHERE status='published' AND index_status IN ('pending','failed') LIMIT " . (int)$limit);
        $queued = 0;
        while ($row = $res->fetch_assoc()) {
            IndexingQueue::enqueue(SITE_URL . '/' . $row['slug'], (int)$row['id']);
            $db->query("UPDATE ge_landing_pages SET index_status='submitted', index_submitted_at=NOW() WHERE id=" . (int)$row['id']);
            $queued++;
        }

        $processResult = $processNow ? self::processQueue(min(50, $queued ?: 50)) : ['processed' => 0];

        return ['queued' => $queued, 'process' => $processResult];
    }

    public static function lastSubmissionSummary(): array
    {
        if (!ge_table_exists('ge_indexing_queue')) {
            return [];
        }
        $row = ge_conn()->query("SELECT response, processed_at FROM ge_indexing_queue WHERE response IS NOT NULL ORDER BY id DESC LIMIT 1");
        if (!$row || !$row->num_rows) {
            return [];
        }
        $data = $row->fetch_assoc();
        $decoded = json_decode($data['response'] ?? '', true);
        return is_array($decoded) ? $decoded : ['raw' => $data['response'], 'at' => $data['processed_at']];
    }

    private static function httpPost(string $url, string $body, array $headers = []): array
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            $response = curl_exec($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return ['ok' => in_array($code, [200, 202], true), 'code' => $code, 'body' => substr((string)$response, 0, 500)];
        }

        $ctx = stream_context_create(['http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $body,
            'timeout' => 20,
            'ignore_errors' => true,
        ]]);
        $response = @file_get_contents($url, false, $ctx);
        $code = 0;
        if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) {
            $code = (int)$m[1];
        }
        return ['ok' => in_array($code, [200, 202], true), 'code' => $code, 'body' => substr((string)$response, 0, 500)];
    }

    private static function httpGet(string $url): array
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20, CURLOPT_FOLLOWLOCATION => true]);
            $response = curl_exec($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return ['ok' => $code >= 200 && $code < 400, 'code' => $code, 'body' => substr((string)$response, 0, 300)];
        }
        $response = @file_get_contents($url);
        return ['ok' => $response !== false, 'code' => 200, 'body' => substr((string)$response, 0, 300)];
    }
}
