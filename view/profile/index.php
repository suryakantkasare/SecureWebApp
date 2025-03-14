<?php
if (!isset($user)) {
    die("User data not available.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile | Secure Web App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .profile-container {
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
  <div class="container profile-container">
    <div class="text-center profile-header">
      <?php if (!empty($user['profile_image'])): ?>
        <img src="<?= UPLOAD_URL . htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="profile-img">
      <?php else: ?>
        <img src="<?= BASE_URL ?>uploads/default-profile.png" alt="Default Profile" class="profile-img">
      <?php endif; ?>
      <h2><?= htmlspecialchars($user['username']) ?></h2>
    </div>
    
    <?php if(isset($success) && $success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if(isset($errors) && !empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    
    <form method="post" action="<?= BASE_URL ?>index.php?controller=profile&action=update" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Biography</label>
        <textarea name="biography" class="form-control" rows="5"><?= htmlspecialchars($user['biography']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Profile Image</label>
        <?php if(!empty($user['profile_image'])): ?>
          <div class="mb-2">
            <!-- <img src="<?= UPLOAD_URL . htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="profile-img"> -->
          </div>
        <?php else: ?>
          <p class="text-muted">No profile image uploaded.</p>
        <?php endif; ?>
        <input type="file" name="profile_image" class="form-control">
      </div>
      <button type="submit" class="btn btn-primary w-100">Update Profile</button>
    </form>
    <div class="mt-4 text-center">
      <a href="<?= BASE_URL ?>index.php?controller=search&action=index" class="btn btn-outline-secondary">Search Users</a>
    </div>
  </div>
  <?php include __DIR__ . '/../includes/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
