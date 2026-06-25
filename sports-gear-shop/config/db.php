<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sportsdb";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    error_log("Database Connection Failed: " . $conn->connect_error);
    $conn = null;
} else {
    $conn->set_charset("utf8mb4");
}
?>