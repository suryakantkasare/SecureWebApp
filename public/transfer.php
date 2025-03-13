<?php
require_once "../config/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once "../includes/log_activity.php";

logActivity("transfer Page");

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Fetch user balance
$query = $db->prepare("SELECT balance, username FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Handle money transfer request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receiver_username = trim($_POST['receiver_username']);
    $amount = floatval($_POST['amount']);
    $comment = htmlspecialchars(trim($_POST['comment']));

    if ($amount <= 0) {
        $errors[] = "Invalid amount.";
    } elseif ($amount > $user['balance']) {
        $errors[] = "Insufficient balance.";
    } else {
        // Get receiver ID from username
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$receiver_username]);
        $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$receiver) {
            $errors[] = "Receiver not found.";
        } elseif ($receiver['id'] == $user_id) {
            $errors[] = "You cannot send money to yourself.";
        } else {
            $receiver_id = $receiver['id'];

            // Perform transaction
            $db->beginTransaction();
            try {
                // Deduct from sender
                $stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                $stmt->execute([$amount, $user_id]);

                // Add to receiver
                $stmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$amount, $receiver_id]);

                // Insert transaction record
                $stmt = $db->prepare("INSERT INTO transactions (sender_id, receiver_id, amount, comment) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $receiver_id, $amount, $comment]);

                $db->commit();
                $success = "Transaction successful!";
            } catch (Exception $e) {
                $db->rollBack();
                $errors[] = "Transaction failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Transfer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title text-center">Money Transfer</h3>
                    <p><strong>Your Balance: </strong>Rs. <?php echo number_format($user['balance'], 2); ?></p>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"> <?php echo $success; ?> </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="receiver_username" class="form-label">Receiver Username</label>
                            <input type="text" class="form-control" name="receiver_username" id="receiver_username" required autocomplete="off">
                            <div id="suggestions" class="list-group position-absolute"></div>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (Rs.)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment (Optional)</label>
                            <textarea class="form-control" name="comment" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Money</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#receiver_username").keyup(function() {
        let query = $(this).val();
        if (query.length > 1) {
            $.ajax({
                url: "search_user.php",
                method: "POST",
                data: {query: query},
                success: function(data) {
                    $("#suggestions").html(data).show();
                }
            });
        } else {
            $("#suggestions").hide();
        }
    });

    $(document).on("click", ".suggestion-item", function() {
        $("#receiver_username").val($(this).text());
        $("#suggestions").hide();
    });
});
</script>
</body>
</html>