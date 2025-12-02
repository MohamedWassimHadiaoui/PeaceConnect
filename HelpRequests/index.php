<?php
/**
 * index.php - Front Controller MVC
 * Routeur central pour l'application PeaceConnect
 */

session_start();

// Récupérer l'action/page demandée
$action = isset($_GET['action']) ? trim($_GET['action'], '/ ') : 'home';
$method = isset($_GET['method']) ? trim($_GET['method'], '/ ') : 'index';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Charger les contrôleurs nécessaires
require_once __DIR__ . '/Controller/HelpRequestController.php';
require_once __DIR__ . '/Controller/OrganisationController.php';
require_once __DIR__ . '/Controller/AuthController.php';

// Routage des actions
switch ($action) {
    // Page d'accueil
    case 'home':
    case '':
        include __DIR__ . '/View/FrontOffice/home.php';
        break;

    // Gestion des demandes d'aide (Help Requests)
    case 'help-requests':
    case 'help-request':
        if ($method === 'create') {
            HelpRequestController::create();
        } elseif ($method === 'edit' && $id) {
            HelpRequestController::edit($id);
        } elseif ($method === 'delete' && $id) {
            HelpRequestController::delete($id);
        } elseif ($id) {
            HelpRequestController::show($id);
        } else {
            HelpRequestController::index();
        }
        break;

    // Gestion des organisations
    case 'organisations':
    case 'organisation':
        if ($method === 'create') {
            OrganisationController::create();
        } elseif ($method === 'edit' && $id) {
            OrganisationController::edit($id);
        } elseif ($method === 'delete' && $id) {
            OrganisationController::delete($id);
        } elseif ($method === 'search') {
            OrganisationController::search();
        } elseif ($id) {
            OrganisationController::show($id);
        } else {
            OrganisationController::index();
        }
        break;

    // Pages statiques (compatibilité rétroactive)
    case 'dashboard':
        // Si section=backoffice => vérifier rôle admin
        $sectionParam = isset($_GET['section']) ? $_GET['section'] : '';
        if ($sectionParam === 'backoffice') {
            if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
                // rediriger vers la page d'accueil si non autorisé
                header('Location: index.php');
                exit;
            }
            require __DIR__ . '/View/BackOffice/dashboard.php';
        } else {
            require __DIR__ . '/View/FrontOffice/home.php';
        }
        break;
    case 'admin_login':
        // POST: admin_password => vérification et session
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthController::loginProcess();
        } else {
            AuthController::showLoginForm();
        }
        break;
    case 'admin_logout':
        AuthController::logout();
        break;
    case 'login':
        include __DIR__ . '/View/FrontOffice/login.php';
        break;
    case 'register':
        include __DIR__ . '/register.html';
        break;
    case 'profile':
        include __DIR__ . '/profile.html';
        break;
    case 'forum':
        include __DIR__ . '/forum.html';
        break;
    case 'events':
        include __DIR__ . '/events.html';
        break;
    case 'reports-management':
        include __DIR__ . '/reports-management.html';
        break;
    case 'create-report':
        include __DIR__ . '/create-report.html';
        break;

    // Erreur 404
    default:
        http_response_code(404);
        include __DIR__ . '/View/404.php';
        break;
}
