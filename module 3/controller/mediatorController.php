<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Mediator.php';

class mediatorController {
    private $db;
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    public function listMediators() {
        $stmt = $this->db->query("SELECT * FROM mediators ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    public function addMediator(Mediator $m) {
        $sql = "INSERT INTO mediators (name, email, phone, expertise, availability) VALUES (:name, :email, :phone, :expertise, :availability)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":name" => $m->getname(), ":email" => $m->getemail(), ":phone" => $m->getphone(), ":expertise" => $m->getexpertise(), ":availability" => $m->getavailability()]);
    }
    
    public function deleteMediator($id) {
        $stmt = $this->db->prepare("DELETE FROM mediators WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }
    
    public function updateMediator(Mediator $m) {
        $sql = "UPDATE mediators SET name=:name, email=:email, phone=:phone, expertise=:expertise, availability=:availability WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":id" => $m->getId(), ":name" => $m->getname(), ":email" => $m->getemail(), ":phone" => $m->getphone(), ":expertise" => $m->getexpertise(), ":availability" => $m->getavailability()]);
    }
    
    public function getMediatorById($id) {
        $stmt = $this->db->prepare("SELECT * FROM mediators WHERE id=:id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetch();
    }
}
?>

