<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/growth/bootstrap.php';

use Growth\Engines\FeedEngine;

header('Content-Type: application/rss+xml; charset=utf-8');
echo FeedEngine::discoverXml(40);
