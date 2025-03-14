<?php
if (!isset($user)) {
    die("User data not available.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($user['username']) ?>'s Profile | Secure Web App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .profile-view-container {
      max-width: 600px;
      margin: 40px auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .profile-img {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #2980b9;
    }
    .profile-header {
      margin-bottom: 20px;
    }
    .profile-header h2 {
      margin-top: 15px;
      font-weight: 500;
    }
  </style>
</head>
<body class="bg-light">
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container profile-view-container text-center">
    <div class="profile-header">
      <?php if (!empty($user['profile_image'])): ?>
        <img src="<?= UPLOAD_URL . htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="profile-img">
      <?php else: ?>
        <img src="<?= BASE_URL ?>uploads/default-profile.png" alt="Default Profile" class="profile-img">
      <?php endif; ?>
      <h2><?= htmlspecialchars($user['username']) ?></h2>
    </div>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <hr>
    <h4>Biography</h4>
    <p><?= nl2br(htmlspecialchars($user['biography'])) ?></p>
    <div class="mt-4">
      <a href="<?= BASE_URL ?>index.php?controller=search&action=index" class="btn btn-secondary">Back to Search</a>
    </div>
  </div>
  <?php include __DIR__ . '/../includes/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
