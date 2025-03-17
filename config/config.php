<?php
// Secure session settings (apply before any output is sent)
ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session
ini_set('session.cookie_secure', 1); // Only allow HTTPS (ensure HTTPS is used)
ini_set('session.use_strict_mode', 1); // Prevent session fixation

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID if it's a new session
if (!isset($_SESSION['initialized'])) {
    session_regenerate_id(true);
    $_SESSION['initialized'] = true;
}

// define('BASE_URL', 'http://localhost:8080/SecureWebApp/');
define('BASE_URL', 'https://localhost:443/');

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

$host = "mysql-db";
$dbname = "securewebapp";
$username = "user";
$password = "password";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
