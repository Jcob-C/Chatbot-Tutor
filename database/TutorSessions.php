<?php
function saveNewSession($conn, $userID, $topicTitle, $jsonTranscript, $topicplan) {
    $stmt = $conn->prepare("INSERT INTO tutor_sessions (user_id, topic_title, transcript, topic_plan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userID, $topicTitle, $jsonTranscript, $topicplan);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        return $conn->insert_id;
    } else {
        return 0;
    }
}

function getSession($conn,  $id) {
    $stmt = $conn->prepare("SELECT * FROM tutor_sessions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $session = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    return $session;
}

function getSessionPlan($conn, $topicTitle) {
    $stmt = $conn->prepare("SELECT topic_plan FROM tutor_sessions WHERE LOWER(topic_title) = LOWER(?) LIMIT 1");
    $stmt->bind_param("s", $topicTitle);
    $stmt->execute();

    $plan = null;
    $stmt->bind_result($plan);
    $result = $stmt->fetch() ? $plan : null;

    $stmt->close();
    return $result;
}


function getLastSessionByTopicSortedByScoreAsc($conn, $id, $limit, $page) {
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("
    SELECT topic_title, quiz_score
    FROM (
        SELECT
            topic_title,
            quiz_score,
            ROW_NUMBER() OVER (
                PARTITION BY LOWER(topic_title)
                ORDER BY
                    (quiz_score IS NULL) ASC,
                    id DESC
            ) AS rn
        FROM tutor_sessions
        WHERE user_id = ?
    ) t
    WHERE rn = 1
    ORDER BY quiz_score ASC
    LIMIT ? OFFSET ?;
    ");

    $stmt->bind_param("iii", $id, $limit, $offset);
    $stmt->execute();

    $result = $stmt->get_result();
    $sessions = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    return $sessions;
}



function getLatestUserSessions($conn, $userID, $limit, $page) {
    $offset = ($page - 1) * $limit;
    $stmt = $conn->prepare("
        SELECT *
        FROM tutor_sessions 
        WHERE user_id = ? 
        ORDER BY id DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $userID, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $sessions = $result->fetch_all(MYSQLI_ASSOC);
    
    $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM tutor_sessions WHERE user_id = ?");
    $countStmt->bind_param("i", $userID);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalCount = $countResult->fetch_assoc()['total'];
    
    $has_prev = $page > 1;
    $has_next = ($offset + $limit) < $totalCount;
    
    return [
        'sessions' => $sessions,
        'has_prev' => $has_prev,
        'has_next' => $has_next,
        'page' => $page
    ];
}

function updateSessionQuiz($conn, $id, $score) {
    $stmt = $conn->prepare("UPDATE tutor_sessions SET quiz_score = ? WHERE id = ?");
    $stmt->bind_param("ii", $score, $id);
    $result = $stmt->execute();

    $stmt->close();
    return $result;
}
?>