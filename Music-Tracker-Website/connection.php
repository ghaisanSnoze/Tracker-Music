<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_music_tracker";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection to database failed: " . mysqli_connect_error());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>