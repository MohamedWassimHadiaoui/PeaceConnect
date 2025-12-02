<?php
/**
 * View/FrontOffice/help_requests/show.php
 * Affichage détaillé d'une demande d'aide
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($req['help_type'] ?? 'Demande') ?></title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <div class="container">
        <header>
            <h1>🕊️ PeaceConnect</h1>
            <nav>
                <a href="index.php">Accueil</a> |
                <a href="index.php?action=help-requests">Demandes d'aide</a>
            </nav>
        </header>

        <main>
            <section class="request-detail">
                <div class="detail-header">
                    <h2><?= htmlspecialchars($req['help_type']) ?></h2>
                    <span class="badge badge-<?= strtolower($req['status']) ?>">
                        <?= htmlspecialchars($req['status']) ?>
                    </span>
                </div>

                <div class="detail-info">
                    <div class="info-row">
                        <label>Type :</label>
                        <span><?= htmlspecialchars($req['help_type']) ?></span>
                    </div>
                    <div class="info-row">
                        <label>Urgence :</label>
                        <span class="urgency-<?= strtolower($req['urgency_level']) ?>">
                            <?= htmlspecialchars($req['urgency_level']) ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <label>Localisation :</label>
                        <span><?= htmlspecialchars($req['location'] ?? '-') ?></span>
                    </div>
                    <div class="info-row">
                        <label>Contact :</label>
                        <span><?= htmlspecialchars($req['contact_method'] ?? '-') ?></span>
                    </div>
                    <div class="info-row">
                        <label>Responsable :</label>
                        <span><?= htmlspecialchars($req['responsable'] ?? '-') ?></span>
                    </div>
                </div>

                <div class="section">
                    <h3>Situation</h3>
                    <p><?= nl2br(htmlspecialchars($req['situation'])) ?></p>
                </div>

                <div class="metadata">
                    <p><small>Créé le <?= date('d/m/Y à H:i', strtotime($req['created_at'])) ?></small></p>
                </div>

                <div class="back-link">
                    <a href="index.php?action=help-requests">← Retour à la liste</a>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 PeaceConnect</p>
        </footer>
    </div>
</body>
</html>

