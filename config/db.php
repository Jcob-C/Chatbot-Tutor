<?php
const host = "localhost";
const user = "root";
const pass = "";
const db = "tutorchat";

function getConnection() {
    return new mysqli(host, user, pass, db);
}
?>