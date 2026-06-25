<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Fetch distinct categories containing operational tracking loops
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
        <h1>PRODUCT CATALOG</h1>
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
                    <div class="accent-line"></div>
                </div>

                <div class="products-grid-layout constrained-view" id="grid_<?php echo $catId; ?>">
                    <?php while($p = mysqli_fetch_assoc($productsResult)):
                        // Filter out empty rows to isolate real image assets
                        $productImages = array_filter([$p['image1'], $p['image2'], $p['image3']]);
                        $productImages = array_values(array_map(function ($img) {
                            if (empty($img)) {
                                return '';
                            }

                            return ltrim($img, '/');
                        }, $productImages));
                        $productImages = array_filter($productImages);

                        $primaryImage = 'assets/images/UI/placeholder.png';
                        foreach ($productImages as $candidateImage) {
                            $candidatePath = __DIR__ . '/' . $candidateImage;
                            if (file_exists($candidatePath)) {
                                $primaryImage = $candidateImage;
                                break;
                            }
                        }

                        if (!empty($productImages) && $primaryImage === 'assets/images/UI/placeholder.png') {
                            $productImages = ['assets/images/UI/placeholder.png'];
                        }

                        $totalImages = count($productImages);
                        $uniqueCardId = "slider_" . $p['id'];
                    ?>
                        <div class="catalog-product-card">
                            <div class="card-media-wrapper" id="<?php echo $uniqueCardId; ?>">
                                <div class="slider-canvas-track">
                                    <?php if(!empty($productImages)): ?>
                                        <?php foreach($productImages as $index => $imgSrc): ?>
                                            <?php $isActive = ($imgSrc === $primaryImage && $index === array_search($primaryImage, $productImages, true)); ?>
                                            <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                                                 class="slide-frame <?php echo $isActive ? 'active-frame' : ''; ?>"
                                                 alt="Product Asset View">
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <img src="assets/images/UI/placeholder.png" class="slide-frame active-frame" alt="Product Asset View">
                                    <?php endif; ?>
                                </div>

                                <?php if($totalImages > 1): ?>
                                    <button class="slider-arrow prev" onclick="shiftSlide('<?php echo $uniqueCardId; ?>', -1)">&#10094;</button>
                                    <button class="slider-arrow next" onclick="shiftSlide('<?php echo $uniqueCardId; ?>', 1)">&#10095;</button>
                                    <div class="slider-bullets-indicator">
                                        <?php foreach($productImages as $index => $tmp): ?>
                                            <span class="bullet-dot <?php echo $index === 0 ? 'active-dot' : ''; ?>"></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-details-wrapper">
                                <span class="p-category-lbl"><?php echo htmlspecialchars($cat['name']); ?></span>
                                <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                                <p class="catalog-brief-desc"><?php echo htmlspecialchars($p['description']); ?></p>

                                <div class="p-card-footer-row">
                                    <span class="price-lbl">Ksh <?php echo number_format($p['price'], 2); ?></span>
                                    <button class="add-to-cart-btn" title="Add to Cart">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php if(mysqli_num_rows($productsResult) > 4): ?>
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
function shiftSlide(containerId, offset) {
    const container = document.getElementById(containerId);
    const slides = container.querySelectorAll('.slide-frame');
    const dots = container.querySelectorAll('.bullet-dot');
    if (slides.length <= 1) return;

    let currentIdx = 0;
    slides.forEach((slide, idx) => {
        if(slide.classList.contains('active-frame')) {
            currentIdx = idx;
        }
    });

    // Remove active markers
    slides[currentIdx].classList.remove('active-frame');
    if(dots.length) dots[currentIdx].classList.remove('active-dot');

    // Compute circular array bounds
    let nextIdx = currentIdx + offset;
    if (nextIdx >= slides.length) nextIdx = 0;
    if (nextIdx < 0) nextIdx = slides.length - 1;

    // Attach new active frame visibility matrix
    slides[nextIdx].classList.add('active-frame');
    if(dots.length) dots[nextIdx].classList.add('active-dot');
}

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