<?php
class Report {
    private ?int $id;
    private ?string $type;
    private ?string $title;
    private ?string $description;
    private ?string $location;
    private ?string $incident_date;
    private ?string $priority;
    private ?string $status;

    public function __construct(?int $id = null, ?string $type = null, ?string $title = null, ?string $description = null, ?string $location = null, ?string $incident_date = null, ?string $priority = null, ?string $status = null) {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->description = $description;
        $this->location = $location;
        $this->incident_date = $incident_date;
        $this->priority = $priority;
        $this->status = $status;
    }

    public function getId(): ?int { return $this->id; }
    public function gettype(): ?string { return $this->type; }
    public function gettitle(): ?string { return $this->title; }
    public function getdescription(): ?string { return $this->description; }
    public function getlocation(): ?string { return $this->location; }
    public function getincident_date(): ?string { return $this->incident_date; }
    public function getpriority(): ?string { return $this->priority; }
    public function getstatus(): ?string { return $this->status; }

    public function setId(?int $id): void { $this->id = $id; }
    public function settype(?string $type): void { $this->type = $type; }
    public function settitle(?string $title): void { $this->title = $title; }
    public function setdescription(?string $description): void { $this->description = $description; }
    public function setlocation(?string $location): void { $this->location = $location; }
    public function setincident_date(?string $incident_date): void { $this->incident_date = $incident_date; }
    public function setpriority(?string $priority): void { $this->priority = $priority; }
    public function setstatus(?string $status): void { $this->status = $status; }
}
?>

