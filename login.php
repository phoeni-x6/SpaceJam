    <?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Fetch user by username
  $sql = "SELECT * FROM users WHERE username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
      // Start user session
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];

      // Redirect to game
      header("Location: game.php");
      exit();
    } else {
      $error = "❌ Invalid password.";
    }
  } else {
    $error = "⚠️ No account found with that username.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | SpaceJam</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
</head>
<body class="bg-dark text-light d-flex flex-column justify-content-center align-items-center vh-100">

  <div class="card bg-secondary p-4 rounded-4 shadow-lg" style="width: 350px;">
    <h2 class="text-center text-info mb-3">🚀 SpaceJam Login</h2>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger py-2"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-info w-100">Login</button>
    </form>

    <p class="mt-3 text-center text-light">
      Don’t have an account? 
      <a href="register.php" class="text-info">Register</a>
    </p>
  </div>

</body>
</html>
