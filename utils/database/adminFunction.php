<?php
require_once __DIR__ . '/../../config/db.php';

// ==================== DASHBOARD STATS ====================
function getDashboardStats($conn) {
    $stats = [];
    
    // Total users
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE acc_role = 'learner'");
    $stats['total_users'] = $result->fetch_assoc()['total'];
    
    // Active users (activated accounts)
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE acc_role = 'learner' AND activated = TRUE");
    $stats['active_users'] = $result->fetch_assoc()['total'];
    
    // Total topics
    $result = $conn->query("SELECT COUNT(*) as total FROM topics");
    $stats['total_topics'] = $result->fetch_assoc()['total'];
    
    // Total sessions
    $result = $conn->query("SELECT COUNT(*) as total FROM tutor_sessions");
    $stats['total_sessions'] = $result->fetch_assoc()['total'];
    
    // Unread feedback
    $result = $conn->query("SELECT COUNT(*) as total FROM feedbacks");
    $stats['total_feedback'] = $result->fetch_assoc()['total'];
    
    // Average improvement
    $result = $conn->query("SELECT AVG(post_score - pre_score) as avg_improvement FROM tutor_sessions");
    $stats['avg_improvement'] = round($result->fetch_assoc()['avg_improvement'] ?? 0, 2);
    
    return $stats;
}

// ==================== USER MANAGEMENT ====================
function getAllUsers($conn, $page = 1, $limit = 20, $search = '') {
    $offset = ($page - 1) * $limit;
    
    $search_query = '';
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $search_query = "WHERE (email LIKE ? OR nick LIKE ?)";
        $search_term = "%{$search}%";
        $params = [$search_term, $search_term];
        $types = 'ss';
    }
    
    // Count total
    $count_sql = "SELECT COUNT(*) as total FROM users $search_query";
    if (!empty($params)) {
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param($types, ...$params);
        $count_stmt->execute();
        $total = $count_stmt->get_result()->fetch_assoc()['total'];
        $count_stmt->close();
    } else {
        $total = $conn->query($count_sql)->fetch_assoc()['total'];
    }
    
    // Get users
    $sql = "SELECT id, email, nick, acc_role, activated FROM users $search_query ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt->bind_param('ii', $limit, $offset);
    }
    
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return [
        'users' => $users,
        'total_pages' => ceil($total / $limit),
        'current_page' => $page,
        'total' => $total
    ];
}

function toggleUserActivation($conn, $user_id) {
    $stmt = $conn->prepare("UPDATE users SET activated = NOT activated WHERE id = ? AND acc_role != 'admin'");
    $stmt->bind_param('i', $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function deleteUser($conn, $user_id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND acc_role != 'admin'");
    $stmt->bind_param('i', $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function getUserDetails($conn, $user_id) {
    $stmt = $conn->prepare("SELECT id, email, nick, acc_role, activated FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $user;
}

// ==================== TOPIC MANAGEMENT ====================
function getAllTopicsAdmin($conn) {
    $result = $conn->query("SELECT id, title, descr, clicks FROM topics ORDER BY title ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTopicById($conn, $topic_id) {
    $stmt = $conn->prepare("SELECT id, title, descr, clicks FROM topics WHERE id = ?");
    $stmt->bind_param('i', $topic_id);
    $stmt->execute();
    $topic = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $topic;
}

function createTopic($title, $descr) {
    $conn = getConnection();    
    $title = trim($title);
    $descr = trim($descr);
    
    if (empty($title) || empty($descr)) {
        return ['success' => false, 'message' => 'Title and description are required.'];
    }
    
    $stmt = $conn->prepare("INSERT INTO topics (title, descr) VALUES (?, ?)");
    $stmt->bind_param('ss', $title, $descr);
    $success = $stmt->execute();
    $stmt->close();
    
    return $success 
        ? ['success' => true, 'message' => 'Topic created successfully!']
        : ['success' => false, 'message' => 'Failed to create topic.'];
}

function updateTopic($conn, $topic_id, $title, $descr) {
    $title = trim($title);
    $descr = trim($descr);
    
    if (empty($title) || empty($descr)) {
        return ['success' => false, 'message' => 'Title and description are required.'];
    }
    
    $stmt = $conn->prepare("UPDATE topics SET title = ?, descr = ? WHERE id = ?");
    $stmt->bind_param('ssi', $title, $descr, $topic_id);
    $success = $stmt->execute();
    $stmt->close();
    
    return $success 
        ? ['success' => true, 'message' => 'Topic updated successfully!']
        : ['success' => false, 'message' => 'Failed to update topic.'];
}

function deleteTopic($conn, $topic_id) {
    $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
    $stmt->bind_param('i', $topic_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

// ==================== FEEDBACK MONITORING ====================
function getAllFeedback($conn, $page = 1, $limit = 20) {
    $offset = ($page - 1) * $limit;
    
    // Count total
    $total = $conn->query("SELECT COUNT(*) as total FROM feedbacks")->fetch_assoc()['total'];
    
    // Get feedback
    $stmt = $conn->prepare("
        SELECT f.id, f.title, f.descr, f.created, u.nick, u.email 
        FROM feedbacks f
        JOIN users u ON f.user_id = u.id
        ORDER BY f.created DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $feedback = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return [
        'feedback' => $feedback,
        'total_pages' => ceil($total / $limit),
        'current_page' => $page,
        'total' => $total
    ];
}

function deleteFeedback($conn, $feedback_id) {
    $stmt = $conn->prepare("DELETE FROM feedbacks WHERE id = ?");
    $stmt->bind_param('i', $feedback_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

// ==================== TEST REPORTS ====================
function getTopicReports($conn) {
    $stmt = $conn->prepare("
        SELECT 
            t.id,
            t.title,
            COUNT(ts.id) as session_count,
            AVG(ts.pre_score) as avg_pre_score,
            AVG(ts.post_score) as avg_post_score,
            AVG(ts.post_score - ts.pre_score) as avg_improvement
        FROM topics t
        LEFT JOIN tutor_sessions ts ON t.id = ts.topic_id
        GROUP BY t.id, t.title
        ORDER BY session_count DESC
    ");
    $stmt->execute();
    $reports = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $reports;
}

function getTopicSessionDetails($conn, $topic_id) {
    $stmt = $conn->prepare("
        SELECT 
            ts.id,
            u.nick,
            u.email,
            ts.pre_score,
            ts.post_score,
            ts.concluded
        FROM tutor_sessions ts
        JOIN users u ON ts.user_id = u.id
        WHERE ts.topic_id = ?
        ORDER BY ts.concluded DESC
    ");
    $stmt->bind_param('i', $topic_id);
    $stmt->execute();
    $sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $sessions;
}

function getOverallStats($conn) {
    $stats = [];
    
    // Total sessions
    $result = $conn->query("SELECT COUNT(*) as total FROM tutor_sessions");
    $stats['total_sessions'] = $result->fetch_assoc()['total'];
    
    // Average scores
    $result = $conn->query("SELECT AVG(pre_score) as avg_pre, AVG(post_score) as avg_post FROM tutor_sessions");
    $row = $result->fetch_assoc();
    $stats['avg_pre_score'] = round($row['avg_pre'] ?? 0, 2);
    $stats['avg_post_score'] = round($row['avg_post'] ?? 0, 2);
    $stats['avg_improvement'] = round(($row['avg_post'] ?? 0) - ($row['avg_pre'] ?? 0), 2);
    
    return $stats;
}

// ==================== SECURITY & LOGGING ====================
function logAdminAction($conn, $admin_id, $action, $details = '') {
    // This could be expanded to a dedicated admin_logs table
    // For now, we'll keep it simple without additional tables
    return true;
}

function getRecentAdminActivity($conn, $limit = 10) {
    // Placeholder for activity log
    // Would require an admin_logs table in production
    return [];
}
?>