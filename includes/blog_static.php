<?php
/**
 * Static HTML snapshots for blog posts — served to Bing/Google crawlers to bypass hCDN/WAF 403.
 */

if (!function_exists('blog_static_dir')) {
    function blog_static_dir(): string
    {
        $dir = dirname(__DIR__) . '/storage/blog-static';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }
}

if (!function_exists('blog_static_file_path')) {
    function blog_static_file_path(string $slug): ?string
    {
        $safe = preg_replace('/[^a-zA-Z0-9-]/', '', trim($slug));
        if ($safe === '') {
            return null;
        }
        return blog_static_dir() . '/' . $safe . '.html';
    }
}

if (!function_exists('blog_static_fetch_html')) {
    function blog_static_fetch_html(string $slug): ?string
    {
        if (!defined('SITE_URL')) {
            require_once __DIR__ . '/config.php';
        }

        $host = parse_url(SITE_URL, PHP_URL_HOST) ?: 'www.nectradigital.com';
        $path = '/' . rawurlencode($slug);
        $headers = "Host: {$host}\r\nUser-Agent: Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)\r\nAccept: text/html,application/xhtml+xml\r\nAccept-Language: en-US,en;q=0.9\r\n";

        foreach (['127.0.0.1', 'localhost'] as $ip) {
            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 60,
                    'header' => $headers,
                    'ignore_errors' => true,
                ],
            ]);
            $html = @file_get_contents('http://' . $ip . $path, false, $ctx);
            if (is_string($html) && strlen($html) > 500 && stripos($html, '</html>') !== false) {
                return $html;
            }
        }

        return null;
    }
}

if (!function_exists('blog_static_render_inline')) {
    function blog_static_render_inline(string $slug): ?string
    {
        $safe = preg_replace('/[^a-zA-Z0-9-]/', '', trim($slug));
        if ($safe === '') {
            return null;
        }

        if (!defined('NECTRA_STATIC_BUILD')) {
            define('NECTRA_STATIC_BUILD', true);
        }

        $level = ob_get_level();
        ob_start();
        try {
            $_GET['slug'] = $safe;
            $_SERVER['REQUEST_URI'] = '/' . $safe;
            $_SERVER['REQUEST_METHOD'] = 'GET';
            unset($_SERVER['HTTP_X_REQUESTED_WITH']);

            include __DIR__ . '/blog-static-render.php';
            $html = ob_get_clean();
        } catch (Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            error_log('blog_static_render_inline: ' . $e->getMessage());
            return null;
        }

        while (ob_get_level() > $level) {
            ob_end_clean();
        }

        return (is_string($html) && strlen($html) > 500 && stripos($html, '</html>') !== false) ? $html : null;
    }
}

if (!function_exists('blog_static_publish')) {
    function blog_static_publish(string $slug): bool
    {
        $path = blog_static_file_path($slug);
        if ($path === null) {
            return false;
        }

        $safe = preg_replace('/[^a-zA-Z0-9-]/', '', trim($slug));
        $html = blog_static_fetch_html($safe);
        if ($html === null) {
            $html = blog_static_render_inline($safe);
        }
        if ($html === null) {
            return false;
        }

        return @file_put_contents($path, $html) !== false;
    }
}

if (!function_exists('blog_static_delete')) {
    function blog_static_delete(string $slug): void
    {
        $path = blog_static_file_path($slug);
        if ($path && is_file($path)) {
            @unlink($path);
        }
    }
}

if (!function_exists('blog_static_rebuild_all')) {
    function blog_static_rebuild_all($conn): int
    {
        if (!$conn instanceof mysqli) {
            return 0;
        }
        $count = 0;
        $res = $conn->query('SELECT slug FROM blog_posts ORDER BY created_at DESC');
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                if (!empty($row['slug']) && blog_static_publish((string)$row['slug'])) {
                    $count++;
                }
            }
        }
        return $count;
    }
}
