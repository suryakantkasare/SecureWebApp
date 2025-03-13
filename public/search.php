<?php
require_once "../config/config.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/log_activity.php";
logActivity("Search Page");

$searchResults = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_query'])) {
    $searchQuery = trim($_POST['search_query']);

    if (!empty($searchQuery)) {
        $stmt = $db->prepare("SELECT id, username, email, profile_image FROM users WHERE username LIKE ? OR email LIKE ?");
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
    <title>Search Users | Secure Web App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="text-center">
        <h2>🔍 Search Users</h2>
        <form method="POST" class="d-flex justify-content-center">
            <input type="text" name="search_query" class="form-control w-50 me-2" placeholder="Enter username or email..." required>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <?php if (!empty($searchResults)): ?>
        <div class="row mt-4">
            <?php foreach ($searchResults as $user): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <img src="../uploads/<?= htmlspecialchars($user['profile_image'] ?: 'default-profile.png') ?>" 
                                 class="rounded-circle border" width="80" height="80">
                            <h5 class="mt-2"><?= htmlspecialchars($user['username']) ?></h5>
                            <p><?= htmlspecialchars($user['email']) ?></p>
                            <a href="profile.php?id=<?= $user['id'] ?>" class="btn btn-outline-primary btn-sm">View Profile</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
