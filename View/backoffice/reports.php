<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/reportController.php";
require_once __DIR__ . "/../../Controller/mediatorController.php";

$rc = new ReportController();
$mc = new MediatorController();
$reports = $rc->listReportsWithMediators();
$mediators = $mc->listMediators();

$statusLabels = ['pending'=>'Pending','assigned'=>'Assigned','in_mediation'=>'In Mediation','resolved'=>'Resolved'];
$priorityLabels = ['high'=>'High','medium'=>'Medium','low'=>'Low'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Management - PeaceConnect</title>
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
                        <h1>📋 Reports</h1>
                        <p>Manage all submitted reports</p>
                    </div>
                    <span class="badge badge-assigned" style="font-size:0.9rem"><?= count($reports) ?> total</span>
                </div>

                <div class="panel-body">
                <?php if (count($reports) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Priority</th>
                            <th>Mediator</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $r): ?>
                        <tr>
                            <td><strong>#<?= $r['id'] ?></strong></td>
                            <td><?= htmlspecialchars($r['title']) ?></td>
                            <td><?= htmlspecialchars($r['type']) ?></td>
                            <td><span class="badge badge-<?= $r['priority'] ?>"><?= $priorityLabels[$r['priority']] ?? $r['priority'] ?></span></td>
                            <td><?= $r['mediator_name'] ? htmlspecialchars($r['mediator_name']) : '<em style="opacity:0.5">Not assigned</em>' ?></td>
                            <td><span class="badge badge-<?= $r['status'] ?>"><?= $statusLabels[$r['status']] ?? $r['status'] ?></span></td>
                            <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                            <td style="white-space:nowrap">
                                <a href="report_form.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form action="../../Controller/reportController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="source" value="backoffice">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this report?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state" style="padding:3rem">
                    <p>No reports yet</p>
                </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>
