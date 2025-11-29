<?php
session_start();

require_once __DIR__ . '/../../controller/userController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login_client.php');
    exit;
}

$userController = new UserController();
$userId = $_SESSION['user_id'];
$user   = $userController->getUserById($userId);

if (!$user) {
    session_destroy();
    header('Location: login_client.php');
    exit;
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Peace</title>

    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="components.css">
    <link rel="stylesheet" href="responsive.css">
</head>

<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="container navbar-content">
        <a href="role.html" class="navbar-brand">
            <span>🕊️ Peace</span>
        </a>

        <button class="navbar-toggle">☰</button>

        <ul class="navbar-menu">
            <li><a href="role.html" class="super-button">Home</a></li>
            <li><a href="#deals" class="super-button">Forum</a></li>
            <li><a href="#deals" class="super-button">Événements</a></li>
            <li><a href="#contact" class="super-button">Signaler</a></li>
        </ul>
    </div>
</nav>


<div class="container mt-4">

    <!-- Profile Header -->
    <div class="card profile-header d-flex align-items-center">
        <img src="https://via.placeholder.com/200"
             alt="User Photo"
             class="profile-photo"
             style="width: 20vw; height: 30vh; object-fit: cover; margin-right: 2vw; border-radius: 10px;">

        <div>
            <h1 class="mb-1"><?= e($user['name'] . " " . $user['lastname']) ?></h1>
            <p class="text-light mb-1"><?= e($user['email']) ?></p>
            <p class="text-light mb-3">Member since <?= date("F Y") ?></p>

            <a href="#" class="btn">Edit Profile</a>
            <a href="#" class="btn">Delete Profile</a>
        </div>
    </div>


    <!-- Tabs -->
    <div class="tabs mt-4">
        <ul class="tabs-list">
            <li class="tab-item active">Profile</li>
            <li class="tab-item">Contributions</li>
            <li class="tab-item">Settings</li>
        </ul>
    </div>


    <!-- Grid Layout -->
    <div class="grid grid-2 mt-4">

        <!-- About Me -->
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">About Me</div>
                </div>

                <div class="card-body">
                    <table class="mb-4 w-100">
                        <tr>
                            <td class="text-light">Full Name:</td>
                            <td><?= e($user['name'] . " " . $user['lastname']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-light">Email:</td>
                            <td><?= e($user['email']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-light">CIN:</td>
                            <td><?= e($user['cin']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-light">Telephone:</td>
                            <td><?= e($user['tel']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-light">Gender:</td>
                            <td><?= e($user['gender']) ?></td>
                        </tr>
                    </table>

                    <div class="section-title mb-2">Badges</div>
                    <div class="d-flex gap-3">
                        <span class="badge badge-info">New Member</span>
                        <span class="badge badge-info">Verified</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Contributions (Demo) -->
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Contributions</div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <strong>Your last login</strong>
                        <p class="mb-0 text-light">Welcome back to Peace!</p>
                    </div>

                    <div class="alert alert-info">
                        <strong>Profile Active</strong>
                        <p class="mb-0 text-light">Your account is verified and active.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<footer style="background-color: var(--color-text); color: white; padding: 2rem 0; margin-top: 4rem;">
    <div class="container" style="text-align: center;">
        <p>&copy; 2025 Peace. All Rights Reserved.</p>
    </div>
</footer>

</body>
</html>
