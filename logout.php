<?php
require_once 'config/config.php';

session_start();
session_unset();
session_destroy();

header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
exit();
?>
