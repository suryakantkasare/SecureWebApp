<?php
class User {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Check if a user exists by username or email
    public function userExists($username, $email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch() ? true : false;
    }
    
    // Register a new user (crediting Rs.100 on signup)
    public function register($username, $email, $password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, balance) VALUES (?, ?, ?, 100.00)");
        return $stmt->execute([$username, $email, $hashed_password]);
    }
    
    // Log in a user; return user data on success
    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    // Get a user by ID
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get all users (if needed for search results)
    public function getAllUsers() {
        $stmt = $this->db->query("SELECT id, username, email, biography, profile_image FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Update profile details (email, biography, and optionally profile image)
    public function updateProfile($id, $email, $biography, $profile_image = null) {
        if ($profile_image !== null) {
            $stmt = $this->db->prepare("UPDATE users SET email = ?, biography = ?, profile_image = ? WHERE id = ?");
            return $stmt->execute([$email, $biography, $profile_image, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET email = ?, biography = ? WHERE id = ?");
            return $stmt->execute([$email, $biography, $id]);
        }
    }
}
?>
