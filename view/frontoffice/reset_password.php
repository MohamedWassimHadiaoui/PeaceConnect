<?php
session_start();

require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../controller/EmailService.php';
require_once __DIR__ . '/../../model/User.php';

$userController = new UserController();
$error = '';
$success = '';
$showCodeForm = true;
$showPasswordForm = false;

// Check if user has a valid reset session
if (!isset($_SESSION['reset_code']) || !isset($_SESSION['reset_user_id'])) {
    $error = 'No active password reset session. Please request a new verification code.';
    $showCodeForm = false;
}

// Handle verification code submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    $enteredCode = $_POST['verification_code'] ?? '';
    $storedCode = $_SESSION['reset_code'] ?? '';
    $codeTimestamp = $_SESSION['reset_code_timestamp'] ?? 0;
    
    // Check if code has expired (15 minutes = 900 seconds)
    if (time() - $codeTimestamp > 900) {
        $error = 'Verification code has expired. Please request a new one.';
        unset($_SESSION['reset_code']);
        unset($_SESSION['reset_code_timestamp']);
        unset($_SESSION['reset_user_id']);
        unset($_SESSION['reset_user_email']);
        $showCodeForm = false;
    } elseif (empty($enteredCode)) {
        $error = 'Please enter the verification code.';
    } elseif ($enteredCode === $storedCode) {
        // Code is valid, show password form
        $showCodeForm = false;
        $showPasswordForm = true;
        $success = 'Verification code accepted. Please enter your new password.';
    } else {
        $error = 'Invalid verification code. Please try again.';
    }
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $userId = $_SESSION['reset_user_id'] ?? null;
    $userEmail = $_SESSION['reset_user_email'] ?? '';
    
    if (!$userId) {
        $error = 'Session expired. Please request a new verification code.';
        $showCodeForm = false;
    } elseif (empty($newPassword)) {
        $error = 'Please enter a new password.';
        $showPasswordForm = true;
    } elseif (strlen($newPassword) < 8) {
        $error = 'Password must be at least 8 characters long.';
        $showPasswordForm = true;
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
        $showPasswordForm = true;
    } else {
        // Get user data
        $user = $userController->getUserById($userId);
        
        if ($user) {
            // Hash and update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $userObj = new User(
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
            $userController->updateUser($userObj, $userId);
            
            // Send confirmation email
            $userName = $user['name'] . ' ' . $user['lastname'];
            EmailService::sendPasswordChangeConfirmation($user['email'], $userName);
            
            // Clear reset session
            unset($_SESSION['reset_code']);
            unset($_SESSION['reset_code_timestamp']);
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_user_email']);
            
            $success = 'Your password has been successfully changed! A confirmation email has been sent to your email address.';
            $showPasswordForm = false;
        } else {
            $error = 'User not found. Please request a new verification code.';
            $showPasswordForm = false;
        }
    }
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Peace</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="components.css">
    <link rel="stylesheet" href="responsive.css">
</head>
<body>

<nav class="navbar">
    <div class="container navbar-content">
        <div class="navbar-brand">
            <span>🕊️ Peace</span>
        </div>
        <ul class="navbar-menu">
            <li><a href="role.html">Home</a></li>
            <li><a href="#deals">Deals</a></li>
            <li><a href="#shop">Shop Now</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </div>
</nav>

<main class="section">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Reset Password</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-info">
                    <?= e($success) ?>
                    <div class="text-center-margin-top">
                        <a href="login_client.php" class="btn btn-primary">
                            Go to Login
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($showCodeForm): ?>
                <!-- Verification Code Form -->
                <form method="post" action="">
                    <input type="hidden" name="verify_code" value="1">
                    <div class="card-body">
                        <p class="text-light mb-3">Please enter the 6-digit verification code that was sent to your email address.</p>

                        <div class="form-group">
                            <label for="verification_code" class="form-label">Verification Code</label>
                            <input
                                type="text"
                                id="verification_code"
                                name="verification_code"
                                class="form-control verification-code-input"
                                placeholder="Enter 6-digit code"
                                required
                                maxlength="6"
                                pattern="[0-9]{6}"
                                autocomplete="off"
                                autofocus
                            >
                            <small class="verification-code-help">
                                Code expires in 15 minutes
                            </small>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            Verify Code
                        </button>
                        <a href="login_client.php?forgot=1" class="btn btn-outline btn-block btn-spacing-top">
                            Request New Code
                        </a>
                    </div>
                </form>
            <?php elseif ($showPasswordForm): ?>
                <!-- New Password Form -->
                <form method="post" action="">
                    <input type="hidden" name="reset_password" value="1">
                    <div class="card-body">
                        <p class="text-light mb-3">Please enter your new password below.</p>

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
                                autofocus
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

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            Reset Password
                        </button>
                        <a href="login_client.php" class="btn btn-outline btn-block btn-spacing-top">
                            Cancel
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <!-- No active session -->
                <div class="card-body">
                    <p class="text-light mb-3">Please request a password reset from the login page.</p>
                    <div class="text-center">
                        <a href="login_client.php?forgot=1" class="btn btn-primary">
                            Request Password Reset
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="java.js"></script>
<script>
// Auto-focus on verification code input and format it
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('verification_code');
    if (codeInput) {
        codeInput.focus();
        
        // Only allow numbers
        codeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Prevent paste of non-numeric characters
        codeInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numbers = paste.replace(/[^0-9]/g, '').substring(0, 6);
            this.value = numbers;
        });
    }
});
</script>
</body>
</html>

