<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$friend_id = intval($_GET['id']);
$my_id = $_SESSION['user_id'];

if (isset($_GET['action']) && isset($_GET['song_id'])) {
    $action = $_GET['action'];
    $target_song_id = intval($_GET['song_id']);

    $check_song = mysqli_query($conn, "SELECT * FROM songs WHERE id = $target_song_id");
    if (mysqli_num_rows($check_song) > 0) {
        $song_data = mysqli_fetch_assoc($check_song);
        
        if ($action == 'clone') {
            $title = mysqli_real_escape_string($conn, $song_data['title']);
            $artist = mysqli_real_escape_string($conn, $song_data['artist']);
            $rating = $song_data['rating'];
            $duration = $song_data['duration'];
            $spotify_url = mysqli_real_escape_string($conn, $song_data['spotify_url']);
            $notes = mysqli_real_escape_string($conn, $song_data['notes']);
            
            mysqli_query($conn, "INSERT INTO songs (user_id, title, artist, rating, duration, spotify_url, notes) 
                                 VALUES ($my_id, '$title', '$artist', $rating, $duration, '$spotify_url', '$notes')");
        } elseif ($action == 'like') {
            
            $check_like = mysqli_query($conn, "SELECT * FROM liked_songs WHERE user_id = $my_id AND song_id = $target_song_id");
            if (mysqli_num_rows($check_like) == 0) {
                mysqli_query($conn, "INSERT INTO liked_songs (user_id, song_id) VALUES ($my_id, $target_song_id)");
            }
        } elseif ($action == 'unlike') {
            mysqli_query($conn, "DELETE FROM liked_songs WHERE user_id = $my_id AND song_id = $target_song_id");
        }
    }
    header("Location: profile.php?id=$friend_id");
    exit;
}

$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $friend_id");
$user_data = mysqli_fetch_assoc($user_query);

if (!$user_data) {
    header("Location: index.php");
    exit;
}

$stat_query = "SELECT COUNT(*) as total_songs, SUM(duration) as total_time FROM songs WHERE user_id = $friend_id";
$stats = mysqli_fetch_assoc(mysqli_query($conn, $stat_query));
$total_songs = $stats['total_songs'] ?? 0;
$total_time = $stats['total_time'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title><?php echo htmlspecialchars($user_data['nickname'] ?: $user_data['username']); ?>'s Station</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <div id="loader-wrapper">
        <div class="loader"></div>
        <div class="loader-text">Loading, Please Wait :3</div>
    </div>

    <div class="container" style="margin-top: 40px;">
        <div class="dashboard-header glass-panel" style="margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div class="avatar" style="width: 70px; height: 70px; font-size: 28px;">
                    <?php echo strtoupper(substr($user_data['nickname'] ?: $user_data['username'], 0, 1)); ?>
                </div>
                <div>
                    <h2><span class="text-highlight"><?php echo htmlspecialchars($user_data['nickname'] ?: $user_data['username']); ?></span>'s Profile 👤</h2>
                    <p style="color: #b1b9c9; font-size: 14px; margin-top: 5px;">@<?php echo htmlspecialchars($user_data['username']); ?> — Music Tracker Dashboard.</p>
                </div>
            </div>
            <a href="index.php" class="btn btn-secondary">Back To Home</a>
        </div>

        <div class="glass-panel" style="padding: 25px; margin-bottom: 25px; display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            <div style="border-right: 1px solid rgba(255,255,255,0.08); padding-right: 20px;">
                <h4 style="font-size: 13px; color: #27ffe9; text-transform: uppercase; margin-bottom: 10px; letter-spacing: 0.5px;"><span> <?php echo htmlspecialchars($user_data['nickname'] ?: $user_data['username']); ?></span>'s Bio</h4>
                <p style="font-size: 14px; color: #ffffff; font-style: italic; line-height: 1.6;">
                    <?php echo !empty($user_data['bio']) ? '"'.htmlspecialchars($user_data['bio']).'"' : "This user haven't added a bio yet 📝"; ?>
                </p>
            </div>
            <div>
                <h4 style="font-size: 13px; color: #cc99ff; text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px;">Music DNA</h4>
                <div style="font-size: 14px; color: #b1b9c9;">
                    <p style="margin-bottom: 8px;">🎵 <span style="color: white; font-weight: 600;">Fav Track:</span> <?php echo htmlspecialchars($user_data['fav_music'] ?: 'Not defined yet'); ?></p>
                    <p>⚡ <span style="color: white; font-weight: 600;">Core Genre:</span> <?php echo htmlspecialchars($user_data['fav_genre'] ?: 'Not defined yet'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <h4>Tracked by Him/Her</h4>
                <p><?php echo $total_songs; ?> <span style="font-size: 16px; font-weight: 400; color: #b1b9c9;">Songs</span></p>
            </div>
            <div class="stat-card">
                <h4>Listening Vibe Time</h4>
                <p><?php echo $total_time; ?> <span style="font-size: 16px; font-weight: 400; color: #b1b9c9;">Mins</span></p>
            </div>
        </div>

        <h3 style="margin-bottom: 25px; font-size: 20px;">Current <span class="text-highlight">Listening History</span> 🪐</h3>
        
        <div class="song-grid">
            <?php
            $query = "SELECT * FROM songs WHERE user_id = $friend_id ORDER BY id DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 0) {
                echo "<p style='grid-column: 1/-1; text-align: center; color: #b1b9c9; padding: 40px 0;'>This user hasn't tracked any songs yet 🎵</p>";
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
                        
                        <div style="margin-bottom: 15px;">
                            <span class="badge badge-rating">⭐ <?php echo $row['rating']; ?>/10</span>
                            <span class="badge badge-time">⏱️ <?php echo $row['duration']; ?> Min</span>
                        </div>
                        
                        <?php if (!empty(trim($row['notes']))): ?>
                            <p class="review-text">"<?php echo htmlspecialchars($row['notes']); ?>"</p>
                        <?php else: ?>
                            <p class="empty-note">No note added yet 📝</p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($embed_url)): ?>
                        <div class="spotify-embed"><iframe src="<?php echo $embed_url; ?>" width="100%" height="80" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe></div>
                    <?php else: ?>
                        <div class="empty-spotify">No spotify link added yet 🎵</div>
                    <?php endif; ?>

                    
                    <div class="card-actions" style="margin-top: 15px; display: flex; gap: 10px;">
                        <?php
                        $like_check = mysqli_query($conn, "SELECT * FROM liked_songs WHERE user_id = $my_id AND song_id = " . $row['id']);
                        $is_liked = mysqli_num_rows($like_check) > 0;
                        ?>
                        
                        <?php if($is_liked): ?>
                            <a href="profile.php?id=<?php echo $friend_id; ?>&action=unlike&song_id=<?php echo $row['id']; ?>" 
                               class="btn" 
                               style="background: rgba(255, 71, 126, 0.15); color: #ff477e; border: 2px solid #ff477e; box-shadow: 0 4px 15px rgba(255, 71, 126, 0.4); flex: 1; text-align:center; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; height: 42px; text-decoration: none; transition: all 0.2s;" 
                               onmouseover="this.innerHTML='💔 Unlike'; this.style.background='rgba(255, 71, 126, 0.3)';" 
                               onmouseout="this.innerHTML='❤️ Liked'; this.style.background='rgba(255, 71, 126, 0.15)';">
                               ❤️ Liked
                            </a>
                        <?php else: ?>
                            <a href="profile.php?id=<?php echo $friend_id; ?>&action=like&song_id=<?php echo $row['id']; ?>" 
                               class="btn" 
                               style="background: transparent; color: #ff477e; border: 2px solid #ff477e; box-shadow: 0 4px 15px rgba(255, 71, 126, 0.15); flex: 1; text-align:center; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; height: 42px; text-decoration: none;">
                               🤍 Like
                            </a>
                        <?php endif; ?>
                        
                        <a href="profile.php?id=<?php echo $friend_id; ?>&action=clone&song_id=<?php echo $row['id']; ?>" class="btn" style="flex: 1; text-align:center; padding: 0 5px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; height: 42px; text-decoration: none;" onclick="return confirm('Clone this song to your tracker? It will appear on your Home page!');">➕ Steal Track</a>
                    </div>

                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>