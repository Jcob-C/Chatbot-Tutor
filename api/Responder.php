<?php
require_once __DIR__ . '/../utils/GeminiAPI.php';
require_once __DIR__ . '/../utils/database/Topics.php';

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$chatHistory = $data['chatHistory'] ?? [];
$newMessage  = $data['newMessage'] ?? '';
$topicID = $data['topicID'] ?? 'Unknown Topic ID';
$topicTitle  = $data['topicTitle'] ?? 'Unknown Topic';
$topicDescription = getTopicDescription($topicID);

// Build strict topic-focused prompt
$prompt = "You are an AI tutor. The conversation topic is: \"$topicTitle\".\n";
$prompt .= "The topic's description is: \"$topicDescription\".\n";
$prompt .= "You MUST answer ONLY within this topic.\n";
$prompt .= "If the user asks something unrelated, politely redirect them back to the topic: \"$topicTitle\".\n";
$prompt .= "If possible, end your response with possible questions the user can ask regarding the topic.\n";
$prompt .= "Use markdown formatting to create a more readable information.\n";
$prompt .= "Don't respond with long and detailed answers, let the user ask before doing so.\n";
$prompt .= "Be simple with words.\n\n";

$prompt .= "Conversation so far:\n";

foreach ($chatHistory as $entry) {
    $role = $entry['role'] === 'user' ? 'User' : 'Assistant';
    $prompt .= "$role: " . $entry['message'] . "\n";
}

$prompt .= "\nUser: $newMessage\nAssistant:";

// Generate response
try {
    echo generateText($prompt);
} 
catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
