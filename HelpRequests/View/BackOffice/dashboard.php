<?php
/**
 * View/BackOffice/dashboard.php
 * Dashboard Administrateur - Vue principale
 */
require_once __DIR__ . '/../../Model/help_request_logic.php';
require_once __DIR__ . '/../../Model/organisation_logic.php';

$helpRequests = hr_get_all();
$organisations = org_get_all();
$pendingRequests = count(array_filter($helpRequests, fn($r) => $r['status'] === 'pending'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PeaceConnect - Dashboard Admin</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        body {
            display: grid;
            grid-template-columns: 250px 1fr;
            grid-template-rows: 70px 1fr;
            min-height: 100vh;
            gap: 0;
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

        .sidebar-nav li {
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
            display: flex;
            align-items: center;
            justify-content: center;
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

        .header-actions {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--color-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        main {
            grid-column: 2;
            grid-row: 2;
            margin-left: 250px;
            padding: var(--spacing-2xl);
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: var(--spacing-2xl);
        }

        .page-header h2 {
            margin-bottom: var(--spacing-md);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-2xl);
            margin-bottom: var(--spacing-3xl);
        }

        .stat-card {
            background: var(--color-bg-white);
            border-radius: var(--radius-2xl);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-lg);
            border-left: 4px solid var(--color-primary);
        }

        .stat-card.danger {
            border-left-color: var(--color-danger);
        }

        .stat-card.info {
            border-left-color: var(--color-info);
        }

        .stat-card.warning {
            border-left-color: var(--color-warning);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--color-text-dark);
            margin-bottom: var(--spacing-sm);
        }

        .stat-label {
            color: var(--color-text-medium);
            font-weight: 500;
        }

        .stat-change {
            font-size: 0.875rem;
            margin-top: var(--spacing-sm);
        }

        .stat-change.positive {
            color: var(--color-success);
        }

        .stat-change.negative {
            color: var(--color-danger);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-md);
            border-bottom: 2px solid var(--color-border);
        }

        .activity-list {
            background: var(--color-bg-white);
            border-radius: var(--radius-2xl);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-lg);
        }

        .activity-item {
            display: flex;
            gap: var(--spacing-md);
            padding: var(--spacing-md) 0;
            border-bottom: 1px solid var(--color-border);
            align-items: flex-start;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--color-bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            margin-bottom: var(--spacing-xs);
        }

        .activity-time {
            font-size: 0.875rem;
            color: var(--color-text-light);
        }

        .quick-actions {
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
            margin-bottom: var(--spacing-3xl);
        }

        @media (max-width: 1200px) {
            body {
                grid-template-columns: 200px 1fr;
            }

            .sidebar {
                width: 200px;
            }

            header {
                margin-left: 200px;
            }

            main {
                margin-left: 200px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
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

            .stats-grid {
                grid-template-columns: 1fr;
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
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">🕊️ Admin</div>
            <div class="sidebar-user">Administrateur</div>
        </div>
        <nav>
            <ul class="sidebar-nav">
                <li><a href="index.php?action=dashboard" class="active">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a></li>
                <li><a href="index.php?action=organisations&section=backoffice">
                    <span class="icon">🏢</span>
                    <span>Organisations</span>
                </a></li>
                <li><a href="index.php?action=help-requests&section=backoffice">
                    <span class="icon">📋</span>
                    <span>Demandes d'aide</span>
                </a></li>
                <li><a href="#">
                    <span class="icon">👥</span>
                    <span>Utilisateurs</span>
                </a></li>
                <li><a href="#">
                    <span class="icon">📈</span>
                    <span>Rapports</span>
                </a></li>
                <li><a href="#">
                    <span class="icon">⚙️</span>
                    <span>Paramètres</span>
                </a></li>
                <li style="margin-top: var(--spacing-2xl); border-top: 1px solid rgba(255,255,255,0.1); padding-top: var(--spacing-xl);"><a href="index.php?action=logout" style="color: var(--color-danger);">
                    <span class="icon">🚪</span>
                    <span>Déconnexion</span>
                </a></li>
            </ul>
        </nav>
    </aside>

    <!-- Header -->
    <header>
        <button class="hamburger" id="hamburger">☰</button>
        <h1>Dashboard</h1>
        <div class="header-actions">
            <div class="user-menu">
                <div class="user-avatar">A</div>
                <span>Admin</span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="page-header">
            <h2>Bienvenue dans l'administration</h2>
            <p class="text-muted">Gérez les organisations et les demandes d'aide</p>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="index.php?action=organisations&section=backoffice&method=create" class="btn btn-primary">
                ➕ Nouvelle organisation
            </a>
            <a href="index.php?action=help-requests&section=backoffice&method=create" class="btn btn-primary">
                ➕ Nouvelle demande
            </a>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= count($helpRequests) ?></div>
                <div class="stat-label">Demandes d'aide</div>
                <div class="stat-change positive">✓ Actives</div>
            </div>

            <div class="stat-card info">
                <div class="stat-value"><?= count($organisations) ?></div>
                <div class="stat-label">Organisations</div>
                <div class="stat-change">Partenaires</div>
            </div>

            <div class="stat-card danger">
                <div class="stat-value"><?= $pendingRequests ?></div>
                <div class="stat-label">Demandes en attente</div>
                <div class="stat-change negative">À traiter</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-value"><?= count(array_filter($helpRequests, fn($r) => $r['status'] === 'in_progress')) ?></div>
                <div class="stat-label">En cours</div>
                <div class="stat-change">En traitement</div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div style="margin-bottom: var(--spacing-3xl);">
            <h3 class="section-title">Demandes récentes</h3>
            <div class="activity-list">
                <?php if (!empty($helpRequests)): ?>
                    <?php foreach (array_slice($helpRequests, -5) as $request): ?>
                    <div class="activity-item">
                        <div class="activity-icon">📋</div>
                        <div class="activity-content">
                            <div class="activity-title"><?= htmlspecialchars($request['title']) ?></div>
                            <div class="activity-time">
                                Statut: <strong><?= htmlspecialchars($request['status']) ?></strong>
                                · <?= htmlspecialchars(substr($request['created_at'], 0, 10)) ?>
                            </div>
                        </div>
                        <div>
                            <a href="index.php?action=help-requests&section=backoffice&method=edit&id=<?= $request['id'] ?>" class="btn btn-sm btn-outline" style="margin: 0;">
                                Modifier
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="activity-item" style="text-align: center; color: var(--color-text-light); padding: var(--spacing-xl);">
                        Aucune demande pour l'instant
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Organisations -->
        <div>
            <h3 class="section-title">Organisations récentes</h3>
            <div class="activity-list">
                <?php if (!empty($organisations)): ?>
                    <?php foreach (array_slice($organisations, -5) as $org): ?>
                    <div class="activity-item">
                        <div class="activity-icon">🏢</div>
                        <div class="activity-content">
                            <div class="activity-title"><?= htmlspecialchars($org['name']) ?></div>
                            <div class="activity-time">
                                <?= htmlspecialchars($org['sector'] ?? 'Secteur non défini') ?>
                                · <?= htmlspecialchars(substr($org['created_at'], 0, 10)) ?>
                            </div>
                        </div>
                        <div>
                            <a href="index.php?action=organisations&section=backoffice&method=edit&id=<?= $org['id'] ?>" class="btn btn-sm btn-outline" style="margin: 0;">
                                Modifier
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="activity-item" style="text-align: center; color: var(--color-text-light); padding: var(--spacing-xl);">
                        Aucune organisation pour l'instant
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // Mobile sidebar toggle
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');

        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when link clicked
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('active');
            });
        });

        // Set active nav link
        const currentPage = new URLSearchParams(window.location.search).get('action');
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            const href = new URLSearchParams(link.getAttribute('href')).get('action');
            if (href === currentPage) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>

