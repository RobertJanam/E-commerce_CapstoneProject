<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product_changes'])) {
    $pId = (int)$_POST['product_id'];
    $pName = mysqli_real_escape_string($conn, $_POST['product_name']);
    $pDesc = mysqli_real_escape_string($conn, $_POST['product_description']);
    $catName = mysqli_real_escape_string($conn, $_POST['category_id']);
    $price = (float)$_POST['price'];

    $orig = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = $pId"));
    $imgPaths = [$orig['image1'], $orig['image2'], $orig['image3']];

    for($i = 1; $i <= 3; $i++) {
        if(isset($_FILES["img_$i"]) && $_FILES["img_$i"]['error'] === UPLOAD_ERR_OK) {
            if(!empty($imgPaths[$i-1]) && file_exists('../../' . $imgPaths[$i-1])) {
                @unlink('../../' . $imgPaths[$i-1]);
            }
            $filename = time() . '_' . $_FILES["img_$i"]['name'];
            $uploadDir = '../../uploads/products/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            move_uploaded_file($_FILES["img_$i"]['tmp_name'], $uploadDir . $filename);
            $imgPaths[$i-1] = 'uploads/products/' . $filename;
        }
    }

    $updateSql = "UPDATE products SET
        name = '$pName',
        description = '$pDesc',
        category = '$catName',
        price = $price,
        image1 = " . ($imgPaths[0] ? "'$imgPaths[0]'" : "NULL") . ",
        image2 = " . ($imgPaths[1] ? "'$imgPaths[1]'" : "NULL") . ",
        image3 = " . ($imgPaths[2] ? "'$imgPaths[2]'" : "NULL") . "
        WHERE id = $pId";

    if(mysqli_query($conn, $updateSql)) {
        $message = "Product records updated";
    }
}

$products = mysqli_query($conn, "SELECT p.*, p.name AS product_name, p.description AS product_description, p.image1, p.image2, p.image3, c.name AS category_name FROM products p LEFT JOIN categories c ON c.name = p.category");
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex Sprint - View & Edit Center</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/update-product.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php if(!empty($message)): ?>
        <div class="flash-notification" id="flashNotification"><?php echo $message; ?></div>
    <?php endif; ?>

    <main class="admin-workspace">
        <div class="navigation-header-row">
            <a href="../index.php" class="back-dashboard-btn">
                <span class="arrow-icon">←</span>
                <div class="text-stack">
                    <span class="small-sub">back to dashboard</span>
                    <span class="main-heading">Comprehensive Product Interface</span>
                </div>
            </a>
        </div>

        <div class="editor-matrix-layout">
            <?php while($p = mysqli_fetch_assoc($products)):
                $images = array_filter([$p['image1'], $p['image2'], $p['image3']]);
            ?>
                <div class="broad-product-card-view">
                    <div class="slider-gallery-box">
                        <div class="slider-canvas" id="slider_<?php echo $p['id']; ?>">
                            <?php if(!empty($images)): foreach($images as $img): ?>
                                <img src="../../<?php echo $img; ?>" class="slide-asset" style="display:none;">
                            <?php endforeach; else: ?>
                                <img src="../../assets/images/UI/placeholder.png" class="slide-asset">
                            <?php endif; ?>
                        </div>
                        <?php if(count($images) > 1): ?>
                            <div class="slider-controls">
                                <button type="button" onclick="cycleSlide(<?php echo $p['id']; ?>, -1)">◀</button>
                                <button type="button" onclick="cycleSlide(<?php echo $p['id']; ?>, 1)">▶</button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="metadata-info-block">
                        <span class="category-meta-lbl"><?php echo htmlspecialchars($p['category_name'] ?? 'Unassigned Category'); ?></span>
                        <h2><?php echo htmlspecialchars($p['product_name']); ?></h2>
                        <p><?php echo htmlspecialchars($p['product_description']); ?></p>
                        <div class="price-indicator">KSh. <?php echo number_format($p['price'], 2); ?></div>
                    </div>

                    <button class="trigger-edit-overlay-btn" onclick="openGlobalModal(<?php echo htmlspecialchars(json_encode($p)); ?>)">
                        Edit
                    </button>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <div class="modal-overlay-backdrop" id="editModalBackdrop">
        <div class="center-modal-box">
            <form action="update-product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="modalProductId">
                <h3>Update Product</h3>

                <div class="input-group-block">
                    <label>Product Name</label>
                    <input type="text" name="product_name" id="modalProductName" required>
                </div>
                <div class="input-group-block">
                    <label>Product Description</label>
                    <textarea name="product_description" id="modalProductDesc" rows="4" required></textarea>
                </div>
                <div class="input-group-block">
                    <label>Select Category</label>
                    <select name="category_id" id="modalProductCategory" required>
                        <?php
                        mysqli_data_seek($categories, 0);
                        while($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo htmlspecialchars($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="input-group-block">
                    <label>Price</label>
                    <input type="number" name="price" id="modalProductPrice" step="0.01" required>
                </div>

                <div class="image-uploader-slots-row">
                    <div><label>Image 1</label><input type="file" name="img_1"></div>
                    <div><label>Image 2</label><input type="file" name="img_2"></div>
                    <div><label>Image 3</label><input type="file" name="img_3"></div>
                </div>

                <div class="modal-actions-wrapper">
                    <button type="submit" name="save_product_changes" class="btn-submit-action">Save Changes</button>
                    <button type="button" class="btn-discard-action" onclick="closeGlobalModal()">Discard</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openGlobalModal(product) {
            document.getElementById('modalProductId').value = product.id;
            document.getElementById('modalProductName').value = product.product_name;
            document.getElementById('modalProductDesc').value = product.product_description;
            document.getElementById('modalProductCategory').value = product.category;
            document.getElementById('modalProductPrice').value = product.price;
            document.getElementById('editModalBackdrop').style.display = 'flex';
        }
        function closeGlobalModal() {
            document.getElementById('editModalBackdrop').style.display = 'none';
        }
        function cycleSlide(id, direction) {
            const container = document.getElementById('slider_' + id);
            const slides = container.getElementsByClassName('slide-asset');
            if(slides.length <= 1) return;
            let activeIdx = 0;
            for(let i=0; i<slides.length; i++) {
                if(slides[i].style.display !== 'none') { activeIdx = i; break; }
            }
            slides[activeIdx].style.display = 'none';
            let nextIdx = (activeIdx + direction + slides.length) % slides.length;
            slides[nextIdx].style.display = 'block';
        }
        document.querySelectorAll('.slider-canvas').forEach(canvas => {
            const first = canvas.querySelector('.slide-asset');
            if(first) first.style.display = 'block';
        });
    </script>
</body>
</html>