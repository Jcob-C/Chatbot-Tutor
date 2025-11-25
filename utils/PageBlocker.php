<?php
require_once 'database/Users.php';

function checkUserID() {
    if (!isset($_SESSION['userID']) || false == checkActivated($_SESSION['userID'])) {
        header("Location: login.php");
        exit;
    }
}

function checkUserRole() {
    
}

function checkUserVerification() {

}

function checkLastSession() {
    
}
?>