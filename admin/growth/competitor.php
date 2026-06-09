<?php
require_once __DIR__ . '/init.php';

use Growth\Engines\CompetitorEngine;

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['competitor_url'])) {
    set_time_limit(60);
    $result = CompetitorEngine::analyze(trim($_POST['competitor_url']));
    if ($result['success'] ?? false) {
        ge_admin_flash('success', 'Competitor analyzed successfully.');
    } else {
        ge_admin_flash('error', $result['error'] ?? 'Analysis failed.');
    }
}

$recent = CompetitorEngine::recent(15);

ge_admin_layout();
ge_admin_layout_start('Competitor Intelligence', 'competitor');
?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="ge-card">
            <h2 class="h6 mb-3">Analyze Competitor URL</h2>
            <form method="POST">
                <div class="mb-3"><label class="form-label">Competitor URL</label><input type="url" name="competitor_url" class="form-control" placeholder="https://competitor.com" required></div>
                <button type="submit" class="btn btn-ge-primary"><i class="fas fa-search me-2"></i>Analyze</button>
            </form>
            <p class="text-muted small mt-3 mb-0">Extracts meta tags, headings, schema types, keyword signals, content gaps, and SEO opportunities.</p>
        </div>
    </div>
    <div class="col-lg-7">
        <?php if ($result && ($result['success'] ?? false)):
            $d = $result['data'];
            $gaps = ge_json_decode($d['content_gaps'] ?? '[]');
            $ops = ge_json_decode($d['opportunities'] ?? '[]');
        ?>
        <div class="ge-card mb-4">
            <h2 class="h6"><?php echo htmlspecialchars($d['domain']); ?></h2>
            <p class="small"><strong>Title:</strong> <?php echo htmlspecialchars($d['meta_title']); ?></p>
            <p class="small text-muted"><?php echo htmlspecialchars(mb_substr($d['meta_description'] ?? '', 0, 200)); ?></p>
            <h3 class="h6 mt-3">Content Gaps</h3>
            <ul class="small"><?php foreach ($gaps as $g): ?><li><?php echo htmlspecialchars($g); ?></li><?php endforeach; ?></ul>
            <h3 class="h6 mt-2">Opportunities</h3>
            <ul class="small"><?php foreach ($ops as $o): ?><li><?php echo htmlspecialchars($o); ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <div class="ge-card">
            <h2 class="h6 mb-3">Recent Analyses</h2>
            <div class="table-responsive"><table class="table ge-table table-sm"><thead><tr><th>Domain</th><th>Title</th><th>Date</th></tr></thead><tbody>
            <?php foreach ($recent as $r): ?>
            <tr><td><?php echo htmlspecialchars($r['domain']); ?></td><td class="small"><?php echo htmlspecialchars(mb_substr($r['meta_title'] ?? '', 0, 60)); ?></td><td class="small text-muted"><?php echo $r['analyzed_at']; ?></td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
        </div>
    </div>
</div>

<?php ge_admin_layout_end(); ?>
