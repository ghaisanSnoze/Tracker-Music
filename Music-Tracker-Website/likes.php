<?php 
include 'connection.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$my_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>My Liked Tracks</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container" style="margin-top: 40px;">
        <div class="dashboard-header glass-panel" style="margin-bottom: 20px;">
            <div>
                <h2>My <span class="text-highlight">Liked Tracks</span> ❤️</h2>
                <p style="color: #b1b9c9; font-size: 14px; margin-top: 5px;">Collection of tracks you've liked.</p>
            </div>
            <a href="index.php" class="btn btn-secondary">Back To Home</a>
        </div>

        <div class="song-grid">
            <?php
            $query = "SELECT songs.*, users.username as owner_name, users.nickname as owner_nick 
                      FROM liked_songs 
                      JOIN songs ON liked_songs.song_id = songs.id 
                      JOIN users ON songs.user_id = users.id
                      WHERE liked_songs.user_id = $my_id ORDER BY liked_songs.id DESC";
                      
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 0) {
                echo "<p style='grid-column: 1/-1; text-align: center; color: #b1b9c9; padding: 40px 0;'>You haven't liked any songs yet. Check out your friends' profiles and like some tracks!</p>";
            }

            while ($row = mysqli_fetch_assoc($result)) {
                $embed_url = "";
                if (!empty($row['spotify_url'])) {
                    $embed_url = str_replace("open.spotify.com/track/", "open.spotify.com/embed/track/", $row['spotify_url']);
                    $embed_url = explode('?', $embed_url)[0];
                }
            ?>
                <div class="song-card">
                    <div class="song-info">
                        <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p>by <?php echo htmlspecialchars($row['artist']); ?></p>
                        
                        <div style="margin-bottom: 10px; font-size: 12px; color: #27ffe9; font-style: italic;">
                            Tracked by: @<?php echo htmlspecialchars($row['owner_name']); ?>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <span class="badge badge-rating">⭐ <?php echo $row['rating']; ?>/10</span>
                            <span class="badge badge-time">⏱️ <?php echo $row['duration']; ?> Min</span>
                        </div>
                    </div>

                    <?php if (!empty($embed_url)): ?>
                        <div class="spotify-embed"><iframe src="<?php echo $embed_url; ?>" width="100%" height="80" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe></div>
                    <?php else: ?>
                        <div class="empty-spotify">No spotify link added yet 🎵</div>
                    <?php endif; ?>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>