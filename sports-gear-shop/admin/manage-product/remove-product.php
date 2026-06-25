<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purge_product'])) {
    $pId = (int)$_POST['product_id'];

    $res = mysqli_query($conn, "SELECT image1, image2, image3 FROM products WHERE id = $pId");
    if ($row = mysqli_fetch_assoc($res)) {
        foreach ([$row['image1'], $row['image2'], $row['image3']] as $path) {
            if (!empty($path) && file_exists('../../' . $path)) {
                @unlink('../../' . $path);
            }
        }
    }

    mysqli_query($conn, "DELETE FROM products WHERE id = $pId");
    $message = "Product record removed permanently.";
}

$productsList = mysqli_query($conn, "SELECT id, name FROM products ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex Sprint - Delete Product</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/remove-product.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php if(!empty($message)): ?>
        <div class="flash-notification alert-triggered" id="flashNotification"><?php echo $message; ?></div>
    <?php endif; ?>

    <main class="admin-workspace">
        <div class="navigation-header-row">
            <a href="../index.php" class="back-dashboard-btn">
                <span class="arrow-icon">←</span>
                <div class="text-stack">
                    <span class="small-sub">back to dashboard</span>
                    <span class="main-heading">DELETE PRODUCT</span>
                </div>
            </a>
        </div>

        <form action="remove-product.php" method="POST" class="workspace-form">
            <div class="dangerous-destruction-box">
                <h2>REMOVE A PRODUCT</h2>
                <p>This action removes the entry from the database and deletes all associated local assets.</p>

                <div class="input-group-block">
                    <label>Select Product</label>
                    <select name="product_id" required>
                        <option value="">Choose</option>
                        <?php while($p = mysqli_fetch_assoc($productsList)): ?>
                            <option value="<?php echo $p['id']; ?>">ID: <?php echo $p['id']; ?> - <?php echo htmlspecialchars($p['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="destructive-actions-row">
                    <button type="submit" name="purge_product" class="btn-danger-confirm">DELETE</button>
                    <a href="../index.php" class="btn-cancel-action">Cancel</a>
                </div>
            </div>
        </form>
    </main>
</body>
</html>