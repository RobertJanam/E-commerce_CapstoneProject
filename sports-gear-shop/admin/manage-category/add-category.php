<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $catName = mysqli_real_escape_string($conn, trim($_POST['category_name']));
    $catDesc = mysqli_real_escape_string($conn, trim($_POST['category_description']));

    if (!empty($catName)) {
        $query = "INSERT INTO categories (name, description) VALUES ('$catName', '$catDesc')";
        if (mysqli_query($conn, $query)) {
            $message = "Category '<strong>" . htmlspecialchars($catName) . "</strong>' added successfully!";
            // Auto create directory under uploads for this category
            $targetDir = "../../uploads/" . strtolower(str_replace(' ', '_', $catName));
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
        } else {
            $message = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex Sprint - Add Category</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/add-category.css">
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
                    <span class="main-heading">Add New Category</span>
                </div>
            </a>
        </div>

        <form action="add-category.php" method="POST" class="workspace-form">
            <div class="form-grid-layout">
                <div class="form-section-box">
                    <h3>Category Parameters</h3>
                    <div class="input-group-block">
                        <label>Category Title Name</label>
                        <input type="text" name="category_name" placeholder="e.g., Elite Footwear" required>
                    </div>
                    <div class="input-group-block">
                        <label>Scope Description</label>
                        <textarea name="category_description" rows="6" placeholder="Define target products metrics..." required></textarea>
                    </div>
                </div>

                <div class="form-actions-sidebar">
                    <div class="sticky-action-box">
                        <h3>Publish Action</h3>
                        <p>Ensure naming metrics remain unique across the active system database.</p>
                        <button type="submit" name="add_category" class="btn-submit-action">Add Category</button>
                        <a href="../index.php" class="btn-discard-action">Discard</a>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <script>
        const flash = document.getElementById('flashNotification');
        if (flash) {
            setTimeout(() => { flash.style.opacity = "0"; setTimeout(() => flash.remove(), 500); }, 4000);
        }
    </script>
</body>
</html>