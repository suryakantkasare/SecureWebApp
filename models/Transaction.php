<?php
//models/Transaction.php

class Transaction {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Create a new transaction record using the correct column name
    public function createTransaction($sender_id, $receiver_id, $amount, $comment) {
        // Optional: Validate or cast data
        $sender_id   = (int)$sender_id;
        $receiver_id = (int)$receiver_id;
        $amount      = (float)$amount;
        $comment     = strip_tags(trim($comment)); // or use an HTML purifier if allowing safe HTML
    
        $stmt = $this->db->prepare("INSERT INTO transactions (sender_id, receiver_id, amount, comment, transaction_time) VALUES (?, ?, ?, ?, NOW())");
        // Bind parameters explicitly for clarity
        $stmt->bindValue(1, $sender_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $receiver_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $amount, PDO::PARAM_STR); // keeping decimals intact
        $stmt->bindValue(4, $comment, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
}
