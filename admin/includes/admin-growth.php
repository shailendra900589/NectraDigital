<?php
/**
 * Growth Engine helpers for legacy NECTRAOS dashboard.
 */
require_once __DIR__ . '/../../includes/growth/bootstrap.php';

use Growth\Models\Service;
use Growth\Models\City;
use Growth\Models\Industry;
use Growth\Models\Keyword;
use Growth\Models\LandingPage;
use Growth\Models\GenerationJob;
use Growth\Models\CrmLead;
use Growth\Models\IndexingQueue;
use Growth\Engines\IndexingEngine;

function admin_growth_ready(): bool
{
    return function_exists('ge_is_ready') && ge_is_ready();
}

function admin_growth_stats(): array
{
    $stats = [
        'ready' => admin_growth_ready(),
        'services' => 0,
        'cities' => 0,
        'industries' => 0,
        'keywords' => 0,
        'pages' => 0,
        'leads_crm' => 0,
        'potential' => 0,
        'indexed' => 0,
        'pending_index' => 0,
        'submitted_index' => 0,
        'failed_index' => 0,
        'queue_pending' => 0,
    ];

    if (!$stats['ready']) {
        return $stats;
    }

    try {
        $stats['services'] = Service::count(true);
        $stats['cities'] = City::count(true);
        $stats['keywords'] = Keyword::count();
        $stats['pages'] = LandingPage::count('published');
        if (ge_table_exists('ge_industries')) {
            $stats['industries'] = Industry::count(true);
        }
        if (ge_table_exists('ge_crm_leads')) {
            $stats['leads_crm'] = CrmLead::stats()['new'] ?? 0;
        }
        $stats['potential'] = $stats['services'] * $stats['cities'] * max(1, $stats['industries'] + 1);

        $idx = LandingPage::indexStats();
        $stats['indexed'] = (int)($idx['indexed'] ?? 0);
        $stats['pending_index'] = (int)($idx['pending'] ?? 0);
        $stats['submitted_index'] = (int)($idx['submitted'] ?? 0);
        $stats['failed_index'] = (int)($idx['failed'] ?? 0);

        if (ge_table_exists('ge_indexing_queue')) {
            $row = ge_conn()->query("SELECT COUNT(*) AS c FROM ge_indexing_queue WHERE status='pending'");
            $stats['queue_pending'] = $row ? (int)$row->fetch_assoc()['c'] : 0;
        }
    } catch (Throwable $e) {
        $stats['error'] = $e->getMessage();
    }

    return $stats;
}

function admin_handle_growth_post(string $page): ?string
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }

    $action = $_POST['growth_action'] ?? '';

    if ($page === 'cities' && $action === 'add_city') {
        if (!admin_growth_ready()) {
            return 'Growth database not migrated. Run database/migrate.php first.';
        }
        City::create([
            'name' => trim($_POST['name'] ?? ''),
            'slug' => ge_slugify($_POST['slug'] ?? $_POST['name'] ?? ''),
            'state' => trim($_POST['state'] ?? ''),
            'country' => trim($_POST['country'] ?? 'India'),
            'population' => (int)($_POST['population'] ?? 0),
            'latitude' => $_POST['latitude'] ?? '',
            'longitude' => $_POST['longitude'] ?? '',
            'city_description' => trim($_POST['city_description'] ?? ''),
            'status' => 'active',
        ]);
        return 'City added successfully.';
    }

    if ($page === 'cities' && $action === 'delete_city') {
        City::delete((int)($_POST['city_id'] ?? 0));
        return 'City deleted.';
    }

    if ($page === 'seo' && $action === 'queue_pending') {
        $r = IndexingEngine::queueAllPending(500, false);
        return "Queued {$r['queued']} pages for indexing.";
    }

    if ($page === 'seo' && $action === 'process_queue') {
        $r = IndexingEngine::processQueue((int)ge_setting('index_batch_size', 50));
        return "Processed {$r['processed']} URLs. Failed: {$r['failed']}.";
    }

    if ($page === 'seo' && $action === 'ping_sitemap') {
        $r = IndexingEngine::pingSitemap();
        return $r['ok'] ? 'Sitemap pinged to search engines.' : 'Sitemap ping failed — check settings.';
    }

    if ($page === 'seo' && $action === 'queue_and_process') {
        $r = IndexingEngine::queueAllPending(500, true);
        $p = $r['process']['processed'] ?? 0;
        return "Queued {$r['queued']} pages. Submitted {$p} via IndexNow.";
    }

    return null;
}

function admin_growth_cities(): array
{
    return admin_growth_ready() ? City::all() : [];
}

function admin_recent_jobs(int $limit = 5): array
{
    return (admin_growth_ready() && ge_table_exists('ge_generation_jobs')) ? GenerationJob::recent($limit) : [];
}

function admin_index_queue(int $limit = 20): array
{
    return (ge_table_exists('ge_indexing_queue')) ? IndexingQueue::all($limit) : [];
}

function admin_indexnow_info(): array
{
    return [
        'key' => IndexingEngine::apiKey(),
        'key_url' => IndexingEngine::keyFileUrl(),
        'host' => IndexingEngine::host(),
    ];
}
