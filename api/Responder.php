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
$prompt = "The conversation topic description is: \"$topicDescription\".\n";
$prompt .= "You MUST answer ONLY within this topic.\n";
$prompt .= "If the user asks something unrelated, politely redirect them back to \"$topicTitle\".\n\n";

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
