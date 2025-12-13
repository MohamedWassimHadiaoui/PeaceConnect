<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/Report.php";

class ReportController {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function addReport($report, $userId = null) {
        $sql = "INSERT INTO reports (type, title, description, location, incident_date, priority, status, mediator_id, attachment_path, user_id) 
                VALUES (:type, :title, :description, :location, :incident_date, :priority, :status, :mediator_id, :attachment_path, :user_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':type' => $report->getType(),
            ':title' => $report->getTitle(),
            ':description' => $report->getDescription(),
            ':location' => $report->getLocation(),
            ':incident_date' => $report->getIncidentDate(),
            ':priority' => $report->getPriority(),
            ':status' => $report->getStatus(),
            ':mediator_id' => $report->getMediatorId(),
            ':attachment_path' => $report->getAttachmentPath(),
            ':user_id' => $userId
        ]);
        return $this->db->lastInsertId();
    }
    
    public function analyzeWithAI($reportId, $title, $description, $type) {
        try {
            require_once __DIR__ . "/aiController.php";
            $aiController = new AIController();
            return $aiController->analyzeReport($reportId, $title, $description, $type);
        } catch (Exception $e) {
            error_log("AI Analysis Error: " . $e->getMessage());
            return null;
        }
    }

    public function listReports() {
        $sql = "SELECT * FROM reports ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function listReportsWithMediators() {
        $sql = "SELECT r.*, m.name as mediator_name, m.expertise as mediator_expertise 
                FROM reports r 
                LEFT JOIN mediators m ON r.mediator_id = m.id 
                ORDER BY r.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getReportById($id) {
        $sql = "SELECT * FROM reports WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getReportsByMediator($mediatorId) {
        $sql = "SELECT r.*, m.name as mediator_name 
                FROM reports r 
                INNER JOIN mediators m ON r.mediator_id = m.id 
                WHERE r.mediator_id = :mediator_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':mediator_id' => $mediatorId]);
        return $stmt->fetchAll();
    }

    public function listReportsByUser($userId) {
        $sql = "SELECT * FROM reports WHERE user_id = :user_id ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function updateReport($report) {
        $sql = "UPDATE reports 
                SET type = :type, title = :title, description = :description, 
                    location = :location, incident_date = :incident_date, 
                    priority = :priority, status = :status, mediator_id = :mediator_id,
                    attachment_path = :attachment_path 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $report->getId(),
            ':type' => $report->getType(),
            ':title' => $report->getTitle(),
            ':description' => $report->getDescription(),
            ':location' => $report->getLocation(),
            ':incident_date' => $report->getIncidentDate(),
            ':priority' => $report->getPriority(),
            ':status' => $report->getStatus(),
            ':mediator_id' => $report->getMediatorId(),
            ':attachment_path' => $report->getAttachmentPath()
        ]);
    }

    public function deleteReport($id) {
        $sql = "DELETE FROM reports WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM reports";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }

    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM reports WHERE status = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetch()['total'];
    }

    public function searchReports($keyword) {
        $sql = "SELECT r.*, m.name as mediator_name FROM reports r 
                LEFT JOIN mediators m ON r.mediator_id = m.id 
                WHERE r.title LIKE :kw OR r.description LIKE :kw OR r.type LIKE :kw 
                ORDER BY r.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':kw' => "%$keyword%"]);
        return $stmt->fetchAll();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $controller = new ReportController();
    $action = $_POST['action'] ?? '';

    // Support frontoffice/backoffice + legacy UI (View/*.php)
    $source = $_POST['source'] ?? '';
    if ($source === '') {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, '/View/frontoffice/') !== false) $source = 'frontoffice';
        elseif (strpos($referer, '/View/backoffice/') !== false) $source = 'backoffice';
        elseif (strpos($referer, '/View/') !== false) $source = 'legacy';
    }

    $isFromFrontoffice = ($source === 'frontoffice');
    
    if ($action === 'add') {
        $errors = [];
        if (empty($_POST['type'])) $errors[] = "Incident type is required";
        if (empty($_POST['title']) || strlen(trim($_POST['title'])) < 5) $errors[] = "Title must be at least 5 characters";
        if (empty($_POST['description']) || strlen(trim($_POST['description'])) < 10) $errors[] = "Description must be at least 10 characters";
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            if ($isFromFrontoffice) header("Location: ../View/frontoffice/create_report.php");
            else header("Location: ../View/backoffice/report_form.php");
            exit;
        }
        
        $incidentDate = !empty($_POST['incident_date']) ? trim($_POST['incident_date']) : null;
        $mediatorId = !empty($_POST['mediator_id']) ? intval($_POST['mediator_id']) : null;
        $location = !empty($_POST['location']) ? htmlspecialchars(trim($_POST['location'])) : '';
        $priority = $_POST['priority'] ?? 'medium';
        $status = 'pending';
        
        $attachmentPath = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            // Validate file size (10MB max for reports - can include PDFs)
            $maxFileSize = 10 * 1024 * 1024;
            if ($_FILES['attachment']['size'] > $maxFileSize) {
                $errors[] = "Attachment file too large. Maximum 10MB allowed.";
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                if ($isFromFrontoffice) {
                    header("Location: ../View/frontoffice/create_report.php");
                } else {
                    header("Location: ../View/backoffice/report_form.php");
                }
                exit;
            }
            
            $tmpName = $_FILES['attachment']['tmp_name'];
            $originalName = basename($_FILES['attachment']['name']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','pdf'];
            if (in_array($extension, $allowed)) {
                // Validate image files
                if (in_array($extension, ['jpg','jpeg','png','gif'])) {
                    $imageInfo = getimagesize($tmpName);
                    if ($imageInfo === false) {
                        $errors[] = "File is not a valid image.";
                        $_SESSION['errors'] = $errors;
                        $_SESSION['old'] = $_POST;
                        if ($isFromFrontoffice) {
                            header("Location: ../View/frontoffice/create_report.php");
                        } else {
                            header("Location: ../View/backoffice/report_form.php");
                        }
                        exit;
                    }
                }
                
                if (!is_dir(__DIR__ . '/../uploads')) mkdir(__DIR__ . '/../uploads', 0755, true);
                $newName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                $destination = __DIR__ . '/../uploads/' . $newName;
                if (move_uploaded_file($tmpName, $destination)) {
                    $attachmentPath = 'uploads/' . $newName;
                }
            }
        }

        $report = new Report(
            null,
            htmlspecialchars(trim($_POST['type'])),
            htmlspecialchars(trim($_POST['title'])),
            htmlspecialchars(trim($_POST['description'])),
            $location,
            $incidentDate,
            $priority,
            $status,
            $mediatorId,
            $attachmentPath
        );
        
        $userId = $_SESSION['user_id'] ?? null;
        $reportId = $controller->addReport($report, $userId);
        
        if ($reportId) {
            $controller->analyzeWithAI($reportId, trim($_POST['title']), trim($_POST['description']), trim($_POST['type']));
        }
        
        $_SESSION['success'] = "Report submitted successfully!";
        if ($isFromFrontoffice) header("Location: ../View/frontoffice/my_reports.php");
        else header("Location: ../View/backoffice/reports.php");
        exit;
    }
    
    elseif ($action === 'update') {
        if (empty($_POST['id'])) { header("Location: ../View/backoffice/reports.php"); exit; }
        
        $errors = [];
        if (empty($_POST['type'])) $errors[] = "Incident type is required";
        if (empty($_POST['title']) || strlen(trim($_POST['title'])) < 5) $errors[] = "Title must be at least 5 characters";
        if (empty($_POST['description']) || strlen(trim($_POST['description'])) < 10) $errors[] = "Description must be at least 10 characters";
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ../View/backoffice/report_form.php?id=" . $_POST['id']);
            exit;
        }
        
        $incidentDate = !empty($_POST['incident_date']) ? trim($_POST['incident_date']) : null;
        $mediatorId = !empty($_POST['mediator_id']) ? intval($_POST['mediator_id']) : null;
        $location = !empty($_POST['location']) ? htmlspecialchars(trim($_POST['location'])) : '';
        
        $attachmentPath = $_POST['existing_attachment'] ?? null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            // Validate file size (10MB max for reports)
            $maxFileSize = 10 * 1024 * 1024;
            if ($_FILES['attachment']['size'] > $maxFileSize) {
                $_SESSION['errors'] = ["Attachment file too large. Maximum 10MB allowed."];
                $_SESSION['old'] = $_POST;
                header("Location: ../View/backoffice/report_form.php?id=" . $_POST['id']);
                exit;
            }
            
            $tmpName = $_FILES['attachment']['tmp_name'];
            $originalName = basename($_FILES['attachment']['name']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','pdf'];
            if (in_array($extension, $allowed)) {
                // Validate image files
                if (in_array($extension, ['jpg','jpeg','png','gif'])) {
                    $imageInfo = getimagesize($tmpName);
                    if ($imageInfo === false) {
                        $_SESSION['errors'] = ["File is not a valid image."];
                        $_SESSION['old'] = $_POST;
                        header("Location: ../View/backoffice/report_form.php?id=" . $_POST['id']);
                        exit;
                    }
                }
                
                if (!is_dir(__DIR__ . '/../uploads')) mkdir(__DIR__ . '/../uploads', 0755, true);
                $newName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                $destination = __DIR__ . '/../uploads/' . $newName;
                
                // Delete old attachment if exists
                $oldAttachment = $_POST['existing_attachment'] ?? null;
                
                if (move_uploaded_file($tmpName, $destination)) {
                    $attachmentPath = 'uploads/' . $newName;
                    
                    // Delete old file
                    if ($oldAttachment && file_exists(__DIR__ . '/../' . $oldAttachment)) {
                        @unlink(__DIR__ . '/../' . $oldAttachment);
                    }
                }
            }
        }

        $report = new Report(
            intval($_POST['id']),
            htmlspecialchars(trim($_POST['type'])),
            htmlspecialchars(trim($_POST['title'])),
            htmlspecialchars(trim($_POST['description'])),
            $location,
            $incidentDate,
            $_POST['priority'] ?? 'medium',
            $_POST['status'] ?? 'pending',
            $mediatorId,
            $attachmentPath
        );
        
        $controller->updateReport($report);
        header("Location: ../View/backoffice/reports.php");
        exit;
    }
    
    elseif ($action === 'delete') {
        if (!empty($_POST['id'])) {
            $id = intval($_POST['id']);
            $report = $controller->getReportById($id);
            if ($report && !empty($report['attachment_path'])) {
                $filePath = __DIR__ . '/../' . $report['attachment_path'];
                if (file_exists($filePath)) @unlink($filePath);
            }
            $controller->deleteReport($id);
        }
        header("Location: ../View/backoffice/reports.php");
        exit;
    }
}
?>
