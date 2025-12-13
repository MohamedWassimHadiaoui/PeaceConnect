<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/authMiddleware.php";
requireLogin();
require_once __DIR__ . "/../../Controller/reportController.php";

$rc = new ReportController();
$reports = $rc->listReportsByUser($_SESSION['user_id']);

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

$statuses = ['pending'=>'Pending','assigned'=>'Assigned','in_mediation'=>'In Mediation','resolved'=>'Resolved','rejected'=>'Rejected'];
$priorities = ['high'=>'High','medium'=>'Medium','low'=>'Low'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem 0;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-header h1 { font-size: 2rem; font-weight: 700; margin: 0; }
        .report-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .report-card:hover {
            border-color: var(--primary);
            box-shadow: 0 5px 20px var(--shadow);
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .report-id { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.25rem; }
        .report-title { font-size: 1.15rem; font-weight: 600; }
        .report-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .report-meta span { display: flex; align-items: center; gap: 0.25rem; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container">
            <div class="page-header">
                <h1>My Reports</h1>
                <a href="create_report.php" class="btn btn-success">+ New Report</a>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success"><div><?= htmlspecialchars($success) ?></div></div>
            <?php endif; ?>

            <?php if (count($reports) > 0): ?>
                <?php foreach ($reports as $r): ?>
                <div class="report-card">
                    <div class="report-header">
                        <div>
                            <div class="report-id">#<?= $r['id'] ?> &middot; <?= date('M j, Y', strtotime($r['created_at'])) ?></div>
                            <div class="report-title"><?= htmlspecialchars($r['title']) ?></div>
                        </div>
                        <span class="badge badge-<?= $r['status'] ?>"><?= $statuses[$r['status']] ?? $r['status'] ?></span>
                    </div>
                    <div class="report-meta">
                        <span>Type: <?= htmlspecialchars(ucfirst($r['type'])) ?></span>
                        <span><?= $priorities[$r['priority']] ?? $r['priority'] ?></span>
                        <?php if (!empty($r['location'])): ?>
                        <span>Location: <?= htmlspecialchars(substr($r['location'], 0, 30)) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="card">
                <div class="empty-state">
                    <h3>No Reports Yet</h3>
                    <p style="color:var(--text-muted);margin-bottom:1.5rem">You haven't submitted any reports.</p>
                    <a href="create_report.php" class="btn btn-success">Create Your First Report</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>

