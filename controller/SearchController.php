<?php
// controller/SearchController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/log_activity.php';

class SearchController {
    private $db;
    private $userModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
    }
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit();
        }
        
        logActivity("Search Page");
        
        $searchResults = [];
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_query'])) {
            $searchQuery = trim($_POST['search_query']);
            // Limit the length of the search query
            if (strlen($searchQuery) > 100) {
                $searchQuery = substr($searchQuery, 0, 100);
            }
            // Remove special characters
            $searchQuery = preg_replace('/[^a-zA-Z0-9\s]/', '', $searchQuery);
            if (!empty($searchQuery)) {
                $stmt = $this->db->prepare("SELECT id, username, email, profile_image FROM users WHERE username LIKE ? OR email LIKE ?");
                $searchTerm = "%" . $searchQuery . "%";
                $stmt->execute([$searchTerm, $searchTerm]);
                $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        
        include __DIR__ . '/../view/search/index.php';
    }
}
?>
