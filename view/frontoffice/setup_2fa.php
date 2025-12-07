<?php
session_start();

require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../controller/TwoFactorAuth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login_client.php');
    exit;
}

$userController = new UserController();
$userId = $_SESSION['user_id'];
$user = $userController->getUserById($userId);

if (!$user) {
    session_destroy();
    header('Location: login_client.php');
    exit;
}

$error = '';
$success = '';
$secret = '';
$qrCodeUrl = '';

// Check if 2FA is already enabled
$is2FAEnabled = isset($user['two_factor_enabled']) && $user['two_factor_enabled'] == 1;

// Handle disable 2FA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disable_2fa'])) {
    if ($userController->disable2FA($userId)) {
        $success = '2FA has been disabled successfully.';
        $is2FAEnabled = false;
        $user = $userController->getUserById($userId); // Refresh user data
    } else {
        $error = 'Failed to disable 2FA. Please try again.';
    }
}

// Handle enable 2FA - Step 1: Generate secret
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['setup']) && !$is2FAEnabled) {
    $secret = TwoFactorAuth::generateSecret();
    // Clean the secret before storing (remove any potential spaces)
    $secret = strtoupper(preg_replace('/\s+/', '', $secret));
    $qrCodeUrl = TwoFactorAuth::getQRCodeUrl($secret, $user['email'], 'Peace');
    $_SESSION['2fa_setup_secret'] = $secret; // Store temporarily in session
}

// Handle enable 2FA - Step 2: Verify and save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_2fa'])) {
    $enteredCode = $_POST['verification_code'] ?? '';
    $secret = $_SESSION['2fa_setup_secret'] ?? '';
    
    // Clean the secret
    $secret = strtoupper(preg_replace('/\s+/', '', $secret));
    
    if (empty($secret)) {
        $error = 'Setup session expired. Please start over.';
    } elseif (empty($enteredCode)) {
        $error = 'Please enter the verification code from your authenticator app.';
    } else {
        // Verify the code
        $isValid = TwoFactorAuth::verifyCode($secret, $enteredCode);
        
        if ($isValid) {
            // Code is valid, save to database
            if ($userController->enable2FA($userId, $secret)) {
                $success = '2FA has been enabled successfully!';
                $is2FAEnabled = true;
                unset($_SESSION['2fa_setup_secret']);
                $user = $userController->getUserById($userId); // Refresh user data
            } else {
                $error = 'Failed to enable 2FA. Please try again.';
                // Keep the secret in session for retry
                $secret = $_SESSION['2fa_setup_secret'] ?? '';
                if ($secret) {
                    $qrCodeUrl = TwoFactorAuth::getQRCodeUrl($secret, $user['email'], 'Peace');
                }
            }
        } else {
            $error = 'Invalid verification code. Please try again. Make sure:';
            $error .= '<ul class="instructions">';
            $error .= '<li>Your device time is synchronized</li>';
            $error .= '<li>You entered the code from the correct account</li>';
            $error .= '<li>You entered all 6 digits</li>';
            $error .= '<li>The code hasn\'t expired (codes change every 30 seconds)</li>';
            $error .= '</ul>';
            // Keep the secret in session for retry
            $secret = $_SESSION['2fa_setup_secret'] ?? '';
            if ($secret) {
                $qrCodeUrl = TwoFactorAuth::getQRCodeUrl($secret, $user['email'], 'Peace');
            }
        }
    }
}

// If we have a secret in session from setup, show QR code
if (isset($_SESSION['2fa_setup_secret']) && !$is2FAEnabled) {
    $secret = $_SESSION['2fa_setup_secret'];
    // Clean the secret
    $secret = strtoupper(preg_replace('/\s+/', '', $secret));
    $qrCodeUrl = TwoFactorAuth::getQRCodeUrl($secret, $user['email'], 'Peace');
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - Peace</title>
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
            <li><a href="profile.php">Profile</a></li>
            <li><a href="role.html">Home</a></li>
        </ul>
    </div>
</nav>

<main class="section">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Two-Factor Authentication (2FA)</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-info">
                    <?= e($success) ?>
                </div>
            <?php endif; ?>

            <div class="card-body">
                <!-- Current Status -->
                <div class="text-center-block">
                    <h3>Current Status:</h3>
                    <?php if ($is2FAEnabled): ?>
                        <span class="status-badge status-enabled">✓ 2FA Enabled</span>
                    <?php else: ?>
                        <span class="status-badge status-disabled">✗ 2FA Disabled</span>
                    <?php endif; ?>
                </div>

                <?php if ($is2FAEnabled): ?>
                    <!-- 2FA is Enabled - Show Disable Option -->
                    <div class="instructions">
                        <strong>2FA is currently enabled for your account.</strong>
                        <p>Your account is protected with two-factor authentication. You'll need to enter a code from your authenticator app every time you log in.</p>
                    </div>

                    <form method="post" action="" onsubmit="return confirm('Are you sure you want to disable 2FA? This will make your account less secure.');">
                        <input type="hidden" name="disable_2fa" value="1">
                        <div class="text-center-margin-top">
                            <button type="submit" class="btn btn-outline">
                                Disable 2FA
                            </button>
                            <a href="profile.php" class="btn btn-spacing">
                                Back to Profile
                            </a>
                        </div>
                    </form>

                <?php elseif ($secret && $qrCodeUrl): ?>
                    <!-- Setup Step 2: Verify Code -->
                    <div class="instructions">
                        <strong>Step 2: Verify Setup</strong>
                        <p>Scan the QR code below with your authenticator app, then enter the 6-digit code to verify.</p>
                    </div>

                    <div class="qr-code-container">
                        <img src="<?= e($qrCodeUrl) ?>" alt="QR Code for 2FA Setup">
                    </div>

                    <div class="text-center">
                        <p class="text-light">Or enter this code manually:</p>
                        <div class="secret-key"><?= e(TwoFactorAuth::formatSecret($secret)) ?></div>
                    </div>

                    <form method="post" action="">
                        <input type="hidden" name="verify_2fa" value="1">
                        
                        <div class="form-group">
                            <label for="verification_code" class="form-label">Enter Verification Code</label>
                            <input
                                type="text"
                                id="verification_code"
                                name="verification_code"
                                class="form-control verification-code-input"
                                placeholder="000000"
                                required
                                maxlength="6"
                                pattern="[0-9]{6}"
                                autocomplete="off"
                                autofocus
                            >
                            <small class="verification-code-help">
                                Enter the 6-digit code from your authenticator app
                            </small>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block">
                                Verify & Enable 2FA
                            </button>
                            <a href="setup_2fa.php" class="btn btn-outline btn-block btn-spacing-top">
                                Cancel
                            </a>
                        </div>
                    </form>

                <?php else: ?>
                    <!-- Setup Step 1: Start Setup -->
                    <div class="instructions">
                        <strong>What is Two-Factor Authentication?</strong>
                        <p>2FA adds an extra layer of security to your account. After entering your password, you'll need to enter a code from your authenticator app (like Google Authenticator) to log in.</p>
                        
                        <strong>How to set it up:</strong>
                        <ol>
                            <li>Install Google Authenticator (or any TOTP app) on your phone</li>
                            <li>Click "Enable 2FA" below</li>
                            <li>Scan the QR code with your authenticator app</li>
                            <li>Enter the 6-digit code to verify</li>
                            <li>Done! Your account is now protected</li>
                        </ol>
                    </div>

                    <div class="text-center-margin-top">
                        <a href="setup_2fa.php?setup=1" class="btn btn-primary">
                            Enable 2FA
                        </a>
                        <a href="profile.php" class="btn btn-outline btn-spacing">
                            Back to Profile
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
// Auto-format verification code input
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

