<?php
/**
 * View/home.php
 * Page d'accueil de l'application PeaceConnect
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PeaceConnect - Accueil</title>
    <link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>
    <div class="container">
        <header>
            <h1>🕊️ PeaceConnect</h1>
            <p class="tagline">Plateforme de gestion des demandes d'aide et des organisations</p>
            <nav>
                <a href="index.php">Accueil</a> |
                <a href="index.php?action=help-requests">Demandes d'aide</a> |
                <a href="index.php?action=organisations">Organisations</a> |
                <a href="index.php?action=dashboard">Tableau de bord</a>
            </nav>
        </header>

        <main>
            <section class="hero">
                <h2>Bienvenue sur PeaceConnect</h2>
                <p>Une plateforme complète pour gérer les demandes d'aide et les organisations.</p>
            </section>

            <section class="features">
                <div class="feature-grid">
                    <div class="feature-card">
                        <h3>📋 Demandes d'Aide</h3>
                        <p>Gérez facilement les demandes d'aide avec un système de priorité et de suivi.</p>
                        <a href="index.php?action=help-requests" class="btn btn-primary">Gérer les demandes</a>
                    </div>

                    <div class="feature-card">
                        <h3>🏢 Organisations</h3>
                        <p>Catalogue complet des organisations partenaires avec toutes leurs informations.</p>
                        <a href="index.php?action=organisations" class="btn btn-primary">Gérer les organisations</a>
                    </div>

                    <div class="feature-card">
                        <h3>📊 Tableau de Bord</h3>
                        <p>Vue d'ensemble avec statistiques et rapports en temps réel.</p>
                        <a href="index.php?action=dashboard" class="btn btn-primary">Accéder au tableau de bord</a>
                    </div>

                    <div class="feature-card">
                        <h3>🔐 Sécurité</h3>
                        <p>Accès sécurisé avec gestion des comptes utilisateurs.</p>
                        <a href="index.php?action=login" class="btn btn-secondary">Se connecter</a>
                    </div>
                </div>
            </section>

            <section class="quick-stats">
                <h3>Statistiques rapides</h3>
                <div class="stats-grid">
                    <?php
                    // Charger les fonctions pour obtenir les stats
                    require_once __DIR__ . '/../Model/help_request_logic.php';
                    require_once __DIR__ . '/../Model/organisation_logic.php';
                    
                    $helpRequests = hr_get_all();
                    $organisations = org_get_all();
                    ?>
                    <div class="stat-item">
                        <strong><?= count($helpRequests) ?></strong>
                        <span>Demandes d'aide</span>
                    </div>
                    <div class="stat-item">
                        <strong><?= count($organisations) ?></strong>
                        <span>Organisations</span>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 PeaceConnect - Tous droits réservés</p>
        </footer>
    </div>
</body>
</html>
