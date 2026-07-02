<?php
if (!defined('GE_BOOTSTRAP_LOADED')) {
    require_once __DIR__ . '/../../includes/growth/bootstrap.php';
}

require_once __DIR__ . '/../../includes/admin-layout.php';

function ge_admin_page_title(string $title): string
{
    return admin_page_title($title);
}

function ge_admin_nav(): array
{
    return admin_nav_items();
}

function ge_admin_layout_start(string $title, string $activePage = ''): void
{
    admin_layout_start($title, $activePage, ['growth_warning' => true]);
}

function ge_pagination_range(int $current, int $totalPages, int $adjacent = 2): array
{
    return admin_pagination_range($current, $totalPages, $adjacent);
}

function ge_admin_pagination(array $pg, array $queryParams = []): void
{
    admin_pagination($pg, $queryParams);
}

function ge_admin_layout_end(): void
{
    admin_layout_end();
}
