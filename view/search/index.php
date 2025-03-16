<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Users | Secure Web App</title>
  <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net;">
  <link href="<?= BASE_URL; ?>assets/css/custom.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .search-container { max-width: 800px; margin: 40px auto; }
  </style>
</head>
<body class="bg-light">
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container search-container">
    <div class="text-center mb-4">
      <h2>🔍 Search Users</h2>
      <form method="POST" class="d-flex justify-content-center">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="text" name="search_query" class="form-control w-50 me-2" placeholder="Enter username or email..." required>
        <button type="submit" class="btn btn-primary">Search</button>
      </form>
    </div>
    <?php if (!empty($searchResults)): ?>
      <div class="row">
        <?php foreach ($searchResults as $user): ?>
          <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
              <div class="card-body text-center">
                <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($user['profile_image'] ?: 'default-profile.png') ?>" 
                     class="rounded-circle border mb-2" width="80" height="80" alt="Profile Image">
                <h5><?= htmlspecialchars($user['username']) ?></h5>
                <p><?= htmlspecialchars($user['email']) ?></p>
                <a href="<?= BASE_URL ?>index.php?controller=profile&action=view&id=<?= $user['id'] ?>" class="btn btn-outline-primary btn-sm">View Profile</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
      <div class="text-center">
        <p class="text-muted">No users found matching your search criteria.</p>
      </div>
    <?php endif; ?>
  </div>
  <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
