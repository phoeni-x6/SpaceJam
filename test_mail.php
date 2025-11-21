<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // Your Gmail + App Password
    $mail->Username = 'dihanhewage123@gmail.com';
    $mail->Password = 'lfrm kopz bdjz tuco';

    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    // Email headers
    $mail->setFrom('dihanhewage123@gmail.com', 'SpaceJamTest');
    $mail->addAddress('dihanhewage880@gmail.com');

    // Email content
    $mail->Subject = 'PHPMailer Test';
    $mail->Body = 'This is a test email sent using PHPMailer in PHP!';

    $mail->send();

    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Mail Error: " . $mail->ErrorInfo;
}
