<?php
class MediationSession {
    private $id;
    private $report_id;
    private $mediator_id;
    private $session_date;
    private $session_time;
    private $session_type;
    private $location;
    private $meeting_link;
    private $status;
    private $notes;

    public function __construct($id = null, $report_id = null, $mediator_id = null, $session_date = null, $session_time = null, $session_type = 'in_person', $location = null, $meeting_link = null, $status = 'scheduled', $notes = null) {
        $this->id = $id;
        $this->report_id = $report_id;
        $this->mediator_id = $mediator_id;
        $this->session_date = $session_date;
        $this->session_time = $session_time;
        $this->session_type = $session_type;
        $this->location = $location;
        $this->meeting_link = $meeting_link;
        $this->status = $status;
        $this->notes = $notes;
    }

    public function getId() { return $this->id; }
    public function getReportId() { return $this->report_id; }
    public function getMediatorId() { return $this->mediator_id; }
    public function getSessionDate() { return $this->session_date; }
    public function getSessionTime() { return $this->session_time; }
    public function getSessionType() { return $this->session_type; }
    public function getLocation() { return $this->location; }
    public function getMeetingLink() { return $this->meeting_link; }
    public function getStatus() { return $this->status; }
    public function getNotes() { return $this->notes; }

    public function setId($id) { $this->id = $id; }
    public function setReportId($report_id) { $this->report_id = $report_id; }
    public function setMediatorId($mediator_id) { $this->mediator_id = $mediator_id; }
    public function setSessionDate($session_date) { $this->session_date = $session_date; }
    public function setSessionTime($session_time) { $this->session_time = $session_time; }
    public function setSessionType($session_type) { $this->session_type = $session_type; }
    public function setLocation($location) { $this->location = $location; }
    public function setMeetingLink($meeting_link) { $this->meeting_link = $meeting_link; }
    public function setStatus($status) { $this->status = $status; }
    public function setNotes($notes) { $this->notes = $notes; }
}
?>
