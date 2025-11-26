<?php
require_once '../utils/CleanerFunctions.php';
require_once '../utils/PageBlocker.php';

session_start();
loginBlock();
redirectAdmin();
checkPost();

function checkPost() {
    if (isset($_POST['logout'])) {
        resetSession();
        headTo('login.php');
    }
    if (isset($_POST['startSession'])) {
        resetSession();
        headTo('login.php');
    }
    clearPost();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/popupMessage.css">
</head>
<body>
    <form method="post">
        <button type="submit" name="logout">Log Out</button>
    </form>  
    <form method="post">
        <button type="submit" name="startSession">Start New Session</button>
    </form>  
</body>
</html>