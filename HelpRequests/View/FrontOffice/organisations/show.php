<?php
/**
 * View/FrontOffice/organisations/show.php
 * Affichage détaillé d'une organisation
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($organisation['name'] ?? 'Organisation') ?></title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <div class="container">
        <header>
            <h1>🕊️ PeaceConnect</h1>
            <nav>
                <a href="index.php">Accueil</a> |
                <a href="index.php?action=organisations">Organisations</a>
            </nav>
        </header>

        <main>
            <section class="organisation-detail">
                <div class="detail-header">
                    <div>
                        <h2><?= htmlspecialchars($organisation['name']) ?></h2>
                        <span class="badge badge-<?= $organisation['status'] === 'active' ? 'success' : 'danger' ?>">
                            <?= htmlspecialchars($organisation['status']) ?>
                        </span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                    <?php if ($organisation['logo_path']): ?>
                        <div class="section">
                            <img src="<?= htmlspecialchars($organisation['logo_path']) ?>" alt="Logo" style="max-width: 100%; border-radius: 8px;" />
                        </div>
                    <?php endif; ?>

                    <div class="info-section">
                        <?php if ($organisation['acronym']): ?>
                            <div class="info-row">
                                <label>Acronyme :</label>
                                <span><?= htmlspecialchars($organisation['acronym']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($organisation['category']): ?>
                            <div class="info-row">
                                <label>Catégorie :</label>
                                <span><?= htmlspecialchars($organisation['category']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($organisation['email']): ?>
                            <div class="info-row">
                                <label>Email :</label>
                                <a href="mailto:<?= htmlspecialchars($organisation['email']) ?>">
                                    <?= htmlspecialchars($organisation['email']) ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($organisation['phone']): ?>
                            <div class="info-row">
                                <label>Téléphone :</label>
                                <a href="tel:<?= htmlspecialchars($organisation['phone']) ?>">
                                    <?= htmlspecialchars($organisation['phone']) ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($organisation['website']): ?>
                            <div class="info-row">
                                <label>Site Web :</label>
                                <a href="<?= htmlspecialchars($organisation['website']) ?>" target="_blank">
                                    <?= htmlspecialchars($organisation['website']) ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php 
                        $addressParts = [];
                        if ($organisation['address']) $addressParts[] = $organisation['address'];
                        if ($organisation['postal_code']) $addressParts[] = $organisation['postal_code'];
                        if ($organisation['city']) $addressParts[] = $organisation['city'];
                        if ($organisation['country']) $addressParts[] = $organisation['country'];
                        $fullAddress = implode(', ', $addressParts);
                        ?>
                        <?php if ($fullAddress): ?>
                            <div class="info-row">
                                <label>Adresse :</label>
                                <span><?= htmlspecialchars($fullAddress) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($organisation['description']): ?>
                    <div class="section">
                        <h3>Description</h3>
                        <p><?= nl2br(htmlspecialchars($organisation['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($organisation['mission']): ?>
                    <div class="section">
                        <h3>Mission</h3>
                        <p><?= nl2br(htmlspecialchars($organisation['mission'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($organisation['vision']): ?>
                    <div class="section">
                        <h3>Vision</h3>
                        <p><?= nl2br(htmlspecialchars($organisation['vision'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="back-link">
                    <a href="index.php?action=organisations">← Retour à la liste</a>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 PeaceConnect</p>
        </footer>
    </div>
</body>
</html>

