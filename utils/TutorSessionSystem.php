<?php
require_once 'database/TutorSessions.php';

function startNewSession($topicID) {
    $_SESSION['tutorSession'] = [];
    $_SESSION['tutorSession']['topicID'] = $topicID;
    headTo('pretest.php');
}
?>
