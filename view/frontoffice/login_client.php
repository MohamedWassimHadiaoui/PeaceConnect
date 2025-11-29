<?php
session_start();

require_once __DIR__ . '/../../controller/userController.php';

$userController = new UserController();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $user     = $userController->getUserByEmail($email);

    if ($user && $user['password'] === $password) {
        $_SESSION['user_id'] = $user['id_user'];
        header('Location: profile.php');
        exit;
    } else {
        $error = 'Incorrect email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Login - Peace</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="components.css">
    <link rel="stylesheet" href="responsive.css">
</head>
<body>

<nav class="navbar">
    <div class="container navbar-content">
        <div class="navbar-brand">
            <span>🕊️ Peace</span>
        </div>
        <ul class="navbar-menu">
            <li><a href="role.html">Home</a></li>
            <li><a href="#deals">Deals</a></li>
            <li><a href="#shop">Shop Now</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </div>
</nav>

<main class="section">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Client Login</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="card-body">

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="email"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="password"
                            required
                        >
                    </div>

                </div>

                <div class="card-footer">
                    <!-- removed onclick="return saisie()" (it was for signup) -->
                    <button type="submit" class="btn btn-primary btn-block">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="java.js"></script>
</body>
</html>
