# Hostinger deploy — NectraDigital-V2

**Git remote:** `https://github.com/shailendra900589/NectraDigital-V2.git` (branch `main`)

**Auto-deploy:** hPanel → Websites → nectradigital.com → Git → connect GitHub repo `shailendra900589/NectraDigital-V2`, branch `main`, type PHP/static.

**DB (never commit):** `includes/db.local.php` on server only — copy from `includes/db.local.php.example` (hPanel → MySQL).

After any manual git pull on server:

```bash
cd ~/domains/nectradigital.com/public_html

cp includes/db.local.php ~/db.local.php.bak
cp includes/config.local.php ~/config.local.php.bak 2>/dev/null || true

git pull origin main || git reset --hard origin/main

cp ~/db.local.php.bak includes/db.local.php
cp ~/config.local.php.bak includes/config.local.php 2>/dev/null || true

php -l includes/db.local.php
php database/test-db.php
php -l admin/growth/init.php
```

## Test after deploy

1. https://www.nectradigital.com/admin/growth/diag.php
2. https://www.nectradigital.com/admin/growth/generate.php
3. https://www.nectradigital.com/admin/growth/settings.php
4. https://www.nectradigital.com/admin/dashboard.php?page=home

## Root cause fixed (latest commit)

Growth admin used `require_once 'includes/layout.php'` which fails on Hostinger when the PHP working directory is not `admin/growth/`. Fixed with `ge_admin_layout()` using `__DIR__`.
