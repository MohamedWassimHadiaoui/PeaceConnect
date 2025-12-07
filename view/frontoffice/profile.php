<?php
session_start();

require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../model/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login_client.php');
    exit;
}

$userController = new UserController();
$userId = $_SESSION['user_id'];
$user   = $userController->getUserById($userId);

if (!$user) {
    session_destroy();
    header('Location: login_client.php');
    exit;
}

// Handle avatar upload
$avatarError = '';
$avatarSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        
        // Validate file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $avatarError = 'Invalid file type. Please upload a JPEG, PNG, GIF, or WebP image.';
        } elseif ($file['size'] > $maxSize) {
            $avatarError = 'File is too large. Maximum size is 5MB.';
        } else {
            // Create uploads directory if it doesn't exist
            $uploadDir = __DIR__ . '/../../uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Delete old avatar if exists
            if (!empty($user['avatar']) && file_exists($uploadDir . $user['avatar'])) {
                unlink($uploadDir . $user['avatar']);
            }
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Verify file was moved and is readable
                if (file_exists($filepath) && is_readable($filepath)) {
                    // Update database
                    if ($userController->updateAvatar($userId, $filename)) {
                        $avatarSuccess = 'Avatar uploaded successfully!';
                        $user = $userController->getUserById($userId); // Refresh user data
                        // Force refresh of avatar path
                        if (!empty($user['avatar'])) {
                            $currentAvatar = '../../uploads/avatars/' . htmlspecialchars($user['avatar']);
                        }
                    } else {
                        $avatarError = 'Failed to update avatar in database. Please check if the avatar column exists.';
                        // Delete uploaded file if database update failed
                        if (file_exists($filepath)) {
                            unlink($filepath);
                        }
                    }
                } else {
                    $avatarError = 'File was uploaded but is not accessible. Please check file permissions.';
                }
            } else {
                $errorMsg = 'Failed to upload file. ';
                if (!is_writable($uploadDir)) {
                    $errorMsg .= 'Upload directory is not writable.';
                } else {
                    $errorMsg .= 'Please try again.';
                }
                $avatarError = $errorMsg;
                error_log('Avatar upload failed. Temp: ' . $file['tmp_name'] . ', Target: ' . $filepath . ', Error: ' . $file['error']);
            }
        }
    } else {
        $errorCode = $_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE;
        switch ($errorCode) {
            case UPLOAD_ERR_NO_FILE:
                $avatarError = 'Please select a file to upload.';
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $avatarError = 'File is too large. Maximum size is 5MB.';
                break;
            default:
                $avatarError = 'An error occurred during file upload. Please try again.';
        }
    }
}

// Handle avatar deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_avatar'])) {
    if (!empty($user['avatar'])) {
        $uploadDir = __DIR__ . '/../../uploads/avatars/';
        $filepath = $uploadDir . $user['avatar'];
        
        // Delete file
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // Update database
        if ($userController->deleteAvatar($userId)) {
            $avatarSuccess = 'Avatar deleted successfully.';
            $user = $userController->getUserById($userId); // Refresh user data
        } else {
            $avatarError = 'Failed to delete avatar from database.';
        }
    }
}

// Handle password change
$passwordError = '';
$passwordSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate current password
    if (empty($currentPassword)) {
        $passwordError = 'Please enter your current password.';
    } elseif (!password_verify($currentPassword, $user['password'])) {
        $passwordError = 'Current password is incorrect.';
    } elseif (empty($newPassword)) {
        $passwordError = 'Please enter a new password.';
    } elseif (strlen($newPassword) < 8) {
        $passwordError = 'New password must be at least 8 characters long.';
    } elseif ($newPassword !== $confirmPassword) {
        $passwordError = 'New passwords do not match.';
    } else {
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updatedUser = new User(
            $user['id_user'],
            $user['name'],
            $user['lastname'],
            $user['email'],
            $hashedPassword,
            $user['cin'],
            $user['tel'],
            $user['gender'],
            $user['role'],
            $user['avatar'] ?? null
        );
        $userController->updateUser($updatedUser, $user['id_user']);
        $passwordSuccess = 'Password changed successfully!';
        // Refresh user data
        $user = $userController->getUserById($userId);
    }
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

// Get avatar path
$defaultAvatars = ['avatar2.png', 'avatar3.png', 'avatar4.png', 'avatar5.png', 'avatar7.png'];
$currentAvatar = '';

if (!empty($user['avatar'])) {
    // User has uploaded avatar - path from view/frontoffice/ to uploads/avatars/
    $avatarPath = '../../uploads/avatars/' . htmlspecialchars($user['avatar']);
    // Check if file exists
    $fullPath = __DIR__ . '/../../uploads/avatars/' . htmlspecialchars($user['avatar']);
    if (file_exists($fullPath)) {
        $currentAvatar = $avatarPath;
    } else {
        // File doesn't exist, use default
        if (!isset($_SESSION['user_avatar'])) {
            $_SESSION['user_avatar'] = $defaultAvatars[array_rand($defaultAvatars)];
        }
        $currentAvatar = $_SESSION['user_avatar'];
    }
} else {
    // Use default avatar from session or random
    if (!isset($_SESSION['user_avatar'])) {
        $_SESSION['user_avatar'] = $defaultAvatars[array_rand($defaultAvatars)];
    }
    $currentAvatar = $_SESSION['user_avatar'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Peace</title>

    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="components.css">
    <link rel="stylesheet" href="responsive.css">
</head>

<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container navbar-content">
        <a href="role.html" class="navbar-brand">
            <span>🕊️ Peace</span>
        </a>

        <button class="navbar-toggle">☰</button>

        <ul class="navbar-menu">
            <li><a href="role.html" class="super-button">Home</a></li>
            <li><a href="#deals" class="super-button">Forum</a></li>
            <li><a href="#deals" class="super-button">Événements</a></li>
            <li><a href="#contact" class="super-button">Signaler</a></li>
        </ul>
    </div>
</nav>


<div class="container mt-4">

    <!-- Profile Header -->
    <div class="card profile-header d-flex align-items-center">
        <img src="<?= e($currentAvatar) ?>"
             alt="User Avatar"
             class="profile-photo profile-avatar"
             id="userAvatarImage"
             onerror="this.onerror=null; this.src='<?= e($defaultAvatars[0] ?? 'avatar2.png') ?>';">

        <div>
            <h1 class="mb-1"><?= e($user['name'] . " " . $user['lastname']) ?></h1>
            <p class="text-light mb-1"><?= e($user['email']) ?></p>
            <p class="text-light mb-3">Member since <?= date("F Y") ?></p>

            <a href="#" class="btn" onclick="toggleAvatarForm(); return false;"><?= !empty($user['avatar']) ? 'Change Avatar' : 'Upload Avatar' ?></a>
            <?php if (!empty($user['avatar'])): ?>
                <a href="#" class="btn" onclick="if(confirm('Are you sure you want to delete your avatar?')) { document.getElementById('deleteAvatarForm').submit(); } return false;">Delete Avatar</a>
            <?php endif; ?>
            <a href="#" class="btn" onclick="togglePasswordForm(); return false;">Change Password</a>
            <a href="setup_2fa.php" class="btn"><?= (isset($user['two_factor_enabled']) && $user['two_factor_enabled'] == 1) ? 'Manage 2FA' : 'Setup 2FA' ?></a>
            <a href="#" class="btn">Delete Profile</a>
            
            <!-- Hidden form for avatar deletion -->
            <form id="deleteAvatarForm" method="post" style="display: none;">
                <input type="hidden" name="delete_avatar" value="1">
            </form>
        </div>
    </div>


    <!-- Tabs -->
    <div class="tabs mt-4">
        <ul class="tabs-list">
            <li class="tab-item active">Profile</li>
            <li class="tab-item">Contributions</li>
            <li class="tab-item">Settings</li>
        </ul>
    </div>


    <!-- Avatar Upload Form (Hidden by default) -->
    <div id="avatarUploadForm" class="card mt-4 d-none">
        <div class="card-header">
            <div class="card-title"><?= !empty($user['avatar']) ? 'Change Avatar' : 'Upload Avatar' ?></div>
        </div>
        <div class="card-body">
            <?php if (!empty($avatarError)): ?>
                <div class="alert alert-error mb-3">
                    <?= htmlspecialchars($avatarError) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($avatarSuccess)): ?>
                <div class="alert alert-info mb-3">
                    <?= htmlspecialchars($avatarSuccess) ?>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data" action="" id="avatarUploadFormElement" novalidate>
                <input type="hidden" name="upload_avatar" value="1">
                
                <div class="form-group">
                    <label for="avatar" class="form-label">Select Image</label>
                    <input
                        type="file"
                        id="avatar"
                        name="avatar"
                        class="form-control"
                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                    >
                    <small class="form-help">
                        Accepted formats: JPEG, PNG, GIF, WebP. Maximum size: 5MB
                    </small>
                </div>
                
                <div id="avatarPreview" class="d-none" style="margin: 20px 0; text-align: center;">
                    <p class="text-light mb-2">Preview:</p>
                    <img id="avatarPreviewImg" src="" alt="Preview" class="avatar-preview-image">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Upload Avatar</button>
                    <button type="button" class="btn" onclick="toggleAvatarForm()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Password Change Form (Hidden by default) -->
    <div id="passwordChangeForm" class="card mt-4 d-none">
        <div class="card-header">
            <div class="card-title">Change Password</div>
        </div>
        <div class="card-body">
            <?php if (!empty($passwordError)): ?>
                <div class="alert alert-error mb-3">
                    <?= htmlspecialchars($passwordError) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($passwordSuccess)): ?>
                <div class="alert alert-info mb-3">
                    <?= htmlspecialchars($passwordSuccess) ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <input type="hidden" name="change_password" value="1">
                
                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        class="form-control"
                        placeholder="Enter your current password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        class="form-control"
                        placeholder="Enter new password (min. 8 characters)"
                        required
                        minlength="8"
                    >
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="form-control"
                        placeholder="Confirm new password"
                        required
                        minlength="8"
                    >
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                    <button type="button" class="btn" onclick="togglePasswordForm()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-2 mt-4">

        <!-- About Me -->
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">About Me</div>
                </div>

                <div class="card-body">
                    <table class="mb-4 w-100">
                        <tr>
                            <td class="text-light">Full Name:</td>
                            <td><?= e($user['name'] . " " . $user['lastname']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-light">Email:</td>
                            <td><?= e($user['email']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-light">CIN:</td>
                            <td><?= e($user['cin']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-light">Telephone:</td>
                            <td><?= e($user['tel']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-light">Gender:</td>
                            <td><?= e($user['gender']) ?></td>
                        </tr>
                    </table>

                    <div class="section-title mb-2">Badges</div>
                    <div class="d-flex gap-3">
                        <span class="badge badge-info">New Member</span>
                        <span class="badge badge-info">Verified</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Contributions (Demo) -->
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Contributions</div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <strong>Your last login</strong>
                        <p class="mb-0 text-light">Welcome back to Peace!</p>
                    </div>

                    <div class="alert alert-info">
                        <strong>Profile Active</strong>
                        <p class="mb-0 text-light">Your account is verified and active.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Peace. All Rights Reserved.</p>
    </div>
</footer>

<script>
function togglePasswordForm() {
    const form = document.getElementById('passwordChangeForm');
    if (form.classList.contains('d-none')) {
        form.classList.remove('d-none');
        // Hide avatar form if open
        document.getElementById('avatarUploadForm').classList.add('d-none');
        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        form.classList.add('d-none');
    }
}

function toggleAvatarForm() {
    const form = document.getElementById('avatarUploadForm');
    if (form.classList.contains('d-none')) {
        form.classList.remove('d-none');
        // Hide password form if open
        document.getElementById('passwordChangeForm').classList.add('d-none');
        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        form.classList.add('d-none');
    }
}

// Show form if there's an error or success message
<?php if (!empty($passwordError) || !empty($passwordSuccess)): ?>
    document.addEventListener('DOMContentLoaded', function() {
        togglePasswordForm();
    });
<?php endif; ?>

<?php if (!empty($avatarError) || !empty($avatarSuccess)): ?>
    document.addEventListener('DOMContentLoaded', function() {
        toggleAvatarForm();
    });
<?php endif; ?>

// Avatar preview and validation
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarPreviewImg = document.getElementById('avatarPreviewImg');
    const avatarForm = document.getElementById('avatarUploadFormElement');
    
    if (avatarInput) {
        // Remove any invalid styling on load
        avatarInput.classList.remove('is-invalid');
        
        avatarInput.addEventListener('change', function(e) {
            // Remove invalid class
            this.classList.remove('is-invalid');
            
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (!allowedTypes.includes(file.type)) {
                    this.classList.add('is-invalid');
                    alert('Invalid file type. Please select a JPEG, PNG, GIF, or WebP image.');
                    this.value = '';
                    avatarPreview.classList.add('d-none');
                    return;
                }
                
                if (file.size > maxSize) {
                    this.classList.add('is-invalid');
                    alert('File is too large. Maximum size is 5MB.');
                    this.value = '';
                    avatarPreview.classList.add('d-none');
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreviewImg.src = e.target.result;
                    avatarPreview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                avatarPreview.classList.add('d-none');
            }
        });
        
        // Form validation
        if (avatarForm) {
            avatarForm.addEventListener('submit', function(e) {
                if (!avatarInput.files || avatarInput.files.length === 0) {
                    e.preventDefault();
                    avatarInput.classList.add('is-invalid');
                    alert('Please select an image file to upload.');
                    return false;
                }
            });
        }
    }
});
</script>

</body>
</html>
