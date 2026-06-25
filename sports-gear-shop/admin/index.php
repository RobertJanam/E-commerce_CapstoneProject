<?php
session_start();
require_once '../config/db.php';

if (!isset($conn) && isset($connection)) {
    $conn = $connection;
}

$displayName = 'Admin Staff';
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $displayName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
}

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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<div class="app-container">
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="sidebar-header">
                <div class="logo">APEX<span class="logo-accent">SPRINT</span></div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="index.php" class="menu-item active">
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="dropdown-level-1">
                    <button class="dropdown-btn" onclick="toggleMenu('inventoryDropdown', this)">
                        <span>Manage Inventory</span>
                        <span class="arrow-indicator">▼</span>
                    </button>

                    <ul id="inventoryDropdown" class="submenu level-1-menu">

                        <li class="dropdown-level-2">
                            <button class="dropdown-sub-btn" onclick="toggleMenu('productsDropdown', this)">
                                <span>Manage Products</span>
                                <span class="arrow-indicator">▼</span>
                            </button>
                            <ul id="productsDropdown" class="submenu level-2-menu">
                                <li><a href="products.php"> Add Product</a></li>
                                <li><a href="manage-product/view-product.php">View Products</a></li>
                                <li><a href="manage-product/update-product.php">Update Product</a></li>
                                <li><a href="manage-product/remove-product.php">Remove Product</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-level-2">
                            <button class="dropdown-sub-btn" onclick="toggleMenu('categoriesDropdown', this)">
                                <span>Manage Categories</span>
                                <span class="arrow-indicator">▼</span>
                            </button>
                            <ul id="categoriesDropdown" class="submenu level-2-menu">
                                <li><a href="manage-category/add-category.php">Add Category</a></li>
                                <li><a href="manage-category/view-category.php">View Categories</a></li>
                                <li><a href="manage-category/update-category.php">Update Category</a></li>
                                <li><a href="manage-category/remove-category.php">Remove Category</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-level-2">
                            <button class="dropdown-sub-btn" onclick="toggleMenu('accessDropdown', this)">
                                <span>Management Access</span>
                                <span class="arrow-indicator">▼</span>
                            </button>
                            <ul id="accessDropdown" class="submenu level-2-menu">
                                <li><a href="manage-access/add-access.php">Add Access</a></li>
                                <li><a href="manage-access/view-access.php">View Access Logs</a></li>
                                <li><a href="manage-access/update-access.php">Edit Permissions</a></li>
                                <li><a href="manage-access/remove-access.php">Revoke Access</a></li>
                            </ul>
                        </li>

                    </ul>
                </li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="user-profile">
                <span class="user-name"><?php echo $displayName; ?></span>
                <a href="../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="main-header">
            <h1>Dashboard Overview</h1>
            <p class="welcome-meta">Real-time status updates and execution matrices.</p>
        </header>

        <section class="metrics-grid">
            <div class="metric-card crimson-glow">
                <h3>Active Orders</h3>
                <div class="metric-value">0</div>
                <p class="metric-meta">Awaiting fulfillment processing</p>
            </div>

            <div class="metric-card gold-glow">
                <h3>Total Revenue</h3>
                <div class="metric-value">KSh 0</div>
                <p class="metric-meta">Gross checkout value this cycle</p>
            </div>

            <div class="metric-card white-glow">
                <h3>Products Listed</h3>
                <div class="metric-value"><?php echo $totalProducts; ?></div>
                <p class="metric-meta">Active gears inside database store</p>
            </div>

            <div class="metric-card crimson-glow alert-triggered">
                <h3>Stock Alerts</h3>
                <div class="metric-value">0</div>
                <p class="metric-meta">Items running critically below threshold</p>
            </div>
        </section>

        <section class="dashboard-details">
            <div class="data-card">
                <h2>Recent Activity</h2>
                <p class="placeholder-text">Database logs.</p>
            </div>
        </section>
    </main>
</div>

<script>
    function toggleMenu(menuId, buttonElement) {
        const submenu = document.getElementById(menuId);
        if (submenu) {
            submenu.classList.toggle('open');
        }
        if (buttonElement) {
            buttonElement.classList.toggle('active');
        }
    }
</script>
</body>
</html>