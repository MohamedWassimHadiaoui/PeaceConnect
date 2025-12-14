<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/authMiddleware.php";
requireLogin();
require_once __DIR__ . "/../../Controller/userController.php";
require_once __DIR__ . "/../../lib/TwoFactorAuth.php";

$uc = new UserController();
$userId = $_SESSION['user_id'];
$user = $uc->getUserById($userId);

$error = '';
$success = '';
$secret = '';
$qrCodeUrl = '';

$isEnabled = !empty($user['two_factor_enabled']);

// Disable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disable_2fa'])) {
    $uc->disable2FA($userId);
    $success = "2FA disabled.";
    $isEnabled = false;
    $user = $uc->getUserById($userId);
}

// Start setup (generate secret)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['setup']) && !$isEnabled) {
    $secret = TwoFactorAuth::generateSecret();
    $_SESSION['2fa_setup_secret'] = $secret;
}

// Verify and enable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_2fa'])) {
    $entered = trim($_POST['code'] ?? '');
    $secret = $_SESSION['2fa_setup_secret'] ?? '';
    if ($secret === '') {
        $error = "Setup expired. Please start again.";
    } elseif ($entered === '') {
        $error = "Please enter the 6-digit code.";
    } elseif (TwoFactorAuth::verifyCode($secret, $entered)) {
        $uc->enable2FA($userId, $secret);
        unset($_SESSION['2fa_setup_secret']);
        $success = "2FA enabled successfully!";
        $isEnabled = true;
        $user = $uc->getUserById($userId);
    } else {
        $error = "Invalid code. Please try again.";
    }
}

// If setup secret exists, show QR
if (!$isEnabled && isset($_SESSION['2fa_setup_secret'])) {
    $secret = $_SESSION['2fa_setup_secret'];
    $qrCodeUrl = TwoFactorAuth::getQRCodeUrl($secret, $user['email'] ?? 'user@example.com', 'PeaceConnect');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Setup - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .qr { display:flex; justify-content:center; margin: 1.25rem 0; }
        .qr img { border-radius: 16px; border: 1px solid var(--border-color); background: #fff; padding: 10px; }
        .secret { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 1.1rem; letter-spacing: 0.15rem; text-align:center; padding: 0.9rem; border-radius: 12px; border: 1px dashed var(--border-color); background: var(--bg-input); }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="hero">
                <h1>Two-Factor Authentication</h1>
                <p>Secure your account with an authenticator app</p>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success"><div><?= htmlspecialchars($success) ?></div></div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="alert alert-danger"><div><?= htmlspecialchars($error) ?></div></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body" style="padding:2rem">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
                        <div>
                            <div class="badge <?= $isEnabled ? 'badge-resolved' : 'badge-pending' ?>">
                                <?= $isEnabled ? 'ENABLED' : 'DISABLED' ?>
                            </div>
                        </div>
                        <a class="btn btn-secondary" href="profile.php">Back to profile</a>
                    </div>

                    <?php if ($isEnabled): ?>
                        <p style="color:var(--text-muted);margin-top:1rem">2FA is enabled. You will be asked for a code after login.</p>
                        <form method="POST" style="margin-top:1rem" onsubmit="return confirm('Disable 2FA?')">
                            <input type="hidden" name="disable_2fa" value="1">
                            <button class="btn btn-danger" type="submit" style="width:100%">Disable 2FA</button>
                        </form>
                    <?php elseif ($qrCodeUrl): ?>
                        <h2 class="card-title" style="margin-top:1rem">Step 2: Scan & verify</h2>
                        <p style="color:var(--text-muted)">Scan the QR with Google Authenticator (or any TOTP app), then enter the 6-digit code.</p>
                        <div class="qr"><img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="2FA QR Code"></div>
                        <div class="secret"><?= htmlspecialchars(TwoFactorAuth::formatSecret($secret)) ?></div>

                        <form method="POST" novalidate style="margin-top:1.25rem">
                            <input type="hidden" name="verify_2fa" value="1">
                            <div class="form-group">
                                <label>Verification code</label>
                                <input class="form-control" type="text" name="code" placeholder="000000" autocomplete="off">
                            </div>
                            <button class="btn btn-primary" type="submit" style="width:100%">Verify & Enable</button>
                        </form>
                    <?php else: ?>
                        <h2 class="card-title" style="margin-top:1rem">Step 1: Start setup</h2>
                        <p style="color:var(--text-muted)">Click below to generate a QR code for your authenticator app.</p>
                        <a class="btn btn-success" style="width:100%;display:block;text-align:center" href="setup_2fa.php?setup=1">Start 2FA Setup</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include '../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    <script>
        var codeInput = document.querySelector('input[name="code"]');
        if (codeInput) {
            codeInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
            });
        }
    </script>
</body>
</html>



