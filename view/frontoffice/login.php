<?php
include __DIR__ . '/partials/header.php';
if (isset($_SESSION['user_id'])) { header("Location: " . $frontoffice . "index.php"); exit; }

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? null;
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['success'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .auth-container { min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .auth-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 24px; padding: 3rem; width: 100%; max-width: 420px; box-shadow: 0 20px 60px var(--shadow); }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-icon { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.25rem; }
        .auth-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
        .auth-header p { color: var(--text-muted); }
        .auth-footer { text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); color: var(--text-muted); }
        .auth-footer a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .forgot-link { display: block; text-align: right; margin-top: 0.5rem; font-size: 0.9rem; }
        .forgot-link a { color: var(--text-muted); text-decoration: none; }
        .forgot-link a:hover { color: var(--primary); }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">🕊️</div>
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success"><div><?= htmlspecialchars($success) ?></div></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <form id="loginForm" action="<?= $controller ?>userController.php" method="POST" novalidate>
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" placeholder="your@email.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Your password">
                    <div class="forgot-link">
                        <a href="<?= $frontoffice ?>forgot_password.php">Forgot password?</a>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:1rem;padding:0.9rem">
                    Sign in
                </button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="<?= $frontoffice ?>register.php">Create one</a>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
    <script src="<?= $assets ?>validation.js"></script>
    <script>
        Validator.init('loginForm', {
            'email': [
                { type: 'required', message: 'Email is required' },
                { type: 'email', message: 'Please enter a valid email' }
            ],
            'password': [
                { type: 'required', message: 'Password is required' }
            ]
        });
    </script>
</body>
</html>

