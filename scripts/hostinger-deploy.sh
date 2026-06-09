#!/bin/bash
# Safe Hostinger deploy — run from public_html:
#   bash scripts/hostinger-deploy.sh
set -e
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "=== Nectra Digital — Safe Deploy ==="

for f in includes/db.local.php includes/config.local.php; do
  [ -f "$f" ] && cp "$f" "/tmp/nectra-$(basename $f).bak" && echo "Backed up $f"
done

git fetch origin main

# Discard server-side edits on tracked files (fixes pull conflicts)
git checkout origin/main -- includes/db.php includes/header.php includes/seo-components.php includes/config.php 2>/dev/null || true

git pull origin main || {
  echo "Pull failed — hard reset to origin/main (secrets restored after)"
  git reset --hard origin/main
}

for f in includes/db.local.php includes/config.local.php; do
  bak="/tmp/nectra-$(basename $f).bak"
  [ -f "$bak" ] && cp "$bak" "$f" && echo "Restored $f"
done

echo ""
echo "=== Verify ==="
php -l includes/db.local.php 2>/dev/null || echo "WARN: fix includes/db.local.php syntax"
php database/test-db.php 2>/dev/null || echo "WARN: DB test failed"
for f in admin/growth/init.php admin/growth/generate.php admin/growth/settings.php cron/process-indexing.php; do
  [ -f "$f" ] && php -l "$f" && echo "OK $f" || echo "MISSING $f"
done

echo ""
echo "Done. Test: /admin/growth/diag.php and /admin/dashboard.php?page=home"
