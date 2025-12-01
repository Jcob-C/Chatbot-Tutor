<?php
function getVerificationCode($conn, $email) {
    $stmt = $conn->prepare("SELECT code FROM verification_codes WHERE email = ? AND expires > NOW() ORDER BY expires DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $code = null;
    $stmt->bind_result($code);
    $result = $stmt->fetch() ? $code : null;

    $stmt->close();
    return $result;
}

function registerVerificationCode($conn, $email, $code) {
    $stmt = $conn->prepare("INSERT INTO verification_codes (email, code, expires) VALUES (?, ?, NOW() + INTERVAL 5 MINUTE)");
    $stmt->bind_param("si", $email, $code);
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}
?>