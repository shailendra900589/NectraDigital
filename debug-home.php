<?php
/**
 * Simulate homepage load — run: php debug-home.php
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'www.nectradigital.com';

echo "Loading index...\n";
require __DIR__ . '/index.php';
echo "\nDone.\n";
