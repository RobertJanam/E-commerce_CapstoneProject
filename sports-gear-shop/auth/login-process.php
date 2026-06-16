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

    // Select all user attributes including the newly added role column
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Match verified cryptographically hashed values
        if (password_verify($password, $user['password'])) {
            // Save user metrics into persistent session runtime memory
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            // Conditional role-based routing checks
            if ($_SESSION['role'] === 'admin') {
                // Route admin directly into the isolated admin management subsystem panel
                header("Location: ../admin/index.php");
            } else {
                // Route regular customer profiles back to the primary shop landing interface
                header("Location: ../index.php");
            }
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