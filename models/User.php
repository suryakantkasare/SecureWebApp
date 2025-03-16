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
        $password = trim($password);
        $username = strtolower(htmlspecialchars($username, ENT_QUOTES, 'UTF-8'));
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
        try {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password, balance) VALUES (?, ?, ?, 100.00)");
            $stmt->execute([$username, $email, $password]);
    
            return true;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry (username/email already exists)
                return "Error: Username or email already taken!";
            } else {
                return "Database error.";
            }
        }
    }
    

    // Log in a user; return user data on success
    public function login($username, $password) {
        $password = trim($password);
        $username = strtolower(htmlspecialchars($username, ENT_QUOTES, 'UTF-8'));
        $stmt = $this->db->prepare("SELECT * FROM users WHERE LOWER(username) = LOWER(?)");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    // Get a user by ID
    public function getUserById($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            return false; // Prevent SQL injection attempts
        }
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user) {
            // Sanitize output
            $user['username'] = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
            $user['email'] = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
            $user['biography'] = htmlspecialchars($user['biography'], ENT_QUOTES, 'UTF-8');
        }
        
        return $user;
    }
    
    
    // Get all users (if needed for search results)
    public function getAllUsers() {
        $stmt = $this->db->query("SELECT id, username, email, biography, profile_image FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($users as &$user) {
            $user['username'] = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
            $user['email'] = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
            $user['biography'] = htmlspecialchars($user['biography'], ENT_QUOTES, 'UTF-8');
        }
    
        return $users;
    }
    
    
    // Update profile details (email, biography, and optionally profile image)
    public function updateProfile($id, $email, $biography, $profile_image = null) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $biography = strip_tags($biography); // Strip HTML to prevent XSS
    
        // Validate ID to prevent SQL injection
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            return "Invalid user ID!";
        }
        
        // If a new profile image filename is provided, update it; otherwise, leave it unchanged.
        if ($profile_image) {
            $stmt = $this->db->prepare("UPDATE users SET email = ?, biography = ?, profile_image = ? WHERE id = ?");
            return $stmt->execute([$email, $biography, $profile_image, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET email = ?, biography = ? WHERE id = ?");
            return $stmt->execute([$email, $biography, $id]);
        }
    }    
}
?>
