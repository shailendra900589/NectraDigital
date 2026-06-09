# Nectra Digital Growth Engine

Programmatic SEO platform for unlimited Service × City landing pages.

## Setup

1. Run migration: visit `https://www.nectradigital.com/database/migrate.php`
2. Login to admin: `/admin/login.php`
3. Open Growth Engine: `/admin/growth/`
4. Add services and cities
5. Generate landing pages from **Generate** module

## Architecture

```
includes/growth/
├── bootstrap.php          # Autoload & helpers
├── helpers.php            # Utilities
├── LandingPageGenerator.php
├── models/                # Service, City, Keyword, LandingPage, etc.
└── engines/
    ├── ContentEngine.php  # Unique content per page
    ├── KeywordEngine.php  # City + service keywords
    ├── GeoEngine.php      # Quick answer, takeaways, summary
    ├── AeoEngine.php      # FAQ, PAA, voice answers
    ├── SeoEngine.php      # Meta, OG, Twitter
    ├── SchemaEngine.php   # JSON-LD
    ├── InternalLinkEngine.php
    └── SitemapEngine.php  # Dynamic sitemap

admin/growth/              # SaaS admin panel
landing.php                # Frontend template
page-router.php            # Routes slugs → landing or blog
```

## URL Pattern

Default: `{url_prefix}-company-{city_slug}`

Examples:
- `seo-company-lucknow`
- `software-development-company-delhi`

Configure in **Settings** or per-service `url_prefix`.

## Scale

- Indexed slug column for O(1) lookups
- Batch generation with configurable batch size
- Sitemap streams 5000 URLs per batch
- Supports 100,000+ landing pages

## Founder / EEAT

Configure founder details in **Settings**. Used in Expert Insight blocks and schema.
