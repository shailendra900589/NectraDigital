# Hostinger — one-time deploy (copy entire block)

```bash
cd ~/domains/nectradigital.com/public_html
cp includes/db.local.php ~/db.local.php.bak
cp includes/config.local.php ~/config.local.php.bak 2>/dev/null || true
git checkout -- includes/db.php includes/header.php includes/seo-components.php includes/config.php
git pull origin main
cp ~/db.local.php.bak includes/db.local.php
cp ~/config.local.php.bak includes/config.local.php 2>/dev/null || true
php database/test-db.php
php -l admin/growth/services.php
php -l admin/growth/init.php
ls -la admin/growth/init.php cron/process-indexing.php
```

If `git pull` still fails:

```bash
git fetch origin main
git reset --hard origin/main
cp ~/db.local.php.bak includes/db.local.php
php database/test-db.php
```

## db.local.php must look like:

```php
<?php
$host = 'localhost';
$user = 'u991240931_9Rahul1432';
$pass = 'YOUR_PASSWORD';
$dbname = 'u991240931_NectraDigital';
```

## After deploy test:

- https://www.nectradigital.com/admin/growth/diag.php
- https://www.nectradigital.com/admin/growth/services.php
- https://www.nectradigital.com/admin/dashboard.php?page=home
