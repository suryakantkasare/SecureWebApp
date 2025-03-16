<?php
// controller/AuthController.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

// Start secure session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class AuthController {
    private $db;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);

        // Ensure a CSRF token is set for GET requests
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function login() {
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF token validation failed.");
            }

            // Sanitize input
            $username = htmlspecialchars(trim($_POST['username']));
            $password = $_POST['password'];

            // Authenticate user
            $user = $this->userModel->login($username, $password);

            if ($user) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Regenerate CSRF token
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

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF token validation failed.");
            }

            // Sanitize input
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
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $result = $this->userModel->register($username, $email, $hashedPassword);
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
        session_start(); // Ensure session is started

        // Destroy session securely
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, 
                $params["path"], $params["domain"], 
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
        exit();
    }
}
?>
