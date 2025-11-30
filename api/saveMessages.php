<?php
session_start();

$data = file_get_contents("php://input");

if ($data) {
    $_SESSION['tutorSession']['messages'] = json_decode($data, true);
    // $_SESSION['tutorSession']['summary'] = 
    echo "saved";
} else {
    echo "No data received.";
}
?>