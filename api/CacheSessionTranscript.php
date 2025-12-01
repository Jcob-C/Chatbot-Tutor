<?php
session_start();
$jsonTranscript = file_get_contents("php://input");

if ($jsonTranscript) {
    try {
        $_SESSION['ongoingTutorSession']['transcriptArray'] = json_decode($jsonTranscript, true);
        echo "Cached.";
    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
} 
else {
    echo "No data received.";
}
?>