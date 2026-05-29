<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sports Gear Shop</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-card">
    <h2>Sign In</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-alert">
            <?php
                if ($_GET['error'] == 'empty') echo "Please fill in all the fields.";
                elseif ($_GET['error'] == 'badcredentials') echo "Invalid email configuration or password.";
            ?>
        </div>
    <?php endif; ?>

    <form id="loginForm" action="auth/login-process.php" method="POST">
        <div class="input-group">
            <input type="email" id="email" name="email" placeholder="Email Address">
        </div>
        <div class="input-group">
            <input type="password" id="password" name="password" placeholder="Password">
        </div>
        <button type="submit" name="login">Submit</button>
    </form>

    <div class="switch-link">
        New? <a href="register.php">Register</a>
    </div>
</div>

<script src="assets/js/login.js"></script>
</body>
</html>