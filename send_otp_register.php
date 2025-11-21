<?php
session_start();
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate input
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
        echo "All fields are required.";
        exit();
    }

    // Store registration data temporarily in session
    $_SESSION['reg_username'] = trim($_POST['username']);
    $_SESSION['reg_email'] = trim($_POST['email']);
    $_SESSION['reg_password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Generate 6-digit OTP
    $_SESSION['reg_otp'] = rand(100000, 999999);
    $otp = $_SESSION['reg_otp'];
    $email = $_SESSION['reg_email'];

    try {
        $mail = new PHPMailer(true);

        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dihanhewage123@gmail.com'; // Your Gmail
        $mail->Password = 'lfrm kopz bdjz tuco';      // Your App Password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Email settings
        $mail->setFrom('dihanhewage123@gmail.com', 'SpaceJam');
        $mail->addAddress($email);
        $mail->Subject = "SpaceJam Email Verification OTP";
        $mail->Body = "Hello ".$_SESSION['reg_username'].",\n\nYour OTP for email verification is: $otp\n\nEnter this OTP to complete your registration.";

        // Send email
        if ($mail->send()) {
            // Redirect to OTP verification page
            header("Location: verify_register_otp.php");
            exit();
        } else {
            echo "Failed to send OTP. Please try again.";
        }

    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }

} else {
    // If accessed directly, redirect to register page
    header("Location: register.php");
    exit();
}
