<?php
class User {
    private $id;
    private $name;
    private $lastname;
    private $email;
    private $password;
    private $cin;
    private $tel;
    private $gender;
    private $role;
    private $avatar;
    private $two_factor_secret;
    private $two_factor_enabled;

    public function __construct($id = null, $name = null, $lastname = null, $email = null, $password = null, $cin = null, $tel = null, $gender = null, $role = 'client', $avatar = null) {
        $this->id = $id;
        $this->name = $name;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->cin = $cin;
        $this->tel = $tel;
        $this->gender = $gender;
        $this->role = $role;
        $this->avatar = $avatar;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getLastname() { return $this->lastname; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getCin() { return $this->cin; }
    public function getTel() { return $this->tel; }
    public function getGender() { return $this->gender; }
    public function getRole() { return $this->role; }
    public function getAvatar() { return $this->avatar; }
    public function getTwoFactorSecret() { return $this->two_factor_secret; }
    public function getTwoFactorEnabled() { return $this->two_factor_enabled; }

    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setLastname($lastname) { $this->lastname = $lastname; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setCin($cin) { $this->cin = $cin; }
    public function setTel($tel) { $this->tel = $tel; }
    public function setGender($gender) { $this->gender = $gender; }
    public function setRole($role) { $this->role = $role; }
    public function setAvatar($avatar) { $this->avatar = $avatar; }
    public function setTwoFactorSecret($secret) { $this->two_factor_secret = $secret; }
    public function setTwoFactorEnabled($enabled) { $this->two_factor_enabled = $enabled; }

    public function getFullName() {
        return $this->name . ' ' . $this->lastname;
    }
}
?>

