<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/authMiddleware.php";
requireLogin();
require_once __DIR__ . "/../../Controller/userController.php";

$uc = new UserController();
$user = $uc->getUserById($_SESSION['user_id']);

// Generate 2FA secret if not exists
if (empty($user['two_factor_secret'])) {
    $secret = $uc->generate2FASecret($user['id']);
    $user['two_factor_secret'] = $secret;
}
$current2FACode = null;

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? null;
unset($_SESSION['errors'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .profile-header { text-align: center; padding: 3rem 2rem; background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(16,185,129,0.1)); border-radius: 24px; margin-bottom: 2rem; }
        .profile-avatar { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 1.5rem; box-shadow: 0 10px 30px rgba(99,102,241,0.3); overflow: hidden; border: 4px solid var(--bg-card); }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-name { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
        .profile-email { color: var(--text-muted); margin-bottom: 1rem; }
        .profile-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 2rem; margin-bottom: 2rem; }
        .profile-section-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--border-color); display: flex; align-items: center; gap: 0.5rem; }
        .tabs { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .tab-btn { padding: 0.75rem 1.5rem; border-radius: 10px; border: none; background: var(--bg-card); color: var(--text-primary); cursor: pointer; font-weight: 500; transition: all 0.3s; border: 1px solid var(--border-color); }
        .tab-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .twofa-box { background: var(--bg-input); border-radius: 12px; padding: 1.5rem; text-align: center; }
        .twofa-status { font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; }
        .twofa-status.enabled { color: #10b981; }
        .twofa-status.disabled { color: #ef4444; }
        .secret-key { background: var(--bg-card); border: 1px dashed var(--border-color); border-radius: 8px; padding: 1rem; font-family: monospace; font-size: 1.1rem; letter-spacing: 0.2rem; margin: 1rem 0; }
        .current-code { font-size: 2rem; font-weight: 700; color: var(--primary); letter-spacing: 0.3rem; }
        .password-requirements { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; }
        .password-requirements.valid { color: #10b981; }
        .password-requirements.invalid { color: #ef4444; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            
            <?php if ($success): ?>
            <div class="alert alert-success"><div><?= htmlspecialchars($success) ?></div></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if (!empty($user['avatar'])): ?>
                    <img src="../../<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar">
                    <?php else: ?><?= htmlspecialchars(strtoupper(substr((string)($user['name'] ?? 'U'), 0, 1))) ?><?php endif; ?>
                </div>
                <div class="profile-name"><?= htmlspecialchars($user['name'] . ' ' . ($user['lastname'] ?? '')) ?></div>
                <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>
                <span class="badge <?= ($user['role'] ?? '') === 'admin' ? 'badge-high' : 'badge-assigned' ?>">
                    <?= ($user['role'] ?? '') === 'admin' ? 'Administrator' : 'Member' ?>
                </span>
                <?php if ($user['two_factor_enabled']): ?>
                <span class="badge" style="background:rgba(16,185,129,0.2);color:#10b981;margin-left:0.5rem">2FA Enabled</span>
                <?php endif; ?>
            </div>

            <div class="tabs">
                <button class="tab-btn active" data-tab="profile">Edit profile</button>
                <button class="tab-btn" data-tab="password">Password</button>
                <button class="tab-btn" data-tab="2fa">Two-factor auth</button>
            </div>

            <div id="profile" class="tab-content active">
                <div class="profile-card">
                    <h3 class="profile-section-title">Profile information</h3>
                    
                    <form id="profileForm" action="../../Controller/userController.php" method="POST" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="action" value="update_profile">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">

                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="tel" class="form-control" placeholder="Your phone number" value="<?= htmlspecialchars($user['tel'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="M" <?= ($user['gender']??'')==='M'?'selected':'' ?>>Male</option>
                                    <option value="F" <?= ($user['gender']??'')==='F'?'selected':'' ?>>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Profile Picture</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                            <div class="form-text" style="color:var(--text-muted);font-size:0.85rem;margin-top:0.25rem">JPG, PNG or GIF (max 5MB)</div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;margin-top:1rem">
                            Save changes
                        </button>
                    </form>
                </div>
            </div>

            <div id="password" class="tab-content">
                <div class="profile-card">
                    <h3 class="profile-section-title">Change password</h3>
                    
                    <form id="passwordForm" action="../../Controller/userController.php" method="POST" novalidate>
                        <input type="hidden" name="action" value="change_password">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">

                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Enter current password">
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Min 8 chars, uppercase, lowercase, number">
                            <div id="password-strength" class="password-requirements">
                                Password must contain: 8+ characters, uppercase, lowercase, number
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password">
                        </div>

                        <button type="submit" class="btn btn-warning" style="width:100%;margin-top:1rem">
                            Change password
                        </button>
                    </form>
                </div>
            </div>

            <div id="2fa" class="tab-content">
                <div class="profile-card">
                    <h3 class="profile-section-title">Two-factor authentication</h3>
                    
                    <div class="twofa-box">
                        <div class="twofa-status <?= $user['two_factor_enabled'] ? 'enabled' : 'disabled' ?>">
                            <?= $user['two_factor_enabled'] ? '2FA is enabled' : '2FA is disabled' ?>
                        </div>
                        
                        <p style="color:var(--text-muted);margin-bottom:1rem">
                            Two-factor authentication adds an extra layer of security to your account.
                        </p>

                        <div style="margin-top:1.25rem">
                            <a href="setup_2fa.php" class="btn btn-primary" style="width:100%;display:block;text-align:center">Manage 2FA</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    <script src="../assets/validation.js"></script>
    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(btn.dataset.tab).classList.add('active');
            });
        });

        // Profile form validation
        Validator.init('profileForm', {
            'name': [
                { type: 'required', message: 'First name is required' },
                { type: 'minLength', min: 2, message: 'First name must be at least 2 characters' }
            ]
        });

        // Password strength checker
        const passwordInput = document.getElementById('new_password');
        const strengthDiv = document.getElementById('password-strength');
        
        if (passwordInput) {
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
        }

        // Password form validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;

            const current = document.querySelector('[name="current_password"]');
            const newPass = document.querySelector('[name="new_password"]');
            const confirm = document.querySelector('[name="confirm_password"]');

            if (current.value.trim() === '') {
                Validator.showError(current, 'Current password is required');
                isValid = false;
            } else {
                Validator.showSuccess(current);
            }

            const hasLength = newPass.value.length >= 8;
            const hasUpper = /[A-Z]/.test(newPass.value);
            const hasLower = /[a-z]/.test(newPass.value);
            const hasNumber = /[0-9]/.test(newPass.value);

            if (!(hasLength && hasUpper && hasLower && hasNumber)) {
                Validator.showError(newPass, 'Password does not meet requirements');
                isValid = false;
            } else {
                Validator.showSuccess(newPass);
            }

            if (confirm.value !== newPass.value) {
                Validator.showError(confirm, 'Passwords do not match');
                isValid = false;
            } else if (confirm.value !== '') {
                Validator.showSuccess(confirm);
            }

            if (isValid) this.submit();
        });
    </script>
</body>
</html>

