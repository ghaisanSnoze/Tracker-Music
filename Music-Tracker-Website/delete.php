<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id']);
$my_id = $_SESSION['user_id'];

$query = "DELETE FROM songs WHERE id = $id AND user_id = $my_id";

if (mysqli_query($conn, $query)) {
    header("Location: index.php");
    exit;
} else {
    echo "Error deleting track: " . mysqli_error($conn);
}
?>