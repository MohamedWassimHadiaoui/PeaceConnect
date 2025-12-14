<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/organisationController.php";

$oc = new OrganisationController();
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$city = trim($_GET['city'] ?? '');
$organisations = $oc->searchOrganisations($q, $category, $city, false);

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organisations - Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <style>
        .panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; }
        .panel-header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .panel-header h1 { font-size: 1.5rem; margin: 0; }
        .panel-header p { color: var(--text-muted); margin: 0.25rem 0 0; font-size: 0.9rem; }
        .panel-filters { padding: 1rem 2rem; background: var(--bg-input); border-bottom: 1px solid var(--border-color); display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
        .panel-filters input { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--text-primary); font-size: 0.9rem; min-width: 140px; }
        .panel-filters input:focus { outline: none; border-color: var(--primary); }
        .panel-filters .btn { padding: 0.5rem 1rem; font-size: 0.9rem; }
        .panel-body { padding: 0; }
        .panel-body .table { margin: 0; }
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

            <div class="panel">
                <div class="panel-header">
                    <div>
                        <h1>🏢 Organisations</h1>
                        <p>Manage partner organisations</p>
                    </div>
                    <a class="btn btn-success" href="organisation_form.php">+ New Organisation</a>
                </div>

                <form class="panel-filters" method="GET" action="organisations.php">
                    <input type="text" name="q" placeholder="🔍 Search..." value="<?= htmlspecialchars($q) ?>">
                    <input type="text" name="category" placeholder="Category" value="<?= htmlspecialchars($category) ?>">
                    <input type="text" name="city" placeholder="City" value="<?= htmlspecialchars($city) ?>">
                    <button class="btn btn-primary" type="submit">Filter</button>
                    <?php if ($q || $category || $city): ?>
                    <a class="btn btn-sm" href="organisations.php" style="background:transparent;color:var(--text-muted)">✕ Clear</a>
                    <?php endif; ?>
                </form>

                <div class="panel-body">
                <?php if (count($organisations) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($organisations as $o): ?>
                        <tr>
                            <td><strong>#<?= (int)$o['id'] ?></strong></td>
                            <td><?= htmlspecialchars($o['name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($o['category'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($o['city'] ?? '-') ?></td>
                            <td><span class="badge <?= ($o['status'] ?? 'active') === 'active' ? 'badge-resolved' : 'badge-pending' ?>"><?= ucfirst($o['status'] ?? 'active') ?></span></td>
                            <td style="white-space:nowrap">
                                <a class="btn btn-sm btn-primary" href="organisation_form.php?id=<?= (int)$o['id'] ?>">Edit</a>
                                <form action="../../Controller/organisationController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="source" value="backoffice">
                                    <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state" style="padding:3rem">
                    <p style="margin-bottom:1rem">No organisations yet</p>
                    <a class="btn btn-success" href="organisation_form.php">+ Create First</a>
                </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>
