<?php
function startNewSession($topicID) {
    $_SESSION['ongoingTutorSession'] = [];
    $_SESSION['ongoingTutorSession']['topicID'] = $topicID;
    header("Location: ../page/chat.php"); exit;
}
?>