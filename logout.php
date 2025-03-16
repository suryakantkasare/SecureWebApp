<?php
require_once 'config/config.php';

session_start();
session_unset();
session_destroy();

// Remove the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
exit();
?>
