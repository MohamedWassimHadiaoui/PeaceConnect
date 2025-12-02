<?php
/**
 * View/FrontOffice/home.php
 * Page d'accueil publique
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PeaceConnect - Accueil</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">🕊️ PeaceConnect</div>
            <nav>
                <a href="index.php">Accueil</a>
                <a href="index.php?action=help-requests">Demandes</a>
                <a href="index.php?action=organisations">Organisations</a>
                <a href="index.php?action=login">Connexion</a>
                <a href="index.php?action=admin_login" class="btn btn-outline" style="margin-left:8px;">Admin</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <h1>Bienvenue sur PeaceConnect</h1>
            <p>Une plateforme complète pour signaler, demander de l'aide et découvrir les organisations partenaires</p>
            <div class="hero-buttons">
                <a href="index.php?action=help-requests&method=create" class="btn btn-primary">Commencer maintenant</a>
                <a href="index.php?action=organisations" class="btn btn-secondary">En savoir plus</a>
            </div>
        </section>

        <section class="container mt-3xl mb-3xl">
            <div class="section-header">
                <h2>Que souhaitez-vous faire ?</h2>
                <p>Explorez nos services pour signaler, demander de l'aide ou découvrir des organisations</p>
            </div>
            <div class="cards-grid">
                <div class="card card-animate scroll-reveal">
                    <div class="card-body text-center">
                        <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">📋</div>
                        <h3 class="card-title">Signaler un incident</h3>
                        <p class="card-text">Signalez une situation qui nécessite une aide ou une intervention particulière.</p>
                        <a href="index.php?action=help-requests&method=create" class="btn btn-primary btn-sm">Créer</a>
                    </div>
                </div>

                <div class="card card-animate scroll-reveal">
                    <div class="card-body text-center">
                        <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">🤝</div>
                        <h3 class="card-title">Demander de l'aide</h3>
                        <p class="card-text">Créez une demande d'aide personnalisée et recevez des suggestions adaptées.</p>
                        <a href="index.php?action=help-requests&method=create" class="btn btn-primary btn-sm">Demander</a>
                    </div>
                </div>

                <div class="card card-animate scroll-reveal">
                    <div class="card-body text-center">
                        <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">💬</div>
                        <h3 class="card-title">Communauté</h3>
                        <p class="card-text">Participez à la communauté et partagez expériences avec d'autres utilisateurs.</p>
                        <a href="index.php?action=help-requests" class="btn btn-outline btn-sm">Participer</a>
                    </div>
                </div>

                <div class="card card-animate scroll-reveal">
                    <div class="card-body text-center">
                        <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">🏢</div>
                        <h3 class="card-title">Organisations</h3>
                        <p class="card-text">Parcourez notre répertoire des organisations partenaires qui peuvent vous aider.</p>
                        <a href="index.php?action=organisations" class="btn btn-primary btn-sm">Parcourir</a>
                    </div>
                </div>

                <div class="card card-animate scroll-reveal">
                    <div class="card-body text-center">
                        <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">📊</div>
                        <h3 class="card-title">Rapports</h3>
                        <p class="card-text">Consultez des rapports et des statistiques sur les demandes d'aide.</p>
                        <a href="index.php?action=reports" class="btn btn-outline btn-sm">Voir</a>
                    </div>
                </div>

                <div class="card card-animate scroll-reveal">
                    <div class="card-body text-center">
                        <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">🔐</div>
                        <h3 class="card-title">Mon Profil</h3>
                        <p class="card-text">Gérez votre compte, vos demandes et vos préférences en toute sécurité.</p>
                        <a href="index.php?action=login" class="btn btn-outline btn-sm">Accéder</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="alt-bg">
            <div class="container mt-3xl mb-3xl">
                <div class="section-header">
                    <h2>Notre Communauté</h2>
                    <p>Rejoignez des milliers de citoyens et organisations engagés pour la paix</p>
                </div>
                <div class="grid">
                    <?php
                    require_once __DIR__ . '/../../Model/help_request_logic.php';
                    require_once __DIR__ . '/../../Model/organisation_logic.php';
                    
                    $helpRequests = hr_get_all();
                    $organisations = org_get_all();
                    ?>
                    <div class="col-4 text-center scroll-reveal fade-in">
                        <h3 style="font-size: 2.5rem; color: var(--color-primary); margin-bottom: var(--spacing-sm);"><?= count($helpRequests) ?></h3>
                        <p class="text-muted">Demandes d'aide</p>
                    </div>
                    <div class="col-4 text-center scroll-reveal fade-in">
                        <h3 style="font-size: 2.5rem; color: var(--color-primary); margin-bottom: var(--spacing-sm);"><?= count($organisations) ?></h3>
                        <p class="text-muted">Organisations partenaires</p>
                    </div>
                    <div class="col-4 text-center scroll-reveal fade-in">
                        <h3 style="font-size: 2.5rem; color: var(--color-primary); margin-bottom: var(--spacing-sm);">24/7</h3>
                        <p class="text-muted">Support disponible</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>PeaceConnect</h3>
                    <p>Une plateforme pour signaler, partager et construire un monde plus pacifique et inclusif.</p>
                </div>
                <div class="footer-section">
                    <h3>Navigation</h3>
                    <ul style="list-style: none; margin: 0;">
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="index.php?action=help-requests">Demandes d'aide</a></li>
                        <li><a href="index.php?action=organisations">Organisations</a></li>
                        <li><a href="index.php?action=login">Connexion</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul style="list-style: none; margin: 0;">
                        <li><a href="#">Aide</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Signaler un problème</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 PeaceConnect - Tous droits réservés</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/scroll-reveal.js"></script>
</body>
</html>
