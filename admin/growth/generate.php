<?php
require_once __DIR__ . '/init.php';

use Growth\Models\Service;
use Growth\Models\City;
use Growth\Models\Industry;
use Growth\Models\LandingPage;
use Growth\Engines\CatalogSyncEngine;
use Growth\LandingPageGenerator;

$result = null;
$preselectServiceId = (int)($_GET['service_id'] ?? 0);
$preselectMode = $_GET['mode'] ?? '';

if (ge_is_ready() && Service::count() === 0) {
    CatalogSyncEngine::syncAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    set_time_limit(600);
    $mode = $_POST['mode'] ?? 'selected';
    $regenerate = !empty($_POST['regenerate']);
    $includeIndustries = !empty($_POST['include_industries']);
    $useQueue = !empty($_POST['use_queue']) && ge_table_exists('ge_generation_queue');

    if ($mode === 'full_matrix') {
        if ($useQueue) {
            $sids = array_column(Service::all(true), 'id');
            $cids = array_column(City::all(true), 'id');
            $iids = $includeIndustries ? array_merge([0], array_column(Industry::all(true), 'id')) : [0];
            $jobId = \Growth\Models\GenerationJob::create('full_matrix', count($sids) * count($cids) * count($iids));
            $queued = LandingPageGenerator::queueBatch($sids, $cids, $iids, $jobId);
            ge_admin_flash('success', "Queued {$queued} pages for background processing. Run cron/process-queue.php.");
            header('Location: generate.php');
            exit;
        }
        $result = LandingPageGenerator::generateFullMatrix($includeIndustries, $regenerate);
    } elseif ($mode === 'service_all_cities') {
        $sid = (int)$_POST['service_id'];
        $cityIds = array_column(City::all(true), 'id');
        $iids = $includeIndustries ? array_merge([0], array_column(Industry::all(true), 'id')) : [0];
        $result = LandingPageGenerator::generateBulk([$sid], $cityIds, $iids, $regenerate);
    } elseif ($mode === 'city_all_services') {
        $cid = (int)$_POST['city_id'];
        $serviceIds = array_column(Service::all(true), 'id');
        $iids = $includeIndustries ? array_merge([0], array_column(Industry::all(true), 'id')) : [0];
        $result = LandingPageGenerator::generateBulk($serviceIds, [$cid], $iids, $regenerate);
    } elseif ($mode === 'triple_matrix') {
        $serviceIds = array_map('intval', $_POST['service_ids'] ?? []);
        $cityIds = array_map('intval', $_POST['city_ids'] ?? []);
        $industryIds = array_map('intval', $_POST['industry_ids'] ?? []);
        if (empty($serviceIds) || empty($cityIds) || empty($industryIds)) {
            ge_admin_flash('error', 'Select services, cities, and industries.');
        } else {
            $result = LandingPageGenerator::generateBulk($serviceIds, $cityIds, $industryIds, $regenerate);
        }
    } else {
        $serviceIds = array_map('intval', $_POST['service_ids'] ?? []);
        $cityIds = array_map('intval', $_POST['city_ids'] ?? []);
        if (empty($serviceIds) || empty($cityIds)) {
            ge_admin_flash('error', 'Select at least one service and one city.');
        } else {
            $iids = $includeIndustries ? array_merge([0], array_column(Industry::all(true), 'id')) : [0];
            $result = LandingPageGenerator::generateBulk($serviceIds, $cityIds, $iids, $regenerate);
        }
    }

    if ($result && ($result['processed'] ?? 0) > 0) {
        ge_admin_flash('success', "Generated {$result['processed']} pages" . ($result['failed'] ? ", {$result['failed']} failed" : '') . '.');
    }
}

$services = ge_is_ready() ? Service::all(true) : [];
$cities = ge_is_ready() ? City::all(true) : [];
$industries = (ge_is_ready() && ge_table_exists('ge_industries')) ? Industry::all(true) : [];
$potentialCity = count($services) * count($cities);
$potentialFull = $potentialCity * max(1, count($industries) + 1);
$coverage = ge_is_ready() ? LandingPage::coverageSummary() : [];
$defaultMode = $preselectMode ?: ($preselectServiceId ? 'service_all_cities' : 'selected');

ge_admin_layout();
ge_admin_layout_start('Page Generator', 'generate');
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="ge-card mb-4">
            <h2 class="h5 mb-2">Programmatic SEO Generator</h2>
            <p class="text-muted small">Generate unique landing pages: Service × City × Industry. Each page includes unique content, metadata, FAQs, schema, keywords, and 10+ internal links.</p>
            <p class="small"><strong>Potential:</strong> <?php echo number_format($potentialCity); ?> (city-only) · <?php echo number_format($potentialFull); ?> (with industries)</p>
            <?php if (!empty($coverage) && $coverage['expected'] > 0): ?>
            <p class="small mb-0"><strong>Coverage:</strong> <?php echo number_format($coverage['pages']); ?>/<?php echo number_format($coverage['expected']); ?> pages (<?php echo $coverage['coverage_pct']; ?>%) · <a href="services.php">Manage services</a> · <a href="landing-pages.php">View pages</a></p>
            <?php elseif (empty($services)): ?>
            <p class="small text-warning mb-0">No services in database. <a href="services.php">Sync from website</a> first.</p>
            <?php endif; ?>
        </div>

        <form method="POST" class="ge-card">
            <div class="mb-4">
                <label class="form-label fw-bold">Generation Mode</label>
                <?php foreach ([
                    'selected' => 'Selected Services + Cities',
                    'triple_matrix' => 'Triple Matrix (Service × City × Industry)',
                    'service_all_cities' => 'One Service → All Cities',
                    'city_all_services' => 'One City → All Services',
                    'full_matrix' => 'Full Matrix (All Combinations)',
                ] as $val => $label): ?>
                <div class="form-check"><input class="form-check-input" type="radio" name="mode" value="<?php echo $val; ?>" id="mode_<?php echo $val; ?>" <?php echo $val===$defaultMode?'checked':''; ?> onchange="toggleMode()"><label class="form-check-label" for="mode_<?php echo $val; ?>"><?php echo $label; ?></label></div>
                <?php endforeach; ?>
            </div>

            <div id="blockSelected" class="mb-4 <?php echo $defaultMode !== 'selected' ? 'd-none' : ''; ?>">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Services</label><select name="service_ids[]" class="form-select" multiple size="8"><?php foreach ($services as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label class="form-label">Cities</label><select name="city_ids[]" class="form-select" multiple size="8"><?php foreach ($cities as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
                </div>
            </div>
            <div id="blockTriple" class="mb-4 d-none">
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label">Services</label><select name="service_ids[]" class="form-select" multiple size="6"><?php foreach ($services as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4"><label class="form-label">Cities</label><select name="city_ids[]" class="form-select" multiple size="6"><?php foreach ($cities as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4"><label class="form-label">Industries</label><select name="industry_ids[]" class="form-select" multiple size="6"><?php foreach ($industries as $i): ?><option value="<?php echo $i['id']; ?>"><?php echo htmlspecialchars($i['name']); ?></option><?php endforeach; ?></select></div>
                </div>
            </div>
            <div id="blockService" class="mb-4 <?php echo $defaultMode !== 'service_all_cities' ? 'd-none' : ''; ?>"><label class="form-label">Service</label><select name="service_id" class="form-select"><?php foreach ($services as $s): ?><option value="<?php echo $s['id']; ?>" <?php echo $preselectServiceId === (int)$s['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
            <div id="blockCity" class="mb-4 d-none"><label class="form-label">City</label><select name="city_id" class="form-select"><?php foreach ($cities as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>

            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="include_industries" id="include_industries"><label class="form-check-label" for="include_industries">Also generate industry variants (Service × City × Industry)</label></div>
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="regenerate" id="regenerate"><label class="form-check-label" for="regenerate">Regenerate existing pages (overwrite)</label></div>
            <?php if (ge_table_exists('ge_generation_queue')): ?>
            <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="use_queue" id="use_queue"><label class="form-check-label" for="use_queue">Queue for background processing (large batches)</label></div>
            <?php endif; ?>

            <button type="submit" class="btn btn-ge-primary btn-lg" onclick="return confirm('Start generation? Large batches may take time.')"><i class="fas fa-magic me-2"></i>Generate Landing Pages</button>
        </form>
    </div>
    <div class="col-lg-4">
        <div class="ge-card">
            <h2 class="h6 mb-3">URL Patterns</h2>
            <p class="small text-muted">City:</p>
            <code class="d-block p-2 bg-dark rounded small mb-2"><?php echo htmlspecialchars(ge_setting('url_pattern_city', '{url_prefix}-company-in-{city_slug}')); ?></code>
            <p class="small text-muted">Industry:</p>
            <code class="d-block p-2 bg-dark rounded small"><?php echo htmlspecialchars(ge_setting('url_pattern_industry', '{url_prefix}-company-in-{city_slug}-for-{industry_slug}')); ?></code>
        </div>
    </div>
</div>

<script>
function toggleMode() {
    const m = document.querySelector('input[name=mode]:checked').value;
    document.getElementById('blockSelected').classList.toggle('d-none', m !== 'selected');
    document.getElementById('blockTriple').classList.toggle('d-none', m !== 'triple_matrix');
    document.getElementById('blockService').classList.toggle('d-none', m !== 'service_all_cities');
    document.getElementById('blockCity').classList.toggle('d-none', m !== 'city_all_services');
}
</script>

<?php ge_admin_layout_end(); ?>
