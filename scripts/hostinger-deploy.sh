#!/bin/bash
# Safe Hostinger deploy — preserves db.local.php & config.local.php
# Run from public_html:
#   bash scripts/hostinger-deploy.sh

set -e
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "=== Nectra Digital — Safe Deploy ==="
echo "Directory: $ROOT"

# 1. Backup server-only secrets (never in git)
for f in includes/db.local.php includes/config.local.php admin/reset_password.php; do
  if [ -f "$f" ]; then
    cp "$f" "/tmp/nectra-backup-$(basename $f)"
    echo "Backed up: $f"
  fi
done

# 2. Discard local edits on tracked files (fixes are already in GitHub main)
git fetch origin main
git checkout origin/main -- includes/db.php includes/header.php includes/seo-components.php includes/config.php 2>/dev/null || true

# 3. Pull latest
git pull origin main

# 4. Restore secrets
for f in includes/db.local.php includes/config.local.php admin/reset_password.php; do
  backup="/tmp/nectra-backup-$(basename $f)"
  if [ -f "$backup" ]; then
    cp "$backup" "$f"
    echo "Restored: $f"
  fi
done

# 5. Ensure db.local.php exists
if [ ! -f includes/db.local.php ]; then
  echo ""
  echo "WARNING: includes/db.local.php missing!"
  echo "Copy from example and add Hostinger MySQL credentials:"
  echo "  cp includes/db.local.php.example includes/db.local.php"
  echo "  nano includes/db.local.php"
fi

# 6. Verify key files
echo ""
echo "=== Verify ==="
for f in admin/dashboard.php admin/includes/admin-growth.php cron/process-indexing.php includes/growth/engines/IndexingEngine.php; do
  if [ -f "$f" ]; then echo "OK  $f"; else echo "MISSING  $f"; fi
done

echo ""
echo "=== Done ==="
echo "Admin: https://www.nectradigital.com/admin/dashboard.php?page=home"
echo "Test:  php database/test-db.php"
echo "Cron:  php cron/process-indexing.php"
