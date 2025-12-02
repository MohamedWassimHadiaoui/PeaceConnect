<?php
/**
 * View/BackOffice/help_requests/form.php
 * Formulaire de création/édition de demande (Admin)
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Protection: only admin can access
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

$isEdit = isset($request) && $request;
$formTitle = $isEdit ? 'Éditer la demande' : 'Créer une demande d\'aide';
$submitText = $isEdit ? 'Mettre à jour' : 'Créer';
$error = isset($error) ? $error : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($formTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        body {
            display: grid;
            grid-template-columns: 250px 1fr;
            grid-template-rows: 70px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            grid-column: 1;
            grid-row: 1 / 3;
            background: var(--color-text-dark);
            color: var(--color-bg-white);
            padding: var(--spacing-lg) 0;
            position: fixed;
            width: 250px;
            height: 100vh;
            overflow-y: auto;
            z-index: 999;
        }
        .sidebar-header {
            padding: var(--spacing-lg);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: var(--spacing-lg);
        }
        .sidebar-logo {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: var(--spacing-sm);
        }
        .sidebar-user {
            font-size: 0.875rem;
            color: var(--color-text-light);
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-md) var(--spacing-lg);
            color: var(--color-text-light);
            text-decoration: none;
            transition: all var(--duration-base) var(--ease-smooth);
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.1);
            color: var(--color-bg-white);
            border-left-color: var(--color-primary);
        }
        .sidebar-nav a .icon {
            font-size: 1.25rem;
            width: 24px;
        }
        header {
            grid-column: 2;
            grid-row: 1;
            background: var(--color-bg-white);
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 var(--spacing-lg);
            margin-left: 250px;
        }
        header h1 {
            font-size: 1.5rem;
            margin: 0;
        }
        main {
            grid-column: 2;
            grid-row: 2;
            margin-left: 250px;
            padding: var(--spacing-2xl);
            overflow-y: auto;
        }
        .form-card {
            background: var(--color-bg-white);
            border-radius: var(--radius-2xl);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-lg);
        }
        .form-card h2 {
            margin-bottom: var(--spacing-2xl);
        }
        .form-actions {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-2xl);
            padding-top: var(--spacing-2xl);
            border-top: 1px solid var(--color-border);
        }
        @media (max-width: 768px) {
            body {
                grid-template-columns: 1fr;
                grid-template-rows: 70px auto 1fr;
            }
            .sidebar {
                grid-column: 1;
                grid-row: 2;
                width: 100%;
                height: auto;
                position: relative;
                display: none;
            }
            .sidebar.active {
                display: block;
            }
            header {
                margin-left: 0;
                grid-column: 1;
            }
            main {
                margin-left: 0;
                grid-column: 1;
                padding: var(--spacing-lg);
            }
            .form-actions {
                flex-direction: column;
            }
            .hamburger {
                display: block;
                background: none;
                border: none;
                cursor: pointer;
                font-size: 1.5rem;
            }
        }
        .hamburger {
            display: none;
        }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">🕊️ Admin</div>
            <div class="sidebar-user">Administrateur</div>
        </div>
        <nav>
            <ul class="sidebar-nav">
                <li><a href="index.php?action=dashboard">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a></li>
                <li><a href="index.php?action=organisations&section=backoffice">
                    <span class="icon">🏢</span>
                    <span>Organisations</span>
                </a></li>
                <li><a href="index.php?action=help-requests&section=backoffice" class="active">
                    <span class="icon">📋</span>
                    <span>Demandes d'aide</span>
                </a></li>
                <li style="margin-top: var(--spacing-2xl); border-top: 1px solid rgba(255,255,255,0.1); padding-top: var(--spacing-xl);"><a href="index.php?action=logout" style="color: var(--color-danger);">
                    <span class="icon">🚪</span>
                    <span>Déconnexion</span>
                </a></li>
            </ul>
        </nav>
    </aside>
    <header>
        <button class="hamburger" id="hamburger">☰</button>
        <h1><?= htmlspecialchars($formTitle) ?></h1>
        <div></div>
    </header>
    <main>
        <div class="form-card">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="title">Titre *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?= htmlspecialchars($request['title'] ?? '') ?>" />
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="help_type">Type d'aide *</label>
                        <input type="text" id="help_type" name="help_type" required 
                               value="<?= htmlspecialchars($request['help_type'] ?? '') ?>" />
                    </div>
                    <div class="form-group">
                        <label for="urgency_level">Urgence *</label>
                        <select id="urgency_level" name="urgency_level" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="low" <?= (($request['urgency_level'] ?? '') === 'low') ? 'selected' : '' ?>>Basse</option>
                            <option value="normal" <?= (($request['urgency_level'] ?? '') === 'normal') ? 'selected' : '' ?>>Normale</option>
                            <option value="high" <?= (($request['urgency_level'] ?? '') === 'high') ? 'selected' : '' ?>>Haute</option>
                            <option value="urgent" <?= (($request['urgency_level'] ?? '') === 'urgent') ? 'selected' : '' ?>>Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="situation">Description *</label>
                    <textarea id="situation" name="situation" required rows="5"><?= htmlspecialchars($request['situation'] ?? '') ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="location">Localisation</label>
                        <input type="text" id="location" name="location" 
                               value="<?= htmlspecialchars($request['location'] ?? '') ?>" />
                    </div>
                    <div class="form-group">
                        <label for="contact_method">Méthode de contact</label>
                        <input type="text" id="contact_method" name="contact_method" 
                               value="<?= htmlspecialchars($request['contact_method'] ?? '') ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="status">Statut</label>
                    <select id="status" name="status">
                        <option value="pending" <?= (($request['status'] ?? 'pending') === 'pending') ? 'selected' : '' ?>>En attente</option>
                        <option value="in_progress" <?= (($request['status'] ?? '') === 'in_progress') ? 'selected' : '' ?>>En cours</option>
                        <option value="completed" <?= (($request['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Complétée</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitText) ?></button>
                    <a href="index.php?action=help-requests&section=backoffice" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </main>
    <script>
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        hamburger?.addEventListener('click', () => {
            sidebar?.classList.toggle('active');
        });
    </script>
</body>
</html>

