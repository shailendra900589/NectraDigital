<?php
/**
 * Orphan blog posts: live at direct URL + indexed, hidden from public listings.
 */

if (!function_exists('blog_orphan_ensure_schema')) {
    function blog_orphan_ensure_schema($conn): void
    {
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
    /** Queue IndexNow + Bing URL submission after publish/update. */
    function blog_signal_post_indexed(string $slug, ?string $publishAt = null): void
    {
        $slug = trim($slug);
        if ($slug === '') {
            return;
        }

        if ($publishAt !== null && strtotime($publishAt) > time()) {
            return;
        }

        if (!defined('SITE_URL')) {
            require_once __DIR__ . '/config.php';
        }

        $bootstrap = __DIR__ . '/growth/bootstrap.php';
        if (!is_file($bootstrap)) {
            return;
        }

        require_once $bootstrap;

        $url = rtrim(SITE_URL, '/') . '/' . $slug;
        \Growth\Engines\DiscoveryEngine::enqueueUrl($url);
        \Growth\Engines\DiscoveryEngine::signalUrls([$url]);
        \Growth\Engines\IndexingEngine::submitBingWebmasterUrls([$url], false);
    }
}
