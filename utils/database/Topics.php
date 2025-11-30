<?php
require_once __DIR__ . '/../../config/db.php';

function getTopicTitle($topicID) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT title FROM topics WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $topicID);

    $stmt->execute();
    $stmt->bind_result($title);
    $result = $stmt->fetch() ? $title : null;

    return $result;
}
?>