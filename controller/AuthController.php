<?php
// controller/AuthController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
    }

    public function login() {
        $errors = [];
        
        // Ensure a CSRF token is set for GET requests
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        }
        
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF token validation failed.");
            }
            
            $username = htmlspecialchars(trim($_POST['username']));
            $password = $_POST['password'];
            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                header("Location: " . BASE_URL . "index.php?controller=transfer&action=index");
                exit();
            } else {
                $errors[] = "Invalid username or password.";
            }
        }
        
        include __DIR__ . '/../view/auth/login.php';
    }

    public function register() {
        $errors = [];
        $success = "";
        
        // Ensure a CSRF token is set for GET requests
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        }
        
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF token validation failed.");
            }
            
            $username = htmlspecialchars(trim($_POST['username']));
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            // Validation
            if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
                $errors[] = "All fields are required.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            }
            if (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters.";
            }
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match.";
            }
            if ($this->userModel->userExists($username, $email)) {
                $errors[] = "Username or email already taken.";
            }
            
            if (empty($errors)) {
                $result = $this->userModel->register($username, $email, $password);
                if ($result) {
                    $success = "Registration successful! <a href='" . BASE_URL . "index.php?controller=auth&action=login'>Login here</a>.";
                } else {
                    $errors[] = "Registration failed. Please try again.";
                }
            }
        }
        
        include __DIR__ . '/../view/auth/register.php';
    }

    public function logout() {

        header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
        exit();
    }
}
