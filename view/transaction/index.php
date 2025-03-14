<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transaction History | Secure Web App</title>
  <link href="<?= BASE_URL; ?>assets/css/custom.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .transaction-container { max-width: 900px; margin: 40px auto; }
  </style>
</head>
<body class="bg-light">
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container transaction-container">
    <h3 class="text-center mb-4">Transaction History</h3>
    <p class="text-center"><strong>Your Balance:</strong> Rs. <?= number_format($user['balance'], 2); ?></p>
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
              <td><?= $index + 1; ?></td>
              <td><?= htmlspecialchars($tx['sender_name']); ?></td>
              <td><?= htmlspecialchars($tx['receiver_name']); ?></td>
              <td><?= number_format($tx['amount'], 2); ?></td>
              <td><?= htmlspecialchars($tx['comment']); ?></td>
              <td><?= $tx['transaction_time']; ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
