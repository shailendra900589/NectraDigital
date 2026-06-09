<?php
if (isset($GLOBALS['nectra_db_conn']) && $GLOBALS['nectra_db_conn'] instanceof mysqli) {
    $conn = $GLOBALS['nectra_db_conn'];
    return;
}

$host = 'localhost';
$user = '';
$pass = '';
$dbname = '';

// Production DB overrides (Hostinger: copy db.local.php.example → db.local.php)
if (is_file(__DIR__ . '/db.local.php')) {
    require __DIR__ . '/db.local.php';
}

if ($user === '' || $dbname === '') {
    die('Database not configured. Create includes/db.local.php with $host, $user, $pass, $dbname.');
}

$conn = new mysqli($host, $user, $pass, $dbname);
$GLOBALS['nectra_db_conn'] = $conn;

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

function is_spam($text) {
    $bad_words = array('<a href', 'http', 'https', 'www', '.com', '.ru', 'cryto', 'forex');
    foreach ($bad_words as $word) {
        if (stripos($text, $word) !== false) {
            return true;
        }
    }
    return false;
}
