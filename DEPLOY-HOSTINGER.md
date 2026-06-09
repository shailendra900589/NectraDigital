# Hostinger deploy — copy this entire block

```bash
cd ~/domains/nectradigital.com/public_html

cp includes/db.local.php ~/db.local.php.bak
cp includes/config.local.php ~/config.local.php.bak 2>/dev/null || true

git fetch origin main
git checkout origin/main -- includes/db.php includes/header.php includes/seo-components.php includes/config.php
git pull origin main || git reset --hard origin/main

cp ~/db.local.php.bak includes/db.local.php
cp ~/config.local.php.bak includes/config.local.php 2>/dev/null || true

php -l includes/db.local.php
php database/test-db.php
php -l admin/growth/init.php
php -l admin/growth/generate.php
ls -la admin/growth/init.php cron/process-indexing.php
```

## Test after deploy

1. https://www.nectradigital.com/admin/growth/diag.php
2. https://www.nectradigital.com/admin/growth/generate.php
3. https://www.nectradigital.com/admin/growth/settings.php
4. https://www.nectradigital.com/admin/dashboard.php?page=home

## Root cause fixed (latest commit)

Growth admin used `require_once 'includes/layout.php'` which fails on Hostinger when the PHP working directory is not `admin/growth/`. Fixed with `ge_admin_layout()` using `__DIR__`.
