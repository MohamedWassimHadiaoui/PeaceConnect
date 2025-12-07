<?php
/**
 * Quick check to see if avatar column exists and if user has avatar
 * Access: http://localhost/ff2/final/check_avatar.php
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controller/userController.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    die('Please log in first.');
}

$userController = new UserController();
$userId = $_SESSION['user_id'];
$user = $userController->getUserById($userId);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Avatar Check</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #e7f3ff; padding: 15px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Avatar Status Check</h1>
    
    <div class="info">
        <h2>User Information</h2>
        <p><strong>User ID:</strong> <?= htmlspecialchars($userId) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? 'N/A') ?></p>
        <p><strong>Avatar Column Exists:</strong> 
            <?= isset($user['avatar']) ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO - Run the SQL migration!</span>' ?>
        </p>
        <p><strong>Avatar Value in DB:</strong> 
            <?= !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : '<span class="error">NULL or Empty</span>' ?>
        </p>
    </div>
    
    <?php if (!empty($user['avatar'])): ?>
        <div class="info">
            <h2>File Check</h2>
            <?php
            $avatarPath = __DIR__ . '/uploads/avatars/' . $user['avatar'];
            $webPath = 'uploads/avatars/' . $user['avatar'];
            ?>
            <p><strong>File Path:</strong> <?= htmlspecialchars($avatarPath) ?></p>
            <p><strong>File Exists:</strong> 
                <?= file_exists($avatarPath) ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO</span>' ?>
            </p>
            <?php if (file_exists($avatarPath)): ?>
                <p><strong>File Size:</strong> <?= filesize($avatarPath) ?> bytes</p>
                <p><strong>Web Path:</strong> <a href="<?= htmlspecialchars($webPath) ?>" target="_blank"><?= htmlspecialchars($webPath) ?></a></p>
                <p><strong>Image Preview:</strong></p>
                <img src="<?= htmlspecialchars($webPath) ?>" alt="Avatar" style="max-width: 200px; border: 2px solid #ddd;">
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class="info">
        <h2>Uploads Directory</h2>
        <?php
        $uploadDir = __DIR__ . '/uploads/avatars/';
        ?>
        <p><strong>Directory Path:</strong> <?= htmlspecialchars($uploadDir) ?></p>
        <p><strong>Directory Exists:</strong> 
            <?= is_dir($uploadDir) ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO</span>' ?>
        </p>
        <p><strong>Directory Writable:</strong> 
            <?= is_writable($uploadDir) ? '<span class="success">✓ YES</span>' : '<span class="error">✗ NO</span>' ?>
        </p>
        <?php if (is_dir($uploadDir)): ?>
            <p><strong>Files in Directory:</strong></p>
            <ul>
                <?php
                $files = scandir($uploadDir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        echo '<li>' . htmlspecialchars($file) . ' (' . filesize($uploadDir . $file) . ' bytes)</li>';
                    }
                }
                ?>
            </ul>
        <?php endif; ?>
    </div>
    
    <div class="info">
        <h2>Next Steps</h2>
        <ol>
            <li>If "Avatar Column Exists" is NO, run: <code>ALTER TABLE user ADD COLUMN avatar VARCHAR(255) NULL DEFAULT NULL AFTER role;</code></li>
            <li>If directory is not writable, set permissions: <code>chmod 755 uploads/avatars/</code></li>
            <li>If file doesn't exist but DB has value, try uploading again</li>
            <li>Check PHP error logs for upload errors</li>
        </ol>
    </div>
    
    <p><a href="view/frontoffice/profile.php">← Back to Profile</a></p>
</body>
</html>

