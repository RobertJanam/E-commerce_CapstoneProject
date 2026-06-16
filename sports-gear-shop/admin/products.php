<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$message = "";
$messageType = ""; // success or error

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $productName = mysqli_real_escape_string($conn, $_POST['product_name']);
    $productDesc = mysqli_real_escape_string($conn, $_POST['product_description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    // Relational fail-safe configuration boundary variables
    $uploadedPaths = [null, null, null];
    $uploadSuccess = true;
    $errorMessage = "";

    // Target abstraction folders
    $baseUploadDir = '../uploads/products/';

    // Explicit runtime directory checks
    if (!file_exists($baseUploadDir)) {
        mkdir($baseUploadDir, 0777, true);
    }

    // Capture and validate file payload elements
    if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
        $files = $_FILES['product_images'];
        $totalFiles = count($files['name']);

        if ($totalFiles > 3) {
            $uploadSuccess = false;
            $errorMessage = "Security Limit Exception: Max 3 layout imagery files authorized.";
        } else {
            for ($i = 0; $i < $totalFiles; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $fileName = $files['name'][$i];
                $fileTmp = $files['tmp_name'][$i];
                $fileType = $files['type'][$i];

                // Content-Type Guardrail: Block non-image media vectors
                if (strpos($fileType, 'video') !== false) {
                    $uploadSuccess = false;
                    $errorMessage = "Validation Error: Video extensions are completely blacklisted.";
                    break;
                }

                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                if (!in_array(strtolower($ext), $allowedExts)) {
                    $uploadSuccess = false;
                    $errorMessage = "Format Error: Unauthorized file extension structure detected.";
                    break;
                }

                // Establish ordered unique naming matrix tracking
                $uniqueIndexName = "image" . ($i + 1) . "_" . time() . "_" . uniqid() . "." . $ext;
                $targetFilePath = $baseUploadDir . $uniqueIndexName;

                if (move_uploaded_file($fileTmp, $targetFilePath)) {
                    // Abstraction of relative base paths to preserve structural integrity upon server migration
                    $uploadedPaths[$i] = "uploads/products/" . $uniqueIndexName;
                } else {
                    $uploadSuccess = false;
                    $errorMessage = "File System Exception: Internal disk permission write failure.";
                    break;
                }
            }
        }
    }

    // Transactional validation checkpoint execution
    if ($uploadSuccess && empty($errorMessage)) {
        $insertQuery = "INSERT INTO products (name, description, category, price, image1, image2, image3)
                        VALUES ('$productName', '$productDesc', '$category', '$price',
                                " . ($uploadedPaths[0] ? "'$uploadedPaths[0]'" : "NULL") . ",
                                " . ($uploadedPaths[1] ? "'$uploadedPaths[1]'" : "NULL") . ",
                                " . ($uploadedPaths[2] ? "'$uploadedPaths[2]'" : "NULL") . ")";

        if (mysqli_query($conn, $insertQuery)) {
            $message = "Product Successfully Added.";
            $messageType = "success";
        } else {
            // Rollback strategy: Clean physical artifacts to prevent orphan files if query execution breaks
            foreach ($uploadedPaths as $path) {
                if ($path && file_exists("../" . $path)) {
                    unlink("../" . $path);
                }
            }
            $message = "Database System Fault: Row transaction aborted. File system changes reverted. " . mysqli_error($conn);
            $messageType = "error";
        }
    } else {
        // Rollback Strategy: Clean physical artifacts from disk if formatting rules fail
        foreach ($uploadedPaths as $path) {
            if ($path && file_exists("../" . $path)) {
                unlink("../" . $path);
            }
        }
        $message = "Validation Engine Warning: " . $errorMessage;
        $messageType = "error";
    }
}

function mysqli_real_escape_with_lk($link, $data) {
    return mysqli_real_escape_string($link, trim($data));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/add-product.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">

</head>
<body class="admin-dashboard-layout">

    <?php if (!empty($message)): ?>
        <div class="flash-popup-notification flash-<?php echo $messageType; ?>" id="flashNotification">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <aside class="sidebar">
        <div class="sidebar-brand">APEX<span>SPRINT</span></div>
        <nav class="sidebar-menu">
            <a href="index.php">Dashboard Overview</a>

            <div class="sidebar-dropdown">
                <button class="dropdown-btn" style="color:var(--neon-cyan)">
                    My Shop <span>▲</span>
                </button>
                <div class="dropdown-container">
                    <a href="products.php" class="active-sub">Products</a>
                    <a href="#" class="disabled-link">Orders (🔒)</a>
                    <a href="#" class="disabled-link">Customers (🔒)</a>
                </div>
            </div>

            <a href="../index.php" target="_blank">View Live Storefront</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../auth/logout.php" class="btn-logout">System Logout</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="product-page-container">

            <div class="header-navigation-wrapper">
                <a href="index.php" class="back-dash-btn">←</a>
                <div class="meta-nav-context">
                    <p>Back to Dashboard</p>
                    <h2>ADD NEW PRODUCT</h2>
                </div>
            </div>

            <form action="products.php" method="POST" enctype="multipart/form-data" id="productIngestionForm">
                <div class="product-editor-grid">

                    <div class="editor-left-column">
                        <div class="form-section-box">
                            <h3>Description</h3>
                            <div class="input-group-block">
                                <label>Product Name</label>
                                <input type="text" name="product_name" placeholder="e.g. Phantom Elite FG Boots" required>
                            </div>
                            <div class="input-group-block">
                                <label>Product Description</label>
                                <textarea name="product_description" rows="6" placeholder="Describe tracking parameters, dynamic fits, or built configurations..." required></textarea>
                            </div>
                        </div>

                        <div class="form-section-box">
                            <h3>Category</h3>
                            <div class="input-group-block">
                                <label>Select Inventory Classification Group</label>
                                <select name="category" required>
                                    <option value="" disabled selected>Choose a category...</option>
                                    <option value="Footwear">Footwear</option>
                                    <option value="Apparel">Apparel</option>
                                    <option value="Equipment">Equipment</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="editor-right-column">
                        <div class="form-section-box">
                            <h3>Product Images</h3>
                            <div class="image-uploader-flex-container">
                                <div class="upload-trigger-square" onclick="triggerFileInput()">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                                    <span>Upload Photo</span>
                                    <input type="file" id="hiddenFileInput" name="product_images[]" multiple accept="image/*" style="display:none;" onchange="handleFileSelection(event)">
                                </div>
                                <div class="preview-canvas-queue" id="previewContainer"></div>
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

        </div>
    </main>

    <script src="../assets/js/add-product.js"></script>

</body>
</html>