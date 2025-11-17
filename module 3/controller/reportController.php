<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Report.php';

class reportController {
    private $db;
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    public function listReports() {
        $stmt = $this->db->query("SELECT * FROM reports ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    public function addReport(Report $r) {
        $sql = "INSERT INTO reports (type, title, description, location, incident_date, priority, status) VALUES (:type, :title, :description, :location, :incident_date, :priority, :status)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":type" => $r->gettype(), ":title" => $r->gettitle(), ":description" => $r->getdescription(), ":location" => $r->getlocation(), ":incident_date" => $r->getincident_date(), ":priority" => $r->getpriority(), ":status" => $r->getstatus()]);
    }
    
    public function deleteReport($id) {
        $stmt = $this->db->prepare("DELETE FROM reports WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }
    
    public function updateReport(Report $r) {
        $sql = "UPDATE reports SET type=:type, title=:title, description=:description, location=:location, incident_date=:incident_date, priority=:priority, status=:status WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":id" => $r->getId(), ":type" => $r->gettype(), ":title" => $r->gettitle(), ":description" => $r->getdescription(), ":location" => $r->getlocation(), ":incident_date" => $r->getincident_date(), ":priority" => $r->getpriority(), ":status" => $r->getstatus()]);
    }
    
    public function getReportById($id) {
        $stmt = $this->db->prepare("SELECT * FROM reports WHERE id=:id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetch();
    }
}
?>

