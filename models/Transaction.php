<?php
//models/Transaction.php

class Transaction {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Create a new transaction record using the correct column name
    public function createTransaction($sender_id, $receiver_id, $amount, $comment) {
        // Changed "created_at" to "transaction_time" to match the DB column
        $stmt = $this->db->prepare("INSERT INTO transactions (sender_id, receiver_id, amount, comment, transaction_time) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$sender_id, $receiver_id, $amount, $comment]);
    }
}
