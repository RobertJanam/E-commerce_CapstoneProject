<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Safe Relational Database Query Mapping
$query = "SELECT p.id,
                p.name AS product_name,
                p.description AS product_description,
                p.price,
                p.category_id,
                p.image1 AS image_path_1,
                p.image2 AS image_path_2,
                p.image3 AS image_path_3,
                c.name AS category_name
          FROM products p
          LEFT JOIN categories c ON c.id = p.category_id
          ORDER BY p.id ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex Sprint - Inventory Catalog View</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/view-product.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <main class="admin-workspace">
        <div class="navigation-header-row">
            <a href="../index.php" class="back-dashboard-btn">
                <span class="arrow-icon">←</span>
                <div class="text-stack">
                    <span class="small-sub">back to dashboard</span>
                    <span class="main-heading">Global Product Tracking</span>
                </div>
            </a>
        </div>

        <div class="products-broad-grid">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($p = mysqli_fetch_assoc($result)):
                    // Filter array to pull valid image records
                    $images = array_filter([$p['image_path_1'], $p['image_path_2'], $p['image_path_3']]);
                    // Auto compensate missing images with unified UI pathing
                    $primaryImage = !empty($images) ? '../../' . reset($images) : '../../assets/images/UI/placeholder.png';
                ?>
                    <div class="broad-product-card-view">
                        <div class="media-preview-side">
                            <img src="<?php echo htmlspecialchars($primaryImage); ?>" alt="Product Canvas Track">
                        </div>
                        <div class="details-content-side">
                            <span class="badge-tag-category"><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></span>
                            <h2><?php echo htmlspecialchars($p['product_name']); ?></h2>
                            <p class="desc-para"><?php echo htmlspecialchars($p['product_description']); ?></p>
                            <div class="financial-row">KSh. <?php echo number_format($p['price'], 2); ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; color: var(--muted-gray); padding: 40px;">No deployed tracking products found inside database architecture.</div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>