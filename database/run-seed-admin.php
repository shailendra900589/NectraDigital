<?php
$GLOBALS['nectra_db_soft_fail'] = true;
require __DIR__ . '/../includes/db.php';
if (!nectra_db_is_connected()) {
    fwrite(STDERR, "DB not connected\n");
    exit(1);
}
$sql = file_get_contents(__DIR__ . '/seed-admin.sql');
foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
    if (strlen($stmt) < 5) {
        continue;
    }
    if (!$conn->query($stmt)) {
        fwrite(STDERR, $conn->error . "\n");
        exit(1);
    }
}
if (PHP_SAPI === 'cli') {
    echo "Admin seeded (user: admin, pass: admin123)\n";
}
