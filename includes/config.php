<?php
// 1. SECURITY HEADERS
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Referrer-Policy: no-referrer-when-downgrade");

// 2. SESSION & CSRF
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3. GLOBAL SETTINGS (ध्यान दें: पीछे स्लैश नहीं है)
define('SITE_NAME', 'Nectra Digital');
define('SITE_URL', 'https://www.nectradigital.com'); 



// 4. ERROR HANDLING
error_reporting(0); 
?>