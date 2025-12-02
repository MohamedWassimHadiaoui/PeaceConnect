<?php
/**
 * Controller/OrganisationController.php
 * Gère les opérations CRUD pour les organisations
 */

require_once __DIR__ . '/../Model/organisation_logic.php';

class OrganisationController {
    
    /**
     * Affiche la liste de toutes les organisations
     */
    public static function index() {
        $organisations = org_get_all();
        $section = isset($_GET['section']) ? $_GET['section'] : 'public';
        
        // Accepter 'admin' ou 'backoffice' comme backoffice
        if ($section === 'admin' || $section === 'backoffice') {
            require __DIR__ . '/../View/BackOffice/organisations/list.php';
        } else {
            require __DIR__ . '/../View/FrontOffice/organisations/list.php';
        }
    }

    /**
     * Affiche les détails d'une organisation
     */
    public static function show($id) {
        $organisation = org_get($id);
        if (!$organisation) {
            header('Location: index.php?action=organisations');
            exit;
        }
        require __DIR__ . '/../View/FrontOffice/organisations/show.php';
    }

    /**
     * Affiche le formulaire de création ou crée une nouvelle organisation
     */
    public static function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $payload = org_get_post_data();
                $id = org_create($payload);
                $section = isset($_GET['section']) ? '&section=' . $_GET['section'] : '';
                header('Location: index.php?action=organisations' . $section);
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $section = isset($_GET['section']) ? $_GET['section'] : 'public';
        if ($section === 'admin' || $section === 'backoffice') {
            require __DIR__ . '/../View/BackOffice/organisations/form.php';
        } else {
            // Front office n'a pas de création
            require __DIR__ . '/../View/FrontOffice/organisations/list.php';
        }
    }

    /**
     * Affiche le formulaire d'édition ou met à jour une organisation
     */
    public static function edit($id) {
        $organisation = org_get($id);
        if (!$organisation) {
            header('Location: index.php?action=organisations');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $payload = org_get_post_data();
                org_update($id, $payload);
                $section = isset($_GET['section']) ? '&section=' . $_GET['section'] : '';
                header('Location: index.php?action=organisations' . $section);
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $section = isset($_GET['section']) ? $_GET['section'] : 'public';
        if ($section === 'admin' || $section === 'backoffice') {
            require __DIR__ . '/../View/BackOffice/organisations/form.php';
        } else {
            require __DIR__ . '/../View/FrontOffice/organisations/list.php';
        }
    }

    /**
     * Supprime une organisation
     */
    public static function delete($id) {
        org_delete($id);
        $section = isset($_GET['section']) ? '&section=' . $_GET['section'] : '';
        header('Location: index.php?action=organisations' . $section);
        exit;
    }

    /**
     * Recherche les organisations par critères
     */
    public static function search() {
        $organisations = [];
        $criteria = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $criteria = [
                'name' => $_POST['name'] ?? '',
                'category' => $_POST['category'] ?? '',
                'status' => $_POST['status'] ?? '',
                'city' => $_POST['city'] ?? '',
            ];
            $organisations = org_search($criteria);
        }
        
        $section = isset($_GET['section']) ? $_GET['section'] : 'public';
        if ($section === 'admin' || $section === 'backoffice') {
            require __DIR__ . '/../View/BackOffice/organisations/search.php';
        } else {
            require __DIR__ . '/../View/FrontOffice/organisations/search.php';
        }
    }
}

?>
