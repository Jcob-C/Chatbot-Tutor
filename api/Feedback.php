<?php
require_once '../utils/database/Feedbacks.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $userid = isset($_SESSION['userID']) ? (int)$_SESSION['userID'] : 0;

    if ($userid === 0) {
        echo "User not logged in.";
        exit;
    }

    if (empty($title) || empty($description)) {
        echo "Title and description cannot be empty.";
        exit;
    }

    if (createFeedback($userid, $title, $description)) {
        echo "Thank you for your feedback!";
    } else {
        echo "Failed to submit feedback.";
    }
} else {
    echo "Invalid request method.";
}
?>
