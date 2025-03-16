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
        
        logActivity("Transfer Page");
    
        $user_id = $_SESSION['user_id'];
        $errors = [];
        $success = "";
        
        // Ensure CSRF token is set
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // Fetch user balance and username (initially)
        $stmt = $this->db->prepare("SELECT balance, username FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // CSRF Token Validation
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $errors[] = "Invalid CSRF token.";
            }
            
            $receiver_username = trim($_POST['receiver_username']);
            // Validate receiver username (only letters, numbers, underscores; length 3-20)
            if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $receiver_username)) {
                $errors[] = "Invalid receiver username format.";
            }
            
            $amount = floatval($_POST['amount']);
            if ($amount <= 0) {
                $errors[] = "Invalid amount.";
            } elseif ($amount > $user['balance']) {
                $errors[] = "Insufficient balance.";
            }
            
            // Sanitize comment: remove HTML tags
            $comment = isset($_POST['comment']) ? strip_tags(trim($_POST['comment'])) : "";
            
            if (empty($errors)) {
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
                        
                        // Regenerate CSRF token after a successful transaction to prevent reuse
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        
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
