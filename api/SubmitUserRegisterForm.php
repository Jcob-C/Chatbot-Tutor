<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../database/Users.php';
require_once __DIR__ . '/../database/VerificationCodes.php';

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$nickname = isset($_POST['nickname']) ? trim($_POST['nickname']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$code = isset($_POST['verificationCode']) ? trim($_POST['verificationCode']) : '';
$conn = new mysqli(host, user, pass, db);

if (empty($email) || empty($nickname) || empty($password) || empty($code)) {
    echo "All fields are required.";
    exit;
}

$userID = getUserID($conn, $email);
if ($userID) {
    echo "Email is already registered.";
    exit;
}

$storedCode = getVerificationCode($conn, $email);
if (!$storedCode) {
    echo "No valid verification code found or it has expired.";
    exit;
}

if ($storedCode != $code) {
    echo "Incorrect verification code.";
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
if (createUser($conn, $email, $nickname, $hashedPassword)) {
    echo "Registration successful!<br>You can now <a href='login.php'>Log in</a>";
} 
else {
    echo "Registration failed. Please try again.";
}
?>
