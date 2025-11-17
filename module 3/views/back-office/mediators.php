<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Médiateurs - PeaceConnect Back-Office</title>
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
        .status-available { background-color: #e8f5e9; color: #388e3c; }
        .status-busy { background-color: #fff3e0; color: #f57c00; }
        .status-unavailable { background-color: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <div class="container">
        <h2>👥 Gestion des Médiateurs</h2>
        <a href="add_mediator.php" class="btn">+ Ajouter un Médiateur</a>
        <a href="reports.php" class="btn" style="background-color: #2196F3; margin-left: 10px;">📋 Signalements</a>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Expertise</th>
                    <th>Disponibilité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once "../../controller/mediatorController.php";
                $mc = new mediatorController();
                $mediators = $mc->listMediators();
                foreach($mediators as $m): 
                ?>
                <tr>
                    <td><?= htmlspecialchars($m['id']) ?></td>
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['phone'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($m['expertise']) ?></td>
                    <td><span class="badge status-<?= htmlspecialchars($m['availability']) ?>"><?= ucfirst(htmlspecialchars($m['availability'])) ?></span></td>
                    <td class="actions">
                        <a href="update_mediator.php?id=<?= htmlspecialchars($m['id']) ?>" class="btn-edit">✏️ Modifier</a>
                        <form method="POST" action="../../controller/delete_mediator.php" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce médiateur ?');">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($m['id']) ?>">
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

