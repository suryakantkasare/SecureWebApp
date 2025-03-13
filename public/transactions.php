<?php
require_once "../config/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "../includes/log_activity.php";

logActivity("Transaction Page");

$user_id = $_SESSION['user_id'];

// Fetch user balance
$query = $db->prepare("SELECT username, balance FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Fetch transaction history
$historyQuery = $db->prepare("SELECT t.*, 
    sender.username AS sender_name, 
    receiver.username AS receiver_name 
    FROM transactions t
    JOIN users sender ON t.sender_id = sender.id
    JOIN users receiver ON t.receiver_id = receiver.id
    WHERE t.sender_id = ? OR t.receiver_id = ?
    ORDER BY t.transaction_time DESC");

$historyQuery->execute([$user_id, $user_id]);
$transactions = $historyQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History | Secure Web App</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h3 class="text-center">Transaction History</h3>
    <p class="text-center"><strong>Your Balance:</strong> Rs. <?php echo number_format($user['balance'], 2); ?></p>

    <table class="table table-striped text-center">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Amount (Rs.)</th>
                <th>Comment</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($transactions) == 0): ?>
                <tr><td colspan="6">No transactions found.</td></tr>
            <?php else: ?>
                <?php foreach ($transactions as $index => $tx): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($tx['sender_name']); ?></td>
                        <td><?php echo htmlspecialchars($tx['receiver_name']); ?></td>
                        <td><?php echo number_format($tx['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($tx['comment']); ?></td>
                        <td><?php echo $tx['transaction_time']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
