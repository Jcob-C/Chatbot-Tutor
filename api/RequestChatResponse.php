<?php
require_once __DIR__ . '/../utils/AI.php';

session_start();

try {
    $lessonPlan = $_SESSION['ongoingTutorSession']['plan'];

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);
    
    $currentSection = $data['section'];
    $aiMessage = $data['aimessage'];
    $userMessage = $data['usermessage'];

    echo generateChatResponse($lessonPlan, $currentSection, $aiMessage, $userMessage);
} 
catch (Exception $e) {
    echo "AI Response Failed";
}