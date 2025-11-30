<?php
require_once __DIR__ . '/../../config/db.php';

function createFeedback($userid, $title, $description) {
    $db = getConnection();
    $stmt = $db->prepare("INSERT INTO feedbacks (user_id, title, descr) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userid, $title, $description);

    return $stmt->execute();
}
?>