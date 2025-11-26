<?php
require __DIR__ . '/../lib/PHPMailer-7.0.1/src/Exception.php';
require __DIR__ . '/../lib/PHPMailer-7.0.1/src/PHPMailer.php';
require __DIR__ . '/../lib/PHPMailer-7.0.1/src/SMTP.php';
require_once __DIR__ . '/../utils/database/VerificationCodes.php'; 
require_once __DIR__ . '/../utils/database/Users.php'; 
require_once __DIR__ . '/../config/mail.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method!";
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Email invalid!";
    exit;
}

if (getUserID($email)) {
    echo "Email is already registered.";
    exit;
}

$verificationCode = rand(100000, 999999);

if (!saveVerificationCode($email, $verificationCode)) {
    echo "Failed to save verification code. Please try again.";
    exit;
}

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';           
    $mail->SMTPAuth = true;
    $mail->Username = email;  
    $mail->Password = appPass;   
    $mail->SMTPSecure = 'tls';          
    $mail->Port = 587;

    // Recipients
    $mail->setFrom(email, 'Chatbot-Tutor');
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Verification Code';
    $mail->Body    = "Your verification code is <b>{$verificationCode}</b> and it expires in <b>5 minutes</b>.";

    // Send email
    $mail->send();

    echo "Email verification code sent!";
} 
catch (Exception $e) {
    echo "Failed to send email: {$mail->ErrorInfo}";
}
