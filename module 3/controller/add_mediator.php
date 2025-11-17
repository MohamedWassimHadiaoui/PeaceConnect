<?php
session_start();
require_once "mediatorController.php";

// Server-side validation
$errors = [];

// Validate name
if (empty($_POST['name'])) {
    $errors[] = "Le nom est obligatoire";
} elseif (strlen(trim($_POST['name'])) < 3) {
    $errors[] = "Le nom doit contenir au moins 3 caractères";
} elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/u", $_POST['name'])) {
    $errors[] = "Le nom contient des caractères invalides";
}

// Validate email
if (empty($_POST['email'])) {
    $errors[] = "L'email est obligatoire";
} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format d'email invalide";
}

// Validate phone (optional but if provided, must be valid)
if (!empty($_POST['phone']) && !preg_match("/^[\d\s\+\-\(\)]{8,}$/", $_POST['phone'])) {
    $errors[] = "Numéro de téléphone invalide (min 8 chiffres)";
}

// Validate expertise
if (empty($_POST['expertise'])) {
    $errors[] = "L'expertise est obligatoire";
} elseif (strlen(trim($_POST['expertise'])) < 3) {
    $errors[] = "L'expertise doit contenir au moins 3 caractères";
}

// Validate availability
$validAvailability = ['available', 'busy', 'unavailable'];
if (!empty($_POST['availability']) && !in_array($_POST['availability'], $validAvailability)) {
    $errors[] = "Disponibilité invalide";
}

// If there are errors, redirect back with error message
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../views/back-office/add_mediator.php");
    exit;
}

// Sanitize inputs
$name = htmlspecialchars(trim($_POST['name']));
$email = htmlspecialchars(trim($_POST['email']));
$phone = !empty($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : null;
$expertise = htmlspecialchars(trim($_POST['expertise']));
$availability = $_POST['availability'] ?? 'available';

$mc = new mediatorController();
$m = new Mediator(null, $name, $email, $phone, $expertise, $availability);
$mc->addMediator($m);
header("Location: ../views/back-office/mediators.php");
?>

