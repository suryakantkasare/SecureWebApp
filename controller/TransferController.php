<?php
// controller/TransferController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../includes/log_activity.php';

class TransferController {
    private $db;
    private $transactionModel;

    public function __construct($db) {
        $this->db = $db;
        $this->transactionModel = new Transaction($db);
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit();
        }
        
        logActivity("transfer Page");

        $user_id = $_SESSION['user_id'];
        $errors = [];
        $success = "";
        
        // Fetch user balance and username (initially)
        $stmt = $this->db->prepare("SELECT balance, username FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $receiver_username = trim($_POST['receiver_username']);
            $amount = floatval($_POST['amount']);
            $comment = htmlspecialchars(trim($_POST['comment']));
            
            if ($amount <= 0) {
                $errors[] = "Invalid amount.";
            } elseif ($amount > $user['balance']) {
                $errors[] = "Insufficient balance.";
            } else {
                // Get receiver ID from username
                $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$receiver_username]);
                $receiver = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$receiver) {
                    $errors[] = "Receiver not found.";
                } elseif ($receiver['id'] == $user_id) {
                    $errors[] = "You cannot send money to yourself.";
                } else {
                    $receiver_id = $receiver['id'];
                    // Begin transaction
                    $this->db->beginTransaction();
                    try {
                        // Deduct from sender
                        $stmt = $this->db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                        $stmt->execute([$amount, $user_id]);

                        // Credit receiver
                        $stmt = $this->db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                        $stmt->execute([$amount, $receiver_id]);

                        // Record the transaction
                        $result = $this->transactionModel->createTransaction($user_id, $receiver_id, $amount, $comment);
                        if (!$result) {
                            throw new Exception("Failed to record transaction.");
                        }
                        
                        $this->db->commit();
                        
                        // Store success message and updated balance in session (flash data)
                        $_SESSION['flash_success'] = "Transaction successful!";
                        
                        // Re-fetch updated user data
                        $stmt = $this->db->prepare("SELECT balance, username FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        $_SESSION['updated_balance'] = $user['balance'];
                        
                        // Redirect to prevent form resubmission
                        header("Location: " . BASE_URL . "index.php?controller=transfer&action=index");
                        exit();
                    } catch (Exception $e) {
                        $this->db->rollBack();
                        $errors[] = "Transaction failed. Please try again.";
                    }
                }
            }
            // If there are errors, store them in session to display after redirect.
            if (!empty($errors)) {
                $_SESSION['flash_errors'] = $errors;
                header("Location: " . BASE_URL . "index.php?controller=transfer&action=index");
                exit();
            }
        }
        
        // Check for flash messages
        if (isset($_SESSION['flash_success'])) {
            $success = $_SESSION['flash_success'];
            unset($_SESSION['flash_success']);
        }
        if (isset($_SESSION['flash_errors'])) {
            $errors = $_SESSION['flash_errors'];
            unset($_SESSION['flash_errors']);
        }
        if (isset($_SESSION['updated_balance'])) {
            $user['balance'] = $_SESSION['updated_balance'];
            unset($_SESSION['updated_balance']);
        }
        
        include __DIR__ . '/../view/transfer/index.php';
    }
}
