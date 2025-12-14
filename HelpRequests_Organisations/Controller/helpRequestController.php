<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/HelpRequest.php";

class HelpRequestController {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function addHelpRequest($request) {
        $sql = "INSERT INTO help_requests (user_id, help_type, urgency_level, situation, location, contact_method, status, responsable) 
                VALUES (:user_id, :help_type, :urgency_level, :situation, :location, :contact_method, :status, :responsable)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $request->getUserId(),
            ':help_type' => $request->getHelpType(),
            ':urgency_level' => $request->getUrgencyLevel(),
            ':situation' => $request->getSituation(),
            ':location' => $request->getLocation(),
            ':contact_method' => $request->getContactMethod(),
            ':status' => $request->getStatus(),
            ':responsable' => $request->getResponsable()
        ]);
        return $this->db->lastInsertId();
    }

    public function listHelpRequests() {
        $sql = "SELECT h.*, u.name as user_name FROM help_requests h LEFT JOIN users u ON h.user_id = u.id ORDER BY h.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getHelpRequestById($id) {
        $sql = "SELECT h.*, u.name as user_name FROM help_requests h LEFT JOIN users u ON h.user_id = u.id WHERE h.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateHelpRequest($request) {
        $sql = "UPDATE help_requests SET help_type = :help_type, urgency_level = :urgency_level, 
                situation = :situation, location = :location, contact_method = :contact_method, 
                status = :status, responsable = :responsable WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $request->getId(),
            ':help_type' => $request->getHelpType(),
            ':urgency_level' => $request->getUrgencyLevel(),
            ':situation' => $request->getSituation(),
            ':location' => $request->getLocation(),
            ':contact_method' => $request->getContactMethod(),
            ':status' => $request->getStatus(),
            ':responsable' => $request->getResponsable()
        ]);
    }

    public function deleteHelpRequest($id) {
        $sql = "DELETE FROM help_requests WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM help_requests";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }

    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM help_requests WHERE status = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetch()['total'];
    }

    public function listUserRequests($userId) {
        $sql = "SELECT * FROM help_requests WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function listAllRequests() {
        $sql = "SELECT h.*, u.name as user_name FROM help_requests h LEFT JOIN users u ON h.user_id = u.id ORDER BY h.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $controller = new HelpRequestController();
    $action = $_POST['action'] ?? '';

    $source = $_POST['source'] ?? '';
    if ($source === '') {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, '/View/backoffice/') !== false) $source = 'backoffice';
        elseif (strpos($referer, '/View/frontoffice/') !== false) $source = 'frontoffice';
    }

    $listPath = ($source === 'backoffice') ? "../View/backoffice/help_requests.php" : "../View/frontoffice/help_request.php";
    $formPath = ($source === 'backoffice') ? "../View/backoffice/help_request_form.php" : "../View/frontoffice/help_request.php";

    if ($action === 'add') {
        $errors = [];
        if (empty($_POST['help_type'])) $errors[] = "Help type is required";
        if (empty($_POST['situation']) || strlen($_POST['situation']) < 20) $errors[] = "Situation must be at least 20 characters";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: " . $formPath);
            exit;
        }

        $urgencyLevel = $_POST['urgency_level'] ?? 'medium';
        $request = new HelpRequest(null, $_SESSION['user_id'] ?? null, $_POST['help_type'], 
                                   $urgencyLevel, $_POST['situation'],
                                   $_POST['location'] ?? null, $_POST['contact_method'] ?? null);
        $requestId = $controller->addHelpRequest($request);
        
        $aiMessage = '';
        if ($requestId) {
            $ai = new AIController();
            $aiResult = $ai->analyzeHelpRequest($_POST['help_type'], $_POST['situation']);
            if ($aiResult) {
                if (($aiResult['violence_detected'] ?? false) || ($aiResult['urgency_level'] ?? '') === 'critical' || ($aiResult['urgency_level'] ?? '') === 'high') {
                    $aiMessage = ' AI flagged this as high priority.';
                }
            }
        }
        
        $_SESSION['success'] = "Help request submitted successfully!" . $aiMessage;
        header("Location: " . $listPath);
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? null;
        if (!$id) { header("Location: " . $listPath); exit; }

        $request = new HelpRequest($id, null, $_POST['help_type'], $_POST['urgency_level'] ?? 'medium',
                                   $_POST['situation'], $_POST['location'] ?? null, 
                                   $_POST['contact_method'] ?? null, $_POST['status'] ?? 'pending',
                                   $_POST['responsable'] ?? null);
        $controller->updateHelpRequest($request);
        header("Location: " . $listPath);
        exit;
    }

    if ($action === 'delete') {
        if (!empty($_POST['id'])) $controller->deleteHelpRequest($_POST['id']);
        header("Location: " . $listPath);
        exit;
    }
}
?>
