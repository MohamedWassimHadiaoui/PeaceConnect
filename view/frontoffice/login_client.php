<?php
session_start();

require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../controller/EmailService.php';
require_once __DIR__ . '/../../controller/TwoFactorAuth.php';

$userController = new UserController();
$error = '';
$success = '';
$forgotPassword = isset($_GET['forgot']) && $_GET['forgot'] === '1';
$require2FA = false;
$pendingUserId = null;

// Handle forgot password - send verification code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        $user = $userController->getUserByEmail($email);
        
        if ($user) {
            // Generate verification code
            $code = EmailService::generateVerificationCode();
            $userName = $user['name'] . ' ' . $user['lastname'];
            
            // Store code in session with expiration (15 minutes)
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_code_timestamp'] = time();
            $_SESSION['reset_user_id'] = $user['id_user'];
            $_SESSION['reset_user_email'] = $user['email'];
            
            // Send email
            $emailSent = EmailService::sendPasswordResetCode($user['email'], $code, $userName);
            
            if ($emailSent) {
                $success = 'A verification code has been sent to your email. Please check your inbox and enter the code to reset your password.';
            } else {
                $error = 'Failed to send verification email. Please try again later.';
                unset($_SESSION['reset_code']);
                unset($_SESSION['reset_code_timestamp']);
                unset($_SESSION['reset_user_id']);
                unset($_SESSION['reset_user_email']);
            }
        } else {
            // Don't reveal if email exists or not (security best practice)
            $success = 'If an account with that email exists, a verification code has been sent.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['forgot_password'])) {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $user     = $userController->getUserByEmail($email);

    $passwordValid = false;
    if ($user) {
        if (password_verify($password, $user['password'])) {
            $passwordValid = true;
        } elseif ($user['password'] === $password) {
            require_once __DIR__ . '/../../model/User.php';
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
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
            $userController->updateUser($userObj, $user['id_user']);
            $passwordValid = true;
        }
    }

    if ($user && $passwordValid) {
        // Check if 2FA is enabled for this user
        $is2FAEnabled = isset($user['two_factor_enabled']) && $user['two_factor_enabled'] == 1;
        
        if ($is2FAEnabled) {
            // Require 2FA verification
            $require2FA = true;
            $pendingUserId = $user['id_user'];
            $_SESSION['pending_user_id'] = $user['id_user'];
            $_SESSION['pending_user_email'] = $user['email'];
            // Don't set user_id yet - wait for 2FA verification
        } else {
            // No 2FA, login directly
            $_SESSION['user_id'] = $user['id_user'];
            header('Location: profile.php');
            exit;
        }
    } else {
        $error = 'Incorrect email or password.';
    }
}

// Handle 2FA verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_2fa'])) {
    $code = $_POST['verification_code'] ?? '';
    $pendingUserId = $_SESSION['pending_user_id'] ?? null;
    
    if (!$pendingUserId) {
        $error = 'Session expired. Please log in again.';
        unset($_SESSION['pending_user_id']);
        unset($_SESSION['pending_user_email']);
    } elseif (empty($code)) {
        $error = 'Please enter the verification code from your authenticator app.';
        $require2FA = true;
    } else {
        $user = $userController->getUserById($pendingUserId);
        
        if ($user && isset($user['two_factor_secret']) && !empty($user['two_factor_secret'])) {
            if (TwoFactorAuth::verifyCode($user['two_factor_secret'], $code)) {
                // 2FA verified, complete login
                $_SESSION['user_id'] = $pendingUserId;
                unset($_SESSION['pending_user_id']);
                unset($_SESSION['pending_user_email']);
                header('Location: profile.php');
                exit;
            } else {
                $error = 'Invalid verification code. Please try again.';
                $require2FA = true;
            }
        } else {
            $error = '2FA is not properly configured. Please contact support.';
            unset($_SESSION['pending_user_id']);
            unset($_SESSION['pending_user_email']);
        }
    }
}

// Check if we have a pending 2FA verification
if (isset($_SESSION['pending_user_id'])) {
    $require2FA = true;
    $pendingUserId = $_SESSION['pending_user_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Login - Peace</title>
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
                <h2 class="card-title">Client Login</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-info">
                    <?= htmlspecialchars($success) ?>
                    <?php if (isset($_SESSION['reset_code'])): ?>
                        <div class="text-center-margin-top">
                            <a href="reset_password.php" class="btn btn-primary">
                                Enter Verification Code
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($forgotPassword && empty($success)): ?>
                <!-- Forgot Password Form -->
                <form method="post" action="">
                    <input type="hidden" name="forgot_password" value="1">
                    <div class="card-body">
                        <p class="text-light mb-3">Enter your email address and we'll send you a verification code to reset your password.</p>

                        <div class="form-group">
                            <label for="forgot_email" class="form-label">Email</label>
                            <input
                                type="email"
                                id="forgot_email"
                                name="email"
                                class="form-control"
                                placeholder="Enter your email"
                                required
                            >
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            Send Verification Code
                        </button>
                        <a href="login_client.php" class="btn btn-outline btn-block btn-spacing-top">
                            Back to Login
                        </a>
                    </div>
                </form>
            <?php elseif ($require2FA): ?>
                <!-- 2FA Verification Form -->
                <form method="post" action="">
                    <input type="hidden" name="verify_2fa" value="1">
                    <div class="card-body">
                        <p class="text-light mb-3">
                            Two-factor authentication is enabled for your account. 
                            Please enter the 6-digit code from your authenticator app.
                        </p>

                        <div class="form-group">
                            <label for="verification_code" class="form-label">Verification Code</label>
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
                                Open your authenticator app and enter the 6-digit code
                            </small>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            Verify & Login
                        </button>
                        <a href="login_client.php" class="btn btn-outline btn-block btn-spacing-top">
                            Cancel
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <!-- Login Form -->
                <form method="post" action="">
                    <div class="card-body">

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                placeholder="email"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="password"
                                required
                            >
                        </div>

                    </div>

                    <div class="card-footer">
                        <!-- removed onclick="return saisie()" (it was for signup) -->
                        <button type="submit" class="btn btn-primary btn-block">
                            Login
                        </button>
                        <div class="text-center mt-2">
                            <a href="login_client.php?forgot=1" class="link-light">
                                Forgot Password?
                            </a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="java.js"></script>
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
