<?php
class Organisation {
    private $id;
    private $name;
    private $acronym;
    private $description;
    private $category;
    private $email;
    private $phone;
    private $website;
    private $address;
    private $city;
    private $country;
    private $logoPath;
    private $status;

    public function __construct($id = null, $name = '', $acronym = null, $description = null, $category = null, $email = null, $phone = null, $website = null, $address = null, $city = null, $country = null, $logoPath = null, $status = 'active') {
        $this->id = $id;
        $this->name = $name;
        $this->acronym = $acronym;
        $this->description = $description;
        $this->category = $category;
        $this->email = $email;
        $this->phone = $phone;
        $this->website = $website;
        $this->address = $address;
        $this->city = $city;
        $this->country = $country;
        $this->logoPath = $logoPath;
        $this->status = $status ?: 'active';
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getAcronym() { return $this->acronym; }
    public function getDescription() { return $this->description; }
    public function getCategory() { return $this->category; }
    public function getEmail() { return $this->email; }
    public function getPhone() { return $this->phone; }
    public function getWebsite() { return $this->website; }
    public function getAddress() { return $this->address; }
    public function getCity() { return $this->city; }
    public function getCountry() { return $this->country; }
    public function getLogoPath() { return $this->logoPath; }
    public function getStatus() { return $this->status; }
}


