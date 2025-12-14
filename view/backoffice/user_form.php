<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/userController.php";
$uc = new UserController();

$user = null;
$isEdit = false;
if (isset($_GET['id'])) {
    $user = $uc->getUserById($_GET['id']);
    $isEdit = true;
}

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'Add' ?> User - PeaceConnect Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="hero">
                <h1><?= $isEdit ? 'Edit User' : 'Add User' ?></h1>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body" style="padding:2rem">
                    <form id="userForm" action="../../Controller/userController.php" method="POST" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'add' ?>">
                        <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="existing_avatar" value="<?= htmlspecialchars($user['avatar'] ?? '') ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="name" class="form-control" placeholder="First name" value="<?= htmlspecialchars($user['name'] ?? $old['name'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lastname" class="form-control" placeholder="Last name" value="<?= htmlspecialchars($user['lastname'] ?? $old['lastname'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" placeholder="email@example.com" value="<?= htmlspecialchars($user['email'] ?? $old['email'] ?? '') ?>">
                        </div>

                        <?php if (!$isEdit): ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Min 6 characters">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirm" class="form-control" placeholder="Repeat password">
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label>CIN</label>
                                <input type="text" name="cin" class="form-control" placeholder="Identity number" value="<?= htmlspecialchars($user['cin'] ?? $old['cin'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="tel" class="form-control" placeholder="+216 XX XXX XXX" value="<?= htmlspecialchars($user['tel'] ?? $old['tel'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="M" <?= (($user['gender'] ?? $old['gender'] ?? '') === 'M') ? 'selected' : '' ?>>Male</option>
                                    <option value="F" <?= (($user['gender'] ?? $old['gender'] ?? '') === 'F') ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control">
                                    <option value="client" <?= (($user['role'] ?? $old['role'] ?? '') === 'client') ? 'selected' : '' ?>>Client</option>
                                    <option value="mediator" <?= (($user['role'] ?? $old['role'] ?? '') === 'mediator') ? 'selected' : '' ?>>Mediator</option>
                                    <option value="admin" <?= (($user['role'] ?? $old['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Avatar</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                            <?php if (!empty($user['avatar'])): ?>
                            <div class="form-text" style="color:var(--text-muted);font-size:0.85rem;margin-top:0.25rem">Current: <?= htmlspecialchars(basename($user['avatar'])) ?></div>
                            <?php endif; ?>
                        </div>

                        <div style="display:flex;gap:1rem;margin-top:1.5rem">
                            <button type="submit" class="btn btn-success" style="flex:1">Save</button>
                            <a href="users.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
    <script src="../assets/validation.js"></script>
    <script>
        const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
        const fieldsConfig = {
            'name': [
                { type: 'required', message: 'First name is required' },
                { type: 'minLength', min: 2, message: 'First name must be at least 2 characters' }
            ],
            'lastname': [
                { type: 'required', message: 'Last name is required' }
            ],
            'email': [
                { type: 'required', message: 'Email is required' },
                { type: 'email', message: 'Please enter a valid email' }
            ]
        };
        
        if (!isEdit) {
            fieldsConfig['password'] = [
                { type: 'required', message: 'Password is required' },
                { type: 'minLength', min: 6, message: 'Password must be at least 6 characters' }
            ];
            fieldsConfig['password_confirm'] = [
                { type: 'required', message: 'Please confirm password' },
                { type: 'passwordMatch', confirmField: '[name="password"]', message: 'Passwords do not match' }
            ];
        }
        
        Validator.init('userForm', fieldsConfig);
    </script>
</body>
</html>


