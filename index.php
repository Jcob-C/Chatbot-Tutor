<?php 
echo password_hash("12345678", PASSWORD_DEFAULT); exit;
header("Location: page/login.php"); exit; 
?>