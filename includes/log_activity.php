<?php
require_once __DIR__ . "/../config/config.php";

function logActivity($page) {
    global $db;

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // 0 for guests
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $timestamp = date("Y-m-d H:i:s");

    // Prepare SQL query
    $query = "INSERT INTO activity_log (user_id, page_accessed, ip_address, timestamp) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);

    // Execute with correct number of parameters
    $stmt->execute([$user_id, $page, $ip_address, $timestamp]);
}
?>


