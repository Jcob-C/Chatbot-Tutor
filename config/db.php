<?php
const host = "localhost";
const user = "root";
const pass = "";
const db = "chatbot_tutor";

function getConnection() {
    return new mysqli(host, user, pass, db);
}
?>