<?php
/**
 * View/FrontOffice/organisations/search.php
 * Page de recherche pour les organisations (public)
 */
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
            <h1>🕊️ PeaceConnect</h1>
            <nav>
                <a href="index.php">Accueil</a> |
                <a href="index.php?action=help-requests">Demandes d'aide</a> |
                <a href="index.php?action=organisations">Organisations</a>
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
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                                   placeholder="Rechercher par nom..." />
                        </div>

                        <div class="form-group">
                            <label for="category">Catégorie</label>
                            <input type="text" id="category" name="category" 
                                   value="<?= htmlspecialchars($_POST['category'] ?? '') ?>" 
                                   placeholder="Ex: ONG, Association..." />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Ville</label>
                            <input type="text" id="city" name="city" 
                                   value="<?= htmlspecialchars($_POST['city'] ?? '') ?>" 
                                   placeholder="Rechercher par ville..." />
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
                        <a href="index.php?action=organisations&method=search" class="btn btn-secondary">Réinitialiser</a>
                    </div>
                </form>

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <hr />
                    
                    <h3>Résultats de recherche</h3>
                    
                    <?php if (empty($organisations)): ?>
                        <div class="empty-state">
                            <p>Aucune organisation ne correspond à votre recherche.</p>
                        </div>
                    <?php else: ?>
                        <div class="organisations-grid">
                            <?php foreach ($organisations as $org): ?>
                                <div class="org-card">
                                    <?php if ($org['logo_path']): ?>
                                        <div class="org-logo">
                                            <img src="<?= htmlspecialchars($org['logo_path']) ?>" alt="Logo" />
                                        </div>
                                    <?php endif; ?>
                                    <div class="org-content">
                                        <h3><?= htmlspecialchars($org['name']) ?></h3>
                                        <?php if ($org['acronym']): ?>
                                            <p class="acronym"><?= htmlspecialchars($org['acronym']) ?></p>
                                        <?php endif; ?>
                                        <p class="category"><?= htmlspecialchars($org['category'] ?? 'N/A') ?></p>
                                        <div class="org-footer">
                                            <span class="badge badge-<?= $org['status'] === 'active' ? 'success' : 'danger' ?>">
                                                <?= htmlspecialchars($org['status']) ?>
                                            </span>
                                            <a href="index.php?action=organisations&id=<?= $org['id'] ?>" class="btn btn-sm btn-info">
                                                Détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 PeaceConnect</p>
        </footer>
    </div>
</body>
</html>

