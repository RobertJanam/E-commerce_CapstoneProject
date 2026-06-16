<?php
session_start();
require_once '../config/db.php';

if (!isset($conn) && isset($connection)) {
    $conn = $connection;
}

// Authorization check - fallback to default or restrict
$displayName = 'Admin Staff';
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $displayName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
}

// Fetch dynamic real-time production numbers
$totalProducts = 0;

if (isset($conn)) {
    $tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'products'");

    if ($tableCheck && mysqli_num_rows($tableCheck) > 0) {
        $productQuery = "SELECT COUNT(*) as total FROM products";
        $result = mysqli_query($conn, $productQuery);

        if ($result && $row = mysqli_fetch_assoc($result)) {
            $totalProducts = (int) $row['total'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APEX SPRINT Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>
<body class="admin-dashboard-layout">

    <aside class="sidebar">
        <div class="sidebar-brand">APEX<span>SPRINT</span></div>
        <nav class="sidebar-menu">
            <a href="index.php" class="active">Dashboard Overview</a>

            <div class="sidebar-dropdown">
                <button class="dropdown-btn" id="myShopToggle">
                    My Shop <span class="arrow-indicator">▼</span>
                </button>
                <div class="dropdown-container" id="myShopDropdown">
                    <a href="products.php">Products</a>
                    <a href="#" class="disabled-link">Orders</a>
                    <a href="#" class="disabled-link">Customers</a>
                </div>
            </div>

            <a href="../index.php" target="_blank">View Live Storefront</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../auth/logout.php" class="btn-logout">System Logout</a>
            <div class="user-profile">Authorized Session: <strong><?php echo $displayName; ?></strong></div>
        </div>
    </aside>

    <main class="main-content">
        <header class="content-header">
            <h1>Dashboard Overview</h1>
        </header>

        <section class="analytics-grid">
            <a href="#" class="metric-card cyan-glow clickable-card">
                <h3>Active Orders</h3>
                <div class="metric-value">12</div>
                <p class="metric-meta">Awaiting fulfillment processing</p>
            </a>

            <div class="metric-card gold-glow">
                <h3>Total Revenue</h3>
                <div class="metric-value">$14,250.00</div>
                <p class="metric-meta">Gross checkout value this cycle</p>
            </div>

            <a href="products.php" class="metric-card white-glow clickable-card">
                <h3>Products Listed</h3>
                <div class="metric-value"><?php echo $totalProducts; ?></div>
                <p class="metric-meta">Active gears inside database store</p>
            </a>

            <div class="metric-card crimson-glow alert-triggered">
                <h3>Stock Alerts</h3>
                <div class="metric-value">3</div>
                <p class="metric-meta">Items running critically below threshold</p>
            </div>
        </section>

        <section class="dashboard-details">
            <div class="data-card">
                <h2>Recent Inventory Activity</h2>
                <p class="placeholder-text">Database dynamic extraction logs will populate here once CRUD functionalities kick off.</p>
            </div>
        </section>
    </main>
    <script>
        document.getElementById('myShopToggle').addEventListener('click', function() {
            const dropdown = document.getElementById('myShopDropdown');
            const arrow = this.querySelector('.arrow-indicator');
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
                arrow.textContent = '▼';
            } else {
                dropdown.style.display = 'block';
                arrow.textContent = '▲';
            }
        });
    </script>
</body>
</html>