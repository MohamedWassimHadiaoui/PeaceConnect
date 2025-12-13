<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/sessionController.php";

$sc = new SessionController();
$sessions = $sc->listSessionsWithDetails();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mediation Sessions - PeaceConnect</title>
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
                        <h1>🗓️ Mediation Sessions</h1>
                        <p>Plan and track mediation sessions</p>
                    </div>
                    <a href="session_form.php" class="btn btn-success">+ New Session</a>
                </div>

                <div class="panel-body">
                <?php if (count($sessions) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Report</th>
                            <th>Mediator</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $s): ?>
                        <tr>
                            <td><strong>#<?= $s['id'] ?></strong></td>
                            <td><?= htmlspecialchars($s['session_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['session_time'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['report_title'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($s['mediator_name'] ?? 'N/A') ?></td>
                            <td><?= ($s['session_type'] ?? '') === 'online' ? '🌐 Online' : '📍 In-person' ?></td>
                            <td>
                                <?php
                                $badges = [
                                    'scheduled' => ['badge-assigned', 'Scheduled'],
                                    'completed' => ['badge-resolved', 'Completed'],
                                    'cancelled' => ['badge-high', 'Cancelled']
                                ];
                                $status = $s['status'] ?? 'scheduled';
                                $b = $badges[$status] ?? ['badge-pending', ucfirst($status)];
                                ?>
                                <span class="badge <?= $b[0] ?>"><?= $b[1] ?></span>
                            </td>
                            <td style="white-space:nowrap">
                                <a href="session_form.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form action="../../Controller/sessionController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="source" value="backoffice">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state" style="padding:3rem">
                    <p style="margin-bottom:1rem">No sessions yet</p>
                    <a href="session_form.php" class="btn btn-success">+ Create First Session</a>
                </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>
