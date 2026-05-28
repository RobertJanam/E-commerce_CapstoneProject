<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sports Gear Shop</title>
    <style>
        body {
            background-color: #FFF8F8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background: #FFFFFF;
            width: 380px;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border-top: 5px solid #147971;
        }
        h2 {
            color: #100000;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
        }
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #147971;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 15px;
            color: #000000;
            outline: none;
            transition: border-color 0.3s ease;
        }
        input:focus {
            border-color: #00FFFF;
            box-shadow: 0 0 5px rgba(0, 255, 255, 0.5);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #147971;
            color: #FFFFFF;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0d544f;
        }
        .error-alert {
            background-color: #FFCDD2;
            color: #B71C1C;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .switch-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #000000;
        }
        .switch-link a {
            color: #147971;
            text-decoration: none;
            font-weight: bold;
        }
        .switch-link a:hover {
            color: #00FFFF;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Sign In</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-alert">
            <?php
                if ($_GET['error'] == 'empty') echo "Please fill in all layout fields.";
                elseif ($_GET['error'] == 'badcredentials') echo "Invalid email configuration or password tracking entry.";
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
        <button type="submit" name="login">Login</button>
    </form>

    <div class="switch-link">
        New to the arena? <a href="register.php">Register Here</a>
    </div>
</div>

<script>
document.getElementById("loginForm").addEventListener("submit", function(e) {
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;

    // Local Browser Intervention Verification Popups
    if (email === "" || password === "") {
        e.preventDefault();
        alert("Authentication system inputs cannot be blank!");
    }
});
</script>
</body>
</html>