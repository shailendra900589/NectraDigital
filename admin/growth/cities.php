<?php
require_once __DIR__ . '/init.php';

use Growth\Models\City;

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => ge_slugify($_POST['slug'] ?? $_POST['name'] ?? ''),
        'state' => trim($_POST['state'] ?? ''),
        'country' => trim($_POST['country'] ?? 'India'),
        'population' => (int)($_POST['population'] ?? 0),
        'latitude' => $_POST['latitude'] ?? '',
        'longitude' => $_POST['longitude'] ?? '',
        'city_description' => trim($_POST['city_description'] ?? ''),
        'status' => $_POST['status'] ?? 'active',
    ];

    if ($action === 'edit' && $id) {
        City::update($id, $data);
        ge_admin_flash('success', 'City updated.');
    } else {
        City::create($data);
        ge_admin_flash('success', 'City created.');
    }
    header('Location: cities.php');
    exit;
}

if ($action === 'delete' && $id) {
    City::delete($id);
    ge_admin_flash('success', 'City deleted.');
    header('Location: cities.php');
    exit;
}

if ($action === 'import' && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['bulk_cities'])) {
    $lines = explode("\n", $_POST['bulk_cities']);
    $imported = 0;
    foreach ($lines as $line) {
        $line = trim($line);
        if (!$line) continue;
        $parts = array_map('trim', explode(',', $line));
        $name = $parts[0] ?? '';
        if (!$name) continue;
        City::create([
            'name' => $name,
            'slug' => ge_slugify($name),
            'state' => $parts[1] ?? '',
            'country' => $parts[2] ?? 'India',
            'population' => (int)($parts[3] ?? 0),
            'city_description' => $parts[4] ?? '',
            'status' => 'active',
        ]);
        $imported++;
    }
    ge_admin_flash('success', "Imported {$imported} cities.");
    header('Location: cities.php');
    exit;
}

$item = $id ? City::find($id) : null;
$cities = ge_is_ready() ? City::all() : [];

ge_admin_layout();
ge_admin_layout_start('City Manager', 'cities');

if ($action === 'add' || ($action === 'edit' && $item)):
?>
<div class="ge-card">
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">City Name *</label><input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($item['name'] ?? ''); ?>"></div>
            <div class="col-md-4"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($item['slug'] ?? ''); ?>"></div>
            <div class="col-md-4"><label class="form-label">State</label><input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($item['state'] ?? ''); ?>"></div>
            <div class="col-md-3"><label class="form-label">Country</label><input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($item['country'] ?? 'India'); ?>"></div>
            <div class="col-md-3"><label class="form-label">Population</label><input type="number" name="population" class="form-control" value="<?php echo (int)($item['population'] ?? 0); ?>"></div>
            <div class="col-md-3"><label class="form-label">Latitude</label><input type="text" name="latitude" class="form-control" value="<?php echo htmlspecialchars($item['latitude'] ?? ''); ?>"></div>
            <div class="col-md-3"><label class="form-label">Longitude</label><input type="text" name="longitude" class="form-control" value="<?php echo htmlspecialchars($item['longitude'] ?? ''); ?>"></div>
            <div class="col-12"><label class="form-label">City Description</label><textarea name="city_description" class="form-control" rows="3"><?php echo htmlspecialchars($item['city_description'] ?? ''); ?></textarea></div>
            <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="col-12"><button type="submit" class="btn btn-ge-primary">Save City</button><a href="cities.php" class="btn btn-outline-secondary ms-2">Cancel</a></div>
        </div>
    </form>
</div>
<?php elseif ($action === 'import'): ?>
<div class="ge-card">
    <form method="POST">
        <label class="form-label">Bulk Import (one per line: Name, State, Country, Population, Description)</label>
        <textarea name="bulk_cities" class="form-control mb-3" rows="10" placeholder="Lucknow, Uttar Pradesh, India, 3500000&#10;Delhi, Delhi NCR, India, 32000000"></textarea>
        <button type="submit" class="btn btn-ge-primary">Import Cities</button>
        <a href="cities.php" class="btn btn-outline-secondary ms-2">Cancel</a>
    </form>
</div>
<?php else: ?>
<div class="d-flex justify-content-between mb-4">
    <span class="text-muted"><?php echo count($cities); ?> cities</span>
    <div><a href="?action=import" class="btn btn-outline-secondary me-2"><i class="fas fa-file-import"></i> Bulk Import</a><a href="?action=add" class="btn btn-ge-primary"><i class="fas fa-plus"></i> Add City</a></div>
</div>
<div class="ge-card"><div class="table-responsive"><table class="table ge-table table-sm"><thead><tr><th>City</th><th>State</th><th>Population</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($cities as $c): ?>
<tr><td><strong><?php echo htmlspecialchars($c['name']); ?></strong><br><code><?php echo htmlspecialchars($c['slug']); ?></code></td><td><?php echo htmlspecialchars($c['state']); ?></td><td><?php echo number_format($c['population']); ?></td><td><?php echo $c['status']; ?></td><td><a href="?action=edit&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a> <a href="?action=delete&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a></td></tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?php endif;
ge_admin_layout_end();
