<?php
require_once "../../controller/userController.php";
require_once "../../model/User.php";

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../frontoffice/login_admin.php');
    exit;
}

$uc = new UserController();

// POST = actually delete
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST["id_user"])) {
        die("User ID is required.");
    }

    $uc->deleteUser($_POST["id_user"]);

    header("Location: index.php");
    exit;
}

// GET = show confirmation
if (!isset($_GET['id'])) {
    die("User ID is required.");
}

$user = $uc->getUserById($_GET['id']);
if (!$user) {
    die("User not found.");
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Supprimer un utilisateur</title>
    <link rel="stylesheet" href="../frontoffice/main.css">
    <link rel="stylesheet" href="../frontoffice/components.css">
    <link rel="stylesheet" href="../frontoffice/responsive.css">
</head>
<body>

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
                <li><a href="../frontoffice/role.html">Retour au site</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="section">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Confirmer la suppression</h2>
            </div>

            <div class="card-body">
                <p class="text-light">
                    Êtes-vous sûr de vouloir supprimer cet utilisateur ?
                </p>

                <ul class="mb-3">
                    <li><strong>ID :</strong> #<?= e($user['id_user']) ?></li>
                    <li><strong>Nom :</strong> <?= e($user['name'] . ' ' . $user['lastname']) ?></li>
                    <li><strong>Email :</strong> <?= e($user['email']) ?></li>
                    <li><strong>Rôle :</strong> <?= e($user['role']) ?></li>
                </ul>

                <form method="post" action="">
                    <input type="hidden" name="id_user" value="<?= e($user['id_user']) ?>">

                    <div class="card-footer" style="display:flex; gap: var(--spacing-sm); margin-top: var(--spacing-md);">
                        <button type="submit" class="btn btn-secondary">
                            Supprimer
                        </button>
                        <a href="index.php" class="btn btn-outline">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

</body>
</html>
