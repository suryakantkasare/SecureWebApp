<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Web App</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="logo" href="index.php">Secure Web App</a>
            <ul class="nav-links">
                <?php if(isset($_SESSION['username'])): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="register-btn">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container content">
        <h1>Welcome to Secure Web Application</h1>
        <p>Ensure security while handling user authentication, transactions, and more.</p>
        <?php if(!isset($_SESSION['username'])): ?>
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn btn-secondary">Register</a>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Secure Web App. All Rights Reserved.</p>
    </footer>
</body>
</html>
