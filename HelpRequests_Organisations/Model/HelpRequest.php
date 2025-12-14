<?php
class HelpRequest {
    private $id;
    private $user_id;
    private $help_type;
    private $urgency_level;
    private $situation;
    private $location;
    private $contact_method;
    private $status;
    private $responsable;
    private $created_at;

    public function __construct($id = null, $user_id = null, $help_type = null, $urgency_level = 'medium', $situation = null, $location = null, $contact_method = null, $status = 'pending', $responsable = null) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->help_type = $help_type;
        $this->urgency_level = $urgency_level;
        $this->situation = $situation;
        $this->location = $location;
        $this->contact_method = $contact_method;
        $this->status = $status;
        $this->responsable = $responsable;
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getHelpType() { return $this->help_type; }
    public function getUrgencyLevel() { return $this->urgency_level; }
    public function getSituation() { return $this->situation; }
    public function getLocation() { return $this->location; }
    public function getContactMethod() { return $this->contact_method; }
    public function getStatus() { return $this->status; }
    public function getResponsable() { return $this->responsable; }
    public function getCreatedAt() { return $this->created_at; }

    public function setId($id) { $this->id = $id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setHelpType($help_type) { $this->help_type = $help_type; }
    public function setUrgencyLevel($urgency_level) { $this->urgency_level = $urgency_level; }
    public function setSituation($situation) { $this->situation = $situation; }
    public function setLocation($location) { $this->location = $location; }
    public function setContactMethod($contact_method) { $this->contact_method = $contact_method; }
    public function setStatus($status) { $this->status = $status; }
    public function setResponsable($responsable) { $this->responsable = $responsable; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

    public function getUrgencyBadge() {
        $badges = [
            'low' => 'badge-low',
            'medium' => 'badge-medium',
            'high' => 'badge-high',
            'critical' => 'badge-critical'
        ];
        return $badges[$this->urgency_level] ?? 'badge-medium';
    }

    public function getStatusBadge() {
        $badges = [
            'pending' => 'badge-pending',
            'in_progress' => 'badge-assigned',
            'resolved' => 'badge-resolved',
            'closed' => 'badge-closed'
        ];
        return $badges[$this->status] ?? 'badge-pending';
    }
}
?>

