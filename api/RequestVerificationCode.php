<?php
require __DIR__ . '/../lib/PHPMailer-7.0.1/src/Exception.php';
require __DIR__ . '/../lib/PHPMailer-7.0.1/src/PHPMailer.php';
require __DIR__ . '/../lib/PHPMailer-7.0.1/src/SMTP.php';
require_once __DIR__ . '/../database/VerificationCodes.php'; 
require_once __DIR__ . '/../database/Users.php'; 
require_once __DIR__ . '/../config/mail.php'; 
require_once __DIR__ . '/../config/db.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli(host, user, pass, db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method!";
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Email invalid!";
    exit;
}

if (getUserID($conn, $email)) {
    echo "Email is already registered.";
    exit;
}

$verificationCode = rand(100000, 999999);

if (!registerVerificationCode($conn, $email, $verificationCode)) {
    echo "Failed to register the verification code. Please try again.";
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
    $mail->setFrom(email, 'TutorChat');
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'TutorChat Verification Code';
    $mail->Body    = "Your verification code is <b>{$verificationCode}</b> and it expires in <b>5 minutes</b>.";

    // Send email
    $mail->send();

    echo "Email verification code sent!";
} 
catch (Exception $e) {
    echo "Failed to send email: {$mail->ErrorInfo}";
}
