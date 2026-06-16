<?php
session_start();
require_once dirname(__DIR__) . '/config/db.php';

if (isset($_POST['admin_register'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Validate inputs are not empty
    if (empty($fullname) || empty($email) || empty($password)) {
        header("Location: ../admin_register.php?error=emptyfields");
        exit();
    }

    // Verify system constraints for email duplication
    $check_user = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_user);
    if (mysqli_num_rows($result) > 0) {
        header("Location: ../admin_register.php?error=userexists");
        exit();
    }

    // Establish dynamic data protection using server hashing
    $secure_password = password_hash($password, PASSWORD_BCRYPT);

    // Modified query to explicitly assign the 'admin' role
    $query = "INSERT INTO users (fullname, email, password, role) VALUES ('$fullname', '$email', '$secure_password', 'admin')";

    if (mysqli_query($conn, $query)) {
        // Establish an active session state for the newly registered admin
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['user_name'] = $fullname;
        $_SESSION['role'] = 'admin';

        // Redirect to the login page with a success message
        header("Location: ../login.php?registration=success");
        exit();
    } else {
        header("Location: ../admin_register.php?error=dbfailed");
        exit();
    }
} else {
    header("Location: ../admin_register.php");
    exit();
}
?>