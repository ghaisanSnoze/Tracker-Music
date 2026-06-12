<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id']);
$my_id = $_SESSION['user_id'];
$query = "SELECT * FROM songs WHERE id = $id AND user_id = $my_id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $artist = mysqli_real_escape_string($conn, $_POST['artist']);
    $rating = intval($_POST['rating']);
    $duration = intval($_POST['duration']);
    $spotify_url = mysqli_real_escape_string($conn, $_POST['spotify_url']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $update_query = "UPDATE songs SET title='$title', artist='$artist', rating='$rating', duration='$duration', spotify_url='$spotify_url', notes='$notes' WHERE id=$id AND user_id=$my_id";
    
    if (mysqli_query($conn, $update_query)) {
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
    <title>Edit Track - Deep Space</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 650px;">
        <div class="glass-panel">
            <h2 style="margin-bottom: 25px;">Edit Your <span class="text-highlight">Track</span> 📝</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Track Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($data['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Artist</label>
                    <input type="text" name="artist" class="form-control" value="<?php echo htmlspecialchars($data['artist']); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Rating (1-10)</label>
                        <input type="number" name="rating" class="form-control" min="1" max="10" value="<?php echo $data['rating']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Listen Duration (Minutes)</label>
                        <input type="number" name="duration" class="form-control" min="1" value="<?php echo $data['duration']; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Spotify Track Link</label>
                    <input type="url" name="spotify_url" class="form-control" value="<?php echo htmlspecialchars($data['spotify_url']); ?>">
                </div>
                <div class="form-group">
                    <label>Notes / Review</label>
                    <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($data['notes']); ?></textarea>
                </div>
                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="submit" class="btn">Update Track</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>