<?php
require_once '../config/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/log_activity.php";
logActivity("Profile Page");

$user_id = $_SESSION['user_id'];
$profile_id = isset($_GET['id']) ? intval($_GET['id']) : $user_id;

// Fetch user details
$stmt = $db->prepare("SELECT username, email, balance, biography, profile_image FROM users WHERE id = :id");
$stmt->bindValue(':id', $profile_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$is_own_profile = ($user_id === $profile_id);

$errors = [];
$success = "";

// Profile Update Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile']) && $is_own_profile) {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "CSRF token validation failed.";
    }

    $biography = htmlspecialchars(trim($_POST['biography']));
    $profile_image = $user['profile_image'];

    // File upload validation
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["profile_image"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        // Check file extension
        if (!in_array($file_ext, $allowed_exts)) {
            $errors[] = "Invalid file format.";
        }
        
        // Validate file MIME type using the Fileinfo extension
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($_FILES["profile_image"]["tmp_name"]);
        if (!in_array($mime_type, $allowed_mime_types)) {
            $errors[] = "Invalid file type.";
        }
        
        if (empty($errors)) {
            $new_file_name = "profile_" . $user_id . "_" . time() . "." . $file_ext;
            $target_file = $target_dir . $new_file_name;

            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image = $new_file_name;
            } else {
                $errors[] = "Error uploading file.";
            }
        }
    }

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE users SET biography = :biography, profile_image = :profile_image WHERE id = :id");
        $stmt->execute([
            ':biography' => $biography,
            ':profile_image' => $profile_image,
            ':id' => $user_id
        ]);

        $success = "Profile updated!";
        header("Refresh: 1; url=profile.php?id=$user_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile | Secure Web App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3><?= htmlspecialchars($user['username']) ?>'s Profile</h3>
                </div>
                <div class="card-body text-center">
                    <img src="../uploads/<?= htmlspecialchars($user['profile_image'] ?: 'default-profile.png') ?>" 
                         class="rounded-circle border" width="150" height="150" alt="Profile Image">
                    
                    <p class="mt-3"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    
                    <?php if ($is_own_profile): ?>
                        <p><strong>Balance:</strong> ₹<?= number_format($user['balance'], 2) ?></p>
                    <?php endif; ?>

                    <p><strong>Biography:</strong> <?= nl2br(htmlspecialchars($user['biography'] ?: 'No biography added.')) ?></p>

                    <?php if ($is_own_profile): ?>
                        <form action="profile.php?id=<?= $user_id ?>" method="post" enctype="multipart/form-data">
                            <!-- CSRF token field -->
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <textarea class="form-control my-2" name="biography" placeholder="Update your bio..."><?= htmlspecialchars($user['biography'] ?? '') ?></textarea>
                            <input type="file" name="profile_image" class="form-control my-2">
                            <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
                        </form>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger mt-2">
                            <?php foreach ($errors as $error): ?>
                                <p><?= htmlspecialchars($error) ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success mt-2">
                            <p><?= htmlspecialchars($success) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
