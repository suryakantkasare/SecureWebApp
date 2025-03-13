<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details securely using PDO
$stmt = $db->prepare("SELECT username, email, balance, biography, profile_image FROM users WHERE id = :id");
$stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $biography = htmlspecialchars(trim($_POST['biography']));
    $profile_image = $user['profile_image']; // Keep old image if no new one is uploaded

    // Profile Image Upload Handling
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["profile_image"]["name"]);
        $file_tmp = $_FILES["profile_image"]["tmp_name"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_exts)) {
            $new_file_name = "profile_" . $user_id . "_" . time() . "." . $file_ext;
            $target_file = $target_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $target_file)) {
                $profile_image = $new_file_name;
            } else {
                $error = "Error uploading file.";
            }
        } else {
            $error = "Invalid file format. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    // Update user details in the database
    $stmt = $db->prepare("UPDATE users SET biography = :biography, profile_image = :profile_image WHERE id = :id");
    $stmt->bindValue(':biography', $biography, PDO::PARAM_STR);
    $stmt->bindValue(':profile_image', $profile_image, PDO::PARAM_STR);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
        header("Refresh: 1; url=profile.php"); // Refresh page after update
    } else {
        $error = "Database error: Could not update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Secure Web App</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">

    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h3>Profile</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                        <form action="profile.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Balance</label>
                                <input type="text" class="form-control" value="₹<?= number_format($user['balance'], 2) ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Biography</label>
                                <textarea class="form-control" name="biography" rows="3"><?= htmlspecialchars($user['biography'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Profile Image</label>
                                <div class="text-center">
                                    <?php if (!empty($user['profile_image'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($user['profile_image']) ?>" class="rounded-circle mb-3" width="150">
                                    <?php else: ?>
                                        <img src="../images/default-profile.png" class="rounded-circle mb-3" width="150">
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control" name="profile_image" accept="image/*">
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
