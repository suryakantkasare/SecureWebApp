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

logActivity("profile Page");
$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Fetch user details securely using PDO
$stmt = $db->prepare("SELECT username, email, balance, biography, profile_image FROM users WHERE id = :id");
$stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $biography = htmlspecialchars(trim($_POST['biography']));
    $profile_image = $user['profile_image']; // Retain old image if none is uploaded

    // Profile Image Upload Handling
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["profile_image"]["name"]);
        $file_tmp = $_FILES["profile_image"]["tmp_name"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        $mime_types = ['image/jpeg', 'image/png', 'image/gif'];

        // Check file extension and MIME type
        $file_mime = mime_content_type($file_tmp);
        if (!in_array($file_ext, $allowed_exts) || !in_array($file_mime, $mime_types)) {
            $errors[] = "Invalid file format. Only JPG, JPEG, PNG, and GIF are allowed.";
        } else {
            // Generate a unique filename
            $new_file_name = "profile_" . $user_id . "_" . time() . "." . $file_ext;
            $target_file = realpath($target_dir) . "/" . $new_file_name; // Prevent directory traversal

            if (move_uploaded_file($file_tmp, $target_file)) {
                $profile_image = $new_file_name;
            } else {
                $errors[] = "Error uploading file.";
            }
        }
    }

    // Update user details in the database if no errors
    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE users SET biography = :biography, profile_image = :profile_image WHERE id = :id");
        $stmt->bindValue(':biography', $biography, PDO::PARAM_STR);
        $stmt->bindValue(':profile_image', $profile_image, PDO::PARAM_STR);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            header("Refresh: 1; url=profile.php");
            exit();
        } else {
            $errors[] = "Database error: Could not update profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Secure Web App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-title {
            margin-bottom: 20px; /* Adds space below the title */
        }
        .profile-img-container {
            text-align: center;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 20px; /* Adds space between the title and the image */
        }
    </style>
</head>
<body class="bg-light">

    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h3 class="profile-title mb-4">Profile</h3>
                    </div>
                    <div class="card-body">
                        <div class="profile-img-container">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($user['profile_image']) ?>" class="profile-image">
                            <?php else: ?>
                                <img src="../images/default-profile.png" class="profile-image">
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($success)) echo "<div class='alert alert-success mt-3'>$success</div>"; ?>
                        <?php if (!empty($errors)) {
                            echo "<div class='alert alert-danger mt-3'><ul>";
                            foreach ($errors as $error) {
                                echo "<li>$error</li>";
                            }
                            echo "</ul></div>";
                        } ?>

                        <form action="profile.php" method="post" enctype="multipart/form-data" class="mt-4">
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

                            <div class="mb-3 text-center">
                                <label class="form-label">Update Profile Image</label><br>
                                <input type="file" class="form-control mt-2" name="profile_image" accept="image/*">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
