<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Authorization check & fallback sequence
$displayName = 'Admin Staff';
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $displayName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
}

$message = "";
$messageType = ""; // success or error

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $productName = mysqli_real_escape_string($conn, $_POST['product_name']);
    $productDesc = mysqli_real_escape_string($conn, $_POST['product_description']);
    $categoryId = (int)$_POST['category'];
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    $categoryResult = mysqli_query($conn, "SELECT name FROM categories WHERE id = $categoryId");
    $categoryName = 'uncategorized';
    if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
        $categoryRow = mysqli_fetch_assoc($categoryResult);
        $categoryName = trim($categoryRow['name']);
    }

    $categoryFolder = strtolower(preg_replace('/[^a-z0-9]+/', '_', $categoryName));
    if ($categoryFolder === '') {
        $categoryFolder = 'uncategorized';
    }

    $baseCategoryDir = '../uploads/' . $categoryFolder . '/';
    $baseUploadDir = $baseCategoryDir;

    // Ensure the category folder exists before saving any image
    if (!is_dir($baseCategoryDir)) {
        $baseUploadDir = '../uploads/';
    }

    // Relational fail-safe configuration boundary variables
    $uploadedPaths = [null, null, null];
    $uploadSuccess = true;
    $errorMessage = "";

    // Capture and validate file payload elements
    if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
        $files = $_FILES['product_images'];
        $totalFiles = count($files['name']);

        if ($totalFiles > 3) {
            $uploadSuccess = false;
            $errorMessage = "Security Limit Exception: Max 3 layout imagery files authorized.";
        } else {
            for ($i = 0; $i < $totalFiles; $i++) {
                if ($files['error'][$i] === 0) {
                    $fileName = time() . '_' . basename($files['name'][$i]);
                    $targetFilePath = $baseUploadDir . $fileName;
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

                    $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
                    if (in_array(strtolower($fileType), $allowTypes)) {
                        if (move_uploaded_file($files['tmp_name'][$i], $targetFilePath)) {
                            $uploadedPaths[$i] = 'uploads/' . $categoryFolder . '/' . $fileName;
                        } else {
                            $uploadSuccess = false;
                            $errorMessage = "Error uploading file: " . $files['name'][$i];
                            break;
                        }
                    } else {
                        $uploadSuccess = false;
                        $errorMessage = "Invalid file format for: " . $files['name'][$i];
                        break;
                    }
                }
            }
        }
    }

    if ($uploadSuccess) {
        $img1 = $uploadedPaths[0];
        $img2 = $uploadedPaths[1];
        $img3 = $uploadedPaths[2];

        // Structural insertion command matching target schema
        $insertQuery = "INSERT INTO products (name, description, category_id, price, image1, image2, image3)
                        VALUES ('$productName', '$productDesc', $categoryId, '$price', '$img1', '$img2', '$img3')";

        if (mysqli_query($conn, $insertQuery)) {
            $message = "Product successfully injected into active repository store.";
            $messageType = "success";
        } else {
            $message = "Database Error: " . mysqli_error($conn);
            $messageType = "error";
        }
    } else {
        $message = $errorMessage;
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APEX SPRINT - Add New Product</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/add-product.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>
<body>

    <?php if (!empty($message)): ?>
        <div id="flashNotification" class="flash-banner-overlay <?php echo $messageType; ?>">
            <div class="flash-content">
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="app-container">

        <aside class="sidebar">
            <div class="sidebar-top">
                <div class="brand-logo">
                    APEX SPRINT <span class="dot-accent">.</span>
                </div>
                <nav class="sidebar-menu">
                    <a href="index.php" class="menu-link">Dashboard</a>

                    <div class="sidebar-dropdown">
                        <button type="button" class="dropdown-btn" id="myShopToggle">
                            <span>My Shop</span>
                            <span class="arrow-indicator">▲</span>
                        </button>
                        <div class="dropdown-container" id="myShopDropdown" style="display: block;">
                            <a href="products.php" class="active-sub">Products</a>
                            <a href="#">Orders</a>
                            <a href="#">Customers</a>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <p class="profile-name"><?php echo $displayName; ?></p>
                    <p class="profile-role">Administrator</p>
                </div>
            </div>
        </aside>

        <main class="main-content">

            <div class="back-navigation-row">
                <a href="index.php" class="btn-back-dashboard">
                    <span class="arrow-icon">&larr;</span> back to dashboard
                </a>
            </div>

            <h1 class="main-page-title">Add New Sports Gear</h1>

            <form action="products.php" method="POST" enctype="multipart/form-data" class="product-entry-form">
                <div class="form-flex-layout">

                    <div class="form-column-left">
                        <div class="form-section-box">
                            <h3>Product Identity</h3>
                            <div class="input-group-block">
                                <label>Product/Gear Name</label>
                                <input type="text" name="product_name" placeholder="e.g., Phantom Elite FG Boots" required>
                            </div>
                        </div>

                        <div class="form-section-box">
                            <h3>Product Description</h3>
                            <div class="input-group-block">
                                <label>Detailed Specifications & Performance Features</label>
                                <textarea name="product_description" rows="10" placeholder="Describe materials, sizing fit matrix, structural enhancements..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-column-right">

                        <div class="form-section-box">
                            <h3>Product Images</h3>
                            <div class="input-group-block">
                                <label>Layout Imagery Assets (Max 3 files)</label>
                                <div class="file-upload-trigger-wrap">
                                    <button type="button" class="btn-upload-trigger" onclick="triggerFileInput()">Add Image</button>
                                    <input type="file" id="hiddenFileInput" name="product_images[]" multiple accept="image/*" style="display:none;" onchange="handleFileSelection(event)">
                                </div>
                                <div class="preview-canvas-queue" id="previewContainer"></div>
                            </div>
                        </div>

                        <div class="form-section-box">
                            <h3>Relational Assignment</h3>
                            <div class="input-group-block">
                                <label>System Categorization Placement Track</label>
                                <select name="category" required class="form-select-dropdown">
                                    <option value="">-- Choose Target Active DB Category --</option>
                                    <?php
                                    $catOptions = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");
                                    if ($catOptions && mysqli_num_rows($catOptions) > 0):
                                        while($opt = mysqli_fetch_assoc($catOptions)):
                                    ?>
                                        <option value="<?php echo $opt['id']; ?>"><?php echo htmlspecialchars($opt['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php
                                        endwhile;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-section-box">
                            <h3>Price</h3>
                            <div class="input-group-block">
                                <label>Retail Value (Currency Normalized to KSh.)</label>
                                <div style="position:relative; display:flex; align-items:center;">
                                    <span style="position:absolute; left:12px; color:var(--muted-gray); font-size:14px; font-weight:700;">KSh.</span>
                                    <input type="number" name="price" step="0.01" min="1" placeholder="0.00" style="padding-left:55px; width:100%;" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions-row">
                            <a href="index.php" class="btn-discard-action">Discard</a>
                            <button type="submit" name="add_product" class="btn-submit-action">Add Product</button>
                        </div>
                    </div>

                </div>
            </form>

        </main>
    </div>

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
    <script src="../assets/js/add-product.js"></script>

</body>
</html>