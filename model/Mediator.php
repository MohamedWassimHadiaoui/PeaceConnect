<?php
class Mediator {
    private $id;
    private $name;
    private $email;
    private $phone;
    private $expertise;
    private $availability;

    public function __construct($id = null, $name = null, $email = null, $phone = null, $expertise = null, $availability = null) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->expertise = $expertise;
        $this->availability = $availability;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPhone() { return $this->phone; }
    public function getExpertise() { return $this->expertise; }
    public function getAvailability() { return $this->availability; }

    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setExpertise($expertise) { $this->expertise = $expertise; }
    public function setAvailability($availability) { $this->availability = $availability; }
}
?>
