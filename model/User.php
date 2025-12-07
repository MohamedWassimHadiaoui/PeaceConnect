<?php
require_once __DIR__ . '/../config.php';
class User {

    private $id_user;
    private $name;
    private $lastname;
    private $email;
    private $password;
    private $cin;
    private $tel;
    private $gender;
    private $role;
    private $avatar;

    public function __construct($id_user, $name, $lastname, $email, $password, $cin, $tel, $gender, $role, $avatar = null) {
        $this->id_user = $id_user;
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
    public function getId() {
        return $this->id_user;
    }

    public function getName() {
        return $this->name;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getCin() {
        return $this->cin;
    }

    public function getTel() {
        return $this->tel;
    }

    public function getGender() {
        return $this->gender;
    }

    public function getRole() {
        return $this->role;
    }

    public function setId($id_user) {
        $this->id_user = $id_user;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setCin($cin) {
        $this->cin = $cin;
    }

    public function setTel($tel) {
        $this->tel = $tel;
    }

    public function setGender($gender) {
        $this->gender = $gender;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function getAvatar() {
        return $this->avatar;
    }

    public function setAvatar($avatar) {
        $this->avatar = $avatar;
    }
}

?>
