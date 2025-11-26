<?php
function headTo($destination) {
    header("Location: $destination");
    exit;
}

function clearPost() {
    if (!empty($_POST)) {
        headTo($_SERVER['PHP_SELF']);
    }
}

function cleanHTML($text) {
    return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
}

function resetSession() {
    $_SESSION = [];
    session_destroy();
}
?>