<?php
// Controller/AuthController.php
require_once __DIR__ . '/../Model/db.php';

class AuthController {
    // Affiche un petit formulaire pour saisir le mot de passe admin
    public static function showLoginForm() {
        // Simple form: POST to ?action=admin_login
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login</title><link rel="stylesheet" href="assets/css/style.css"></head><body style="display:flex;align-items:center;justify-content:center;height:100vh;background:#f3f4f6;"><div style="background:white;padding:2rem;border-radius:12px;box-shadow:0 4px 18px rgba(0,0,0,0.08);width:360px;"><h2 style="margin-top:0;margin-bottom:1rem">Accès Admin</h2><p>Entrez le mot de passe administrateur pour accéder au BackOffice.</p><form method="POST" action="index.php?action=admin_login"><label for="admin_password">Mot de passe</label><input id="admin_password" name="admin_password" type="password" required style="width:100%;padding:0.5rem;margin-top:0.5rem;margin-bottom:1rem;border:1px solid #e5e7eb;border-radius:6px;"><div style="display:flex;gap:0.5rem;justify-content:flex-end;"><a href="index.php" class="btn btn-outline" style="text-decoration:none;padding:0.5rem 0.75rem;border-radius:6px;">Annuler</a><button class="btn btn-primary" type="submit">Valider</button></div></form></div></body></html>';
        exit;
    }

    // Traitement du mot de passe admin
    public static function loginProcess() {
        if (empty($_POST['admin_password'])) {
            header('Location: index.php');
            exit;
        }

        $password = $_POST['admin_password'];

        // Authentification simplifiée: mot de passe fixe "admin123"
        $expectedHash = hash('sha256', 'admin123');
        if (hash('sha256', $password) === $expectedHash) {
            $_SESSION['user'] = [
                'id' => 1,
                'name' => 'Admin démo',
                'email' => 'admin@peaceconnect.tn',
                'role' => 'admin'
            ];
            header('Location: index.php?action=dashboard&section=backoffice');
            exit;
        }

        // Mot de passe incorrect
        $_SESSION['auth_error'] = 'Mot de passe administrateur incorrect.';
        header('Location: index.php?action=admin_login');
        exit;
    }

    public static function logout() {
        unset($_SESSION['user']);
        session_regenerate_id(true);
        header('Location: index.php');
        exit;
    }
}
?>