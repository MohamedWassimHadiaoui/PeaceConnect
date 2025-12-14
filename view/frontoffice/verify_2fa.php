<?php
include __DIR__ . '/partials/header.php';
if (!isset($_SESSION['2fa_pending'])) { header("Location: " . $frontoffice . "login.php"); exit; }

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

require_once __DIR__ . "/../../Controller/userController.php";
$uc = new UserController();
$demoCode = $uc->get2FACode($_SESSION['2fa_user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Verification - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .auth-container { min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .auth-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 24px; padding: 3rem; width: 100%; max-width: 420px; box-shadow: 0 20px 60px var(--shadow); text-align: center; }
        .auth-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 1.5rem; }
        h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
        .subtitle { color: var(--text-muted); margin-bottom: 2rem; }
        .code-input { font-size: 2rem; text-align: center; letter-spacing: 0.5rem; padding: 1rem; }
        .demo-code { background: rgba(99,102,241,0.1); border: 2px dashed #6366f1; border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; }
        .demo-code .label { font-size: 0.8rem; color: var(--text-muted); }
        .demo-code .code { font-size: 1.5rem; font-weight: 700; color: #6366f1; letter-spacing: 0.3rem; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>

    <main class="auth-container">
        <div class="auth-card">
            <div class="auth-icon">🛡️</div>
            <h1>Two-Factor Authentication</h1>
            <p class="subtitle">Enter the 6-character code</p>

            <?php if ($demoCode): ?>
            <div class="demo-code">
                <div class="label">Your current code (changes every minute)</div>
                <div class="code"><?= htmlspecialchars($demoCode) ?></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <form action="../../Controller/userController.php" method="POST" novalidate>
                <input type="hidden" name="action" value="verify_2fa">
                
                <div class="form-group">
                    <input type="text" name="code" class="form-control code-input" placeholder="______" autofocus>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;padding:0.9rem">
                    Verify
                </button>
            </form>

            <div style="margin-top:1.5rem">
                <a href="login.php" style="color:var(--text-muted)">Back to login</a>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
    <script>
        document.querySelector('input[name="code"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').substring(0, 6).toUpperCase();
        });
    </script>
</body>
</html>


