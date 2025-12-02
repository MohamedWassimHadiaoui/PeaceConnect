<?php
require_once "../controller/reportController.php";
$rc = new ReportController();
$reports = $rc->listReportsWithMediators();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Signalements - PeaceConnect</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; }
        
        .navbar { background: white; padding: 1rem 0; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar-container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; font-size: 1.5rem; font-weight: 700; color: #1e293b; }
        .navbar-links { display: flex; gap: 0.5rem; }
        .navbar-links a { padding: 0.5rem 1rem; text-decoration: none; color: #64748b; font-weight: 500; border-radius: 6px; }
        .navbar-links a:hover { color: #2563eb; background: #f1f5f9; }
        .navbar-links a.active { color: #2563eb; background: #eff6ff; }
        
        .hero { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 2rem; color: white; }
        .hero-container { max-width: 1200px; margin: 0 auto; }
        .hero h1 { font-size: 1.75rem; }
        
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table th { padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; background: #f8fafc; }
        .table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .table tr:hover { background: #f8fafc; }
        
        .badge { padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-high { background: #fee2e2; color: #991b1b; }
        .badge-medium { background: #fef3c7; color: #92400e; }
        .badge-low { background: #dbeafe; color: #1e40af; }
        .badge-resolved { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #f3f4f6; color: #374151; }
        .badge-assigned { background: #e0e7ff; color: #3730a3; }
        .badge-in_mediation { background: #fef3c7; color: #92400e; }
        
        .btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-warning { background: #f59e0b; color: white; padding: 0.5rem 1rem; font-size: 0.875rem; }
        .btn-danger { background: #ef4444; color: white; padding: 0.5rem 1rem; font-size: 0.875rem; }
        
        .empty-state { padding: 4rem 2rem; text-align: center; color: #64748b; }
        .empty-state-icon { font-size: 4rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.html" class="navbar-brand">🕊️ PeaceConnect</a>
            <div class="navbar-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="reports.php" class="active">Signalements</a>
                <a href="mediators.php">Médiateurs</a>
                <a href="sessions.php">Séances</a>
                <a href="search.php">Recherche</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-container">
            <h1>📋 Gestion des Signalements</h1>
        </div>
    </section>

    <div class="container">
        <div class="card">
            <?php if (count($reports) > 0): ?>
            <table class="table">
                <thead><tr><th>ID</th><th>Titre</th><th>Type</th><th>Priorité</th><th>Médiateur</th><th>Pièce jointe</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($reports as $r): ?>
                    <tr>
                        <td><strong>#<?php echo $r['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($r['title']); ?></td>
                        <td><?php echo htmlspecialchars($r['type']); ?></td>
                        <td><span class="badge badge-<?php echo $r['priority']; ?>"><?php echo strtoupper($r['priority']); ?></span></td>
                        <td><?php echo !empty($r['mediator_name']) ? htmlspecialchars($r['mediator_name']) : '<span style="color:#9ca3af;font-style:italic">Non assigné</span>'; ?></td>
                        <td>
                            <?php if (!empty($r['attachment_path'])): ?>
                                <a href="../<?php echo htmlspecialchars($r['attachment_path']); ?>" target="_blank">📎 Voir</a>
                            <?php else: ?>
                                <span style="color:#9ca3af;font-style:italic">Aucune</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-<?php echo $r['status']; ?>"><?php echo strtoupper($r['status']); ?></span></td>
                        <td>
                            <a href="report_form.php?id=<?php echo $r['id']; ?>" class="btn btn-warning">✏️ Modifier</a>
                            <form action="../controller/reportController.php" method="POST" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer ce signalement?')">🗑️ Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state"><div class="empty-state-icon">📭</div><p style="font-size:1.125rem;">Aucun signalement</p></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
