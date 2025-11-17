<?php
session_start();
require_once "reportController.php";

// Server-side validation
$errors = [];

// Validate type
if (empty($_POST['type'])) {
    $errors[] = "Le type est obligatoire";
} elseif (strlen(trim($_POST['type'])) < 3) {
    $errors[] = "Le type doit contenir au moins 3 caractères";
}

// Validate title
if (empty($_POST['title'])) {
    $errors[] = "Le titre est obligatoire";
} elseif (strlen(trim($_POST['title'])) < 5) {
    $errors[] = "Le titre doit contenir au moins 5 caractères";
} elseif (strlen(trim($_POST['title'])) > 100) {
    $errors[] = "Le titre ne doit pas dépasser 100 caractères";
}

// Validate description
if (empty($_POST['description'])) {
    $errors[] = "La description est obligatoire";
} elseif (strlen(trim($_POST['description'])) < 10) {
    $errors[] = "La description doit contenir au moins 10 caractères";
}

// Validate location (optional but if provided, min 3 chars)
if (!empty($_POST['location']) && strlen(trim($_POST['location'])) < 3) {
    $errors[] = "Le lieu doit contenir au moins 3 caractères";
}

// Validate incident_date (optional but if provided, must be valid date)
if (!empty($_POST['incident_date'])) {
    $date = $_POST['incident_date'];
    $d = \DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        $errors[] = "Format de date invalide";
    } elseif ($d > new \DateTime()) {
        $errors[] = "La date ne peut pas être dans le futur";
    }
}

// Validate priority
$validPriorities = ['low', 'medium', 'high'];
if (!empty($_POST['priority']) && !in_array($_POST['priority'], $validPriorities)) {
    $errors[] = "Priorité invalide";
}

// If there are errors, redirect back with error message
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../views/back-office/add_report.php");
    exit;
}

// Sanitize inputs
$type = htmlspecialchars(trim($_POST['type']));
$title = htmlspecialchars(trim($_POST['title']));
$description = htmlspecialchars(trim($_POST['description']));
$location = !empty($_POST['location']) ? htmlspecialchars(trim($_POST['location'])) : null;
$incident_date = !empty($_POST['incident_date']) ? $_POST['incident_date'] : null;
$priority = $_POST['priority'] ?? 'medium';

$rc = new reportController();
$r = new Report(null, $type, $title, $description, $location, $incident_date, $priority, 'pending');
$rc->addReport($r);
header("Location: ../views/back-office/reports.php");
?>

