<?php
session_start();
include 'config.php';

// Check if OTP is set
if (!isset($_SESSION['reg_otp'])) {
    header("Location: register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['reg_otp']) {

        // Insert user into database
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss",
            $_SESSION['reg_username'],
            $_SESSION['reg_email'],
            $_SESSION['reg_password']
        );

        if ($stmt->execute()) {

            // Clear session variables
            unset($_SESSION['reg_otp']);
            unset($_SESSION['reg_username']);
            unset($_SESSION['reg_email']);
            unset($_SESSION['reg_password']);

            header("Location: login.php?verified=1");
            exit();

        } else {
            $error = "Database Error: " . $conn->error;
        }
    } else {
        $error = "❌ Incorrect OTP. Try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-dark text-light d-flex justify-content-center align-items-center vh-100">

<div class="card bg-secondary p-4 rounded-4 shadow-lg" style="width: 350px;">
    <h3 class="text-center text-info">Email Verification</h3>
    <p class="text-center">Enter the 6-digit OTP sent to your email.</p>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">OTP</label>
            <input type="text" name="otp" maxlength="6" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-info w-100">Verify</button>
    </form>
</div>

</body>
</html>
