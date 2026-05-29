<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sports Gear Shop</title>
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>

<div class="register-card">
    <h2>Create Account</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-alert">
            <?php
                if ($_GET['error'] == 'emptyfields') echo "All layout inputs are mandatory.";
                elseif ($_GET['error'] == 'userexists') echo "This account entity already exists.";
                elseif ($_GET['error'] == 'dbfailed') echo "Execution error. Please try again.";
            ?>
        </div>
    <?php endif; ?>

    <form id="regForm" action="auth/register-process.php" method="POST">
        <div class="input-group"><input type="text" id="fullname" name="fullname" placeholder="Full Name"></div>
        <div class="input-group"><input type="email" id="email" name="email" placeholder="Email Address"></div>
        <div class="input-group"><input type="password" id="password" name="password" placeholder="Password (Min 6 chars)"></div>
        <div class="input-group"><input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password"></div>
        <button type="submit" name="register">Submit</button>
    </form>

    <div class="switch-link">
        <a href="login.php">Login with password</a>
    </div>
</div>

<script src="assets/js/register.js"></script>
</body>
</html>