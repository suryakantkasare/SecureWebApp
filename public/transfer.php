<?php
require_once 'config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $recipient = $_POST['recipient_id'];
    $amount = (float) $_POST['amount'];

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $senderBalance = $stmt->fetchColumn();

    if ($senderBalance >= $amount) {
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $_SESSION['user_id']]);

        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $recipient]);

        $pdo->commit();
        echo "Transfer successful.";
    } else {
        $pdo->rollBack();
        echo "Insufficient balance.";
    }
}
?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="number" name="recipient_id" required placeholder="Recipient ID">
    <input type="number" name="amount" required placeholder="Amount">
    <button type="submit">Transfer</button>
</form>
give