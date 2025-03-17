<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/log_activity.php';
class ProfileController {
    private $db;
    private $userModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
    }
    
    // Display the logged-in user's profile for update
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit();
        }
        $user_id = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($user_id);
        include __DIR__ . '/../view/profile/index.php';
        logActivity("profile page");
    }
    
    // Process profile update (email, biography, profile image)
    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
            exit();
        }
    
        $user_id = $_SESSION['user_id'];
        $errors = [];
        $success = "";
    
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // ✅ CSRF Token Validation
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF attack detected!");
            }
    
            $email = trim($_POST['email']);
            $biography = trim($_POST['biography']);
    
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            }
    
            // ✅ Secure Profile Image Upload Handling
            $profile_image = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
    
                if (!in_array($file_ext, $allowed_extensions)) {
                    $errors[] = "Invalid image format. Allowed types: jpg, jpeg, png, gif.";
                } else {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $_FILES['profile_image']['tmp_name']);
                    finfo_close($finfo);
                    $allowed_mime = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!in_array($mime, $allowed_mime)) {
                        $errors[] = "Invalid image file.";
                    } else {
                        if (!is_dir(UPLOAD_DIR)) {
                            mkdir(UPLOAD_DIR, 0755, true);
                        }
                        $new_filename = "profile_" . $user_id . "_" . time() . "." . $file_ext;
                        $destination = UPLOAD_DIR . $new_filename;
                        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                            $profile_image = $new_filename;
                        } else {
                            $errors[] = "Failed to upload image.";
                        }
                    }
                }
            }
    
            if (empty($errors)) {
                $this->userModel->updateProfile($user_id, $email, $biography, $profile_image);
                $success = "Profile updated successfully.";
    
                // ✅ Regenerate CSRF token after a successful update
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        }
    
        $user = $this->userModel->getUserById($user_id);
        include __DIR__ . '/../view/profile/index.php';
    }
    
    
    // View a single user's profile (read‑only) via GET parameter "id"
    public function view() {
        if (!isset($_GET['id'])) {
            die("No user specified.");
        }
        $view_user_id = intval($_GET['id']);
        $user = $this->userModel->getUserById($view_user_id);
        if (!$user) {
            die("User not found.");
        }
        include __DIR__ . '/../view/profile/view.php';
    }
}
?>
