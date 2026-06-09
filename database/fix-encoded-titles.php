<?php
/**
 * One-time fix: normalize blog_posts titles/categories stored with &amp; entities.
 * CLI: php database/fix-encoded-titles.php
 */
require_once __DIR__ . '/../includes/db.php';

$fields = ['title', 'category', 'meta_description'];
$res = $conn->query('SELECT id, title, category, meta_description FROM blog_posts');
$updated = 0;

while ($row = $res->fetch_assoc()) {
    $sets = [];
    $params = [];
    $types = '';

    foreach ($fields as $field) {
        $raw = $row[$field] ?? '';
        if ($raw === '' || $raw === null) {
            continue;
        }
        $fixed = sanitize_db_text($raw);
        if ($fixed !== $raw) {
            $sets[] = "$field = ?";
            $params[] = $fixed;
            $types .= 's';
        }
    }

    if (empty($sets)) {
        continue;
    }

    $params[] = (int) $row['id'];
    $types .= 'i';
    $sql = 'UPDATE blog_posts SET ' . implode(', ', $sets) . ' WHERE id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        $updated++;
        echo "Fixed post #{$row['id']}: " . sanitize_db_text($row['title']) . PHP_EOL;
    }
}

echo "Done. Updated {$updated} post(s)." . PHP_EOL;
