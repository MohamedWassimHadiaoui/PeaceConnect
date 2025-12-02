<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../model/Report.php";

class ReportController {
    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    public function addReport($report) {
        $sql = "INSERT INTO reports (type, title, description, location, incident_date, priority, status, mediator_id, attachment_path) 
                VALUES (:type, :title, :description, :location, :incident_date, :priority, :status, :mediator_id, :attachment_path)";
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
            ':attachment_path' => $report->getAttachmentPath()
        ]);
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
        $result = $stmt->fetch();
        return $result ? $result['total'] : 0;
    }

    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM reports WHERE status = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status]);
        $result = $stmt->fetch();
        return $result ? $result['total'] : 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $controller = new ReportController();
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'add') {
        $errors = [];
        
        if (empty($_POST['type'])) {
            $errors[] = "Le type d'incident est obligatoire";
        }
        if (empty($_POST['title']) || strlen(trim($_POST['title'])) < 5) {
            $errors[] = "Le titre doit contenir au moins 5 caractères";
        }
        if (empty($_POST['description']) || strlen(trim($_POST['description'])) < 10) {
            $errors[] = "La description doit contenir au moins 10 caractères";
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            if (isset($_POST['source']) && $_POST['source'] === 'frontoffice') {
                header("Location: ../views/create_report.php");
            } else {
                header("Location: ../views/report_form.php");
            }
            exit;
        }
        
        $priority = isset($_POST['priority']) ? $_POST['priority'] : 'medium';
        if (isset($_POST['source']) && $_POST['source'] === 'frontoffice') {
            if (in_array($_POST['type'], ['violence', 'harassment', 'discrimination'])) {
                $priority = 'high';
            }
        }
        
        $status = 'pending';
        $mediatorId = null;
        if (isset($_POST['mediator_id']) && !empty($_POST['mediator_id'])) {
            $mediatorId = $_POST['mediator_id'];
            $status = 'assigned';
        }
        
        $incidentDate = null;
        if (!empty($_POST['incident_date'])) {
            $incidentDate = $_POST['incident_date'];
        }
        
        $attachmentPath = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['attachment']['tmp_name'];
            $originalName = basename($_FILES['attachment']['name']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $extensionsAutorisees = ['jpg','jpeg','png','gif','pdf'];
            if (in_array($extension, $extensionsAutorisees)) {
                if (!is_dir(__DIR__ . '/../uploads')) {
                    mkdir(__DIR__ . '/../uploads', 0755, true);
                }
                $nouveauNom = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                $destination = __DIR__ . '/../uploads/' . $nouveauNom;
                if (move_uploaded_file($tmpName, $destination)) {
                    $attachmentPath = 'uploads/' . $nouveauNom;
                }
            }
        }

        $report = new Report(
            null,
            htmlspecialchars(trim($_POST['type'])),
            htmlspecialchars(trim($_POST['title'])),
            htmlspecialchars(trim($_POST['description'])),
            htmlspecialchars(trim($_POST['location'] ?? '')),
            $incidentDate,
            $priority,
            $status,
            $mediatorId,
            $attachmentPath
        );
        
        $controller->addReport($report);
        
        if (isset($_POST['source']) && $_POST['source'] === 'frontoffice') {
            header("Location: ../views/my_reports.php");
        } else {
            header("Location: ../views/reports.php");
        }
        exit;
    }
    
    elseif ($action === 'update') {
        if (empty($_POST['id'])) {
            header("Location: ../views/reports.php");
            exit;
        }
        
        $errors = [];
        if (empty($_POST['type'])) {
            $errors[] = "Le type d'incident est obligatoire";
        }
        if (empty($_POST['title']) || strlen(trim($_POST['title'])) < 5) {
            $errors[] = "Le titre doit contenir au moins 5 caractères";
        }
        if (empty($_POST['description']) || strlen(trim($_POST['description'])) < 10) {
            $errors[] = "La description doit contenir au moins 10 caractères";
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ../views/report_form.php?id=" . $_POST['id']);
            exit;
        }
        
        $incidentDate = null;
        if (!empty($_POST['incident_date'])) {
            $incidentDate = trim($_POST['incident_date']);
        }
        
        $mediatorId = null;
        if (!empty($_POST['mediator_id'])) {
            $mediatorId = intval($_POST['mediator_id']);
        }
        
        $location = '';
        if (!empty($_POST['location'])) {
            $location = htmlspecialchars(trim($_POST['location']));
        }
        
        $attachmentPath = $_POST['existing_attachment'] ?? null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['attachment']['tmp_name'];
            $originalName = basename($_FILES['attachment']['name']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $extensionsAutorisees = ['jpg','jpeg','png','gif','pdf'];
            if (in_array($extension, $extensionsAutorisees)) {
                if (!is_dir(__DIR__ . '/../uploads')) {
                    mkdir(__DIR__ . '/../uploads', 0755, true);
                }
                $nouveauNom = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
                $destination = __DIR__ . '/../uploads/' . $nouveauNom;
                if (move_uploaded_file($tmpName, $destination)) {
                    $attachmentPath = 'uploads/' . $nouveauNom;
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
        header("Location: ../views/reports.php");
        exit;
    }
    
    elseif ($action === 'delete') {
        if (!empty($_POST['id'])) {
            $id = intval($_POST['id']);
            $report = $controller->getReportById($id);
            if ($report && !empty($report['attachment_path'])) {
                $filePath = __DIR__ . '/../' . $report['attachment_path'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $controller->deleteReport($id);
        }
        header("Location: ../views/reports.php");
        exit;
    }
}
?>
