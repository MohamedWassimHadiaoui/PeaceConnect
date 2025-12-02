<?php
/**
 * View/FrontOffice/organisations/list.php
 * Liste des organisations (public)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Organisations</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <div class="container">
        <header>
            <h1>🕊️ PeaceConnect</h1>
            <nav>
                <a href="index.php">Accueil</a>
                <a href="index.php?action=help-requests">Demandes d'aide</a>
                <a href="index.php?action=organisations">Organisations</a>
            </nav>
        </header>

        <main>
            <section class="organisations-section">
                <div class="action-bar">
                    <h2>Organisations Partenaires</h2>
                    <a href="index.php?action=organisations&method=search" class="btn btn-secondary">
                        🔍 Rechercher
                    </a>
                </div>

                <?php if (empty($organisations)): ?>
                    <div class="empty-state">
                        <p>Aucune organisation trouvée.</p>
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
                                    <?php if ($org['description']): ?>
                                        <p class="description"><?= htmlspecialchars(substr($org['description'], 0, 100)) ?>...</p>
                                    <?php endif; ?>
                                    <div class="org-footer">
                                        <span class="badge badge-<?= $org['status'] === 'active' ? 'success' : 'danger' ?>">
                                            <?= htmlspecialchars($org['status']) ?>
                                        </span>
                                        <a href="index.php?action=organisations&id=<?= $org['id'] ?>" class="btn btn-sm btn-info">
                                            Plus de détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 PeaceConnect</p>
        </footer>
    </div>
</body>
</html>
