<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$displayName = 'Admin Staff';
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $displayName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
}

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $productName = mysqli_real_escape_string($conn, $_POST['product_name']);
    $productDesc = mysqli_real_escape_string($conn, $_POST['product_description']);
    $categoryId = (int)$_POST['category'];
    $price = floatval($_POST['price']);

    // Fetch the correct category name to match folder structures
    $categoryResult = mysqli_query($conn, "SELECT name FROM categories WHERE id = $categoryId");
    $categoryName = 'uncategorized';
    if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
        $categoryRow = mysqli_fetch_assoc($categoryResult);
        $categoryName = trim($categoryRow['name']);
    }

    // Match exactly with add-category.php folder mapping
    $categoryFolder = strtolower(str_replace(' ', '_', $categoryName));
    if ($categoryFolder === '') {
        $categoryFolder = 'uncategorized';
    }

    // Relative directory mapping from the project root
    $targetDirName = "uploads/" . $categoryFolder . "/";
    $absoluteUploadDir = __DIR__ . '/../' . $targetDirName; // Resolves to root uploads/ directory

    // Fallback if directory was somehow wiped out
    if (!is_dir($absoluteUploadDir)) {
        if (!mkdir($absoluteUploadDir, 0777, true) && !is_dir($absoluteUploadDir)) {
            $message = "Unable to create the upload folder for this category.";
            $messageType = "error";
        }
    }

    $uploadedPaths = ['image1' => '', 'image2' => '', 'image3' => ''];

    if (empty($message) && isset($_FILES['product_images']) && is_array($_FILES['product_images']['name'])) {
        $productFiles = $_FILES['product_images'];
        $fileCount = count($productFiles['name']);

        for ($i = 0; $i < $fileCount && $i < 3; $i++) {
            if ($productFiles['error'][$i] === UPLOAD_ERR_OK) {
                $fileTmpPath = $productFiles['tmp_name'][$i];
                $fileName = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9\._\-]/', '', basename($productFiles['name'][$i]));
                $destAbsolutePath = $absoluteUploadDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $destAbsolutePath)) {
                    $fieldKey = 'image' . ($i + 1);
                    $uploadedPaths[$fieldKey] = $targetDirName . $fileName;
                }
            }
        }
    }

    if (empty($message)) {
        $img1 = mysqli_real_escape_string($conn, $uploadedPaths['image1']);
        $img2 = mysqli_real_escape_string($conn, $uploadedPaths['image2']);
        $img3 = mysqli_real_escape_string($conn, $uploadedPaths['image3']);

        // Relational Database Insertion Layer
        $insertQuery = "INSERT INTO products (name, description, price, category_id, image1, image2, image3)
                        VALUES ('$productName', '$productDesc', '$price', '$categoryId', '$img1', '$img2', '$img3')";

        if (mysqli_query($conn, $insertQuery)) {
            $message = "Product added successfully";
            $messageType = "success";
        } else {
            $message = "Database Error: " . mysqli_error($conn);
            $messageType = "error";
        }
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

        <main class="main-content">

            <div class="back-navigation-row">
                <a href="index.php" class="btn-back-dashboard">
                    <span class="arrow-icon">&larr;</span> back to dashboard
                </a>
            </div>

            <h1 class="main-page-title">Add Product</h1>

            <form action="products.php" method="POST" enctype="multipart/form-data" class="product-entry-form">
                <div class="form-flex-layout">

                    <div class="form-column-left">
                        <div class="form-section-box">
                            <h3>Product Name</h3>
                            <div class="input-group-block">
                                <label>Gear</label>
                                <input type="text" name="product_name" placeholder="e.g. Boots" required>
                            </div>
                        </div>

                        <div class="form-section-box">
                            <h3>Product Description</h3>
                            <div class="input-group-block">
                                <label>Detailed Specifications</label>
                                <textarea name="product_description" rows="10" placeholder="Describe materials..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-column-right">

                        <div class="form-section-box">
                            <h3>Product Images</h3>
                            <div class="input-group-block">
                                <label>Maximum of 3 image files</label>
                                <div class="file-upload-trigger-wrap">
                                    <button type="button" class="btn-upload-trigger" onclick="triggerFileInput()">Add Image</button>
                                    <input type="file" id="hiddenFileInput" name="product_images[]" multiple accept="image/*" style="display:none;" onchange="handleFileSelection(event)">
                                </div>
                                <div class="preview-canvas-queue" id="previewContainer"></div>
                            </div>
                        </div>

                        <div class="form-section-box">
                            <h3>Category</h3>
                            <div class="input-group-block">
                                <label>Select Category</label>
                                <select name="category" required class="form-select-dropdown">
                                    <option value="">Choose</option>
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
                                <label>Value</label>
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