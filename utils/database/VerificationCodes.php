<?php
require_once __DIR__ . '/../../config/db.php';

function getVerificationCode($email) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT code FROM verification_codes WHERE email = ? AND expires > NOW() ORDER BY expires DESC LIMIT 1");
    $stmt->bind_param("s", $email);

    $stmt->execute();
    $stmt->bind_result($code);
    $result = $stmt->fetch() ? $code : null;

    return $result;
}

function saveVerificationCode($email, $code) {
    $db = getConnection();

    $stmt = $db->prepare("INSERT INTO verification_codes (email, code, expires) VALUES (?, ?, NOW() + INTERVAL 5 MINUTE)");
    $stmt->bind_param("si", $email, $code);

    return $stmt->execute();
}
?>