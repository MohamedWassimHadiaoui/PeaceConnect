<?php
session_start();

require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../model/User.php';

$userController = new UserController();
$error = '';

// Generate CAPTCHA if not already set (only on GET requests or first load)
if (!isset($_SESSION['captcha_answer'])) {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha_question'] = $num1 . ' + ' . $num2;
    $_SESSION['captcha_answer'] = $num1 + $num2;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $cin      = $_POST['cin'] ?? '';
    $tel      = $_POST['tel'] ?? '';
    $gender   = $_POST['gender'] ?? '';
    $role     = 'client';
    $captcha_answer = isset($_POST['captcha']) ? (int)$_POST['captcha'] : 0;

    // Validate CAPTCHA
    if (!isset($_SESSION['captcha_answer']) || $captcha_answer !== $_SESSION['captcha_answer']) {
        $error = 'CAPTCHA verification failed. Please solve the math problem correctly.';
        // Generate new CAPTCHA for retry
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $_SESSION['captcha_question'] = $num1 . ' + ' . $num2;
        $_SESSION['captcha_answer'] = $num1 + $num2;
    } else {
        // Hash the password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $user = new User(
            null,
            $name,
            $lastname,
            $email,
            $hashedPassword,
            $cin,
            $tel,
            $gender,
            $role,
            null
        );

        try {
            $userController->addUser($user);
            // fixed the broken line "$\ninsertedUser"
            $insertedUser = $userController->getUserByEmail($email);

            if ($insertedUser) {
                // Clear CAPTCHA on successful signup
                unset($_SESSION['captcha_answer']);
                unset($_SESSION['captcha_question']);
                $_SESSION['user_id'] = $insertedUser['id_user'];
                header('Location: profile.php');
                exit;
            } else {
                $error = 'Signup succeeded but user could not be loaded.';
            }
        } catch (Exception $e) {
            $error = 'Error during signup: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Sign Up - Peace</title>
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
            <li><a href="#home" class="super-button">Home</a></li>
            <li><a href="#deals" class="super-button">Forum</a></li>
            <li><a href="#deals" class="super-button">Événements</a></li>
            <li><a href="#contact" class="super-button">Signaler</a></li>
        </ul>
    </div>
</nav>

<main class="section">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Client Sign Up</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="" onsubmit="return saisie();">
                <!-- JS uses this element; now styled via template alerts -->
                <div id="errorBox" class="alert alert-error"></div>

                <div class="card-body">

                    <div class="form-group">
                        <label for="name" class="form-label">First name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            placeholder="name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="form-label">Last name</label>
                        <input
                            type="text"
                            id="lastname"
                            name="lastname"
                            class="form-control"
                            placeholder="last name"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="Email"
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
                            placeholder="Password"
                            required
                        >
                        <div class="password-strength-indicator">
                            <div class="strength-segment" id="strength-segment-1"></div>
                            <div class="strength-segment" id="strength-segment-2"></div>
                            <div class="strength-segment" id="strength-segment-3"></div>
                        </div>
                        <small class="password-strength-text" id="password-strength-text"></small>
                    </div>

                    <div class="form-group">
                        <label for="password2" class="form-label">Confirm password</label>
                        <input
                            type="password"
                            id="password2"
                            name="password2"
                            class="form-control"
                            placeholder="Confirm password"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="cin" class="form-label">CIN</label>
                        <input
                            type="text"
                            id="cin"
                            name="cin"
                            class="form-control"
                            placeholder="CIN"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="tel" class="form-label">Telephone</label>
                        <input
                            type="text"
                            id="tel"
                            name="tel"
                            class="form-control"
                            placeholder="Telephone"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <span class="form-label">Gender</span>
                        <div class="form-check">
                            <input type="radio" id="genderM" name="gender" value="M">
                            <label for="genderM" class="form-check-label">Male</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" id="genderF" name="gender" value="F">
                            <label for="genderF" class="form-check-label">Female</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="captcha" class="form-label">
                            CAPTCHA: What is <?= htmlspecialchars($_SESSION['captcha_question'] ?? '?') ?>?
                        </label>
                        <input
                            type="number"
                            id="captcha"
                            name="captcha"
                            class="form-control"
                            placeholder="Enter the answer"
                            required
                            min="0"
                        >
                        <small class="verification-code-help">Please solve the math problem to verify you're human.</small>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        Sign Up
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="java.js"></script>
</body>
</html>
