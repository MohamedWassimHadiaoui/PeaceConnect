<?php
session_start();
require_once __DIR__ . '/../../controller/userController.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../frontoffice/login_admin.php');
    exit;
}

$userController = new UserController();

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

// Get filter parameters from GET (form uses method="get")
$search     = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';

// Map role filter to database values
// Database stores: 0 = client, 1 = admin (integers)
$roleFilterValue = null;
if ($roleFilter === 'admin') {
    $roleFilterValue = 1; // Admin is stored as 1 in database
} elseif ($roleFilter === 'client') {
    $roleFilterValue = 0; // Client is stored as 0 in database
}

// Get all users
$allUsers = $userController->listUsers();

// Filter users based on search and role
$users = [];
foreach ($allUsers as $u) {
    $fullName = trim(($u['name'] ?? '') . ' ' . ($u['lastname'] ?? ''));
    
    // Filter by search (name or email)
    if ($search !== '') {
        $nameMatch = stripos($u['name'] ?? '', $search) !== false;
        $lastnameMatch = stripos($u['lastname'] ?? '', $search) !== false;
        $emailMatch = stripos($u['email'] ?? '', $search) !== false;
        $fullNameMatch = stripos($fullName, $search) !== false;
        
        if (!$nameMatch && !$lastnameMatch && !$emailMatch && !$fullNameMatch) {
            continue;
        }
    }
    
    // Filter by role - handle both integer (0/1) and string ('client'/'admin') formats
    if ($roleFilterValue !== null) {
        $userRole = $u['role'] ?? '';
        // Convert to integer for comparison (database stores as int, but might be string)
        $userRoleInt = (int)$userRole;
        
        // Also check if it's stored as string 'admin' or 'client'
        $userRoleStr = strtolower((string)$userRole);
        
        // Check if user role matches the filter
        $roleMatches = false;
        if ($roleFilterValue === 1) {
            // Looking for admin: check for 1, '1', or 'admin'
            $roleMatches = ($userRoleInt === 1 || $userRoleStr === '1' || $userRoleStr === 'admin');
        } elseif ($roleFilterValue === 0) {
            // Looking for client: check for 0, '0', or 'client'
            $roleMatches = ($userRoleInt === 0 || $userRoleStr === '0' || $userRoleStr === 'client');
        }
        
        if (!$roleMatches) {
            continue;
        }
    }
    
    $users[] = $u;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Peace Admin – Gestion des utilisateurs</title>

    <!-- same template as other screens -->
    <link rel="stylesheet" href="../frontoffice/main.css">
    <link rel="stylesheet" href="../frontoffice/components.css">
    <link rel="stylesheet" href="../frontoffice/responsive.css">
</head>
<body>

<!-- Navbar (template) -->
<nav class="navbar">
    <div class="container">
        <div class="navbar-content">
            <a href="index.php" class="navbar-brand">
                <span>🕊️</span>
                <span>Peace Admin</span>
            </a>

            <button class="navbar-toggle">☰</button>

            <ul class="navbar-menu">
                <li><a href="index.php" class="active">Utilisateurs</a></li>
                <!-- only what we actually have for now -->
                <li><a href="../frontoffice/role.html">Retour au site</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="section">
    <div class="container">

        <!-- Page title -->
        <header class="section-header">
            <h1>Gestion des utilisateurs</h1>
            <p class="text-light">
                Gérer les comptes clients et administrateurs de la plateforme Peace.
            </p>
        </header>

        <!-- Filters card -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title">Filtres</div>
            </div>
            <div class="card-body">
                <form method="get" class="grid grid-3" style="gap: var(--spacing-md);">
                    <div class="form-group">
                        <label for="search" class="form-label">Rechercher</label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            class="form-control"
                            placeholder="Nom, email…"
                            value="<?= e($search) ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Rôle</label>
                        <select id="role" name="role" class="form-control">
                            <option value="">Tous les rôles</option>
                            <option value="admin"  <?= $roleFilter === 'admin'  ? 'selected' : '' ?>>Admin</option>
                            <option value="client" <?= $roleFilter === 'client' ? 'selected' : '' ?>>Client</option>
                        </select>
                    </div>

                    <div class="form-group" style="align-self: flex-end; display: flex; gap: var(--spacing-sm);">
                        <button type="submit" class="btn btn-primary">
                            Filtrer
                        </button>
                        <?php if ($search !== '' || $roleFilter !== ''): ?>
                            <a href="index.php" class="btn btn-outline">
                                Réinitialiser
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users table -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Liste des utilisateurs</div>
            </div>

            <div class="card-body table-container">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>CIN</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 2rem; color: #666;">
                                <?php if ($search !== '' || $roleFilter !== ''): ?>
                                    Aucun utilisateur trouvé avec ces critères de recherche.
                                <?php else: ?>
                                    Aucun utilisateur trouvé.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <?php
                            $fullName = trim(($u['name'] ?? '') . ' ' . ($u['lastname'] ?? ''));
                            ?>
                            <tr>
                                <td>#<?= e($u['id_user']) ?></td>
                                <td><?= e($fullName) ?></td>
                                <td><?= e($u['email']) ?></td>
                                <td><?= e($u['cin']) ?></td>
                                <td>
                                    <span class="badge <?= ($u['role'] ?? '') === 'admin'
                                            ? 'badge-danger'
                                            : 'badge-success' ?>">
                                        <?= e(ucfirst($u['role'] ?? 'client')) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="update_user.php?id=<?= e($u['id_user']) ?>"
                                       class="btn btn-outline btn-sm">
                                        Voir
                                    </a>
                                    <a href="delete_user.php?id=<?= e($u['id_user']) ?>"
                                       class="btn btn-secondary btn-sm"
                                       onclick="return confirm('Supprimer cet utilisateur ?');">
                                        Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

</body>
</html>
