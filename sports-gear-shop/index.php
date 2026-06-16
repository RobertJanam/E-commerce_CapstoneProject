<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="hero-section">
    <div class="hero-content">
        <span class="hero-tag">New Season Arrivals</span>
        <h1>UNLEASH YOUR<br>INNER ATHLETE</h1>
        <p>Discover professional grade sports gear, engineered garments, and elite footwear optimized for maximum performance.</p>
        <div class="hero-cta">
            <a href="products.php" class="btn-primary">Shop Latest</a>
            <a href="products.php?filter=trending" class="btn-secondary">Explore Trending</a>
        </div>
    </div>
</section>

<section class="categories-section">
    <div class="section-header">
        <h2>Shop By Category</h2>
        <p>Find specialized gear tailored to your training demands</p>
    </div>

    <div class="category-grid">
        <a href="products.php?category=footwear" class="category-card">
            <div class="category-img-placeholder">
                <i class="fa-solid fa-shoe-prints"></i>
            </div>
            <div class="category-info">
                <h3>Elite Footwear</h3>
                <span>View Items <i class="fa-solid fa-arrow-right"></i></span>
            </div>
        </a>

        <a href="products.php?category=apparel" class="category-card">
            <div class="category-img-placeholder">
                <i class="fa-solid fa-shirt"></i>
            </div>
            <div class="category-info">
                <h3>Performance Apparel</h3>
                <span>View Items <i class="fa-solid fa-arrow-right"></i></span>
            </div>
        </a>

        <a href="products.php?category=equipment" class="category-card">
            <div class="category-img-placeholder">
                <i class="fa-solid fa-dumbbell"></i>
            </div>
            <div class="category-info">
                <h3>Gear & Equipment</h3>
                <span>View Items <i class="fa-solid fa-arrow-right"></i></span>
            </div>
        </a>
    </div>
</section>

<section class="products-section">
    <div class="section-header">
        <h2>Trending Now</h2>
        <p>The highly requested choices across the community this week</p>
    </div>

    <div class="products-grid">
        <div class="product-card">
            <div class="product-badge">Hot</div>
            <div class="product-img-box">
                <i class="fa-solid fa-socks product-icon-placeholder"></i>
            </div>
            <div class="product-details">
                <span class="p-category">(Category)</span>
                <h3 class="p-title">(Product)</h3>
                <div class="p-rating">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <span>(0)</span>
                </div>
                <div class="p-footer">
                    <span class="p-price">Ksh 0</span>
                    <button class="add-to-cart-btn" title="Add to Cart">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="product-card">
            <div class="product-img-box">
                <i class="fa-solid fa-shirt product-icon-placeholder"></i>
            </div>
            <div class="product-details">
                <span class="p-category">(Category)</span>
                <h3 class="p-title">(Product)</h3>
                <div class="p-rating">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <span>(0)</span>
                </div>
                <div class="p-footer">
                    <span class="p-price">Ksh 0</span>
                    <button class="add-to-cart-btn" title="Add to Cart">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="product-card">
            <div class="product-img-box">
                <i class="fa-solid fa-basketball product-icon-placeholder"></i>
            </div>
            <div class="product-details">
                <span class="p-category">(Category)</span>
                <h3 class="p-title">(Product)</h3>
                <div class="p-rating">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <span>(0)</span>
                </div>
                <div class="p-footer">
                    <span class="p-price">Ksh 0</span>
                    <button class="add-to-cart-btn" title="Add to Cart">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="product-card">
            <div class="product-badge accent">New</div>
            <div class="product-img-box">
                <i class="fa-solid fa-clock product-icon-placeholder"></i>
            </div>
            <div class="product-details">
                <span class="p-category">(Category)</span>
                <h3 class="p-title">(Product)</h3>
                <div class="p-rating">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <span>(0)</span>
                </div>
                <div class="p-footer">
                    <span class="p-price">Ksh 0</span>
                    <button class="add-to-cart-btn" title="Add to Cart">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Static layout inclusion for the footer component
include 'includes/footer.php';
?>