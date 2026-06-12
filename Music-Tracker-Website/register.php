<?php
include 'connection.php';

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $check_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check_user) > 0) {
            $error = "Username is already taken. Please choose another one.";
        } else {
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
            if (mysqli_query($conn, $query)) {
                $success = "Account created successfully! Please login.";
            } else {
                $error = "Failed to register. Database error.";
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Register - Zaazz Music Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container auth-wrapper">
        <div class="glass-panel auth-card">
            <h2 style="text-align: center; margin-bottom: 10px;">Create <span class="text-highlight">Account</span> 👤</h2>
            <p style="text-align: center; color: #8E9BAE; font-size:14px; margin-bottom: 25px;">Join the Zaazz Music Tracker</p>

            <?php if($error): ?> <div class="alert"><?php echo $error; ?></div> <?php endif; ?>
            <?php if($success): ?> <div class="alert alert-success"><?php echo $success; ?></div> <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username..." required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" name="register" class="btn" style="width: 100%; margin-top: 10px;">Register</button>
            </form>
            <p style="text-align: center; margin-top: 20px; font-size: 13px; color: #8E9BAE;">
                Already have an account? <a href="login.php" class="text-highlight" style="text-decoration:none; font-weight:600;">Login</a>
            </p>
        </div>
    </div>
</body>
</html>