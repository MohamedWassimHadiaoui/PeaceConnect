<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/publicationController.php";
$pc = new PublicationController();
$publications = $pc->listAllPublications();
$categories = [
    'discussion' => 'Discussion',
    'help' => 'Help',
    'experience' => 'Experience',
    'legal' => 'Legal',
    'events' => 'Events',
    'resources' => 'Resources',
    'support' => 'Support',
    'question' => 'Question'
];
$pendingCount = count(array_filter($publications, fn($p) => ($p['statut'] ?? '') === 'pending'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Moderation - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <style>
        .panel { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; }
        .panel-header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .panel-header h1 { font-size: 1.5rem; margin: 0; }
        .panel-header p { color: var(--text-muted); margin: 0.25rem 0 0; font-size: 0.9rem; }
        .panel-body { padding: 0; }
        .panel-body .table { margin: 0; }
        .stat-pills { display: flex; gap: 0.75rem; }
        .stat-pill { padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem; background: var(--bg-input); }
        .stat-pill.pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
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
                        <h1>💬 Forum Moderation</h1>
                        <p>Moderate forum publications</p>
                    </div>
                    <div class="stat-pills">
                        <span class="stat-pill"><?= count($publications) ?> total</span>
                        <?php if ($pendingCount > 0): ?>
                        <span class="stat-pill pending"><?= $pendingCount ?> pending</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="panel-body">
                <?php if (count($publications) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Likes</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($publications as $p):
                            $status = $p['statut'] ?? 'pending';
                            $statusBadge = [
                                'pending' => 'badge-pending',
                                'approved' => 'badge-resolved',
                                'rejected' => 'badge-high'
                            ];
                            $badgeClass = $statusBadge[$status] ?? 'badge-pending';
                        ?>
                        <tr>
                            <td><strong>#<?= $p['id'] ?></strong></td>
                            <td><?= htmlspecialchars($p['titre']) ?></td>
                            <td><?= htmlspecialchars($p['auteur']) ?></td>
                            <td><span class="badge badge-assigned"><?= $categories[$p['categorie']] ?? $p['categorie'] ?></span></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span></td>
                            <td><?= (int)($p['nombre_likes'] ?? 0) ?></td>
                            <td><?= date('M d, Y', strtotime($p['created_at'])) ?></td>
                            <td style="white-space:nowrap">
                                <?php if ($status === 'pending'): ?>
                                <form action="../../Controller/publicationController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="source" value="backoffice">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success">✓ Approve</button>
                                </form>
                                <?php endif; ?>
                                <form action="../../Controller/publicationController.php" method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="source" value="backoffice">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state" style="padding:3rem">
                    <p>No publications yet</p>
                </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>
