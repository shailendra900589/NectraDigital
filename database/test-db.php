<?php
/**
 * Quick DB connection test — CLI: php database/test-db.php
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "Step 1: PHP OK\n";

$dbFile = __DIR__ . '/../includes/db.php';
if (!is_file($dbFile)) {
    die("ERROR: db.php not found at $dbFile\n");
}
echo "Step 2: Loading db.php...\n";

require_once $dbFile;

echo "Step 3: DB connected successfully\n";
echo "Server: " . $conn->host_info . "\n";

$res = $conn->query("SHOW TABLES LIKE 'ge_%'");
$count = $res ? $res->num_rows : 0;
echo "Step 4: ge_* tables found: $count\n";

if ($count > 0) {
    echo "Database already migrated. Admin: https://www.nectradigital.com/admin/growth/\n";
} else {
    echo "No ge_* tables — run: php database/migrate.php\n";
}
