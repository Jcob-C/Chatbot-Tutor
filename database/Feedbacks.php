<?php
function createFeedback($conn, $userid, $title, $description) {
    $stmt = $conn->prepare("INSERT INTO feedbacks (user_id, title, descr) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userid, $title, $description);
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}
?>