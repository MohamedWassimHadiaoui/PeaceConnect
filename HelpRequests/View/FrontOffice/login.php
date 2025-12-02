<?php
/**
 * View/FrontOffice/login.php
 * Page de connexion
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Connexion - PeaceConnect</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <div class="container">
        <header>
            <h1>🕊️ PeaceConnect</h1>
            <nav>
                <a href="index.php">Accueil</a> |
                <a href="index.php?action=register">S'inscrire</a>
            </nav>
        </header>

        <main>
            <section style="display: flex; justify-content: center; align-items: center; min-height: 60vh; padding: 2rem 0;">
                <div class="form-section" style="max-width: 450px; width: 100%;">
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <h2>🔐 Connexion</h2>
                        <p style="color: var(--text-light);">Accédez à votre compte PeaceConnect</p>
                    </div>

                    <form method="POST" class="login-form">
                        <div class="form-group">
                            <label for="email">Adresse Email</label>
                            <input type="email" id="email" name="email" required placeholder="votre@email.com" />
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" name="password" required placeholder="Votre mot de passe" />
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="remember" />
                                Se souvenir de moi
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">Se connecter</button>
                    </form>

                    <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border-color);">

                    <div style="text-align: center;">
                        <p style="margin-bottom: 1rem;">
                            Pas encore inscrit ? <a href="index.php?action=register" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Créer un compte</a>
                        </p>
                        <p style="font-size: 0.9rem; color: var(--text-light);">
                            <a href="index.php" style="color: var(--primary-color); text-decoration: none;">← Retour à l'accueil</a>
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 PeaceConnect</p>
        </footer>
    </div>
</body>
</html>

