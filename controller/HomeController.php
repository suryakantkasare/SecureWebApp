<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class HomeController {
    private $db;
    private $userModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
    }
    
    public function index() {
        $user = null;
        if (isset($_SESSION['user_id'])) {
            $user = $this->userModel->getUserById($_SESSION['user_id']);
        }
        require_once __DIR__ . '/../view/Home.php';
    }
}
?>
