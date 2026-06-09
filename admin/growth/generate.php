<?php
require_once 'includes/auth.php';
require_once __DIR__ . '/../../includes/growth/bootstrap.php';

use Growth\Models\Service;
use Growth\Models\City;
use Growth\LandingPageGenerator;

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    set_time_limit(300);
    $mode = $_POST['mode'] ?? 'single';
    $regenerate = !empty($_POST['regenerate']);

    if ($mode === 'full_matrix') {
        $result = LandingPageGenerator::generateFullMatrix($regenerate);
    } elseif ($mode === 'service_all_cities') {
        $sid = (int)$_POST['service_id'];
        $cityIds = array_column(City::all(true), 'id');
        $result = LandingPageGenerator::generateBulk([$sid], $cityIds, $regenerate);
    } elseif ($mode === 'city_all_services') {
        $cid = (int)$_POST['city_id'];
        $serviceIds = array_column(Service::all(true), 'id');
        $result = LandingPageGenerator::generateBulk($serviceIds, [$cid], $regenerate);
    } else {
        $serviceIds = array_map('intval', $_POST['service_ids'] ?? []);
        $cityIds = array_map('intval', $_POST['city_ids'] ?? []);
        if (empty($serviceIds) || empty($cityIds)) {
            ge_admin_flash('error', 'Select at least one service and one city.');
        } else {
            $result = LandingPageGenerator::generateBulk($serviceIds, $cityIds, $regenerate);
        }
    }

    if ($result && ($result['processed'] ?? 0) > 0) {
        ge_admin_flash('success', "Generated {$result['processed']} pages" . ($result['failed'] ? ", {$result['failed']} failed" : '') . '.');
    }
}

$services = ge_is_ready() ? Service::all(true) : [];
$cities = ge_is_ready() ? City::all(true) : [];
$potential = count($services) * count($cities);

require_once 'includes/layout.php';
ge_admin_layout_start('Page Generator', 'generate');
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="ge-card mb-4">
            <h2 class="h5 mb-2">Programmatic SEO Generator</h2>
            <p class="text-muted small">Generate unique landing pages for Service × City combinations. Each page gets unique content, metadata, FAQs, schema, keywords, and internal links.</p>
            <p class="small"><strong>Potential pages:</strong> <?php echo number_format($potential); ?> (<?php echo count($services); ?> services × <?php echo count($cities); ?> cities)</p>
        </div>

        <form method="POST" class="ge-card">
            <div class="mb-4">
                <label class="form-label fw-bold">Generation Mode</label>
                <?php foreach ([
                    'selected' => 'Selected Services + Cities',
                    'service_all_cities' => 'One Service → All Cities',
                    'city_all_services' => 'One City → All Services',
                    'full_matrix' => 'Full Matrix (All Services × All Cities)',
                ] as $val => $label): ?>
                <div class="form-check"><input class="form-check-input" type="radio" name="mode" value="<?php echo $val; ?>" id="mode_<?php echo $val; ?>" <?php echo $val==='selected'?'checked':''; ?> onchange="toggleMode()"><label class="form-check-label" for="mode_<?php echo $val; ?>"><?php echo $label; ?></label></div>
                <?php endforeach; ?>
            </div>

            <div id="blockSelected" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Services</label><select name="service_ids[]" class="form-select" multiple size="8"><?php foreach ($services as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label class="form-label">Cities</label><select name="city_ids[]" class="form-select" multiple size="8"><?php foreach ($cities as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
                </div>
            </div>
            <div id="blockService" class="mb-4 d-none"><label class="form-label">Service</label><select name="service_id" class="form-select"><?php foreach ($services as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
            <div id="blockCity" class="mb-4 d-none"><label class="form-label">City</label><select name="city_id" class="form-select"><?php foreach ($cities as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>

            <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="regenerate" id="regenerate"><label class="form-check-label" for="regenerate">Regenerate existing pages (overwrite content)</label></div>

            <button type="submit" class="btn btn-ge-primary btn-lg" onclick="return confirm('Start generation? Large batches may take several minutes.')"><i class="fas fa-magic me-2"></i>Generate Landing Pages</button>
        </form>
    </div>
    <div class="col-lg-4">
        <div class="ge-card">
            <h2 class="h6 mb-3">URL Pattern</h2>
            <code class="d-block p-2 bg-dark rounded small">{url_prefix}-company-{city_slug}</code>
            <p class="text-muted small mt-2 mb-0">Example: <code>seo-company-lucknow</code>, <code>software-development-company-delhi</code></p>
            <hr>
            <h2 class="h6 mb-2">Auto-Generated Per Page</h2>
            <ul class="small text-muted ps-3"><li>Unique meta title & description</li><li>H1, H2, content body</li><li>Quick Answer & Key Takeaways</li><li>FAQ + People Also Ask</li><li>Voice search answer</li><li>Schema (Service, LocalBusiness, FAQ)</li><li>20+ city-specific keywords</li><li>Internal links</li><li>Sitemap & index queue</li></ul>
        </div>
    </div>
</div>

<script>
function toggleMode() {
    const m = document.querySelector('input[name=mode]:checked').value;
    document.getElementById('blockSelected').classList.toggle('d-none', m !== 'selected');
    document.getElementById('blockService').classList.toggle('d-none', m !== 'service_all_cities');
    document.getElementById('blockCity').classList.toggle('d-none', m !== 'city_all_services');
}
</script>

<?php ge_admin_layout_end(); ?>
