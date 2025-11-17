<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Signalement - PeaceConnect Back-Office</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        input[type="text"], textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
        textarea { min-height: 100px; resize: vertical; }
        .btn { padding: 12px 24px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background-color: #45a049; }
        .btn-secondary { background-color: #6c757d; margin-right: 10px; }
        .btn-secondary:hover { background-color: #5a6268; }
        .error-message { color: #f44336; font-size: 0.875rem; margin-top: 0.25rem; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📋 Ajouter un Signalement</h2>
        <form method="POST" action="../../controller/add_report.php">
            <div class="form-group">
                <label for="type">Type *</label>
                <input type="text" id="type" name="type" placeholder="Ex: Conflit, Harcèlement, Violence..." required>
            </div>
            
            <div class="form-group">
                <label for="title">Titre *</label>
                <input type="text" id="title" name="title" placeholder="Titre du signalement" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" placeholder="Description détaillée..." required></textarea>
            </div>
            
            <div class="form-group">
                <label for="location">Lieu (optionnel)</label>
                <input type="text" id="location" name="location" placeholder="Lieu de l'incident">
            </div>
            
            <div class="form-group">
                <label for="incident_date">Date de l'incident (AAAA-MM-JJ)</label>
                <input type="text" id="incident_date" name="incident_date" placeholder="2025-01-15">
            </div>
            
            <div class="form-group">
                <label for="priority">Priorité *</label>
                <select id="priority" name="priority" required>
                    <option value="low">Basse</option>
                    <option value="medium" selected>Moyenne</option>
                    <option value="high">Haute</option>
                </select>
            </div>
            
            <div style="margin-top: 30px;">
                <a href="reports.php" class="btn btn-secondary" style="text-decoration: none; display: inline-block;">← Retour</a>
                <button type="submit" class="btn">Ajouter</button>
            </div>
        </form>
    </div>
    
    <script src="../../assets/js/backoffice-validation.js"></script>
</body>
</html>

