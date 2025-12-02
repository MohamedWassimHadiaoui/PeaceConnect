<?php
require_once "../controller/reportController.php";
require_once "../controller/mediatorController.php";

$rc = new ReportController();
$mc = new MediatorController();
$mediators = $mc->listMediators();
$results = [];
$selected = null;

if (isset($_GET['mediator_id']) && $_GET['mediator_id'] != '') {
    $selected = $_GET['mediator_id'];
    $results = $rc->getReportsByMediator($selected);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche - PeaceConnect</title>
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
        .hero h1 { font-size: 1.75rem; margin-bottom: 0.25rem; }
        .hero p { opacity: 0.8; }
        
        .container { max-width: 1000px; margin: 2rem auto; padding: 0 2rem; }
        
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 2rem; margin-bottom: 1.5rem; }
        .card-title { font-size: 1.125rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem; }
        
        .form-group { margin-bottom: 1rem; }
        .form-control { width: 100%; padding: 0.875rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; }
        .form-control:focus { outline: none; border-color: #2563eb; }
        
        .btn { padding: 0.875rem 1.5rem; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table th { padding: 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; background: #f8fafc; }
        .table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; }
        .table tr:hover { background: #f8fafc; }
        
        .badge { padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-high { background: #fee2e2; color: #991b1b; }
        .badge-medium { background: #fef3c7; color: #92400e; }
        .badge-low { background: #dbeafe; color: #1e40af; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
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
                <a href="sessions.php">Séances</a>
                <a href="search.php" class="active">Recherche</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-container">
            <h1>🔍 Recherche par Médiateur</h1>
            <p>Trouvez tous les signalements assignés à un médiateur</p>
        </div>
    </section>

    <div class="container">
        <div class="card">
            <h3 class="card-title">Sélectionner un médiateur</h3>
            <form method="GET">
                <div class="form-group">
                    <select name="mediator_id" class="form-control">
                        <option value="">-- Choisir un médiateur --</option>
                        <?php foreach ($mediators as $m): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo ($selected == $m['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($m['name']); ?> - <?php echo htmlspecialchars($m['expertise']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">🔍 Rechercher</button>
            </form>
        </div>

        <?php if ($selected !== null): ?>
        <div class="card">
            <h3 class="card-title">Résultats: <?php echo count($results); ?> signalement(s) trouvé(s)</h3>
            <?php if (count($results) > 0): ?>
            <table class="table">
                <thead><tr><th>ID</th><th>Titre</th><th>Type</th><th>Priorité</th><th>Médiateur</th><th>Statut</th></tr></thead>
                <tbody>
                    <?php foreach ($results as $r): ?>
                    <tr>
                        <td><strong>#<?php echo $r['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($r['title']); ?></td>
                        <td><?php echo htmlspecialchars($r['type']); ?></td>
                        <td><span class="badge badge-<?php echo $r['priority']; ?>"><?php echo strtoupper($r['priority']); ?></span></td>
                        <td><?php echo htmlspecialchars($r['mediator_name']); ?></td>
                        <td><span class="badge <?php echo $r['status']=='resolved'?'badge-green':'badge-blue'; ?>"><?php echo strtoupper($r['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align:center;color:#64748b;padding:2rem">Aucun signalement trouvé pour ce médiateur</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
