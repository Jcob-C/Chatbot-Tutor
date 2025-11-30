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

function getTopicDescription($topicID) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT descr FROM topics WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $topicID);

    $stmt->execute();
    $stmt->bind_result($title);
    $result = $stmt->fetch() ? $title : null;

    return $result;
}

function addTopicClick($topicID) {
    $db = getConnection();

    $stmt = $db->prepare("UPDATE topics SET clicks = clicks + 1 WHERE id = ?");
    $stmt->bind_param("i", $topicID);

    return $stmt->execute();
}

function getTopics($limit, $page) {
    $db = getConnection();

    $offset = ($page - 1) * $limit;
    
    // Get topics for current page
    $stmt = $db->prepare("SELECT * FROM topics LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $topics = $result->fetch_all(MYSQLI_ASSOC);
    
    // Get total count to determine if there are more pages
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM topics");
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalCount = $countResult->fetch_assoc()['total'];
    
    // Calculate pagination flags
    $has_prev = $page > 1;
    $has_next = ($offset + $limit) < $totalCount;
    
    return [
        'topics' => $topics,
        'has_prev' => $has_prev,
        'has_next' => $has_next,
        'page' => $page
    ];
}
?>