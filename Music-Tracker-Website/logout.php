<?php
include 'connection.php';
session_destroy();
header("Location: login.php");
exit;
?>