#!/bin/bash
# Run on Hostinger (SSH or hPanel Terminal) from public_html after Git is linked to NectraDigital-V2.
set -euo pipefail
cd ~/domains/nectradigital.com/public_html

if [[ -f includes/db.local.php ]]; then
  cp includes/db.local.php ~/db.local.php.bak
fi
if [[ -f includes/config.local.php ]]; then
  cp includes/config.local.php ~/config.local.php.bak
fi

git remote set-url origin git@github.com:shailendra900589/NectraDigital-V2.git 2>/dev/null || true
git fetch origin main
git reset --hard origin/main

if [[ -f ~/db.local.php.bak ]]; then
  cp ~/db.local.php.bak includes/db.local.php
fi
if [[ -f ~/config.local.php.bak ]]; then
  cp ~/config.local.php.bak includes/config.local.php
fi

php -l includes/db.local.php
echo "Deploy pull complete."
