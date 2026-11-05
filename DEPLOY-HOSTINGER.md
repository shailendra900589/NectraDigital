# Hostinger deploy — NectraDigital-V2

**Git remote:** `https://github.com/shailendra900589/NectraDigital-V2.git` (branch `main`)

**Auto-deploy (recommended):**

1. hPanel → **Websites** → **nectradigital.com** → **Manage** → **Advanced** → **Git**
2. Connect **GitHub** if not connected; grant access to **`NectraDigital-V2`**
3. Repository: `shailendra900589/NectraDigital-V2`, branch **`main`**, deploy to **public_html** (root)
4. Add Hostinger **SSH public key** (same Git screen) as a **Deploy key** on GitHub repo → Settings → Deploy keys

If Git pull fails because an old manual `.git` exists, run in **SSH / Terminal**:

```bash
bash scripts/production-git-pull.sh
```

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
