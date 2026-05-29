<?php
session_start();
require_once dirname(__DIR__) . '/config/db.php';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../login.php?error=empty");
        exit();
    }

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Match verified cryptographically hashed values
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];

            // Route seamlessly to production system dashboard route
            header("Location: ../index.php");
            exit();
        } else {
            header("Location: ../login.php?error=badcredentials");
            exit();
        }
    } else {
        header("Location: ../login.php?error=badcredentials");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>