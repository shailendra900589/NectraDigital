<?php
namespace Growth\Engines;

class CompetitorEngine
{
    public static function analyze(string $url): array
    {
        $url = trim($url);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'error' => 'Invalid URL'];
        }

        $html = self::fetch($url);
        if (!$html) {
            return ['success' => false, 'error' => 'Could not fetch URL'];
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $title = '';
        $nodes = $xpath->query('//title');
        if ($nodes->length) {
            $title = trim($nodes->item(0)->textContent);
        }

        $metaDesc = '';
        foreach ($xpath->query('//meta[@name="description"]') as $meta) {
            $metaDesc = trim($meta->getAttribute('content'));
            break;
        }

        $h1 = [];
        foreach ($xpath->query('//h1') as $node) {
            $t = trim($node->textContent);
            if ($t) $h1[] = $t;
        }

        $h2 = [];
        foreach ($xpath->query('//h2') as $node) {
            $t = trim($node->textContent);
            if ($t) $h2[] = $t;
            if (count($h2) >= 15) break;
        }

        $schemas = [];
        foreach ($xpath->query('//script[@type="application/ld+json"]') as $script) {
            $json = trim($script->textContent);
            if ($json) {
                $decoded = json_decode($json, true);
                if ($decoded) {
                    $type = is_array($decoded['@type'] ?? null) ? implode(', ', $decoded['@type']) : ($decoded['@type'] ?? 'Unknown');
                    $schemas[] = $type;
                }
            }
        }

        $text = strtolower(strip_tags($html));
        $keywords = self::detectKeywords($text, $title, $h1, $h2);
        $gaps = self::contentGaps($title, $metaDesc, $h1, $schemas);
        $opportunities = self::opportunities($gaps, $keywords);

        $domain = parse_url($url, PHP_URL_HOST) ?: $url;
        $analysis = [
            'competitor_url' => $url,
            'domain' => $domain,
            'meta_title' => mb_substr($title, 0, 500),
            'meta_description' => $metaDesc,
            'h1_tags' => ge_json_encode($h1),
            'h2_tags' => ge_json_encode($h2),
            'schemas_found' => ge_json_encode(array_values(array_unique($schemas))),
            'keywords_detected' => ge_json_encode($keywords),
            'content_gaps' => ge_json_encode($gaps),
            'opportunities' => ge_json_encode($opportunities),
            'raw_analysis' => ge_json_encode(['h1_count' => count($h1), 'h2_count' => count($h2), 'schema_count' => count($schemas)]),
        ];

        if (ge_table_exists('ge_competitor_analyses')) {
            self::save($analysis);
        }

        return ['success' => true, 'data' => $analysis];
    }

    private static function fetch(string $url): ?string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_USERAGENT => 'NectraGrowthBot/2.0 (+https://nectradigital.com)',
            ]);
            $body = curl_exec($ch);
            curl_close($ch);
            return $body ?: null;
        }
        $ctx = stream_context_create(['http' => ['timeout' => 15, 'header' => "User-Agent: NectraGrowthBot/2.0\r\n"]]);
        $body = @file_get_contents($url, false, $ctx);
        return $body ?: null;
    }

    private static function detectKeywords(string $text, string $title, array $h1, array $h2): array
    {
        $phrases = array_merge([$title], $h1, array_slice($h2, 0, 5));
        $found = [];
        foreach ($phrases as $p) {
            $p = trim($p);
            if (strlen($p) > 5) {
                $found[] = $p;
            }
        }
        preg_match_all('/\b(seo|marketing|development|design|agency|services|company|digital|software|automation)\b/i', $text, $m);
        foreach (array_unique($m[0] ?? []) as $w) {
            $found[] = strtolower($w);
        }
        return array_values(array_unique(array_slice($found, 0, 30)));
    }

    private static function contentGaps(string $title, string $meta, array $h1, array $schemas): array
    {
        $gaps = [];
        if (!$title) $gaps[] = 'Missing meta title';
        if (!$meta) $gaps[] = 'Missing meta description';
        if (empty($h1)) $gaps[] = 'Missing H1 tag';
        if (empty($schemas)) $gaps[] = 'No JSON-LD schema detected';
        if (!in_array('FAQPage', $schemas, true) && !in_array('FAQ', $schemas, true)) {
            $gaps[] = 'No FAQ schema — opportunity for AEO';
        }
        if (!in_array('LocalBusiness', $schemas, true)) {
            $gaps[] = 'No LocalBusiness schema — local SEO gap';
        }
        return $gaps;
    }

    private static function opportunities(array $gaps, array $keywords): array
    {
        $ops = [];
        foreach ($gaps as $g) {
            $ops[] = "Outrank competitor by fixing: {$g}";
        }
        if (count($keywords) < 10) {
            $ops[] = 'Expand keyword coverage with programmatic landing pages';
        }
        $ops[] = 'Create city × service landing pages targeting detected terms';
        $ops[] = 'Add GEO quick answers and voice-search optimized FAQs';
        return array_slice($ops, 0, 10);
    }

    private static function save(array $data): int
    {
        $db = ge_conn();
        $stmt = $db->prepare(
            "INSERT INTO ge_competitor_analyses (competitor_url, domain, meta_title, meta_description, h1_tags, h2_tags, schemas_found, keywords_detected, content_gaps, opportunities, raw_analysis)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'sssssssssss',
            $data['competitor_url'], $data['domain'], $data['meta_title'], $data['meta_description'],
            $data['h1_tags'], $data['h2_tags'], $data['schemas_found'], $data['keywords_detected'],
            $data['content_gaps'], $data['opportunities'], $data['raw_analysis']
        );
        $stmt->execute();
        return (int)$db->insert_id;
    }

    public static function recent(int $limit = 20): array
    {
        if (!ge_table_exists('ge_competitor_analyses')) return [];
        $db = ge_conn();
        $limit = (int)$limit;
        $res = $db->query("SELECT * FROM ge_competitor_analyses ORDER BY analyzed_at DESC LIMIT {$limit}");
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
}
