<?php
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | SpaceJam</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
</head>
<body class="bg-dark text-light d-flex flex-column justify-content-center align-items-center vh-100">

  <div class="card bg-secondary p-4 rounded-4 shadow-lg" style="width: 350px;">
    <h2 class="text-center text-info mb-3">🚀 SpaceJam Register</h2>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger py-2"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- IMPORTANT: Registration now goes to send_otp_register.php -->
    <form method="POST" action="send_otp_register.php">

      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-info w-100">Send OTP</button>
    </form>

    <p class="mt-3 text-center text-light">
      Already have an account? 
      <a href="login.php" class="text-info">Login</a>
    </p>
  </div>

</body>
</html>
