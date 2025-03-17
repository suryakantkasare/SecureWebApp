<?php
if (!isset($user)) {
    die("User data not available.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['username']) ?>'s Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 35px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .profile-img {
            width: 170px;
            height: 170px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #007bff;
        }
        .profile-header h2 {
            margin-top: 15px;
            font-weight: 600;
            color: #333;
        }
        .text-muted {
            font-size: 1rem;
        }
        .bio-section {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .btn-custom {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container">
        <div class="profile-container">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?= htmlspecialchars(UPLOAD_URL . $user['profile_image']) ?>" alt="Profile Image" class="profile-img">
            <?php else: ?>
              <img src="<?= BASE_URL ?>uploads/default-profile.png" alt="Default Profile" class="profile-img">            <?php endif; ?>
    
            <div class="profile-header">
                <h2><?= htmlspecialchars($user['username']) ?></h2>
                <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
            </div>
    
            <div class="bio-section">
                <h5>Biography</h5>
                <p class="text-secondary"><?= nl2br(htmlspecialchars($user['biography'])) ?></p>
            </div>
    
            <a href="<?= BASE_URL ?>index.php?controller=search&action=index" class="btn btn-outline-primary btn-custom">Back to Search</a>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

