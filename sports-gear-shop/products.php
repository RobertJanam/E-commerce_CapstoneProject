<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Fetch categories that contain at least one mapped product trace loop
$categoryQuery = "SELECT DISTINCT c.* FROM categories c
                  INNER JOIN products p ON c.id = p.category_id
                  ORDER BY c.name ASC";
$categoriesResult = mysqli_query($conn, $categoryQuery);

include 'includes/header.php';
include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/products.css">
</head>
<body>

<section class="catalog-showcase-container">
    <div class="catalog-hero-header">
        <h1>PERFORMANCE CATALOG</h1>
        <p>Admin authorized distribution tracks. Pure elite configurations matching professional standards.</p>
    </div>

    <?php if(mysqli_num_rows($categoriesResult) > 0): ?>
        <?php while($cat = mysqli_fetch_assoc($categoriesResult)):
            $catId = $cat['id'];
            $productQuery = "SELECT * FROM products WHERE category_id = $catId ORDER BY id DESC";
            $productsResult = mysqli_query($conn, $productQuery);
        ?>
            <div class="category-showcase-block">
                <div class="category-block-header">
                    <h2><?php echo htmlspecialchars($cat['name']); ?></h2>
                    <span class="decorator-line"></span>
                </div>

                <div class="customer-products-grid constrained-view" id="grid_<?php echo $catId; ?>">
                    <?php while($p = mysqli_fetch_assoc($productsResult)):
                        $images = array_filter([$p['image_path_1'], $p['image_path_2'], $p['image_path_3']]);
                        $displayImg = !empty($images) ? reset($images) : 'assets/images/UI/placeholder.png';
                    ?>
                        <div class="client-product-card">
                            <div class="card-media-wrapper">
                                <img src="<?php echo $displayImg; ?>" alt="Asset Visual Track">
                            </div>
                            <div class="card-details-wrapper">
                                <h3><?php echo htmlspecialchars($p['product_name']); ?></h3>
                                <div class="price-lbl">Ksh <?php echo number_format($p['price'], 2); ?></div>
                                <a href="product-detail.php?id=<?php echo $p['id']; ?>" class="view-item-btn">Inspect Gear</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php if(mysqli_num_rows($productsResult) > 8): ?>
                    <div class="expansion-control-row">
                        <button class="expand-toggle-btn" onclick="toggleCatalogRows(this, <?php echo $catId; ?>)">
                            <span>View All Items</span>
                            <div class="hollow-chevron-down"></div>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="global-empty-state">No dynamic asset lines matching production filters found inside the database engine stack.</div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>

<script>
function toggleCatalogRows(btn, catId) {
    const grid = document.getElementById('grid_' + catId);
    if(grid.classList.contains('constrained-view')) {
        grid.classList.remove('constrained-view');
        btn.querySelector('span').textContent = 'Collapse View';
        btn.querySelector('.hollow-chevron-down').style.transform = 'rotate(-135deg)';
    } else {
        grid.classList.add('constrained-view');
        btn.querySelector('span').textContent = 'View All Items';
        btn.querySelector('.hollow-chevron-down').style.transform = 'rotate(45deg)';
    }
}
</script>
</body>
</html>