<?php
namespace Growth\Engines;

use Growth\Models\IndexingQueue;

/**
 * Orchestrates search engine discovery: IndexNow, sitemap pings, feed signals.
 */
class DiscoveryEngine
{
    /** Notify search engines about new/updated URLs (non-blocking best-effort). */
    public static function signalUrls(array $urls): array
    {
        $urls = array_values(array_unique(array_filter($urls)));
        if (empty($urls)) {
            return ['ok' => false];
        }

        $results = ['indexnow' => IndexingEngine::submitIndexNow($urls)];

        if (ge_setting('auto_sitemap_ping', '1') === '1') {
            $results['sitemap'] = IndexingEngine::pingAllSitemaps();
        }

        return $results;
    }

    /** Full site discovery push: submit all URLs via IndexNow + ping sitemaps. */
    public static function publishAll(int $queueLimit = 10000, int $processBatchSize = 100): array
    {
        $queued = IndexingEngine::queueAllPending($queueLimit, false, false);
        $submit = IndexingEngine::submitAllPublishedUrls(true, true);
        $process = IndexingEngine::processAllQueue($processBatchSize, 50);

        return [
            'queued' => $queued['queued'],
            'processed' => (int)($submit['urls_submitted'] ?? 0) + (int)($process['processed'] ?? 0),
            'failed' => (int)($process['failed'] ?? 0),
            'submit' => $submit,
            'sitemap' => $submit['sitemap'] ?? null,
        ];
    }

    public static function coreUrls(): array
    {
        $base = rtrim(SITE_URL, '/');
        $paths = ['', '/services', '/about', '/contact', '/insights', '/aeo', '/rss.xml', '/discover-feed.xml', '/news-sitemap.xml', '/sitemap.xml'];
        require_once __DIR__ . '/../../seo-data.php';
        foreach (array_keys(get_services_data()) as $slug) {
            $paths[] = '/' . $slug;
        }
        foreach (array_keys(get_cities_data()) as $slug) {
            $paths[] = '/digital-agency-' . $slug;
        }
        return array_map(fn($p) => $base . $p, $paths);
    }

    public static function enqueueUrl(string $url, int $landingPageId = 0, bool $expandLanguages = true): void
    {
        if (ge_setting('auto_index_queue', '1') !== '1') {
            return;
        }

        $urls = [$url];
        if ($expandLanguages) {
            require_once __DIR__ . '/../../i18n.php';
            $urls = nectra_language_url_variants($url);
        }

        foreach ($urls as $variant) {
            try {
                IndexingQueue::enqueue($variant, $landingPageId);
            } catch (\Throwable $e) {
                error_log('DiscoveryEngine::enqueueUrl: ' . $e->getMessage());
            }
        }
    }
}
