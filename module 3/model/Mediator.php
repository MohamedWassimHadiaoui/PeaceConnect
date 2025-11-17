<?php
class Mediator {
    private ?int $id;
    private ?string $name;
    private ?string $email;
    private ?string $phone;
    private ?string $expertise;
    private ?string $availability;

    public function __construct(?int $id = null, ?string $name = null, ?string $email = null, ?string $phone = null, ?string $expertise = null, ?string $availability = null) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->expertise = $expertise;
        $this->availability = $availability;
    }

    public function getId(): ?int { return $this->id; }
    public function getname(): ?string { return $this->name; }
    public function getemail(): ?string { return $this->email; }
    public function getphone(): ?string { return $this->phone; }
    public function getexpertise(): ?string { return $this->expertise; }
    public function getavailability(): ?string { return $this->availability; }

    public function setId(?int $id): void { $this->id = $id; }
    public function setname(?string $name): void { $this->name = $name; }
    public function setemail(?string $email): void { $this->email = $email; }
    public function setphone(?string $phone): void { $this->phone = $phone; }
    public function setexpertise(?string $expertise): void { $this->expertise = $expertise; }
    public function setavailability(?string $availability): void { $this->availability = $availability; }
}
?>

