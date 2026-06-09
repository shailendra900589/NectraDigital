<?php
namespace Growth\Engines;

use Growth\Models\LandingPage;

class SitemapEngine
{
    public static function generateXml(): string
    {
        $base = SITE_URL;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $static = [
            '' => '1.0', 'services' => '0.9', 'about' => '0.8', 'contact' => '0.8',
            'insights' => '0.9', 'portfolio' => '0.8', 'aeo' => '0.7', 'hire-experts' => '0.7',
            'careers' => '0.6', 'privacy' => '0.3', 'terms' => '0.3',
        ];

        $now = date('c');
        foreach ($static as $path => $priority) {
            $loc = $base . ($path ? '/' . $path : '');
            $xml .= self::urlEntry($loc, $now, 'weekly', $priority);
        }

        require_once __DIR__ . '/../../seo-data.php';
        foreach (get_services_data() as $slug => $svc) {
            $xml .= self::urlEntry($base . '/' . $slug, $now, 'weekly', '0.85');
        }
        foreach (get_cities_data() as $slug => $city) {
            $xml .= self::urlEntry($base . '/digital-agency-' . $slug, $now, 'weekly', '0.8');
        }

        global $conn;
        if (isset($conn) && $conn instanceof \mysqli) {
            $res = @$conn->query("SELECT slug, created_at FROM blog_posts ORDER BY created_at DESC");
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $xml .= self::urlEntry($base . '/' . $row['slug'], date('c', strtotime($row['created_at'])), 'monthly', '0.75');
                }
            }
        }

        if (ge_is_ready()) {
            $offset = 0;
            $batch = 5000;
            do {
                $pages = LandingPage::forSitemap($batch, $offset);
                foreach ($pages as $p) {
                    $date = !empty($p['updated_at']) ? date('c', strtotime($p['updated_at'])) : date('c', strtotime($p['generated_at']));
                    $xml .= self::urlEntry($base . '/' . $p['slug'], $date, 'monthly', '0.7');
                }
                $offset += $batch;
            } while (count($pages) === $batch);
        }

        $xml .= '</urlset>';
        return $xml;
    }

    private static function urlEntry(string $loc, string $lastmod, string $freq, string $priority): string
    {
        return "  <url><loc>" . htmlspecialchars($loc) . "</loc><lastmod>{$lastmod}</lastmod><changefreq>{$freq}</changefreq><priority>{$priority}</priority></url>\n";
    }
}
