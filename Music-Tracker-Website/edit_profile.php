<?php
include 'connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$my_id = $_SESSION['user_id'];
$success = "";
$error = "";
if (isset($_POST['update_profile'])) {
    $nickname = mysqli_real_escape_string($conn, trim($_POST['nickname']));
    $bio = mysqli_real_escape_string($conn, trim($_POST['bio']));
    $fav_music = mysqli_real_escape_string($conn, trim($_POST['fav_music']));
    $fav_genre = mysqli_real_escape_string($conn, trim($_POST['fav_genre']));

    $update_query = "UPDATE users SET nickname='$nickname', bio='$bio', fav_music='$fav_music', fav_genre='$fav_genre' WHERE id=$my_id";
    
    if (mysqli_query($conn, $update_query)) {
        $success = "Profile updated successfully!";
    } else {
        $error = "ups, something went wrong with the update: " . mysqli_error($conn);
    }
}

$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $my_id");
$user = mysqli_fetch_assoc($user_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Your Identity</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container auth-wrapper">
        <div class="glass-panel auth-card" style="max-width: 550px;">
            <h2 style="margin-bottom: 5px;">Edit <span class="text-highlight">Your Identity</span> 🛸</h2>
            <p style="color: #b1b9c9; font-size: 13px; margin-bottom: 25px;">Customize how your friends see your music taste.</p>

            <?php if($success): ?> <div class="alert alert-success"><?php echo $success; ?></div> <?php endif; ?>
            <?php if($error): ?> <div class="alert"><?php echo $error; ?></div> <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Your Nickname</label>
                    <input type="text" name="nickname" class="form-control" placeholder="Ex: DJ Zaazz" value="<?php echo htmlspecialchars($user['nickname'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Short Bio</label>
                    <textarea name="bio" class="form-control" rows="3" placeholder="Tell your friends about your rhythm..." style="resize: none; font-family: inherit;"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>All Time Fav Track</label>
                        <input type="text" name="fav_music" class="form-control" placeholder="Ex: Bohemian Rhapsody" value="<?php echo htmlspecialchars($user['fav_music'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Main Genre</label>
                        <input type="text" name="fav_genre" class="form-control" placeholder="Ex: Synthwave / Rock" value="<?php echo htmlspecialchars($user['fav_genre'] ?? ''); ?>">
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 10px;">
                    <button type="submit" name="update_profile" class="btn" style="flex: 2;">Save Identity</button>
                    <a href="index.php" class="btn btn-secondary" style="flex: 1;">Back</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>