<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Signalements - PeaceConnect Back-Office</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin-bottom: 20px; }
        .btn:hover { background-color: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: 600; color: #333; }
        tr:hover { background-color: #f5f5f5; }
        .actions { display: flex; gap: 10px; }
        .btn-edit { color: #2196F3; text-decoration: none; }
        .btn-delete { background: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .btn-delete:hover { background: #da190b; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.875rem; }
        .badge-low { background-color: #e3f2fd; color: #1976d2; }
        .badge-medium { background-color: #fff3e0; color: #f57c00; }
        .badge-high { background-color: #ffebee; color: #c62828; }
        .status-pending { background-color: #fff9c4; color: #f57f17; }
        .status-assigned { background-color: #e3f2fd; color: #1976d2; }
        .status-resolved { background-color: #e8f5e9; color: #388e3c; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📋 Gestion des Signalements</h2>
        <a href="add_report.php" class="btn">+ Ajouter un Signalement</a>
        <a href="mediators.php" class="btn" style="background-color: #2196F3; margin-left: 10px;">👥 Médiateurs</a>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Titre</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once "../../controller/reportController.php";
                $rc = new reportController();
                $reports = $rc->listReports();
                foreach($reports as $r): 
                ?>
                <tr>
                    <td><?= htmlspecialchars($r['id']) ?></td>
                    <td><?= htmlspecialchars($r['type']) ?></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><span class="badge badge-<?= htmlspecialchars($r['priority']) ?>"><?= ucfirst(htmlspecialchars($r['priority'])) ?></span></td>
                    <td><span class="badge status-<?= htmlspecialchars($r['status']) ?>"><?= ucfirst(htmlspecialchars($r['status'])) ?></span></td>
                    <td class="actions">
                        <a href="update_report.php?id=<?= htmlspecialchars($r['id']) ?>" class="btn-edit">✏️ Modifier</a>
                        <form method="POST" action="../../controller/delete_report.php" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce signalement ?');">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($r['id']) ?>">
                            <button type="submit" class="btn-delete">🗑️ Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

