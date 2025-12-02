<?php
require_once "../controller/reportController.php";
require_once "../controller/mediatorController.php";
require_once "../controller/sessionController.php";

$rc = new ReportController();
$mc = new MediatorController();
$sc = new SessionController();

$totalReports = $rc->countAll();
$pendingReports = $rc->countByStatus('pending');
$resolvedReports = $rc->countByStatus('resolved');
$totalMediators = $mc->countAll();
$totalSessions = $sc->countAll();
$resolutionRate = $totalReports > 0 ? round(($resolvedReports / $totalReports) * 100) : 0;
$recentReports = array_slice($rc->listReportsWithMediators(), 0, 5);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - PeaceConnect</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; }
        
        /* Navbar */
        .navbar {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }
        .navbar-links { display: flex; gap: 0.5rem; }
        .navbar-links a {
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            border-radius: 6px;
        }
        .navbar-links a:hover { color: #2563eb; background: #f1f5f9; }
        .navbar-links a.active { color: #2563eb; background: #eff6ff; }
        
        /* Hero */
        .hero { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 2rem; color: white; }
        .hero-container { max-width: 1200px; margin: 0 auto; }
        .hero h1 { font-size: 1.75rem; margin-bottom: 0.25rem; }
        .hero p { opacity: 0.8; }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .stat-label { color: #64748b; font-size: 0.875rem; }
        .stat-icon { font-size: 1.5rem; }
        .stat-value { font-size: 2rem; font-weight: 700; color: #1e293b; }
        
        /* Quick Actions */
        .section-title { font-size: 1.25rem; color: #1e293b; margin-bottom: 1rem; }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .action-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .action-card-icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .action-card-title { font-weight: 600; color: #1e293b; }
        
        /* Card */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-title { font-size: 1.125rem; font-weight: 600; color: #1e293b; }
        
        /* Table */
        .table { width: 100%; border-collapse: collapse; }
        .table th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            background: #f8fafc;
        }
        .table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .table tr:hover { background: #f8fafc; }
        
        /* Badge */
        .badge { padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-high { background: #fee2e2; color: #991b1b; }
        .badge-medium { background: #fef3c7; color: #92400e; }
        .badge-low { background: #dbeafe; color: #1e40af; }
        .badge-resolved { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #f3f4f6; color: #374151; }
        
        /* Button */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-primary { background: #2563eb; color: white; }
        .btn-sm { padding: 0.375rem 0.75rem; }
        
        .empty-state { padding: 3rem; text-align: center; color: #64748b; }
        .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; }
        
        @media (max-width: 1024px) { .stats-grid, .quick-actions { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.html" class="navbar-brand">🕊️ PeaceConnect</a>
            <div class="navbar-links">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="reports.php">Signalements</a>
                <a href="mediators.php">Médiateurs</a>
                <a href="sessions.php">Séances</a>
                <a href="search.php">Recherche</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-container">
            <h1>📊 Dashboard Administration</h1>
            <p>Vue d'ensemble du système de gestion</p>
        </div>
    </section>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header"><span class="stat-label">Total Signalements</span><span class="stat-icon">📋</span></div>
                <div class="stat-value"><?php echo $totalReports; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header"><span class="stat-label">En Attente</span><span class="stat-icon">⏳</span></div>
                <div class="stat-value"><?php echo $pendingReports; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header"><span class="stat-label">Résolus</span><span class="stat-icon">✅</span></div>
                <div class="stat-value"><?php echo $resolvedReports; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header"><span class="stat-label">Taux de Résolution</span><span class="stat-icon">📈</span></div>
                <div class="stat-value"><?php echo $resolutionRate; ?>%</div>
            </div>
        </div>

        <h2 class="section-title">Actions Rapides</h2>
        <div class="quick-actions">
            <a href="mediator_form.php" class="action-card"><div class="action-card-icon">👤</div><div class="action-card-title">Ajouter Médiateur</div></a>
            <a href="session_form.php" class="action-card"><div class="action-card-icon">📅</div><div class="action-card-title">Planifier Séance</div></a>
            <a href="search.php" class="action-card"><div class="action-card-icon">🔍</div><div class="action-card-title">Rechercher</div></a>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Derniers Signalements</h2>
                <a href="reports.php" class="btn btn-primary btn-sm">Voir tout</a>
            </div>
            <?php if (count($recentReports) > 0): ?>
            <table class="table">
                <thead><tr><th>ID</th><th>Titre</th><th>Type</th><th>Priorité</th><th>Médiateur</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($recentReports as $r): ?>
                    <tr>
                        <td><strong>#<?php echo $r['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($r['title']); ?></td>
                        <td><?php echo htmlspecialchars($r['type']); ?></td>
                        <td><span class="badge badge-<?php echo $r['priority']; ?>"><?php echo strtoupper($r['priority']); ?></span></td>
                        <td><?php echo !empty($r['mediator_name']) ? htmlspecialchars($r['mediator_name']) : '<span style="color:#9ca3af">Non assigné</span>'; ?></td>
                        <td><span class="badge badge-<?php echo $r['status']; ?>"><?php echo strtoupper($r['status']); ?></span></td>
                        <td><a href="report_form.php?id=<?php echo $r['id']; ?>" class="btn btn-primary btn-sm">Modifier</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state"><div class="empty-state-icon">📭</div><p>Aucun signalement</p></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
