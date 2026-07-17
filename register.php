<?php
include 'db.php';
$error = '';
$success = '';

if (isset($_POST['register'])) {
    $name  = clean($conn, $_POST['name']);
    $email = clean($conn, $_POST['email']);
    $phone = clean($conn, $_POST['phone']);
    $role  = in_array($_POST['role'], ['farmer', 'buyer']) ? $_POST['role'] : 'farmer';
    $pass  = md5($_POST['password']);

    // check duplicate email
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered. Please login.";
    } else {
        mysqli_query($conn, "INSERT INTO users(name,email,password,phone,role) VALUES('$name','$email','$pass','$phone','$role')");
        $success = "Registration successful! <a href='login.php'>Login here</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - AgriSmart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="brand">🌾 AgriSmart</a>
    <div class="nav-links">
        <a href="login.php">Login</a>
        <a href="register.php" class="btn-nav">Register</a>
    </div>
</nav>

<div class="container">
    <div class="form-box">
        <h2>📝 Create Account</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="Enter phone number">
            </div>
            <div class="form-group">
                <label>Register As</label>
                <select name="role" required>
                    <option value="farmer">🌾 Farmer (buy & sell)</option>
                    <option value="buyer">🛒 Buyer (buy only)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create password" required>
            </div>
            <button type="submit" name="register" class="btn btn-green btn-full">Register</button>
        </form>

        <p style="text-align:center;margin-top:16px;font-size:0.9rem;">
            Already have an account? <a href="login.php" style="color:#2e7d32;font-weight:600;">Login</a>
        </p>
    </div>
</div>
</body>
</html>
