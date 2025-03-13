<?php
require_once "../config/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once "../includes/log_activity.php";

logActivity("search Page");

$searchResults = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_query'])) {
    $searchQuery = trim($_POST['search_query']);

    if (!empty($searchQuery)) {
        $stmt = $db->prepare("SELECT id, username, email, balance, profile_image FROM users WHERE username LIKE ? OR email LIKE ?");
        $searchTerm = "%{$searchQuery}%";
        $stmt->execute([$searchTerm, $searchTerm]);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Users | Secure Web App</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .search-container {
            max-width: 600px;
            margin: auto;
            margin-top: 50px;
        }
        .search-results {
            margin-top: 30px;
        }
        .user-card {
            transition: 0.3s;
            border-radius: 10px;
        }
        .user-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
        }
    </style>
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="search-container text-center">
            <h2 class="mb-4">🔍 Search Users</h2>
            <form method="POST" class="d-flex">
                <input type="text" name="search_query" class="form-control me-2" placeholder="Enter username or email..." required>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <?php if (!empty($searchResults)): ?>
            <div class="search-results">
                <h4 class="mt-4 text-center">Search Results</h4>
                <div class="row mt-3">
                    <?php foreach ($searchResults as $user): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card user-card p-3">
                                <div class="text-center">
                                    <img src="<?php echo $user['profile_image'] ? 'uploads/' . $user['profile_image'] : 'assets/default-user.png'; ?>" 
                                         class="profile-pic mb-2" 
                                         alt="Profile Picture">
                                    <h5 class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></h5>
                                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                            
                                    <a href="profile.php?id=<?php echo $user['id']; ?>" class="btn btn-outline-primary btn-sm">View Profile</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p class="text-center text-danger mt-3">No users found.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>

</body>
</html>
