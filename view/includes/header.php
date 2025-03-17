<?php
require_once 'config/config.php'; // Include config if needed

$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Secure Web App</title>
  <link href="<?= BASE_URL; ?>assets/css/custom.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL; ?>">Secure Web App</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>">Home</a></li>

        <?php if ($isLoggedIn): ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>index.php?controller=profile&action=index">My Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>index.php?controller=transfer&action=index">Transfer</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>index.php?controller=transaction&action=index">Transactions</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>index.php?controller=search&action=index">Search</a></li>
          <li class="nav-item">
            <a class="nav-link btn btn-danger text-white" href="<?= BASE_URL; ?>index.php?controller=auth&action=logout">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>index.php?controller=auth&action=login">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL; ?>index.php?controller=auth&action=register">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
