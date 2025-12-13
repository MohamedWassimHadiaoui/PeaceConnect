<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/mediatorController.php";
$mc = new MediatorController();
$mediators = $mc->listMediators();
$availabilityLabels = [
    'available' => 'Available',
    'busy' => 'Busy',
    'unavailable' => 'Unavailable'
];
$availabilityBadges = [
    'available' => 'badge-resolved',
    'busy' => 'badge-medium',
    'unavailable' => 'badge-pending'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mediators Management - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <style>
        .panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; }
        .panel-header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .panel-header h1 { font-size: 1.5rem; margin: 0; }
        .panel-header p { color: var(--text-muted); margin: 0.25rem 0 0; font-size: 0.9rem; }
        .panel-body { padding: 0; }
        .panel-body .table { margin: 0; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container" style="padding-top: 2rem;">
            <div class="panel">
                <div class="panel-header">
                    <div>
                        <h1>⚖️ Mediators</h1>
                        <p>Manage mediators</p>
                    </div>
                    <a href="mediator_form.php" class="btn btn-success">+ New Mediator</a>
                </div>

                <div class="panel-body">
                <?php if (count($mediators) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Expertise</th>
                            <th>Availability</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mediators as $m): ?>
                        <tr>
                            <td><strong>#<?= $m['id'] ?></strong></td>
                            <td><?= htmlspecialchars($m['name']) ?></td>
                            <td><?= htmlspecialchars($m['email']) ?></td>
                            <td><?= htmlspecialchars($m['phone'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($m['expertise'] ?? '-') ?></td>
                            <?php $avail = $m['availability'] ?? 'available'; ?>
                            <td><span class="badge <?= $availabilityBadges[$avail] ?? 'badge-pending' ?>"><?= $availabilityLabels[$avail] ?? strtoupper($avail) ?></span></td>
                            <td style="white-space:nowrap">
                                <a href="mediator_form.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form action="../../Controller/mediatorController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="source" value="backoffice">
                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state" style="padding:3rem">
                    <p style="margin-bottom:1rem">No mediators yet</p>
                    <a href="mediator_form.php" class="btn btn-success">+ Add First Mediator</a>
                </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>


