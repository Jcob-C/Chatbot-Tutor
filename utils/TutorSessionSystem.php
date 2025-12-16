<?php
require_once __DIR__ . '/AI.php';
require_once __DIR__ . '/PopupMessage/.php';
require_once __DIR__ . '/../database/Topics.php';
require_once __DIR__ . '/../database/TutorSessions.php';

function startNewSession($topicTitle, $conn) {
    $_SESSION['ongoingTutorSession'] = [];
    $_SESSION['ongoingTutorSession']['topic'] = $topicTitle;
    try {
        $plan = getTopicPlan($conn, $topicTitle);
        if (!$plan) {
            $plan = getSessionPlan($conn, $topicTitle);
        }
        if ($plan) {
            $_SESSION['ongoingTutorSession']['plan'] = $plan;
        }
        else {
            $_SESSION['ongoingTutorSession']['plan'] = generateLessonPlan($topicTitle);
        }
        header("Location: ../page/tutor.php"); exit;
    }
    catch (Exception $e) {
        setPopupMessage("Failed to Generate a Lesson Plan");
        $_SESSION['ongoingTutorSession'] = [];
        return;
    }
}

function resetTutorSession() {
    $_SESSION['ongoingTutorSession'] = [];
    unset($_SESSION['ongoingTutorSession']);
}
?>