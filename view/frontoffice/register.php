<?php
include __DIR__ . '/partials/header.php';
if (isset($_SESSION['user_id'])) { header("Location: " . $frontoffice . "index.php"); exit; }

require_once __DIR__ . "/../../Controller/userController.php";

// Generate captcha
$captcha = UserController::generateCaptcha();
$_SESSION['captcha_answer'] = $captcha['answer'];

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .auth-container { min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .auth-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 24px; padding: 3rem; width: 100%; max-width: 480px; box-shadow: 0 20px 60px var(--shadow); }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-icon { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.25rem; }
        .auth-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
        .auth-header p { color: var(--text-muted); }
        .auth-footer { text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); color: var(--text-muted); }
        .auth-footer a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .captcha-box { background: var(--bg-input); border: 2px dashed var(--border-color); border-radius: 12px; padding: 1rem; text-align: center; margin-bottom: 1rem; }
        .captcha-question { font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem; }
        .captcha-label { font-size: 0.85rem; color: var(--text-muted); }
        .password-requirements { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; }
        .password-requirements.valid { color: #10b981; }
        .password-requirements.invalid { color: #ef4444; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">🕊️</div>
                <h1>Create Account</h1>
                <p>Join the PeaceConnect community</p>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <form id="registerForm" action="../../Controller/userController.php" method="POST" novalidate>
                <input type="hidden" name="action" value="register">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="name" class="form-control" placeholder="John" value="<?= htmlspecialchars($old['name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lastname" class="form-control" placeholder="Doe" value="<?= htmlspecialchars($old['lastname'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" placeholder="your@email.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Min 8 chars, uppercase, lowercase, number">
                    <div id="password-strength" class="password-requirements">
                        Password must contain: 8+ characters, uppercase, lowercase, number
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirm" class="form-control" placeholder="Repeat password">
                </div>

                <!-- CAPTCHA -->
                <div class="captcha-box">
                    <div class="captcha-label">Security check</div>
                    <div class="captcha-question"><?= $captcha['question'] ?></div>
                </div>
                <div class="form-group">
                    <label>Your Answer</label>
                    <input type="text" name="captcha" class="form-control" placeholder="Enter the result" autocomplete="off">
                </div>

                <button type="submit" class="btn btn-success" style="width:100%;margin-top:1rem;padding:0.9rem">
                    Create account
                </button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
    <script src="../assets/validation.js"></script>
    <script>
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthDiv = document.getElementById('password-strength');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            
            if (hasLength && hasUpper && hasLower && hasNumber) {
                strengthDiv.textContent = 'Password meets all requirements';
                strengthDiv.className = 'password-requirements valid';
            } else {
                let missing = [];
                if (!hasLength) missing.push('8+ chars');
                if (!hasUpper) missing.push('uppercase');
                if (!hasLower) missing.push('lowercase');
                if (!hasNumber) missing.push('number');
                strengthDiv.textContent = 'Missing: ' + missing.join(', ');
                strengthDiv.className = 'password-requirements invalid';
            }
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;
            
            const name = document.querySelector('[name="name"]');
            if (name.value.trim().length < 2) {
                Validator.showError(name, 'Name must be at least 2 characters');
                isValid = false;
            } else {
                Validator.showSuccess(name);
            }
            
            const email = document.querySelector('[name="email"]');
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                Validator.showError(email, 'Please enter a valid email');
                isValid = false;
            } else {
                Validator.showSuccess(email);
            }
            
            const password = document.querySelector('[name="password"]');
            const hasLength = password.value.length >= 8;
            const hasUpper = /[A-Z]/.test(password.value);
            const hasLower = /[a-z]/.test(password.value);
            const hasNumber = /[0-9]/.test(password.value);
            
            if (!(hasLength && hasUpper && hasLower && hasNumber)) {
                Validator.showError(password, 'Password must have 8+ chars, uppercase, lowercase, number');
                isValid = false;
            } else {
                Validator.showSuccess(password);
            }
            
            const confirm = document.querySelector('[name="password_confirm"]');
            if (confirm.value !== password.value) {
                Validator.showError(confirm, 'Passwords do not match');
                isValid = false;
            } else {
                Validator.showSuccess(confirm);
            }
            
            const captcha = document.querySelector('[name="captcha"]');
            if (captcha.value.trim() === '') {
                Validator.showError(captcha, 'Please solve the captcha');
                isValid = false;
            } else {
                Validator.showSuccess(captcha);
            }
            
            if (isValid) this.submit();
        });
    </script>
</body>
</html>

