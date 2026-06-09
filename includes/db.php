<?php
$host = "localhost";
$user = "nectrogl_NectraDigital";
$pass = "9Rahul@1432";
$dbname = "nectrogl_NectraDigital";

// Production overrides (Hostinger: copy db.local.php.example → db.local.php)
if (is_file(__DIR__ . '/db.local.php')) {
    require __DIR__ . '/db.local.php';
} elseif (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

function is_spam($text) {
    $bad_words = array("<a href", "http", "https", "www", ".com", ".ru", "cryto", "forex");
    foreach ($bad_words as $word) {
        if (stripos($text, $word) !== false) {
            return true;
        }
    }
    return false;
}
