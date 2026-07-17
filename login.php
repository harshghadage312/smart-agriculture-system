<?php
include 'db.php';
$error = '';

if (isset($_POST['login'])) {
    $email = clean($conn, $_POST['email']);
    $pass  = md5($_POST['password']);

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pass'");
    if (mysqli_num_rows($res) == 1) {
        $user = mysqli_fetch_assoc($res);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - AgriSmart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="login.php" class="active">Login</a>
        <a href="register.php" class="btn-nav">Register</a>
    </div>
</nav>

<div class="container">
    <div class="form-box">
        <h2>🔐 Login</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

       

        <form method="post">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" name="login" class="btn btn-green btn-full">Login</button>
        </form>

        <p style="text-align:center;margin-top:16px;font-size:0.9rem;">
            New user? <a href="register.php" style="color:#2e7d32;font-weight:600;">Register here</a>
        </p>
    </div>
</div>
</body>
</html>
