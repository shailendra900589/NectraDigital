<?php
/**
 * blog_posts schema helpers — auto-add columns on admin save.
 */

if (!function_exists('blog_schema_table_exists')) {
    function blog_schema_table_exists($conn): bool
    {
        static $exists = null;
        if ($exists !== null) {
            return $exists;
        }
        if (!$conn instanceof mysqli) {
            $exists = false;
            return false;
        }
        $r = @$conn->query("SHOW TABLES LIKE 'blog_posts'");
        $exists = $r && $r->num_rows > 0;
        return $exists;
    }
}

if (!function_exists('blog_schema_reset_column_cache')) {
    function blog_schema_reset_column_cache(): void
    {
        blog_schema_column_exists(null, '', true);
    }
}

if (!function_exists('blog_schema_add_column')) {
    function blog_schema_add_column($conn, string $column, string $definition): void
    {
        if (!$conn instanceof mysqli || !blog_schema_table_exists($conn)) {
            return;
        }
        try {
            $col = @$conn->query("SHOW COLUMNS FROM blog_posts LIKE '" . $conn->real_escape_string($column) . "'");
            if ($col && $col->num_rows === 0) {
                @$conn->query("ALTER TABLE blog_posts ADD COLUMN {$definition}");
                blog_schema_reset_column_cache();
            }
        } catch (Throwable $e) {
            error_log('blog_schema_add_column(' . $column . '): ' . $e->getMessage());
        }
    }
}

if (!function_exists('blog_schema_ensure')) {
    function blog_schema_ensure($conn): void
    {
        static $done = false;
        if ($done || !$conn instanceof mysqli) {
            return;
        }

        if (!blog_schema_table_exists($conn)) {
            return;
        }

        try {
            blog_schema_add_column($conn, 'meta_description', 'meta_description TEXT NULL');
            blog_schema_add_column($conn, 'faq_json', 'faq_json TEXT NULL');
            blog_schema_add_column($conn, 'is_orphan', 'is_orphan TINYINT(1) NOT NULL DEFAULT 0');
        } catch (Throwable $e) {
            error_log('blog_schema_ensure: ' . $e->getMessage());
        }

        $done = true;
    }
}

if (!function_exists('blog_schema_column_exists')) {
    function blog_schema_column_exists($conn, string $column, bool $reset = false): bool
    {
        static $cache = [];
        if ($reset) {
            $cache = [];
            return false;
        }
        if (isset($cache[$column])) {
            return $cache[$column];
        }
        if (!$conn instanceof mysqli || !blog_schema_table_exists($conn)) {
            $cache[$column] = false;
            return false;
        }
        try {
            $col = @$conn->query("SHOW COLUMNS FROM blog_posts LIKE '" . $conn->real_escape_string($column) . "'");
            $cache[$column] = $col && $col->num_rows > 0;
        } catch (Throwable $e) {
            error_log('blog_schema_column_exists(' . $column . '): ' . $e->getMessage());
            $cache[$column] = false;
        }
        return $cache[$column];
    }
}

if (!function_exists('blog_stmt_bind')) {
    function blog_stmt_bind(mysqli_stmt $stmt, string $types, array $values): bool
    {
        if ($types === '' || strlen($types) !== count($values)) {
            return false;
        }
        $bind = [$types];
        foreach ($values as $i => $_) {
            $bind[] = &$values[$i];
        }
        return call_user_func_array([$stmt, 'bind_param'], $bind);
    }
}

if (!function_exists('blog_admin_mime_type')) {
    function blog_admin_mime_type(string $path): ?string
    {
        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($path);
            if (is_string($mime) && $mime !== '') {
                return $mime;
            }
        }
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = @$finfo->file($path);
            if (is_string($mime) && $mime !== '') {
                return $mime;
            }
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'webp') {
            return 'image/webp';
        }
        if ($ext === 'svg') {
            return 'image/svg+xml';
        }
        return null;
    }
}

if (!function_exists('blog_normalize_datetime')) {
    function blog_normalize_datetime(?string $value): string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return date('Y-m-d H:i:s');
        }
        $value = str_replace('T', ' ', $value);
        $ts = strtotime($value);
        if ($ts === false) {
            return date('Y-m-d H:i:s');
        }
        return date('Y-m-d H:i:s', $ts);
    }
}

if (!function_exists('blog_insert_post')) {
    function blog_insert_post($conn, array $data): array
    {
        if (!$conn instanceof mysqli) {
            return ['ok' => false, 'error' => 'Database connection unavailable'];
        }
        if (!blog_schema_table_exists($conn)) {
            return ['ok' => false, 'error' => 'blog_posts table not found'];
        }

        blog_schema_ensure($conn);
        blog_schema_reset_column_cache();

        $dateCol = 'created_at';
        if (!blog_schema_column_exists($conn, 'created_at') && blog_schema_column_exists($conn, 'date')) {
            $dateCol = 'date';
        }

        $fields = ['title', 'category', 'image', 'content', 'slug', $dateCol];
        $types = 'ssssss';
        $values = [
            (string)($data['title'] ?? ''),
            (string)($data['category'] ?? ''),
            (string)($data['image'] ?? ''),
            (string)($data['content'] ?? ''),
            (string)($data['slug'] ?? ''),
            blog_normalize_datetime($data['created_at'] ?? null),
        ];

        if (blog_schema_column_exists($conn, 'meta_description')) {
            $fields[] = 'meta_description';
            $types .= 's';
            $values[] = (string)($data['meta_description'] ?? '');
        }
        if (blog_schema_column_exists($conn, 'faq_json')) {
            $fields[] = 'faq_json';
            $types .= 's';
            $values[] = (string)($data['faq_json'] ?? '[]');
        }
        if (blog_schema_column_exists($conn, 'is_orphan')) {
            $fields[] = 'is_orphan';
            $types .= 'i';
            $values[] = (int)($data['is_orphan'] ?? 0);
        }

        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $sql = 'INSERT INTO blog_posts (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';

        try {
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                return ['ok' => false, 'error' => $conn->error ?: 'Prepare failed'];
            }
            if (!blog_stmt_bind($stmt, $types, $values)) {
                return ['ok' => false, 'error' => 'Bind failed — check column types'];
            }
            if (!$stmt->execute()) {
                return ['ok' => false, 'error' => $stmt->error ?: 'Insert failed'];
            }
            return ['ok' => true, 'id' => (int)$conn->insert_id];
        } catch (Throwable $e) {
            error_log('blog_insert_post: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('blog_update_post')) {
    function blog_update_post($conn, int $id, array $data): array
    {
        if (!$conn instanceof mysqli) {
            return ['ok' => false, 'error' => 'Database connection unavailable'];
        }
        if (!blog_schema_table_exists($conn)) {
            return ['ok' => false, 'error' => 'blog_posts table not found'];
        }

        blog_schema_ensure($conn);
        blog_schema_reset_column_cache();

        $sets = ['title=?', 'category=?', 'image=?', 'content=?', 'slug=?'];
        $types = 'sssss';
        $values = [
            (string)($data['title'] ?? ''),
            (string)($data['category'] ?? ''),
            (string)($data['image'] ?? ''),
            (string)($data['content'] ?? ''),
            (string)($data['slug'] ?? ''),
        ];

        if (blog_schema_column_exists($conn, 'meta_description')) {
            $sets[] = 'meta_description=?';
            $types .= 's';
            $values[] = (string)($data['meta_description'] ?? '');
        }
        if (blog_schema_column_exists($conn, 'faq_json')) {
            $sets[] = 'faq_json=?';
            $types .= 's';
            $values[] = (string)($data['faq_json'] ?? '[]');
        }
        if (blog_schema_column_exists($conn, 'is_orphan')) {
            $sets[] = 'is_orphan=?';
            $types .= 'i';
            $values[] = (int)($data['is_orphan'] ?? 0);
        }

        $types .= 'i';
        $values[] = $id;

        $sql = 'UPDATE blog_posts SET ' . implode(', ', $sets) . ' WHERE id=?';

        try {
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                return ['ok' => false, 'error' => $conn->error ?: 'Prepare failed'];
            }
            if (!blog_stmt_bind($stmt, $types, $values)) {
                return ['ok' => false, 'error' => 'Bind failed — check column types'];
            }
            if (!$stmt->execute()) {
                return ['ok' => false, 'error' => $stmt->error ?: 'Update failed'];
            }
            return ['ok' => true];
        } catch (Throwable $e) {
            error_log('blog_update_post: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
