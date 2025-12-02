<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../model/MediationSession.php";

class SessionController {
    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    public function addSession($session) {
        $sql = "INSERT INTO mediation_sessions 
                (report_id, mediator_id, session_date, session_time, session_type, location, meeting_link, status, notes) 
                VALUES (:report_id, :mediator_id, :session_date, :session_time, :session_type, :location, :meeting_link, :status, :notes)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':report_id' => $session->getReportId(),
            ':mediator_id' => $session->getMediatorId(),
            ':session_date' => $session->getSessionDate(),
            ':session_time' => $session->getSessionTime(),
            ':session_type' => $session->getSessionType(),
            ':location' => $session->getLocation(),
            ':meeting_link' => $session->getMeetingLink(),
            ':status' => $session->getStatus(),
            ':notes' => $session->getNotes()
        ]);
        $this->updateReportStatus($session->getReportId(), 'in_mediation');
    }

    public function listSessions() {
        $sql = "SELECT * FROM mediation_sessions ORDER BY session_date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function listSessionsWithDetails() {
        $sql = "SELECT s.*, 
                       r.title as report_title, r.type as report_type,
                       m.name as mediator_name, m.expertise as mediator_expertise
                FROM mediation_sessions s
                LEFT JOIN reports r ON s.report_id = r.id
                LEFT JOIN mediators m ON s.mediator_id = m.id
                ORDER BY s.session_date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getSessionById($id) {
        $sql = "SELECT * FROM mediation_sessions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateSession($session) {
        $sql = "UPDATE mediation_sessions 
                SET report_id = :report_id, mediator_id = :mediator_id, 
                    session_date = :session_date, session_time = :session_time,
                    session_type = :session_type, location = :location, 
                    meeting_link = :meeting_link, status = :status, notes = :notes 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $session->getId(),
            ':report_id' => $session->getReportId(),
            ':mediator_id' => $session->getMediatorId(),
            ':session_date' => $session->getSessionDate(),
            ':session_time' => $session->getSessionTime(),
            ':session_type' => $session->getSessionType(),
            ':location' => $session->getLocation(),
            ':meeting_link' => $session->getMeetingLink(),
            ':status' => $session->getStatus(),
            ':notes' => $session->getNotes()
        ]);
        if ($session->getStatus() === 'completed') {
            $this->updateReportStatus($session->getReportId(), 'resolved');
        }
    }

    public function deleteSession($id) {
        $sql = "DELETE FROM mediation_sessions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    private function updateReportStatus($reportId, $status) {
        $sql = "UPDATE reports SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status, ':id' => $reportId]);
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM mediation_sessions";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result ? $result['total'] : 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $controller = new SessionController();
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'add') {
        $errors = [];
        
        if (empty($_POST['report_id'])) {
            $errors[] = "Veuillez sélectionner un signalement";
        }
        if (empty($_POST['mediator_id'])) {
            $errors[] = "Veuillez sélectionner un médiateur";
        }
        if (empty($_POST['session_date'])) {
            $errors[] = "La date est obligatoire";
        }
        if (empty($_POST['session_time'])) {
            $errors[] = "L'heure est obligatoire";
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ../views/session_form.php");
            exit;
        }
        
        $location = '';
        if (!empty($_POST['location'])) {
            $location = htmlspecialchars(trim($_POST['location']));
        }
        $meetingLink = '';
        if (!empty($_POST['meeting_link'])) {
            $meetingLink = htmlspecialchars(trim($_POST['meeting_link']));
        }
        $notes = '';
        if (!empty($_POST['notes'])) {
            $notes = htmlspecialchars(trim($_POST['notes']));
        }
        
        $session = new MediationSession(
            null,
            intval($_POST['report_id']),
            intval($_POST['mediator_id']),
            trim($_POST['session_date']),
            trim($_POST['session_time']),
            $_POST['session_type'] ?? 'in_person',
            $location,
            $meetingLink,
            'scheduled',
            $notes
        );
        
        $controller->addSession($session);
        header("Location: ../views/sessions.php");
        exit;
    }
    
    elseif ($action === 'update') {
        if (empty($_POST['id'])) {
            header("Location: ../views/sessions.php");
            exit;
        }
        
        $errors = [];
        if (empty($_POST['report_id'])) {
            $errors[] = "Veuillez sélectionner un signalement";
        }
        if (empty($_POST['mediator_id'])) {
            $errors[] = "Veuillez sélectionner un médiateur";
        }
        if (empty($_POST['session_date'])) {
            $errors[] = "La date est obligatoire";
        }
        if (empty($_POST['session_time'])) {
            $errors[] = "L'heure est obligatoire";
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ../views/session_form.php?id=" . $_POST['id']);
            exit;
        }
        
        $location = '';
        if (!empty($_POST['location'])) {
            $location = htmlspecialchars(trim($_POST['location']));
        }
        $meetingLink = '';
        if (!empty($_POST['meeting_link'])) {
            $meetingLink = htmlspecialchars(trim($_POST['meeting_link']));
        }
        $notes = '';
        if (!empty($_POST['notes'])) {
            $notes = htmlspecialchars(trim($_POST['notes']));
        }
        
        $session = new MediationSession(
            intval($_POST['id']),
            intval($_POST['report_id']),
            intval($_POST['mediator_id']),
            trim($_POST['session_date']),
            trim($_POST['session_time']),
            $_POST['session_type'] ?? 'in_person',
            $location,
            $meetingLink,
            $_POST['status'] ?? 'scheduled',
            $notes
        );
        
        $controller->updateSession($session);
        header("Location: ../views/sessions.php");
        exit;
    }
    
    elseif ($action === 'delete') {
        if (!empty($_POST['id'])) {
            $controller->deleteSession(intval($_POST['id']));
        }
        header("Location: ../views/sessions.php");
        exit;
    }
}
?>
