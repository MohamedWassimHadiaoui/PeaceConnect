<?php
require_once "../controller/reportController.php";
$controller = new ReportController();
$reports = $controller->listReportsWithMediators();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes signalements - PeaceConnect</title>
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
        .hero {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            padding: 3rem 2rem;
            text-align: center;
            color: white;
        }
        .hero h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        
        /* Container */
        .container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-header h2 { color: #1e293b; }
        
        /* Button */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            color: white;
        }
        .btn-primary:hover { transform: translateY(-2px); }
        
        /* Grid */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        /* Card */
        .report-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .card-title { font-size: 1.125rem; font-weight: 600; color: #1e293b; }
        
        /* Badge */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-high { background: #fee2e2; color: #991b1b; }
        .badge-medium { background: #fef3c7; color: #92400e; }
        .badge-low { background: #dbeafe; color: #1e40af; }
        .badge-pending { background: #f3f4f6; color: #374151; }
        .badge-assigned { background: #e0e7ff; color: #3730a3; }
        .badge-in_mediation { background: #fef3c7; color: #92400e; }
        .badge-resolved { background: #d1fae5; color: #065f46; }
        
        .info-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        .info-row strong { color: #1e293b; }
        
        .mediator-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        .mediator-box h4 { color: #1e40af; font-size: 0.875rem; margin-bottom: 0.25rem; }
        .mediator-box p { color: #1e40af; font-size: 0.875rem; }
        
        /* Empty */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .empty-state-icon { font-size: 4rem; margin-bottom: 1rem; }
        .empty-state h2 { color: #1e293b; margin-bottom: 0.5rem; }
        .empty-state p { color: #64748b; margin-bottom: 1.5rem; }
        
        @media (max-width: 768px) { .reports-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.html" class="navbar-brand">🕊️ PeaceConnect</a>
            <div class="navbar-links">
                <a href="../index.html">Accueil</a>
                <a href="create_report.php">Signaler</a>
                <a href="my_reports.php" class="active">Mes signalements</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <h1>📋 Mes Signalements</h1>
        <p>Suivez l'avancement de vos signalements en temps réel</p>
    </section>

    <div class="container">
        <div class="page-header">
            <h2><?php echo count($reports); ?> signalement(s)</h2>
            <a href="create_report.php" class="btn btn-primary">+ Nouveau signalement</a>
        </div>

        <?php if (count($reports) > 0): ?>
        <div class="reports-grid">
            <?php foreach ($reports as $r): ?>
            <div class="report-card">
                <div class="card-header">
                    <div class="card-title"><?php echo htmlspecialchars($r['title']); ?></div>
                    <span class="badge badge-<?php echo $r['priority']; ?>"><?php echo strtoupper($r['priority']); ?></span>
                </div>
                <div class="info-row"><span>📌</span><strong>Type:</strong> <?php echo htmlspecialchars($r['type']); ?></div>
                <div class="info-row"><span>📅</span><strong>Créé le:</strong> <?php echo isset($r['created_at']) ? $r['created_at'] : 'N/A'; ?></div>
                <div class="info-row">
                    <strong>Statut:</strong>
                    <span class="badge badge-<?php echo $r['status']; ?>">
                        <?php 
                        $statuts = ['pending' => '⏳ En attente', 'assigned' => '👤 Assigné', 'in_mediation' => '🤝 En médiation', 'resolved' => '✅ Résolu'];
                        echo isset($statuts[$r['status']]) ? $statuts[$r['status']] : $r['status'];
                        ?>
                    </span>
                </div>
                <?php if (!empty($r['location'])): ?>
                <div class="info-row"><span>📍</span><strong>Lieu:</strong> <?php echo htmlspecialchars($r['location']); ?></div>
                <?php endif; ?>
                <?php if (!empty($r['attachment_path'])): ?>
                <div class="info-row"><span>📎</span><strong>Pièce jointe:</strong> <a href="../<?php echo htmlspecialchars($r['attachment_path']); ?>" target="_blank">Ouvrir le fichier</a></div>
                <?php endif; ?>
                <?php if (!empty($r['mediator_name'])): ?>
                <div class="mediator-box">
                    <h4>👤 Médiateur assigné</h4>
                    <p><strong><?php echo htmlspecialchars($r['mediator_name']); ?></strong></p>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📭</div>
            <h2>Aucun signalement</h2>
            <p>Vous n'avez pas encore créé de signalement</p>
            <a href="create_report.php" class="btn btn-primary">Créer mon premier signalement</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
