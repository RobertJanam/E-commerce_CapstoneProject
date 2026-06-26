<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sportsdb";

$conn = new mysqli($host, $user, $password, $dbname);

function ensure_activity_log_table($conn) {
    if (!$conn) {
        return false;
    }

    $createTableQuery = "CREATE TABLE IF NOT EXISTS activity_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        action_type VARCHAR(50) NOT NULL,
        item_type VARCHAR(50) NOT NULL,
        item_name VARCHAR(255) NOT NULL DEFAULT '',
        performed_by VARCHAR(100) NOT NULL DEFAULT '',
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    return mysqli_query($conn, $createTableQuery);
}

function log_admin_activity($conn, $actionType, $itemType, $itemName, $performedBy) {
    if (!$conn) {
        return false;
    }

    ensure_activity_log_table($conn);

    $actionType = trim((string) $actionType);
    $itemType = trim((string) $itemType);
    $itemName = trim((string) $itemName);
    $performedBy = trim((string) $performedBy);

    $stmt = mysqli_prepare($conn, "INSERT INTO activity_log (action_type, item_type, item_name, performed_by) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'ssss', $actionType, $itemType, $itemName, $performedBy);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $success;
}

if ($conn->connect_error) {
    error_log("Database Connection Failed: " . $conn->connect_error);
    $conn = null;
} else {
    $conn->set_charset("utf8mb4");
    ensure_activity_log_table($conn);
}
?>