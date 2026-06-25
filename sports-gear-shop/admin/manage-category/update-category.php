<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

$message = "";
if (isset($_GET['fetch_id'])) {
    $targetId = (int)$_GET['fetch_id'];
    $res = mysqli_query($conn, "SELECT description FROM categories WHERE id = $targetId");
    if($row = mysqli_fetch_assoc($res)) {
        echo $row['description'];
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $catId = (int)$_POST['category_id'];
    $newName = mysqli_real_escape_string($conn, trim($_POST['new_category_name']));
    $newDesc = mysqli_real_escape_string($conn, trim($_POST['edit_description']));

    $origRes = mysqli_query($conn, "SELECT name FROM categories WHERE id = $catId");
    if ($origRow = mysqli_fetch_assoc($origRes)) {
        $oldName = $origRow['name'];

        $updateQuery = "UPDATE categories SET name = '$newName', description = '$newDesc' WHERE id = $catId";
        if (mysqli_query($conn, $updateQuery)) {
            $message = "Category structural properties updated successfully!";

            $oldDir = "../../uploads/" . strtolower(str_replace(' ', '_', $oldName));
            $newDir = "../../uploads/" . strtolower(str_replace(' ', '_', $newName));
            if (file_exists($oldDir) && !file_exists($newDir)) {
                rename($oldDir, $newDir);
            }

            $oldDbPattern = "uploads/" . strtolower(str_replace(' ', '_', $oldName)) . "/";
            $newDbPattern = "uploads/" . strtolower(str_replace(' ', '_', $newName)) . "/";

            mysqli_query($conn, "UPDATE products SET
                image_path_1 = REPLACE(image_path_1, '$oldDbPattern', '$newDbPattern'),
                image_path_2 = REPLACE(image_path_2, '$oldDbPattern', '$newDbPattern'),
                image_path_3 = REPLACE(image_path_3, '$oldDbPattern', '$newDbPattern')
                WHERE category_id = $catId");
        }
    }
}

$categoriesList = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex Sprint - Update Category</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/update-category.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php if(!empty($message)): ?>
        <div class="flash-notification success-glow" id="flashNotification"><?php echo $message; ?></div>
    <?php endif; ?>

    <main class="admin-workspace">
        <div class="navigation-header-row">
            <a href="../index.php" class="back-dashboard-btn">
                <span class="arrow-icon">←</span>
                <div class="text-stack">
                    <span class="small-sub">back to dashboard</span>
                    <span class="main-heading">Modify Category Properties</span>
                </div>
            </a>
        </div>

        <form action="update-category.php" method="POST" class="workspace-form">
            <div class="form-grid-layout">
                <div class="form-section-box">
                    <h3>Selection Layer</h3>
                    <div class="input-group-block">
                        <label>Target Active Category</label>
                        <select name="category_id" id="categorySelect" onchange="loadCategoryDescription(this.value)" required>
                            <option value="">-- Choose Category Target Track --</option>
                            <?php while($c = mysqli_fetch_assoc($categoriesList)): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="input-group-block">
                        <label>New Assigned Category Name</label>
                        <input type="text" name="new_category_name" id="newCategoryName" required>
                    </div>

                    <div class="input-group-block">
                        <label>Edit Target Description</label>
                        <textarea name="edit_description" id="editDescription" rows="6" required></textarea>
                    </div>
                </div>

                <div class="form-actions-sidebar">
                    <div class="sticky-action-box">
                        <h3>Publish Manifest</h3>
                        <button type="submit" name="update_category" class="btn-submit-action">Update Category</button>
                        <a href="../index.php" class="btn-discard-action">Discard Changes</a>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <script>
        function loadCategoryDescription(id) {
            if(!id) return;
            const selectElement = document.getElementById('categorySelect');
            document.getElementById('newCategoryName').value = selectElement.options[selectElement.selectedIndex].text;

            fetch('update-category.php?fetch_id=' + id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('editDescription').value = data;
                });
        }
        const flash = document.getElementById('flashNotification');
        if (flash) { setTimeout(() => { flash.style.opacity = "0"; setTimeout(() => flash.remove(), 500); }, 4000); }
    </script>
</body>
</html>