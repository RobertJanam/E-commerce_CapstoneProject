<?php
session_start();

// Strict Authentication Gatekeeper
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'update_avatar') {
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $filename = $_FILES['avatar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $upload_dir = 'uploads/profile/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
                $target_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) {
                    $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                    $stmt->bind_param("si", $target_path, $user_id);
                    $stmt->execute();
                    $stmt->close();
                    $success_msg = "Profile picture updated successfully!";
                } else {
                    $error_msg = "Failed to save uploaded file.";
                }
            } else {
                $error_msg = "Invalid file type. Allowed: JPG, JPEG, PNG, WEBP.";
            }
        } else {
            $error_msg = "Please select a valid image file.";
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update_bio') {
        $bio = trim($_POST['bio']);
        $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE id = ?");
        $stmt->bind_param("si", $bio, $user_id);
        if ($stmt->execute()) {
            $success_msg = "Bio updated successfully!";
        } else {
            $error_msg = "Failed to update bio.";
        }
        $stmt->close();
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update_contact') {
        $fullname = trim($_POST['fullname']);
        $phone = trim($_POST['phone']);

        if (!empty($fullname)) {
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("ssi", $fullname, $phone, $user_id);
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $fullname;
                $success_msg = "Contact details saved successfully!";
            } else {
                $error_msg = "Failed to update info.";
            }
            $stmt->close();
        } else {
            $error_msg = "Legal Full Name cannot be empty.";
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
            if ($new_password === $confirm_password) {
                // Fetch existing password hash
                $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (password_verify($current_password, $res['password'])) {
                    $secure_hash = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $secure_hash, $user_id);
                    $stmt->execute();
                    $stmt->close();
                    $success_msg = "Password updated securely!";
                } else {
                    $error_msg = "Current password verification failed.";
                }
            } else {
                $error_msg = "New passwords do not match.";
            }
        } else {
            $error_msg = "All password fields are required.";
        }
    }
}

$fullname = "Elite Athlete";
$email = "customer@sportsgear.com";
$role = "customer";
$phone = "";
$bio = "Welcome to your training dashboard. Click the edit button to write a bio about your athletic journey.";
$profile_pic = "";

if (isset($conn) && $conn instanceof mysqli) {
    $query = "SELECT fullname, email, role, phone, bio, profile_pic FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $fullname = htmlspecialchars($user['fullname']);
            $email = htmlspecialchars($user['email']);
            $role = htmlspecialchars($user['role']);
            if (!empty($user['phone'])) $phone = htmlspecialchars($user['phone']);
            if (!empty($user['bio'])) $bio = htmlspecialchars($user['bio']);
            if (!empty($user['profile_pic'])) $profile_pic = htmlspecialchars($user['profile_pic']);
        }
        $stmt->close();
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/profile.css">

<main class="profile-main-wrapper">
    <div class="profile-container">

        <div class="profile-back-nav">
            <a href="index.php" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Back to Home page
            </a>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div class="profile-alert success"><i class="fa-solid fa-circle-check"></i> <?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="profile-alert error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="profile-grid">
            <div class="profile-sidebar-card">
                <div class="avatar-upload-container">
                    <div class="avatar-preview-box">
                        <?php if (!empty($profile_pic) && file_exists($profile_pic)): ?>
                            <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="user-avatar-img">
                        <?php else: ?>
                            <div class="avatar-initials-fallback">
                                <?php echo strtoupper(substr($fullname, 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <form action="profile.php" method="POST" enctype="multipart/form-data" id="avatarForm">
                        <input type="hidden" name="action" value="update_avatar">
                        <label for="avatarFile" class="upload-avatar-btn" title="Upload Photo">
                            <i class="fa-solid fa-camera"></i> Update Photo
                        </label>
                        <input type="file" id="avatarFile" name="avatar" accept="image/*" style="display: none;" onchange="document.getElementById('avatarForm').submit();">
                    </form>
                </div>

                <div class="sidebar-user-meta">
                    <h2 class="sidebar-user-name"><?php echo $fullname; ?></h2>
                    <span class="user-badge-role"><?php echo strtoupper($role); ?></span>
                </div>
            </div>

            <div class="profile-content-area">

                <div class="profile-details-card">
                    <div class="card-heading-box">
                        <h3 class="card-heading"><i class="fa-solid fa-address-card"></i> About</h3>
                        <button type="button" class="action-edit-icon-btn" onclick="toggleBioForm()" title="Edit Bio">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </div>

                    <div id="bioStaticView" class="about-text-block">
                        <p><?php echo nl2br($bio); ?></p>
                    </div>

                    <div id="bioEditView" style="display: none; margin-top: 1rem;">
                        <form action="profile.php" method="POST">
                            <input type="hidden" name="action" value="update_bio">
                            <textarea name="bio" class="profile-textarea" rows="4" required><?php echo htmlspecialchars($bio); ?></textarea>
                            <div class="form-action-row" style="margin-top: 0.75rem;">
                                <button type="submit" class="btn-save-sm">Save Bio</button>
                                <button type="button" class="btn-discard-sm" onclick="toggleBioForm()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="profile-details-card">
                    <div class="card-heading-box">
                        <h3 class="card-heading"><i class="fa-solid fa-address-book"></i> Personal Information</h3>
                    </div>

                    <form action="profile.php" method="POST" class="contact-info-form">
                        <input type="hidden" name="action" value="update_contact">

                        <div class="contact-info-grid">
                            <div class="info-group">
                                <label class="info-label">Full Name</label>
                                <div class="info-value-box focusable">
                                    <i class="fa-solid fa-user icon-field"></i>
                                    <input type="text" name="fullname" class="info-input" value="<?php echo $fullname; ?>" required>
                                </div>
                            </div>

                            <div class="info-group">
                                <label class="info-label">Email Address</label>
                                <div class="info-value-box locked">
                                    <i class="fa-solid fa-envelope icon-field"></i>
                                    <span class="info-value-text"><?php echo $email; ?></span>
                                </div>
                            </div>

                            <div class="info-group">
                                <label class="info-label">Contact Number</label>
                                <div class="info-value-box focusable">
                                    <i class="fa-solid fa-phone icon-field"></i>
                                    <input type="text" name="phone" class="info-input" value="<?php echo $phone; ?>" placeholder="e.g. 0712345678">
                                </div>
                            </div>
                        </div>

                        <div class="form-footer-actions">
                            <button type="submit" class="btn-save-primary">Save Info</button>
                            <button type="button" class="btn-security-secondary" onclick="openPasswordModal()">
                                <i class="fa-solid fa-key"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</main>

<div class="security-modal-overlay" id="passwordModal">
    <div class="security-modal-card">
        <div class="modal-header">
            <h3><i class="fa-solid fa-shield-halved"></i> Update Password</h3>
            <button class="modal-close-x" onclick="closePasswordModal()">&times;</button>
        </div>
        <form action="profile.php" method="POST" class="modal-form">
            <input type="hidden" name="action" value="update_password">

            <div class="modal-input-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required placeholder="••••••••">
            </div>

            <div class="modal-input-group">
                <label>New Password</label>
                <input type="password" name="new_password" required placeholder="••••••••">
            </div>

            <div class="modal-input-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required placeholder="••••••••">
            </div>

            <div class="modal-actions-row">
                <button type="submit" class="btn-modal-submit">Save Changes</button>
                <button type="button" class="btn-modal-cancel" onclick="closePasswordModal()">Discard Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleBioForm() {
    const staticView = document.getElementById('bioStaticView');
    const editView = document.getElementById('bioEditView');
    if(staticView.style.display === 'none') {
        staticView.style.display = 'block';
        editView.style.display = 'none';
    } else {
        staticView.style.display = 'none';
        editView.style.display = 'block';
    }
}

function openPasswordModal() {
    document.getElementById('passwordModal').classList.add('active');
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.remove('active');
}

// Close password change modal if background layout overlay is clicked
window.onclick = function(event) {
    const modal = document.getElementById('passwordModal');
    if (event.target === modal) {
        closePasswordModal();
    }
}
</script>

<?php
include 'includes/footer.php';
?>