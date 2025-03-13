<?php
require_once "config/config.php";

// Check if the user is logged in
$loggedIn = isset($_SESSION['user_id']);

if ($loggedIn) {
    // Fetch user details
    $user_id = $_SESSION['user_id'];
    $query = $db->prepare("SELECT username, balance FROM users WHERE id = ?");
    $query->execute([$user_id]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
}
require_once "includes/log_activity.php";

logActivity("index Page");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $loggedIn ? 'Dashboard' : 'Welcome'; ?> | Secure Web App</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">

    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <?php if ($loggedIn): ?>
            <!-- Dashboard View for Logged-in Users -->
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h2 class="card-title">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
                    <p class="card-text">Your Balance: <strong>Rs. <?php echo number_format($user['balance'], 2); ?></strong></p>
                    
                    <div class="d-grid gap-2 col-6 mx-auto mt-3">
                        <a href="public/profile.php" class="btn btn-primary">View Profile</a>
                        <a href="public/transfer.php" class="btn btn-success">Transfer Money</a>
                        <a href="public/history.php" class="btn btn-warning">Transaction History</a>
                        <a href="public/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Welcome Page for Non-Logged-in Users -->
            <div class="text-center">
                <h1>Welcome to Secure Web App</h1>
                <p class="lead">Manage your finances securely and easily.</p>
                <div class="d-grid gap-2 col-6 mx-auto mt-3">
                    <a href="public/login.php" class="btn btn-primary">Login</a>
                    <a href="public/register.php" class="btn btn-success">Register</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
