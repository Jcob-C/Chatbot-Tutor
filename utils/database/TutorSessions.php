<?php
require_once __DIR__ . '/../../config/db.php';

function saveNewSession($userID, $topicID, $preScore, $postScore, $messages) {
    $db = getConnection();
    $jsonMessages = json_encode($messages);

    $stmt = $db->prepare("INSERT INTO tutor_sessions (user_id, topic_id, pre_score, post_score, messages) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiis", $userID, $topicID, $preScore, $postScore, $jsonMessages);

    if ($stmt->execute()) {
        return $db->insert_id;
    } else {
        return 0;
    }
}

function getSession($id) {
    $db = getConnection();

    $stmt = $db->prepare("SELECT * FROM tutor_sessions WHERE id = ?");
    $stmt->bind_param("i", $id);

    $stmt->execute();
    $result = $stmt->get_result();
    $resultArray = $result->fetch_all(MYSQLI_ASSOC);

    return $resultArray;
}

function getLatestUserSessions($userID, $limit, $page) {
    $db = getConnection();

    $offset = ($page - 1) * $limit;
    
    // Get sessions with topic title using JOIN
    $stmt = $db->prepare("
        SELECT 
            tutor_sessions.*, 
            topics.title as topic_title 
        FROM tutor_sessions 
        JOIN topics ON tutor_sessions.topic_id = topics.id 
        WHERE tutor_sessions.user_id = ? 
        ORDER BY tutor_sessions.id DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $userID, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $sessions = $result->fetch_all(MYSQLI_ASSOC);
    
    // Get total count for this user
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM tutor_sessions WHERE user_id = ?");
    $countStmt->bind_param("i", $userID);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalCount = $countResult->fetch_assoc()['total'];
    
    // Calculate pagination flags
    $has_prev = $page > 1;
    $has_next = ($offset + $limit) < $totalCount;
    
    return [
        'sessions' => $sessions,
        'has_prev' => $has_prev,
        'has_next' => $has_next,
        'page' => $page
    ];
}

function getPreviousTopicIDsSortedByPostScore($userID, $limit, $page) {
    $db = getConnection();
    $offset = ($page - 1) * $limit;

    $sql = "
        SELECT ts.topic_id
        FROM tutor_sessions ts
        INNER JOIN (
            SELECT topic_id, MAX(id) AS latest_id
            FROM tutor_sessions
            WHERE user_id = ?
            GROUP BY topic_id
        ) latest
        ON ts.id = latest.latest_id
        ORDER BY ts.post_score ASC
        LIMIT ? OFFSET ?
    ";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("iii", $userID, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $topicIDs = [];
    while ($row = $result->fetch_assoc()) {
        $topicIDs[] = $row['topic_id'];
    }

    return $topicIDs;
}
?>