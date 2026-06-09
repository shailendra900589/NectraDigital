# Nectra Digital Growth Platform v2

Enterprise programmatic SEO: **Service × City × Industry** with full admin control.

## Setup

1. Run migration: visit `https://www.nectradigital.com/database/migrate.php` (runs v1 + v2)
2. Login: `/admin/login.php`
3. Open Growth Platform: `/admin/growth/`
4. Add **Services**, **Cities**, **Industries** (all admin-managed — nothing hardcoded)
5. Generate pages from **Generate** (supports triple matrix + background queue)
6. Optional cron: `php cron/process-queue.php` every 5 minutes for large batches

## URL Patterns

- City: `{url_prefix}-company-in-{city_slug}` → `seo-company-in-lucknow`
- Industry: `{url_prefix}-company-in-{city_slug}-for-{industry_slug}`

Configure in **Settings** (`url_pattern_city`, `url_pattern_industry`).

## Admin Modules

| Module | Path |
|--------|------|
| Services, Cities, Industries, Keywords | `/admin/growth/` |
| Landing Pages, Generate, Indexing | `/admin/growth/` |
| Authors (EEAT), Knowledge Base, Case Studies | `/admin/growth/` |
| Leads (CRM), Competitor Intel, Tools, Analytics | `/admin/growth/` |

## Public

- Landing pages: routed via `page-router.php`
- Tools marketplace: `/tools` and `/tools/{slug}`
- AI Chatbot: enabled via `chatbot_enabled` setting

## Scale

- Unique key: `(service_id, city_id, industry_id)`
- Async queue: `ge_generation_queue` + `cron/process-queue.php`
- Sitemap auto-updates on new entities
