<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/helpRequestController.php";
$hc = new HelpRequestController();
$requests = $hc->listAllRequests();
$types = ['social' => 'Social', 'legal' => 'Legal', 'psychological' => 'Psychological'];
$urgencies = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'];
$statuses = ['pending' => 'Pending', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Requests - PeaceConnect</title>
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
                        <h1>🤝 Help Requests</h1>
                        <p>Manage assistance requests</p>
                    </div>
                    <span class="badge badge-assigned" style="font-size:0.9rem"><?= count($requests) ?> total</span>
                </div>

                <div class="panel-body">
                <?php if (count($requests) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Requester</th>
                            <th>Type</th>
                            <th>Urgency</th>
                            <th>Situation</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $r): ?>
                        <tr>
                            <td><strong>#<?= $r['id'] ?></strong></td>
                            <td><?= htmlspecialchars($r['user_name'] ?? 'Anonymous') ?></td>
                            <td><span class="badge badge-assigned"><?= $types[$r['help_type']] ?? $r['help_type'] ?></span></td>
                            <td><span class="badge badge-<?= $r['urgency_level'] ?>"><?= $urgencies[$r['urgency_level']] ?? ucfirst($r['urgency_level'] ?? '') ?></span></td>
                            <td><?= htmlspecialchars(mb_strlen($r['situation'] ?? '') > 60 ? mb_substr($r['situation'], 0, 60) . '...' : ($r['situation'] ?? '')) ?></td>
                            <td><span class="badge badge-<?= str_replace('_', '-', $r['status']) ?>"><?= $statuses[$r['status']] ?? ucfirst($r['status'] ?? '') ?></span></td>
                            <td><?= htmlspecialchars($r['responsable'] ?? '-') ?></td>
                            <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                            <td style="white-space:nowrap">
                                <a href="help_request_form.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form action="../../Controller/helpRequestController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="source" value="backoffice">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state" style="padding:3rem">
                    <p>No help requests yet</p>
                </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>
