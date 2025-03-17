<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Our Bank | Secure Banking</title>
  <link href="<?= BASE_URL; ?>assets/css/custom.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f4f6f9;
      font-family: 'Poppins', sans-serif;
    }
    .hero-section {
      text-align: center;
      padding: 60px 20px;
      background: linear-gradient(to right, #004d99, #00a3cc);
      color: white;
      border-radius: 10px;
      margin-bottom: 30px;
    }
    .hero-section h1 {
      font-size: 2.8rem;
      font-weight: 600;
    }
    .hero-section p {
      font-size: 1.2rem;
    }
    .dashboard {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }
    .dashboard h2 {
      font-size: 2rem;
      margin-bottom: 15px;
      font-weight: 500;
    }
    .balance-card {
      background: linear-gradient(to right, #1e3c72, #2a5298);
      color: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      font-size: 1.3rem;
    }
    .btn-bank {
      padding: 12px 20px;
      font-size: 1rem;
      border-radius: 8px;
      font-weight: 500;
    }
    .btn-primary {
      background: #007bff;
      border: none;
    }
    .btn-primary:hover {
      background: #0056b3;
    }
    .btn-danger {
      background: #dc3545;
      border: none;
    }
    .btn-danger:hover {
      background: #b02a37;
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="container">
    <div class="hero-section">
      <h1>Welcome to Our Bank</h1>
      <p>Your trusted financial partner for a secure future.</p>
    </div>

    <?php if (isset($_SESSION['user_id']) && isset($user)): ?>
      <div class="dashboard">
        <h2>Hello, <strong><?= htmlspecialchars($user['username']) ?></strong></h2>
        <p>Your latest account summary is below.</p>
        
        <div class="balance-card">
          <p>Account Balance: <strong>$<?= number_format($user['balance'], 2); ?></strong></p>
        </div>

        <div class="mt-4">
          <a href="<?= BASE_URL ?>index.php?controller=profile&action=view&id=<?= $user['id'] ?>" class="btn btn-primary btn-bank">View Profile</a>
          <a href="<?= BASE_URL ?>index.php?controller=transaction" class="btn btn-success btn-bank">Transactions</a>
          <a href="<?= BASE_URL ?>index.php?controller=auth&action=logout" class="btn btn-danger btn-bank">Logout</a>
        </div>
      </div>
    <?php else: ?>
      <div class="text-center">
        <h3>Join Our Bank Today</h3>
        <p>Sign up for a secure and seamless banking experience.</p>
        <a href="<?= BASE_URL ?>index.php?controller=auth&action=login" class="btn btn-primary btn-lg">Login</a>
        <a href="<?= BASE_URL ?>index.php?controller=auth&action=register" class="btn btn-warning btn-lg">Register</a>
      </div>
    <?php endif; ?>
  </div>

  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
