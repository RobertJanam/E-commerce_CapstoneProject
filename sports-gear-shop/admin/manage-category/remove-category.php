<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $catId = (int)$_POST['category_id'];

    if ($catId > 0) {
        $deleteQuery = "DELETE FROM categories WHERE id = $catId";
        if (mysqli_query($conn, $deleteQuery)) {
            $message = "Category metadata drop matrix completed successfully. Files preserved.";
        } else {
            $message = "Error executing constraint purge tracking: " . mysqli_error($conn);
        }
    }
}
$categoriesList = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apex Sprint - Remove Category</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/remove-category.css">
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
                    <span class="main-heading">Remove System Category</span>
                </div>
            </a>
        </div>

        <form action="remove-category.php" method="POST" class="workspace-form">
            <div class="dangerous-destruction-box">
                <h2>Security Alert Layer</h2>
                <p>Removing a structural category breaks schema relational mappings. Under your ruleset limits, directory path chains will not be destroyed on the local drive.</p>

                <div class="input-group-block custom-input-theme">
                    <label>Select Category to Delete</label>
                    <select name="category_id" required>
                        <option value="">-- Choose Destructive Selection Target --</option>
                        <?php while($row = mysqli_fetch_assoc($categoriesList)): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="destructive-actions-row">
                    <button type="submit" name="delete_category" class="btn-danger-confirm">Confirm Destructive Purge</button>
                    <a href="../index.php" class="btn-cancel-action">Cancel Operation</a>
                </div>
            </div>
        </form>
    </main>
</body>
</html>