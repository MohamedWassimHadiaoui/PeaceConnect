<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Vérifier droit admin
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php');
    exit;
}
require_once __DIR__ . '/../../../Model/organisation_logic.php';
$organisations = org_get_all();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Organisations - BackOffice</title>
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
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-2xl);
        }
        .page-header h2 {
            margin: 0;
        }
        .data-table {
            background: var(--color-bg-white);
            border-radius: var(--radius-2xl);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-lg);
            overflow-x: auto;
        }
        .data-table .table {
            margin: 0;
        }
        .action-buttons {
            display: flex;
            gap: var(--spacing-sm);
        }
        .empty-state {
            text-align: center;
            padding: var(--spacing-3xl);
            color: var(--color-text-light);
        }
        .badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success {
            background: rgba(16, 185, 129, 0.2);
            color: #065f46;
        }
        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #78350f;
        }
        .badge-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #7f1d1d;
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
            .page-header {
                flex-direction: column;
                gap: var(--spacing-md);
                align-items: flex-start;
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
                <li><a href="index.php?action=organisations&section=backoffice" class="active">
                    <span class="icon">🏢</span>
                    <span>Organisations</span>
                </a></li>
                <li><a href="index.php?action=help-requests&section=backoffice">
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
        <h1>Organisations</h1>
        <div></div>
    </header>
    <main>
        <div class="page-header">
            <div>
                <h2>Gestion des Organisations</h2>
                <p class="text-muted">Modifiez ou supprimez les organisations partenaires</p>
            </div>
            <a href="index.php?action=organisations&section=backoffice&method=create" class="btn btn-primary">
                ➕ Nouvelle organisation
            </a>
        </div>
        <div class="data-table">
            <?php
            // data already loaded above
            if (empty($organisations)): ?>
                <div class="empty-state">
                    <p style="font-size: 3rem;">📭</p>
                    <p>Aucune organisation pour le moment</p>
                    <a href="index.php?action=organisations&section=backoffice&method=create" class="btn btn-primary">
                        Créer la première
                    </a>
                </div>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Secteur</th>
                            <th>Statut</th>
                            <th>Téléphone</th>
                            <th>Date création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($organisations as $org): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($org['name']) ?></strong></td>
                            <td><?= htmlspecialchars($org['sector'] ?? '—') ?></td>
                            <td>
                                <span class="badge badge-<?= $org['status'] === 'active' ? 'success' : ($org['status'] === 'inactive' ? 'danger' : 'warning') ?>">
                                    <?= htmlspecialchars($org['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($org['phone'] ?? '—') ?></td>
                            <td><?= date('d/m/Y', strtotime($org['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="index.php?action=organisations&section=backoffice&method=show&id=<?= $org['id'] ?>" class="btn btn-sm btn-outline" title="Voir">👁️</a>
                                    <a href="index.php?action=organisations&section=backoffice&method=edit&id=<?= $org['id'] ?>" class="btn btn-sm btn-outline" title="Éditer">✏️</a>
                                    <a href="index.php?action=organisations&section=backoffice&method=delete&id=<?= $org['id'] ?>" class="btn btn-sm" style="background: var(--color-danger); color: white;" onclick="return confirm('Êtes-vous sûr ?')" title="Supprimer">🗑️</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
    <script>
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('active');
            });
        });
    </script>
</body>
</html>

