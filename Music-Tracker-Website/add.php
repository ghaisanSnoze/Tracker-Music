<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['submit'])) {
    $my_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $artist = mysqli_real_escape_string($conn, $_POST['artist']);
    $rating = intval($_POST['rating']);
    $duration = intval($_POST['duration']);
    $spotify_url = mysqli_real_escape_string($conn, $_POST['spotify_url']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $query = "INSERT INTO songs (user_id, title, artist, rating, duration, spotify_url, notes) VALUES ($my_id, '$title', '$artist', '$rating', '$duration', '$spotify_url', '$notes')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Add Track - Zaazz Music Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 650px;">
        <div class="glass-panel">
            <h2 style="margin-bottom: 25px;">Add New <span class="text-highlight">Track</span> 🎵</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Track Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Ex: November Rain" required>
                </div>
                <div class="form-group">
                    <label>Artist</label>
                    <input type="text" name="artist" class="form-control" placeholder="Ex: Guns N' Roses" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Rating (1-10)</label>
                        <input type="number" name="rating" class="form-control" min="1" max="10" placeholder="10" required>
                    </div>
                    <div class="form-group">
                        <label>Listen Duration (Minutes)</label>
                        <input type="number" name="duration" class="form-control" min="1" placeholder="4" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Spotify Track Link</label>
                    <input type="url" name="spotify_url" class="form-control" placeholder="https://open.spotify.com/track/...">
                </div>
                <div class="form-group">
                    <label>Notes / Review</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="This song reminds me of..."></textarea>
                </div>
                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="submit" class="btn">Save Track</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>