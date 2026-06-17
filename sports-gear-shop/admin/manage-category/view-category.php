<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

$query = "SELECT c.*, COUNT(p.id) AS total_products
          FROM categories c
          LEFT JOIN products p ON c.id = p.category_id
          GROUP BY c.id ORDER BY c.name ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex Sprint - View Categories</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/view-category.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <main class="admin-workspace">
        <div class="navigation-header-row">
            <a href="../index.php" class="back-dashboard-btn">
                <span class="arrow-icon">←</span>
                <div class="text-stack">
                    <span class="small-sub">back to dashboard</span>
                    <span class="main-heading">System Categories Overview</span>
                </div>
            </a>
        </div>

        <div class="category-listing-container">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="thin-category-strip">
                        <div class="left-identity">
                            <span class="cat-name"><?php echo htmlspecialchars($row['name']); ?></span>
                            <span class="product-badge-counter"><?php echo $row['total_products']; ?> Items</span>
                        </div>
                        <div class="right-description">
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state-box">No categorical metrics configured inside database store yet.</div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>