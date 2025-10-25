<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $username, $email, $password);

  if ($stmt->execute()) {
    header("Location: login.php");
    exit();
  } else {
    $error = "⚠️ Error: " . $conn->error;
  }
}
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

    <?php if (isset($error)): ?>
      <div class="alert alert-danger py-2"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
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

      <button type="submit" class="btn btn-info w-100">Register</button>
    </form>

    <p class="mt-3 text-center text-light">
      Already have an account? 
      <a href="login.php" class="text-info">Login</a>
    </p>
  </div>

</body>
</html>
