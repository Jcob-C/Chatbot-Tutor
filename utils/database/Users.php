<?php
require_once __DIR__ . '/../../config/db.php';

function getUserID($email) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);

    $stmt->execute();
    $stmt->bind_result($id);
    $result = $stmt->fetch() ? $id : null;

    return $result;
}

function getEmail($userid) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userid);

    $stmt->execute();
    $stmt->bind_result($email);
    $result = $stmt->fetch() ? $email : null;

    return $result;
}

function updatePassword($userid, $password) {
    $db = getConnection();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("UPDATE users SET pass = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $userid);

    return $stmt->execute();
}

function updateNickname($userid, $nickname) {
    $db = getConnection();

    $stmt = $db->prepare("UPDATE users SET nick = ? WHERE id = ?");
    $stmt->bind_param("si", $nickname, $userid);

    return $stmt->execute();
}

function getUserRole($userID) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT acc_role FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userID);

    $stmt->execute();
    $stmt->bind_result($role);
    $result = $stmt->fetch() ? $role : null;

    return $result;
}

function getHashedPassword($email) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT pass FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);

    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $result = $stmt->fetch() ? $hashedPassword : null;

    return $result;
}

function getHashedPasswordWithID($userID) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT pass FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userID);

    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $result = $stmt->fetch() ? $hashedPassword : null;

    return $result;
}

function getNickname($userID) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT nick FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userID);

    $stmt->execute();
    $stmt->bind_result($nickname);
    $result = $stmt->fetch() ? $nickname : null;

    return $result;
}

function checkActivated($userID) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT activated FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userID);

    $stmt->execute();
    $stmt->bind_result($activated);
    $result = $stmt->fetch() ? $activated : null;

    return $result;
}

function createUser($email, $nick, $pass) {
    $db = getConnection();
    $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (email, nick, pass) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $nick, $hashedPassword);

    return $stmt->execute();
}

function deactivateUser($userid) {
    $db = getConnection();

    $stmt = $db->prepare("UPDATE users SET activated = 0 WHERE id = ?");
    $stmt->bind_param("i", $userid);

    return $stmt->execute();
}
?>
