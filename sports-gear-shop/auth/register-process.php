<?php
session_start();
require_once dirname(__DIR__) . '/config/db.php';

if (isset($_POST['register'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($fullname) || empty($email) || empty($password)) {
        header("Location: ../register.php?error=emptyfields");
        exit();
    }

    $check_user = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_user);
    if (mysqli_num_rows($result) > 0) {
        header("Location: ../register.php?error=userexists");
        exit();
    }

    $secure_password = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO users (fullname, email, password) VALUES ('$fullname', '$email', '$secure_password')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['user_name'] = $fullname;

        header("Location: ../index.php");
        exit();
    } else {
        header("Location: ../register.php?error=dbfailed");
        exit();
    }
} else {
    header("Location: ../register.php");
    exit();
}
?>