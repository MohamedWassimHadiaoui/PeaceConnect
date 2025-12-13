<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/Mediator.php";

class MediatorController {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function addMediator($mediator) {
        $sql = "INSERT INTO mediators (name, email, phone, expertise, availability) 
                VALUES (:name, :email, :phone, :expertise, :availability)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $mediator->getName(),
            ':email' => $mediator->getEmail(),
            ':phone' => $mediator->getPhone(),
            ':expertise' => $mediator->getExpertise(),
            ':availability' => $mediator->getAvailability()
        ]);
    }

    public function listMediators() {
        $sql = "SELECT * FROM mediators ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function listAvailableMediators() {
        $sql = "SELECT * FROM mediators WHERE availability = 'available' ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getMediatorById($id) {
        $sql = "SELECT * FROM mediators WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateMediator($mediator) {
        $sql = "UPDATE mediators 
                SET name = :name, email = :email, phone = :phone, 
                    expertise = :expertise, availability = :availability 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $mediator->getId(),
            ':name' => $mediator->getName(),
            ':email' => $mediator->getEmail(),
            ':phone' => $mediator->getPhone(),
            ':expertise' => $mediator->getExpertise(),
            ':availability' => $mediator->getAvailability()
        ]);
    }

    public function deleteMediator($id) {
        $sql = "DELETE FROM mediators WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM mediators";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result ? $result['total'] : 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $controller = new MediatorController();
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    $formPath = '../View/backoffice/mediator_form.php';
    $listPath = '../View/backoffice/mediators.php';
    
    if ($action === 'add') {
        $errors = [];
        
        if (empty($_POST['name']) || strlen(trim($_POST['name'])) < 3) {
            $errors[] = "Name must be at least 3 characters";
        }
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address";
        }
        if (empty($_POST['expertise']) || strlen(trim($_POST['expertise'])) < 3) {
            $errors[] = "Expertise must be at least 3 characters";
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: " . $formPath);
            exit;
        }

        $availability = $_POST['availability'] ?? ($_POST['status'] ?? 'available');
        if ($availability === 'inactive') $availability = 'unavailable';
        
        $mediator = new Mediator(
            null,
            htmlspecialchars(trim($_POST['name'])),
            htmlspecialchars(trim($_POST['email'])),
            htmlspecialchars(trim($_POST['phone'] ?? '')),
            htmlspecialchars(trim($_POST['expertise'])),
            $availability
        );
        
        $controller->addMediator($mediator);
        header("Location: " . $listPath);
        exit;
    }
    
    elseif ($action === 'update') {
        if (empty($_POST['id'])) {
            header("Location: " . $listPath);
            exit;
        }
        
        $errors = [];
        if (empty($_POST['name']) || strlen(trim($_POST['name'])) < 3) {
            $errors[] = "Name must be at least 3 characters";
        }
        if (empty($_POST['email']) || !filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address";
        }
        if (empty($_POST['expertise']) || strlen(trim($_POST['expertise'])) < 3) {
            $errors[] = "Expertise must be at least 3 characters";
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: " . $formPath . "?id=" . $_POST['id']);
            exit;
        }

        $availability = $_POST['availability'] ?? ($_POST['status'] ?? 'available');
        if ($availability === 'inactive') $availability = 'unavailable';
        
        $phone = '';
        if (!empty($_POST['phone'])) {
            $phone = htmlspecialchars(trim($_POST['phone']));
        }
        
        $mediator = new Mediator(
            intval($_POST['id']),
            htmlspecialchars(trim($_POST['name'])),
            htmlspecialchars(trim($_POST['email'])),
            $phone,
            htmlspecialchars(trim($_POST['expertise'])),
            $availability
        );
        
        $controller->updateMediator($mediator);
        header("Location: " . $listPath);
        exit;
    }
    
    elseif ($action === 'delete') {
        if (!empty($_POST['id'])) {
            $controller->deleteMediator(intval($_POST['id']));
        }
        header("Location: " . $listPath);
        exit;
    }
}
?>
