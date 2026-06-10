<?php
/**
 * blog_posts schema helpers — auto-add columns on admin save.
 */

if (!function_exists('blog_schema_add_column')) {
    function blog_schema_add_column($conn, string $column, string $definition): void
    {
        $col = @$conn->query("SHOW COLUMNS FROM blog_posts LIKE '" . $conn->real_escape_string($column) . "'");
        if ($col && $col->num_rows === 0) {
            @$conn->query("ALTER TABLE blog_posts ADD COLUMN {$definition}");
        }
    }
}

if (!function_exists('blog_schema_ensure')) {
    function blog_schema_ensure($conn): void
    {
        static $done = false;
        if ($done) {
            return;
        }

        blog_schema_add_column($conn, 'meta_description', 'meta_description TEXT NULL');
        blog_schema_add_column($conn, 'faq_json', 'faq_json TEXT NULL');
        blog_schema_add_column($conn, 'is_orphan', 'is_orphan TINYINT(1) NOT NULL DEFAULT 0');

        $done = true;
    }
}

if (!function_exists('blog_schema_column_exists')) {
    function blog_schema_column_exists($conn, string $column): bool
    {
        static $cache = [];
        if (isset($cache[$column])) {
            return $cache[$column];
        }
        $col = @$conn->query("SHOW COLUMNS FROM blog_posts LIKE '" . $conn->real_escape_string($column) . "'");
        $cache[$column] = $col && $col->num_rows > 0;
        return $cache[$column];
    }
}

if (!function_exists('blog_insert_post')) {
    function blog_insert_post($conn, array $data): array
    {
        blog_schema_ensure($conn);

        $fields = ['title', 'category', 'image', 'content', 'slug', 'created_at'];
        $types = 'ssssss';
        $values = [
            $data['title'],
            $data['category'],
            $data['image'],
            $data['content'],
            $data['slug'],
            $data['created_at'],
        ];

        if (blog_schema_column_exists($conn, 'meta_description')) {
            $fields[] = 'meta_description';
            $types .= 's';
            $values[] = $data['meta_description'] ?? '';
        }
        if (blog_schema_column_exists($conn, 'faq_json')) {
            $fields[] = 'faq_json';
            $types .= 's';
            $values[] = $data['faq_json'] ?? '[]';
        }
        if (blog_schema_column_exists($conn, 'is_orphan')) {
            $fields[] = 'is_orphan';
            $types .= 'i';
            $values[] = (int)($data['is_orphan'] ?? 0);
        }

        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $sql = 'INSERT INTO blog_posts (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'error' => $conn->error ?: 'Prepare failed'];
        }

        $stmt->bind_param($types, ...$values);
        if (!$stmt->execute()) {
            return ['ok' => false, 'error' => $stmt->error ?: 'Insert failed'];
        }

        return ['ok' => true, 'id' => (int)$conn->insert_id];
    }
}

if (!function_exists('blog_update_post')) {
    function blog_update_post($conn, int $id, array $data): array
    {
        blog_schema_ensure($conn);

        $sets = ['title=?', 'category=?', 'image=?', 'content=?', 'slug=?'];
        $types = 'sssss';
        $values = [
            $data['title'],
            $data['category'],
            $data['image'],
            $data['content'],
            $data['slug'],
        ];

        if (blog_schema_column_exists($conn, 'meta_description')) {
            $sets[] = 'meta_description=?';
            $types .= 's';
            $values[] = $data['meta_description'] ?? '';
        }
        if (blog_schema_column_exists($conn, 'faq_json')) {
            $sets[] = 'faq_json=?';
            $types .= 's';
            $values[] = $data['faq_json'] ?? '[]';
        }
        if (blog_schema_column_exists($conn, 'is_orphan')) {
            $sets[] = 'is_orphan=?';
            $types .= 'i';
            $values[] = (int)($data['is_orphan'] ?? 0);
        }

        $types .= 'i';
        $values[] = $id;

        $sql = 'UPDATE blog_posts SET ' . implode(', ', $sets) . ' WHERE id=?';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'error' => $conn->error ?: 'Prepare failed'];
        }

        $stmt->bind_param($types, ...$values);
        if (!$stmt->execute()) {
            return ['ok' => false, 'error' => $stmt->error ?: 'Update failed'];
        }

        return ['ok' => true];
    }
}
