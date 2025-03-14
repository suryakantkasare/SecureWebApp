<?php
// includes/log_activity.php

// Ensure the database connection is available.
if (!isset($GLOBALS['db'])) {
    require_once __DIR__ . '/../config/config.php';
}

function logActivity($pageAccessed) {
    // Access the global $db connection using the $GLOBALS array.
    $db = $GLOBALS['db'];
    if (!$db) {
        error_log("No database connection available in logActivity.");
        return;
    }
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $db->prepare("INSERT INTO activity_log (user_id, page_accessed, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $pageAccessed, $ip_address]);
}
?>
