<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Money Transfer | Secure Web App</title>
  <link href="<?= BASE_URL; ?>assets/css/custom.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .transfer-container { max-width: 500px; margin: 40px auto; }
    #suggestions { z-index: 1000; }
  </style>
</head>
<body class="bg-light">
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container transfer-container">
    <div class="card shadow-sm">
      <div class="card-body">
        <h3 class="text-center mb-3">Money Transfer</h3>
        <p class="text-center"><strong>Your Balance:</strong> Rs. <?= number_format($user['balance'], 2); ?></p>
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $error) echo "<p>" . htmlspecialchars($error) . "</p>"; ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
          <div class="mb-3 position-relative">
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
  <script>
    $(document).ready(function() {
      $("#receiver_username").keyup(function() {
        let query = $(this).val();
        if (query.length > 1) {
          $.ajax({
            url: "<?= BASE_URL ?>search_user.php",
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
  <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
