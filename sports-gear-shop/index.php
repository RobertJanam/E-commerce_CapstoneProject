<?php
session_start();
// Security clearance barrier: redirect unauthenticated queries back to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Arena - Sports Gear Shop</title>
</head>
<body style="background-color: #FFF8F8; color: #000000; font-family: sans-serif; padding: 50px; text-align: center;">
    <h1 style="color: #147971;">Welcome to the Main Arena, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Your authentication workflow execution redirected successfully.</p>
    <p style="margin-top: 30px;">
        <a href="login.php" style="color: red; font-weight: bold; text-decoration: none;">Exit Session</a>
    </p>
</body>
</html>