<?php
/**
 * Run database migration for Growth Engine
 * Access: /database/migrate.php (protect in production)
 */
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

$sqlFile = __DIR__ . '/schema.sql';
if (!file_exists($sqlFile)) {
    die("Schema file not found.\n");
}

$sql = file_get_contents($sqlFile);
$sql = preg_replace('/^--.*$/m', '', $sql);
$sql = preg_replace('/^SET .*$/m', '', $sql);

$statements = array_filter(array_map('trim', explode(';', $sql)));

$ok = 0;
$fail = 0;

foreach ($statements as $statement) {
    if (strlen($statement) < 5) continue;
    if ($conn->query($statement)) {
        $ok++;
        if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $m)) {
            echo "OK: Table {$m[1]}\n";
        } elseif (preg_match('/INSERT/i', $statement)) {
            echo "OK: Settings seeded\n";
        }
    } else {
        $fail++;
        echo "FAIL: " . $conn->error . "\n";
        echo substr($statement, 0, 80) . "...\n";
    }
}

echo "\nMigration complete. Success: $ok, Failed: $fail\n";
echo "Next: Login to /admin/growth/ to manage services and cities.\n";
