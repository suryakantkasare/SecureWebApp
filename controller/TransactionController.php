<?php
// controller/TransactionController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/log_activity.php';

class TransactionController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit();
        }

        // Log page activity
        logActivity("Transaction Page");

        $user_id = $_SESSION['user_id'];

        // Fetch user balance and username
        $query = $this->db->prepare("SELECT username, balance FROM users WHERE id = ?");
        $query->execute([$user_id]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        // Fetch transaction history
        $historyQuery = $this->db->prepare(
            "SELECT t.*, 
                sender.username AS sender_name, 
                receiver.username AS receiver_name 
             FROM transactions t
             JOIN users sender ON t.sender_id = sender.id
             JOIN users receiver ON t.receiver_id = receiver.id
             WHERE t.sender_id = ? OR t.receiver_id = ?
             ORDER BY t.transaction_time DESC"
        );
        $historyQuery->execute([$user_id, $user_id]);
        $transactions = $historyQuery->fetchAll(PDO::FETCH_ASSOC);

        // Pass data to the view
        include __DIR__ . '/../view/transaction/index.php';
    }
}
