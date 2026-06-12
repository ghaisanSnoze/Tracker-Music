<?php 
include 'connection.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$my_id = $_SESSION['user_id'];
$my_username = $_SESSION['username'];

$friend_error = "";
$friend_success = "";
 
if (isset($_POST['add_friend'])) {
    $target_name = mysqli_real_escape_string($conn, trim($_POST['friend_username']));
    if ($target_name === $my_username) {
        $friend_error = "You can't add yourself as a friend!";
    } else {
        $user_query = mysqli_query($conn, "SELECT id FROM users WHERE username = '$target_name'");
        if (mysqli_num_rows($user_query) === 1) {
            $target = mysqli_fetch_assoc($user_query);
            $friend_id = $target['id'];
            $check_friend = mysqli_query($conn, "SELECT * FROM friendships WHERE (user_id = $my_id AND friend_id = $friend_id) OR (user_id = $friend_id AND friend_id = $my_id)");
            if (mysqli_num_rows($check_friend) == 0) {
                mysqli_query($conn, "INSERT INTO friendships (user_id, friend_id, status) VALUES ($my_id, $friend_id, 'accepted')");
                $friend_success = "Successfully added $target_name as a friend!";
            } else { $friend_error = "You are already friends with $target_name!"; }
        } else { $friend_error = "User not found! please try again."; }
    }
}

$me_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $my_id");
$me_data = mysqli_fetch_assoc($me_query);
$stat_query = "SELECT COUNT(*) as total_songs, SUM(duration) as total_time FROM songs WHERE user_id = $my_id";
$stats = mysqli_fetch_assoc(mysqli_query($conn, $stat_query));
$total_songs = $stats['total_songs'] ?? 0;
$total_time = $stats['total_time'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Zaazz Music Tracker</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <div id="loader-wrapper">
        <div class="loader"></div>
        <div class="loader-text">Loading, Please Wait :3</div>
    </div>

    <div class="container">
        <div class="main-layout">
            
            
            <div>
                <div class="dashboard-header glass-panel">
                    <div>
                        <h2>Zaazz Music <span class="text-highlight">Tracker</span> 🎵</h2>
                        <p style="color: #b1b9c9; font-size: 14px; margin-top: 5px;">Monitor your music tracking activity.</p>
                    </div>
                    <a href="add.php" class="btn">+ Add Track</a>
                </div>

                <div class="stats-container">
                    <div class="stat-card">
                        <h4>Total Tracked</h4>
                        <p><?php echo $total_songs; ?> <span style="font-size: 15px; font-weight:400; color:#b1b9c9;">Songs</span></p>
                    </div>
                    <div class="stat-card">
                        <h4>Total Spend Time</h4>
                        <p><?php echo $total_time; ?> <span style="font-size: 15px; font-weight:400; color:#b1b9c9;">Mins</span></p>
                    </div>
                </div>

                <!-- SEARCH BAR JAVASCRIPT -->
                <div style="margin-bottom: 25px;">
                    <input type="text" id="searchTrack" class="form-control" placeholder="🔍 Search your tracks by title or artist..." style="background: rgba(255,255,255,0.02); border: 1px solid rgba(39, 255, 233, 0.2); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                </div>

                <div class="song-grid">
                    <?php
                    $query = "SELECT * FROM songs WHERE user_id = $my_id ORDER BY id DESC";
                    $result = mysqli_query($conn, $query);
                    if (mysqli_num_rows($result) == 0) {
                        echo "<p style='grid-column: 1/-1; text-align: center; color: #b1b9c9; padding: 40px 0;'>You haven't tracked any songs yet 🎵</p>";
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
                                    <p class="empty-note">You haven't added any note yet 📝</p>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($embed_url)): ?>
                                <div class="spotify-embed"><iframe src="<?php echo $embed_url; ?>" width="100%" height="80" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe></div>
                            <?php else: ?>
                                <div class="empty-spotify">You haven't added any spotify link yet 🎵</div>
                            <?php endif; ?>

                            <div class="card-actions">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this track?');">Delete</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            
            <aside>
                <div class="glass-panel" style="padding: 25px;">
                    <div class="user-profile-section" style="border-bottom: none; margin-bottom: 0; padding-bottom: 10px;">
                        <div class="avatar"><?php echo strtoupper(substr($me_data['nickname'] ?: $my_username, 0, 1)); ?></div>
                        <div style="flex: 1;">
                            <h4 style="font-size: 16px; font-weight:600;"><?php echo htmlspecialchars($me_data['nickname'] ?: $my_username); ?></h4>
                            <p style="color: #27ffe9; font-size: 11px; font-weight:500;">@<?php echo htmlspecialchars($my_username); ?></p>
                        </div>
                        <a href="edit_profile.php" title="Edit Identity" style="color: #b1b9c9; text-decoration: none; font-size: 18px; transition: color 0.3s;" onmouseover="this.style.color='#27ffe9'" onmouseout="this.style.color='#b1b9c9'">⚙️</a>
                    </div>
                    
                    <?php if(!empty($me_data['bio'])): ?>
                        <p style="font-size: 12px; color: #b1b9c9; font-style: italic; background: rgba(0,0,0,0.15); padding: 10px; border-radius: 12px; margin-bottom: 15px; line-height: 1.4;">"<?php echo htmlspecialchars($me_data['bio']); ?>"</p>
                    <?php endif; ?>

                    <div style="display: flex; gap: 10px;">
                        <a href="likes.php" class="btn" style="flex: 1; padding:10px; font-size: 12px; text-align: center;">❤️ Liked Tracks</a>
                        <a href="logout.php" class="btn btn-danger" style="flex: 1; padding:10px; font-size: 12px; text-align: center;">Secure Logout</a>
                    </div>
                </div>

                <div class="glass-panel" style="padding: 25px;">
                    <h3>Friend <span class="text-highlight">Collaboration</span> 👥</h3>
                    <?php if($friend_error): ?> <div class="alert" style="padding:8px; font-size:12px;"><?php echo $friend_error; ?></div> <?php endif; ?>
                    <?php if($friend_success): ?> <div class="alert alert-success" style="padding:8px; font-size:12px;"><?php echo $friend_success; ?></div> <?php endif; ?>

                    <form method="POST" action="" style="margin-bottom: 20px;">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <input type="text" name="friend_username" class="form-control" placeholder="Enter friend's username..." style="padding:10px 15px; font-size:13px;" required>
                        </div>
                        <button type="submit" name="add_friend" class="btn" style="width:100%; padding:10px; font-size:12px;">+ Connect Friend</button>
                    </form>

                    <h4 style="font-size:12px; color:#b1b9c9; text-transform:uppercase; letter-spacing:0.5px;">Connected Friends</h4>
                    <ul class="friend-list">
                        <?php
                        $friends_query = mysqli_query($conn, "
                            SELECT users.id, users.username, users.nickname FROM friendships 
                            JOIN users ON (friendships.friend_id = users.id AND friendships.user_id = $my_id) 
                                       OR (friendships.user_id = users.id AND friendships.friend_id = $my_id) 
                            WHERE friendships.status = 'accepted' AND users.id != $my_id
                        ");
                        if(mysqli_num_rows($friends_query) == 0) { echo "<p style='font-size:13px; color:#b1b9c9; font-style:italic; margin-top:10px;'>You don't have any friends yet.</p>"; }
                        while($friend = mysqli_fetch_assoc($friends_query)) {
                        ?>
                            <li class="friend-item">
                                <a href="profile.php?id=<?php echo $friend['id']; ?>" class="friend-link">
                                    <div class="avatar" style="width:30px; height:30px; font-size:12px; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); box-shadow:none; color:white;">
                                        <?php echo strtoupper(substr($friend['nickname'] ?: $friend['username'], 0, 1)); ?>
                                    </div>
                                    <span><?php echo htmlspecialchars($friend['nickname'] ?: $friend['username']); ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </aside>

        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>