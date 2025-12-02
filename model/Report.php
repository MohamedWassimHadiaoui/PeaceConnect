<?php
class Report {
    private $id;
    private $type;
    private $title;
    private $description;
    private $location;
    private $incident_date;
    private $priority;
    private $status;
    private $mediator_id;
    private $attachment_path;

    public function __construct($id = null, $type = null, $title = null, $description = null, $location = null, $incident_date = null, $priority = null, $status = null, $mediator_id = null, $attachment_path = null) {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->description = $description;
        $this->location = $location;
        $this->incident_date = $incident_date;
        $this->priority = $priority;
        $this->status = $status;
        $this->mediator_id = $mediator_id;
        $this->attachment_path = $attachment_path;
    }

    public function getId() { return $this->id; }
    public function getType() { return $this->type; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getLocation() { return $this->location; }
    public function getIncidentDate() { return $this->incident_date; }
    public function getPriority() { return $this->priority; }
    public function getStatus() { return $this->status; }
    public function getMediatorId() { return $this->mediator_id; }
    public function getAttachmentPath() { return $this->attachment_path; }

    public function setId($id) { $this->id = $id; }
    public function setType($type) { $this->type = $type; }
    public function setTitle($title) { $this->title = $title; }
    public function setDescription($description) { $this->description = $description; }
    public function setLocation($location) { $this->location = $location; }
    public function setIncidentDate($incident_date) { $this->incident_date = $incident_date; }
    public function setPriority($priority) { $this->priority = $priority; }
    public function setStatus($status) { $this->status = $status; }
    public function setMediatorId($mediator_id) { $this->mediator_id = $mediator_id; }
    public function setAttachmentPath($attachment_path) { $this->attachment_path = $attachment_path; }
}
?>
