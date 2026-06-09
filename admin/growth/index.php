<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../../includes/growth/bootstrap.php';

use Growth\Models\Service;
use Growth\Models\City;
use Growth\Models\Industry;
use Growth\Models\Keyword;
use Growth\Models\LandingPage;
use Growth\Models\GenerationJob;
use Growth\Models\CrmLead;

$stats = [
    'services' => 0,
    'cities' => 0,
    'industries' => 0,
    'keywords' => 0,
    'pages' => 0,
    'leads' => 0,
    'potential' => 0,
];
$indexStats = ['indexed' => 0, 'pending' => 0, 'total' => 0];
$recentJobs = [];
$dashboardError = null;

try {
    if (ge_is_ready()) {
        $stats['services'] = Service::count(true);
        $stats['cities'] = City::count(true);
        $stats['keywords'] = Keyword::count();
        $stats['pages'] = LandingPage::count('published');
        $indexStats = LandingPage::indexStats();
        if (ge_table_exists('ge_generation_jobs')) {
            $recentJobs = GenerationJob::recent(5);
        }
    }
    if (ge_table_exists('ge_industries')) {
        $stats['industries'] = Industry::count(true);
    }
    if (ge_table_exists('ge_crm_leads')) {
        $stats['leads'] = CrmLead::stats()['new'] ?? 0;
    }
    $stats['potential'] = $stats['services'] * $stats['cities'] * max(1, $stats['industries'] + 1);
} catch (Throwable $e) {
    $dashboardError = $e->getMessage();
}

require_once __DIR__ . '/includes/layout.php';
ge_admin_layout_start('Dashboard', 'dashboard');
?>

<?php if ($dashboardError): ?>
<div class="alert alert-danger mb-4">
    <strong>Dashboard data error:</strong> <?php echo htmlspecialchars($dashboardError); ?>
    <div class="small mt-1">Check database migration or run <a href="../../database/migrate.php" target="_blank">database/migrate.php</a>.</div>
</div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="ge-stat-card">
            <div class="ge-stat-value"><?php echo number_format($stats['services']); ?></div>
            <div class="ge-stat-label">Active Services</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ge-stat-card">
            <div class="ge-stat-value"><?php echo number_format($stats['cities']); ?></div>
            <div class="ge-stat-label">Active Cities</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ge-stat-card">
            <div class="ge-stat-value"><?php echo number_format($stats['pages']); ?></div>
            <div class="ge-stat-label">Landing Pages</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ge-stat-card">
            <div class="ge-stat-value"><?php echo number_format($stats['potential']); ?></div>
            <div class="ge-stat-label">Possible Pages</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="ge-card">
            <h2 class="h5 mb-4">Programmatic SEO Matrix</h2>
            <p class="text-muted small">Services × Cities × Industries = unlimited dynamic landing pages with unique content, GEO/AEO blocks, schema, and internal links.</p>
            <div class="progress mb-3" style="height: 8px; background: var(--ge-surface-2);">
                <?php $pct = $stats['potential'] > 0 ? min(100, round(($stats['pages'] / $stats['potential']) * 100)) : 0; ?>
                <div class="progress-bar bg-info" style="width: <?php echo $pct; ?>%"></div>
            </div>
            <p class="small text-muted mb-3"><?php echo $pct; ?>% generated (<?php echo number_format($stats['pages']); ?> / <?php echo number_format($stats['potential']); ?>)</p>
            <a href="generate.php" class="btn btn-ge-primary"><i class="fas fa-magic me-2"></i>Generate Landing Pages</a>
            <a href="services.php?action=add" class="btn btn-outline-secondary ms-2">Add Service</a>
            <a href="cities.php?action=add" class="btn btn-outline-secondary ms-2">Add City</a>
            <a href="industries.php?action=add" class="btn btn-outline-secondary ms-2">Add Industry</a>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ge-card">
            <h2 class="h6 mb-3">Indexing Status</h2>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted small">Indexed</span><strong class="text-success"><?php echo number_format($indexStats['indexed'] ?? 0); ?></strong></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted small">Pending</span><strong class="text-warning"><?php echo number_format($indexStats['pending'] ?? 0); ?></strong></div>
            <div class="d-flex justify-content-between mb-3"><span class="text-muted small">Total Pages</span><strong><?php echo number_format($indexStats['total'] ?? 0); ?></strong></div>
            <a href="indexing.php" class="btn btn-sm btn-outline-secondary w-100">Manage Indexing</a>
        </div>
    </div>
</div>

<?php if (!empty($recentJobs)): ?>
<div class="ge-card">
    <h2 class="h6 mb-3">Recent Generation Jobs</h2>
    <div class="table-responsive">
        <table class="table ge-table table-sm">
            <thead><tr><th>ID</th><th>Type</th><th>Progress</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($recentJobs as $job): ?>
            <tr>
                <td>#<?php echo $job['id']; ?></td>
                <td><?php echo htmlspecialchars($job['job_type']); ?></td>
                <td><?php echo $job['processed']; ?>/<?php echo $job['total_pages']; ?></td>
                <td><span class="ge-badge ge-badge-<?php echo $job['status'] === 'completed' ? 'indexed' : 'pending'; ?>"><?php echo $job['status']; ?></span></td>
                <td class="small text-muted"><?php echo $job['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php ge_admin_layout_end(); ?>
