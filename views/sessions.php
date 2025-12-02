<?php
require_once "../controller/sessionController.php";
$sc = new SessionController();
$sessions = $sc->listSessionsWithDetails();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Séances - PeaceConnect</title>
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
        .hero-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .hero h1 { font-size: 1.75rem; }
        
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        
        .btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-success { background: #10b981; color: white; }
        .btn-warning { background: #f59e0b; color: white; padding: 0.5rem 1rem; font-size: 0.875rem; }
        .btn-danger { background: #ef4444; color: white; padding: 0.5rem 1rem; font-size: 0.875rem; }
        
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table th { padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; background: #f8fafc; }
        .table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .table tr:hover { background: #f8fafc; }
        
        .badge { padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        
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
                <a href="reports.php">Signalements</a>
                <a href="mediators.php">Médiateurs</a>
                <a href="sessions.php" class="active">Séances</a>
                <a href="search.php">Recherche</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-container">
            <h1>📅 Séances de Médiation</h1>
            <a href="session_form.php" class="btn btn-success">+ Nouvelle Séance</a>
        </div>
    </section>

    <div class="container">
        <div class="card">
            <?php if (count($sessions) > 0): ?>
            <table class="table">
                <thead><tr><th>ID</th><th>Date</th><th>Heure</th><th>Signalement</th><th>Médiateur</th><th>Type</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($sessions as $s): ?>
                    <tr>
                        <td><strong>#<?php echo $s['id']; ?></strong></td>
                        <td><?php echo $s['session_date']; ?></td>
                        <td><?php echo $s['session_time']; ?></td>
                        <td><?php echo htmlspecialchars($s['report_title'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($s['mediator_name'] ?? 'N/A'); ?></td>
                        <td><?php echo $s['session_type']=='online'?'💻 En ligne':'🏢 Présentiel'; ?></td>
                        <td>
                            <?php 
                            $bc = 'badge-blue'; $st = 'PLANIFIÉE';
                            if ($s['status']=='completed') { $bc = 'badge-green'; $st = 'TERMINÉE'; }
                            elseif ($s['status']=='cancelled') { $bc = 'badge-red'; $st = 'ANNULÉE'; }
                            ?>
                            <span class="badge <?php echo $bc; ?>"><?php echo $st; ?></span>
                        </td>
                        <td>
                            <a href="session_form.php?id=<?php echo $s['id']; ?>" class="btn btn-warning">✏️</a>
                            <form action="../controller/sessionController.php" method="POST" style="display:inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer?')">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state"><div class="empty-state-icon">📅</div><p style="font-size:1.125rem;margin-bottom:1rem">Aucune séance</p><a href="session_form.php" class="btn btn-success">Planifier la première</a></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
