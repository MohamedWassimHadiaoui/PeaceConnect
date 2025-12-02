<?php
/**
 * View/BackOffice/help_requests/list.php
 * Liste des demandes d'aide avec filtres et actions CRUD
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Vérifier droit admin
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

$requirePath = __DIR__ . '/../../../Model/help_request_logic.php';
if (!file_exists($requirePath)) {
    // fallback for older path
    $requirePath = __DIR__ . '/../../Model/help_request_logic.php';
}
require_once $requirePath;
$requests = hr_get_all();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestion des Demandes d'Aide</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
        body {
            display: grid;
            grid-template-columns: 250px 1fr;
            grid-template-rows: 70px 1fr;
            min-height: 100vh;
            margin: 0;
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
            gap: 1rem;
        }
        header h1 {
            font-size: 1.5rem;
            margin: 0;
            flex: 1;
        }
        header .header-stats {
            font-size: 0.875rem;
            color: #6b7280;
            display: flex;
            gap: 2rem;
        }
        main {
            grid-column: 2;
            grid-row: 2;
            margin-left: 250px;
            padding: var(--spacing-2xl);
            overflow-y: auto;
        }
        .filters-card {
            background: var(--color-bg-white);
            padding: 1.5rem;
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-lg);
            margin-bottom: 1.5rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        .filters-card > div {
            display: flex;
            flex-direction: column;
        }
        .filters-card label {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #374151;
        }
        .filters-card input,
        .filters-card select {
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            font-family: inherit;
        }
        .filters-card input:focus,
        .filters-card select:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(47, 176, 76, 0.1);
        }
        .data-table {
            background: var(--color-bg-white);
            border-radius: var(--radius-2xl);
            padding: 1.5rem;
            box-shadow: var(--shadow-lg);
            overflow-x: auto;
        }
        .data-table table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .data-table thead {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }
        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
        }
        .data-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.2s;
        }
        .data-table tbody tr:hover {
            background: #f9fafb;
        }
        .data-table td {
            padding: 1rem;
            vertical-align: middle;
            color: #374151;
        }
        .badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .badge-urgent {
            background: #fecaca;
            color: #991b1b;
        }
        .badge-high {
            background: #fed7aa;
            color: #92400e;
        }
        .badge-normal {
            background: #fef3c7;
            color: #78350f;
        }
        .badge-low {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-pending {
            background: #fef3c7;
            color: #78350f;
        }
        .badge-in_progress {
            background: #bfdbfe;
            color: #1e3a8a;
        }
        .badge-completed {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-rejected {
            background: #fecaca;
            color: #991b1b;
        }
        .action-buttons {
            display: flex;
            gap: 0.25rem;
        }
        .action-btn {
            padding: 0.5rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
        }
        .action-btn-view {
            background: #e0f2fe;
            color: #0369a1;
        }
        .action-btn-view:hover {
            background: #0ea5e9;
            color: white;
        }
        .action-btn-edit {
            background: #fef3c7;
            color: #92400e;
        }
        .action-btn-edit:hover {
            background: #fcd34d;
            color: #78350f;
        }
        .action-btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }
        .action-btn-delete:hover {
            background: #fca5a5;
            color: white;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #9ca3af;
        }
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .empty-state h3 {
            margin: 0 0 0.5rem 0;
            color: #374151;
        }
        .empty-state p {
            margin: 0 0 1.5rem 0;
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
                flex-wrap: wrap;
            }
            header h1 {
                flex: 1 1 100%;
            }
            main {
                margin-left: 0;
                grid-column: 1;
                padding: var(--spacing-lg);
            }
            .filters-card {
                grid-template-columns: 1fr;
            }
            .hamburger {
                display: block;
            }
            .data-table {
                overflow-x: auto;
            }
        }
        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
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
                <li style="margin-top: var(--spacing-2xl); border-top: 1px solid rgba(255,255,255,0.1); padding-top: var(--spacing-xl);"><a href="index.php?action=logout" style="color: #ef4444;">
                    <span class="icon">🚪</span>
                    <span>Déconnexion</span>
                </a></li>
            </ul>
        </nav>
    </aside>
    <header>
        <button class="hamburger" id="hamburger">☰</button>
        <h1>📋 Demandes d'Aide</h1>
        <div class="header-stats">
            <div><strong><?= count($requests) ?></strong> demandes</div>
            <div><strong><?= count(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'pending')) ?></strong> en attente</div>
        </div>
        <a href="index.php?action=help-requests&op=create&section=backoffice" class="btn btn-primary">+ Nouvelle</a>
    </header>
    <main>
        <!-- Filtres -->
        <div class="filters-card">
            <div>
                <label for="filterUrgency">Urgence</label>
                <select id="filterUrgency">
                    <option value="">Tous</option>
                    <option value="urgent">🔴 Urgent</option>
                    <option value="high">🟠 Élevée</option>
                    <option value="normal">🟡 Normale</option>
                    <option value="low">🟢 Basse</option>
                </select>
            </div>
            <div>
                <label for="filterStatus">Statut</label>
                <select id="filterStatus">
                    <option value="">Tous</option>
                    <option value="pending">⏳ En attente</option>
                    <option value="in_progress">🔄 En cours</option>
                    <option value="completed">✅ Complétée</option>
                    <option value="rejected">❌ Rejetée</option>
                </select>
            </div>
            <div>
                <label for="searchInput">Recherche</label>
                <input type="text" id="searchInput" placeholder="Titre, type...">
            </div>
        </div>

        <!-- Tableau -->
        <div class="data-table">
            <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <h3>Aucune demande d'aide</h3>
                    <p>Commencez en créant votre première demande</p>
                    <a href="index.php?action=help-requests&op=create&section=backoffice" class="btn btn-primary">+ Créer une demande</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Urgence</th>
                            <th>Statut</th>
                            <th>Localisation</th>
                            <th>Date</th>
                            <th style="width: 120px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr data-urgency="<?= strtolower($req['urgency_level'] ?? 'normal') ?>" data-status="<?= strtolower($req['status'] ?? 'pending') ?>">
                                <td><strong><?= htmlspecialchars($req['title'] ?? $req['help_type'] ?? 'N/A') ?></strong></td>
                                <td><?= htmlspecialchars($req['help_type'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge badge-<?= strtolower($req['urgency_level'] ?? 'normal') ?>">
                                        <?php
                                        $urgency = $req['urgency_level'] ?? 'normal';
                                        echo match($urgency) {
                                            'urgent' => '🔴 Urgent',
                                            'high' => '🟠 Élevée',
                                            'normal' => '🟡 Normale',
                                            'low' => '🟢 Basse',
                                            default => 'Normal'
                                        };
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= strtolower($req['status'] ?? 'pending') ?>">
                                        <?php
                                        $status = $req['status'] ?? 'pending';
                                        echo match($status) {
                                            'pending' => '⏳ En attente',
                                            'in_progress' => '🔄 En cours',
                                            'completed' => '✅ Complétée',
                                            'rejected' => '❌ Rejetée',
                                            default => 'Inconnu'
                                        };
                                        ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($req['location'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($req['created_at'] ?? 'now')) ?></td>
                                <td style="text-align: center;">
                                    <div class="action-buttons">
                                        <a href="index.php?action=help-requests&op=show&id=<?= $req['id'] ?>&section=backoffice" class="action-btn action-btn-view" title="Voir">👁️</a>
                                        <a href="index.php?action=help-requests&op=edit&id=<?= $req['id'] ?>&section=backoffice" class="action-btn action-btn-edit" title="Éditer">✏️</a>
                                        <form method="POST" action="index.php?action=help-requests&op=delete&id=<?= $req['id'] ?>&section=backoffice" style="display: inline;" onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette demande ?\n\nCette action est irréversible.');">
                                            <button type="submit" class="action-btn action-btn-delete" title="Supprimer">🗑️</button>
                                        </form>
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
        // Hamburger menu
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        hamburger?.addEventListener('click', () => {
            sidebar?.classList.toggle('active');
        });

        // Filtres en temps réel
        const filterUrgency = document.getElementById('filterUrgency');
        const filterStatus = document.getElementById('filterStatus');
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('tbody tr');

        function filterTable() {
            const urgency = filterUrgency?.value.toLowerCase() || '';
            const status = filterStatus?.value.toLowerCase() || '';
            const search = searchInput?.value.toLowerCase() || '';

            rows.forEach(row => {
                const rowUrgency = row.dataset.urgency || '';
                const rowStatus = row.dataset.status || '';
                const titleCell = row.cells[0]?.textContent.toLowerCase() || '';
                const typeCell = row.cells[1]?.textContent.toLowerCase() || '';

                const matchUrgency = !urgency || rowUrgency.includes(urgency);
                const matchStatus = !status || rowStatus.includes(status);
                const matchSearch = !search || titleCell.includes(search) || typeCell.includes(search);

                row.style.display = (matchUrgency && matchStatus && matchSearch) ? '' : 'none';
            });
        }

        filterUrgency?.addEventListener('change', filterTable);
        filterStatus?.addEventListener('change', filterTable);
        searchInput?.addEventListener('keyup', filterTable);
    </script>
</body>
</html>
