<?php
/**
 * View/BackOffice/organisations/search.php
 * Page de recherche avancée pour les organisations (Admin)
 */
$section = isset($_GET['section']) ? $_GET['section'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rechercher une Organisation</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <div class="container">
        <header>
            <h1>🕊️ PeaceConnect - Administration</h1>
            <nav>
                <a href="index.php">Accueil</a> |
                <a href="index.php?action=dashboard">Tableau de bord</a> |
                <a href="index.php?action=organisations&section=<?= htmlspecialchars($section) ?>">Organisations</a>
            </nav>
        </header>

        <main>
            <section class="search-section">
                <h2>🔍 Rechercher une Organisation</h2>

                <form method="POST" class="search-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nom</label>
                            <input type="text" id="name" name="name" 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
                        </div>

                        <div class="form-group">
                            <label for="category">Catégorie</label>
                            <input type="text" id="category" name="category" 
                                   value="<?= htmlspecialchars($_POST['category'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Ville</label>
                            <input type="text" id="city" name="city" 
                                   value="<?= htmlspecialchars($_POST['city'] ?? '') ?>" />
                        </div>

                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select id="status" name="status">
                                <option value="">Tous</option>
                                <option value="active" <?= (($_POST['status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (($_POST['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                <option value="suspended" <?= (($_POST['status'] ?? '') === 'suspended') ? 'selected' : '' ?>>Suspendue</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Rechercher</button>
                        <a href="index.php?action=organisations&method=search&section=<?= htmlspecialchars($section) ?>" class="btn btn-secondary">Réinitialiser</a>
                    </div>
                </form>

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <hr />
                    <h3>Résultats</h3>
                    
                    <?php if (empty($organisations)): ?>
                        <div class="empty-state">
                            <p>Aucune organisation ne correspond à votre recherche.</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Ville</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($organisations as $org): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($org['name']) ?></strong></td>
                                        <td><?= htmlspecialchars($org['category'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($org['city'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $org['status'] === 'active' ? 'success' : 'danger' ?>">
                                                <?= htmlspecialchars($org['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="index.php?action=organisations&id=<?= $org['id'] ?>&section=<?= htmlspecialchars($section) ?>" class="btn btn-sm btn-info">Voir</a>
                                            <a href="index.php?action=organisations&method=edit&id=<?= $org['id'] ?>&section=<?= htmlspecialchars($section) ?>" class="btn btn-sm btn-warning">Éditer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 PeaceConnect - Administration</p>
        </footer>
    </div>
</body>
</html>

