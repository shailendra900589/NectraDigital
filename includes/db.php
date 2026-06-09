<?php
$host = "localhost";
$user = "nectrogl_NectraDigital"; // अपनी होस्टिंग का यूजरनेम डालें
$pass = "9Rahul@1432";     // अपनी होस्टिंग का पासवर्ड डालें
$dbname = "nectrogl_NectraDigital";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// SECURITY FUNCTION (Anti-Hacking)
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// SPAM DETECTOR
function is_spam($text) {
    // अगर कोई लिंक या स्क्रिप्ट टैग है तो स्पैम है
    $bad_words = array("<a href", "http", "https", "www", ".com", ".ru", "cryto", "forex");
    foreach ($bad_words as $word) {
        if (stripos($text, $word) !== false) {
            return true;
        }
    }
    return false;
}
?>