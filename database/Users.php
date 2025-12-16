<?php
function getUserID($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $id = null;
    $stmt->bind_result($id);
    $result = $stmt->fetch() ? $id : null;

    $stmt->close();
    return $result;
}

function getEmail($conn, $userid) {
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    
    $email = null;
    $stmt->bind_result($email);
    $result = $stmt->fetch() ? $email : null;

    $stmt->close();
    return $result;
}

function updatePassword($conn, $userid, $hashedPassword) {
    $stmt = $conn->prepare("UPDATE users SET pass = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $userid);
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}

function updateNickname($conn, $userid, $nickname) {
    $stmt = $conn->prepare("UPDATE users SET nickname = ? WHERE id = ?");
    $stmt->bind_param("si", $nickname, $userid);
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}

function getUserRole($conn, $userid) {
    $stmt = $conn->prepare("SELECT acc_role FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userid);
    $stmt->execute();

    $role = null;
    $stmt->bind_result($role);
    $result = $stmt->fetch() ? $role : null;

    $stmt->close();
    return $result;
}

function getHashedPasswordByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT pass FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $hashedPassword = '';
    $stmt->bind_result($hashedPassword);
    $result = $stmt->fetch() ? $hashedPassword : '';

    $stmt->close();
    return $result;
}

function getHashedPasswordByID($conn, $userID) {
    $stmt = $conn->prepare("SELECT pass FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    return $row ? $row['pass'] : null;
}

function getNickname($conn, $userID) {
    $stmt = $conn->prepare("SELECT nickname FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userID);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    return $row ? $row['nickname'] : null;
}

function checkActivated($conn, $userID) {
    $stmt = $conn->prepare("SELECT activated FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $userID);
    $stmt->execute();

    $activated = null;
    $stmt->bind_result($activated);
    $result = $stmt->fetch() ? $activated : null;

    $stmt->close();
    return $result;
}

function createUser($conn, $email, $nick, $hashedPassword) {
    $stmt = $conn->prepare("INSERT INTO users (email, nickname, pass) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $nick, $hashedPassword);
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}

function deactivateUser($conn, $userid) {
    $stmt = $conn->prepare("UPDATE users SET activated = 0 WHERE id = ?");
    $stmt->bind_param("i", $userid);
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}
?>
