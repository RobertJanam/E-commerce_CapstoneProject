<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sportsdb";

// establishing connection using standard procedural mysqli
$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>