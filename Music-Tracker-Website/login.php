<?php
include 'connection.php';

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Unavailable username, try another one!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Zaazz Music Tracker</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <div id="loader-wrapper">
        <div class="loader"></div>
        <div class="loader-text">Loading, Please Wait :3</div>
    </div>

    <div class="container auth-wrapper">
        <div class="glass-panel auth-card">
            <h2 style="text-align: center; margin-bottom: 10px;">Zaazz Music Tracker <span class="text-highlight">Login</span> 🎵</h2>
            <p style="text-align: center; color: #8E9BAE; font-size:14px; margin-bottom: 25px;">Sign in to check your interactive vibe</p>

            <?php if($error): ?> <div class="alert"><?php echo $error; ?></div> <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username..." required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" name="login" class="btn" style="width: 100%; margin-top: 10px;">Login</button>
            </form>
            <p style="text-align: center; margin-top: 20px; font-size: 13px; color: #8E9BAE;">
                haven't got an account? <a href="register.php" class="text-highlight" style="text-decoration:none; font-weight:600;">Create one here</a>
            </p>
        </div>
    </div>
</body>
</html>