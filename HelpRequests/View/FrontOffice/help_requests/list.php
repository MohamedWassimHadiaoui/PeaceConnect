<?php
/**
 * View/FrontOffice/help_requests/list.php
 * Liste des demandes d'aide (public)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Demandes d'Aide</title>
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
            <section class="help-requests-section">
                <div class="action-bar">
                    <h2>Demandes d'Aide</h2>
                    <a href="index.php?action=help-requests&method=create" class="btn btn-primary">
                        + Nouvelle demande
                    </a>
                </div>

                <?php if (empty($requests)): ?>
                    <div class="empty-state">
                        <p>Aucune demande trouvée. <a href="index.php?action=help-requests&method=create">Créez la vôtre</a></p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Situation</th>
                                <th>Urgence</th>
                                <th>Statut</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td><?= htmlspecialchars($req['help_type']) ?></td>
                                    <td><?= htmlspecialchars(substr($req['situation'], 0, 50)) ?>...</td>
                                    <td><span class="badge"><?= htmlspecialchars($req['urgency_level']) ?></span></td>
                                    <td><?= htmlspecialchars($req['status']) ?></td>
                                    <td><?= htmlspecialchars($req['location'] ?? '-') ?></td>
                                    <td><?= date('d/m/Y', strtotime($req['created_at'])) ?></td>
                                    <td>
                                        <a href="index.php?action=help-requests&id=<?= $req['id'] ?>" class="btn btn-sm btn-info">Voir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 PeaceConnect</p>
        </footer>
    </div>
</body>
</html>

