<?php
/**
 * Orphan blog posts: live at direct URL + indexed, hidden from public listings.
 */

if (!function_exists('blog_orphan_ensure_schema')) {
    function blog_orphan_ensure_schema($conn): void
    {
        if (is_file(__DIR__ . '/blog_schema.php')) {
            require_once __DIR__ . '/blog_schema.php';
            blog_schema_ensure($conn);
            return;
        }
        static $done = false;
        if ($done) {
            return;
        }
        $col = @$conn->query("SHOW COLUMNS FROM blog_posts LIKE 'is_orphan'");
        if ($col && $col->num_rows === 0) {
            @$conn->query('ALTER TABLE blog_posts ADD COLUMN is_orphan TINYINT(1) NOT NULL DEFAULT 0');
        }
        $done = true;
    }
}

if (!function_exists('blog_listable_sql')) {
    /** Posts that appear on Insights, RSS, related links, etc. */
    function blog_listable_sql(string $alias = ''): string
    {
        $col = $alias !== '' ? "{$alias}.is_orphan" : 'is_orphan';
        return "({$col} = 0 OR {$col} IS NULL)";
    }
}

if (!function_exists('blog_is_orphan')) {
    function blog_is_orphan(array $post): bool
    {
        return !empty($post['is_orphan']);
    }
}

if (!function_exists('blog_signal_post_indexed')) {
    /** Notify Bing/IndexNow immediately after publish (queue is backup only). */
    function blog_signal_post_indexed(string $slug, ?string $publishAt = null): void
    {
        $slug = trim($slug);
        if ($slug === '') {
            return;
        }

        if ($publishAt !== null && strtotime(str_replace('T', ' ', $publishAt)) > time()) {
            return;
        }

        try {
            if (!defined('SITE_URL')) {
                require_once __DIR__ . '/config.php';
            }

            $bootstrap = __DIR__ . '/growth/bootstrap.php';
            if (!is_file($bootstrap)) {
                return;
            }

            require_once $bootstrap;

            $url = rtrim(SITE_URL, '/') . '/' . $slug;
            $urls = [$url];

            try {
                \Growth\Engines\IndexingEngine::submitIndexNow($urls, false);
            } catch (\Throwable $e) {
                error_log('blog_signal IndexNow: ' . $e->getMessage());
            }

            try {
                \Growth\Engines\IndexingEngine::submitBingWebmasterUrls($urls, false);
            } catch (\Throwable $e) {
                error_log('blog_signal Bing API: ' . $e->getMessage());
            }

            try {
                \Growth\Engines\IndexingEngine::pingSitemapLegacy();
            } catch (\Throwable $e) {
                error_log('blog_signal sitemap ping: ' . $e->getMessage());
            }

            if (function_exists('ge_table_exists') && ge_table_exists('ge_indexing_queue')) {
                \Growth\Engines\DiscoveryEngine::enqueueUrl($url, 0, false);
            }
        } catch (\Throwable $e) {
            error_log('blog_signal_post_indexed: ' . $e->getMessage());
        }
    }
}
