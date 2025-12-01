<?php
require_once __DIR__ . '/../utils/GeminiAPI.php';
require_once __DIR__ . '/../database/Topics.php';
require_once __DIR__ . '/../config/db.php';

session_start();
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$conn = new mysqli(host, user, pass, db);

$chatHistory = $data['chatHistory'];
$newMessage  = $data['newMessage'];
$topicTitle  = getTopicTitle($conn, $_SESSION['ongoingTutorSession']['topicID']);

$prompt = "
You are an AI tutor. The conversation topic is: \"$topicTitle\".
You MUST answer ONLY within this topic.
If the user asks something unrelated, politely redirect them back to the topic: \"$topicTitle\".
If possible, end your response with possible questions the user can ask regarding the topic.
Use markdown formatting to create a more readable information.
Use indents, lists, pointers, and headers for better readability.
Be simple with words and try to keep the conversation going.

Conversation so far:
";

foreach ($chatHistory as $entry) {
    $role = $entry['role'] === 'user' ? 'User' : 'Assistant';
    $prompt .= "$role: " . $entry['message'] . "\n";
}

$prompt .= "\nUser: $newMessage\nAssistant:";

try {
    echo generateText($prompt);
} 
catch (Exception $e) {
    echo $e->getMessage();
}
