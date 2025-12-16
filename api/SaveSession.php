<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../database/TutorSessions.php';
require_once __DIR__ . '/../utils/AI.php';

session_start();
$jsonTranscript = file_get_contents("php://input");
$conn = new mysqli(host,user,pass,db);

if ($jsonTranscript) {
    try {
        $_SESSION['ongoingTutorSession']['id'] = saveNewSession($conn, $_SESSION['loggedinUserID'], $_SESSION['ongoingTutorSession']['topic'], $jsonTranscript, $_SESSION['ongoingTutorSession']['plan']);
        $_SESSION['ongoingTutorSession']['quiz'] = json_decode(generateQuiz($_SESSION['ongoingTutorSession']['plan']), true);
        if ($_SESSION['ongoingTutorSession']['quiz'] == null) {
            throw new Exception();
        }
        echo 'saved';
    }
    catch (Exception $e) {  
        echo "Save Session Failed";
    }
} 
else {
    echo "No data received.";
}
?>