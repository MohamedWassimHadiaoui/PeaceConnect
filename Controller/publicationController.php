<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/Publication.php";

class PublicationController {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function addPublication($pub) {
        $sql = "INSERT INTO publications (user_id, titre, contenu, categorie, tags, auteur, statut) 
                VALUES (:user_id, :titre, :contenu, :categorie, :tags, :auteur, :statut)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $pub->getUserId(),
            ':titre' => $pub->getTitre(),
            ':contenu' => $pub->getContenu(),
            ':categorie' => $pub->getCategorie(),
            ':tags' => $pub->getTags(),
            ':auteur' => $pub->getAuteur(),
            ':statut' => $pub->getStatut()
        ]);
        return $this->db->lastInsertId();
    }

    public function listPublications() {
        $sql = "SELECT * FROM publications WHERE statut = 'approved' ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function listAllPublications() {
        $sql = "SELECT * FROM publications ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getPublicationById($id) {
        $sql = "SELECT * FROM publications WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function approvePublication($id) {
        $sql = "UPDATE publications SET statut = 'approved' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function rejectPublication($id) {
        $sql = "UPDATE publications SET statut = 'rejected' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function listPendingPublications() {
        $sql = "SELECT * FROM publications WHERE statut = 'pending' ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function incrementLikes($id) {
        $sql = "UPDATE publications SET nombre_likes = nombre_likes + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function getPublicationWithComments($publicationId) {
        $pub = $this->getPublicationById($publicationId);
        if (!$pub) return null;

        $sql = "SELECT c.*, u.name as user_name
                FROM commentaires c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.publication_id = :id
                ORDER BY c.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $publicationId]);
        $comments = $stmt->fetchAll();

        return [
            'publication' => $pub,
            'commentaires' => $comments
        ];
    }

    public function deletePublication($id) {
        $sql = "DELETE FROM publications WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM publications";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }

    public function countPending() {
        $sql = "SELECT COUNT(*) as total FROM publications WHERE statut = 'pending'";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $controller = new PublicationController();
    $action = $_POST['action'] ?? '';

    // Infer source (frontoffice/backoffice) to make redirects work from different UIs
    $source = $_POST['source'] ?? '';
    if ($source === '') {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, '/View/backoffice/') !== false) $source = 'backoffice';
        elseif (strpos($referer, '/View/frontoffice/') !== false) $source = 'frontoffice';
    }

    $redirectBack = function ($fallback) {
        $returnTo = $_POST['return_to'] ?? '';
        if (!empty($returnTo)) {
            header("Location: " . $returnTo);
            exit;
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (!empty($referer)) {
            header("Location: " . $referer);
            exit;
        }
        header("Location: " . $fallback);
        exit;
    };

    if ($action === 'add') {
        $errors = [];

        $categorie = trim($_POST['categorie'] ?? '');
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $auteur = trim($_POST['auteur'] ?? '');

        if ($auteur === '') {
            $auteur = $_SESSION['user_name'] ?? 'Anonymous';
        }

        if ($categorie === '') $errors[] = "Category is required";
        if ($titre === '' || strlen($titre) < 5) $errors[] = "Title must be at least 5 characters";
        if ($contenu === '' || strlen($contenu) < 20) $errors[] = "Content must be at least 20 characters";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $fallback = "../View/frontoffice/forum/create.php";
            $redirectBack($fallback);
        }

        $userId = $_SESSION['user_id'] ?? null;
        
        // AI moderation
        $ai = new AIController();
        $aiResult = $ai->analyzeForumPost($titre, $contenu);
        $status = 'pending';
        $aiMessage = '';
        
        if ($aiResult['auto_approve']) {
            $status = 'approved';
            $aiMessage = ' AI approved it automatically!';
        } elseif (!$aiResult['is_safe']) {
            $status = 'rejected';
            $_SESSION['errors'] = ['Your post was flagged by our AI moderation system for potentially inappropriate content. Please revise and try again.'];
            $_SESSION['old'] = $_POST;
            $fallback = "../View/frontoffice/forum/create.php";
            $redirectBack($fallback);
        }
        
        $pub = new Publication(
            null,
            $userId,
            htmlspecialchars($titre),
            htmlspecialchars($contenu),
            htmlspecialchars($categorie),
            htmlspecialchars($tags),
            htmlspecialchars($auteur),
            $status
        );

        $controller->addPublication($pub);
        $_SESSION['success'] = "Your post was submitted." . ($status === 'approved' ? $aiMessage : ' Pending moderation.');
        $successPath = "../View/frontoffice/forum.php";
        header("Location: " . $successPath);
        exit;
    }

    if ($action === 'approve') {
        $controller->approvePublication($_POST['id']);
        $fallback = "../View/backoffice/forum.php";
        $redirectBack($fallback);
    }

    if ($action === 'reject') {
        if (!empty($_POST['id'])) $controller->rejectPublication($_POST['id']);
        $fallback = "../View/backoffice/forum.php";
        $redirectBack($fallback);
    }

    if ($action === 'like') {
        if (!empty($_POST['id'])) $controller->incrementLikes($_POST['id']);
        $fallback = "../View/frontoffice/forum.php";
        $redirectBack($fallback);
    }

    if ($action === 'delete') {
        if (!empty($_POST['id'])) $controller->deletePublication($_POST['id']);
        $fallback = "../View/backoffice/forum.php";
        $redirectBack($fallback);
    }
}
?>
