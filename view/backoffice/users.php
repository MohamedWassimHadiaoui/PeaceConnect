<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/userController.php";
$uc = new UserController();

// Get filter parameters
$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';

// Use filtered search if parameters exist
if (!empty($search) || !empty($roleFilter)) {
    $users = $uc->searchUsers($search, $roleFilter);
} else {
    $users = $uc->listUsers();
}

$roles = ['admin' => 'Admin', 'client' => 'User'];

$success = $_SESSION['success'] ?? null;
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['success'], $_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <style>
        .panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; }
        .panel-header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .panel-header h1 { font-size: 1.5rem; margin: 0; }
        .panel-header p { color: var(--text-muted); margin: 0.25rem 0 0; font-size: 0.9rem; }
        .panel-filters { padding: 1rem 2rem; background: var(--bg-input); border-bottom: 1px solid var(--border-color); display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
        .panel-filters input, .panel-filters select { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-primary); font-size: 0.9rem; min-width: 180px; }
        .panel-filters input:focus, .panel-filters select:focus { outline: none; border-color: var(--primary); }
        .panel-filters .btn { padding: 0.5rem 1.25rem; font-size: 0.9rem; }
        .panel-body { padding: 0; }
        .panel-body .table { margin: 0; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; color: #fff; font-weight: 600; }
        .result-info { padding: 0.75rem 2rem; background: rgba(99, 102, 241, 0.1); color: var(--primary); font-size: 0.85rem; border-bottom: 1px solid var(--border-color); }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container" style="padding-top: 2rem;">
            <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom:1rem"><div><?= htmlspecialchars($success) ?></div></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="margin-bottom:1rem"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <div class="panel">
                <div class="panel-header">
                    <div>
                        <h1>👥 Users</h1>
                        <p>Manage user accounts</p>
                    </div>
                    <a href="user_form.php" class="btn btn-success">+ Add User</a>
                </div>

                <form class="panel-filters" method="GET">
                    <input type="text" name="search" placeholder="🔍 Search name or email..." value="<?= htmlspecialchars($search) ?>">
                    <select name="role">
                        <option value="">All Roles</option>
                        <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="client" <?= $roleFilter === 'client' ? 'selected' : '' ?>>User</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <?php if (!empty($search) || !empty($roleFilter)): ?>
                    <a href="users.php" class="btn btn-sm" style="background:transparent;color:var(--text-muted)">✕ Clear</a>
                    <?php endif; ?>
                </form>

                <?php if (!empty($search) || !empty($roleFilter)): ?>
                <div class="result-info">
                    Found <strong><?= count($users) ?></strong> user(s)
                    <?php if (!empty($search)): ?> matching "<strong><?= htmlspecialchars($search) ?></strong>"<?php endif; ?>
                    <?php if (!empty($roleFilter)): ?> with role "<strong><?= htmlspecialchars($roleFilter) ?></strong>"<?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="panel-body">
                <?php if (count($users) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>2FA</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <?php if (!empty($u['avatar'])): ?>
                                <img src="../../<?= htmlspecialchars($u['avatar']) ?>" class="avatar" alt="Avatar">
                                <?php else: ?>
                                <div class="avatar"><?= htmlspecialchars(strtoupper(substr((string)($u['name'] ?? 'U'), 0, 1))) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($u['name'] . ' ' . ($u['lastname'] ?? '')) ?></strong></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['tel'] ?? 'N/A') ?></td>
                            <td><span class="badge <?= $u['role']==='admin'?'badge-high':'badge-assigned' ?>"><?= $roles[$u['role']] ?? $u['role'] ?></span></td>
                            <td><?= ($u['two_factor_enabled'] ?? 0) ? 'Enabled' : 'Disabled' ?></td>
                            <td style="white-space:nowrap">
                                <a href="user_form.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <form action="../../Controller/userController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="source" value="backoffice">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state" style="padding:3rem">
                    <p style="margin-bottom:1rem">No users found</p>
                    <a href="user_form.php" class="btn btn-primary">+ Add First User</a>
                </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>

