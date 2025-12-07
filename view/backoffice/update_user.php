<?php
require_once "../../controller/userController.php";
require_once "../../model/User.php";

session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../frontoffice/login_admin.php');
    exit;
}

// Controller
$uc = new UserController();

// If GET: we expect ?id=...
if (!isset($_GET['id']) && $_SERVER["REQUEST_METHOD"] !== "POST") {
    die("User ID is required.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // We trust hidden id_user
    if (!isset($_POST["id_user"])) {
        die("User ID is required.");
    }

    $userData = $uc->getUserById($_POST["id_user"]);
    if (!$userData) {
        die("User not found.");
    }

    $password = $userData["password"];

    // If new password is entered => (re)hash it
    if (!empty($_POST["password"])) {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    }

    $updatedUser = new User(
        $_POST["id_user"],
        $_POST["name"],
        $_POST["lastname"],
        $_POST["email"],
        $password,
        $_POST["cin"],
        $_POST["tel"],
        $_POST["gender"],
        $_POST["role"],
        $userData["avatar"] ?? null
    );

    $uc->updateUser($updatedUser, $_POST["id_user"]);

    header("Location: index.php");
    exit;
} else {
    // GET: load user
    $user = $uc->getUserById($_GET['id']);
    if (!$user) {
        die("User not found.");
    }
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'utilisateur</title>
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
                <h2 class="card-title">Modifier l'utilisateur</h2>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <input type="hidden" name="id_user" value="<?= e($user['id_user']) ?>">

                    <div class="grid grid-2" style="gap: var(--spacing-md);">
                        <div class="form-group">
                            <label for="name" class="form-label">Prénom</label>
                            <input type="text" id="name" name="name" class="form-control"
                                   value="<?= e($user['name']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="lastname" class="form-label">Nom</label>
                            <input type="text" id="lastname" name="lastname" class="form-control"
                                   value="<?= e($user['lastname']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                   value="<?= e($user['email']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">
                                Nouveau mot de passe
                                <span class="text-light">(laisser vide pour garder l'actuel)</span>
                            </label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="cin" class="form-label">CIN</label>
                            <input type="text" id="cin" name="cin" class="form-control"
                                   value="<?= e($user['cin']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="tel" class="form-label">Téléphone</label>
                            <input type="text" id="tel" name="tel" class="form-control"
                                   value="<?= e($user['tel']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="gender" class="form-label">Genre</label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="">Genre</option>
                                <option value="male"   <?= ($user['gender'] === 'male' || $user['gender'] === 'M') ? 'selected' : '' ?>>Homme</option>
                                <option value="female" <?= ($user['gender'] === 'female' || $user['gender'] === 'F') ? 'selected' : '' ?>>Femme</option>
                                <option value="other"  <?= $user['gender'] === 'other' ? 'selected' : '' ?>>Autre</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="role" class="form-label">Rôle</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="0" <?= (int)$user['role'] === 0 ? 'selected' : '' ?>>Client</option>
                                <option value="1" <?= (int)$user['role'] === 1 ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>

                    </div>

                    <div class="card-footer" style="margin-top: var(--spacing-md); display:flex; gap: var(--spacing-sm);">
                        <button type="submit" class="btn btn-primary">
                            Enregistrer les modifications
                        </button>
                        <a href="index.php" class="btn btn-outline">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

</body>
</html>
