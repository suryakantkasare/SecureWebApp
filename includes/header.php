<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/index.php">Secure Web App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="/SecureWebApp/index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/SecureWebApp/public/profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="/SecureWebApp/public/transfer.php">Transfer Money</a></li>
                    <li class="nav-item"><a class="nav-link" href="/SecureWebApp/public/transactions.php">Transaction History</a></li>
                    <li class="nav-item"><a class="nav-link" href="/SecureWebApp/public/search.php">Search</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="/SecureWebApp/public/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/SecureWebApp/public/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="/SecureWebApp/public/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

