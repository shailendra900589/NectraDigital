<?php

function ge_slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function ge_replace_tokens(string $template, array $vars): string {
    foreach ($vars as $key => $value) {
        $template = str_replace('{' . $key . '}', (string)$value, $template);
    }
    return $template;
}

function ge_json_decode($json, $default = []) {
    if (empty($json)) return $default;
    if (is_array($json)) return $json;
    $decoded = json_decode($json, true);
    return is_array($decoded) ? $decoded : $default;
}

function ge_json_encode($data): string {
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function ge_hash_content(string $content): string {
    return hash('sha256', $content);
}

function ge_format_population(int $pop): string {
    if ($pop >= 10000000) return round($pop / 10000000, 1) . ' Crore';
    if ($pop >= 100000) return round($pop / 100000, 1) . ' Lakh';
    if ($pop >= 1000) return number_format($pop);
    return (string)$pop;
}

function ge_pick_variant(array $variants, int $seed): string {
    if (empty($variants)) return '';
    return $variants[$seed % count($variants)];
}

function ge_admin_flash(string $type, string $message): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['ge_flash'] = ['type' => $type, 'message' => $message];
}

function ge_admin_get_flash(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!empty($_SESSION['ge_flash'])) {
        $flash = $_SESSION['ge_flash'];
        unset($_SESSION['ge_flash']);
        return $flash;
    }
    return null;
}

function ge_upload_image(array $file, string $prefix = 'ge_'): array {
    $target_dir = dirname(__DIR__, 2) . '/assets/uploads/';
    if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
    if ($file['error'] !== UPLOAD_ERR_OK) return ['error' => 'Upload error.'];
    if ($file['size'] > 2097152) return ['error' => 'Max 2MB.'];
    $allowed = ['image/webp', 'image/png', 'image/jpeg', 'image/svg+xml'];
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowed)) return ['error' => 'Invalid image type.'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid($prefix, true) . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $target_dir . $name)) {
        return ['success' => 'assets/uploads/' . $name];
    }
    return ['error' => 'Move failed.'];
}

function ge_build_landing_slug(string $url_prefix, string $city_slug, ?string $pattern = null): string {
    $pattern = $pattern ?: ge_setting('url_pattern', '{url_prefix}-company-{city_slug}');
    return ge_slugify(ge_replace_tokens($pattern, [
        'url_prefix' => $url_prefix,
        'city_slug' => $city_slug,
        'service_slug' => $url_prefix,
    ]));
}

function ge_paginate(int $total, int $page, int $per_page = 25): array {
    $pages = max(1, (int)ceil($total / $per_page));
    $page = max(1, min($page, $pages));
    $offset = ($page - 1) * $per_page;
    return ['page' => $page, 'per_page' => $per_page, 'total' => $total, 'pages' => $pages, 'offset' => $offset];
}
