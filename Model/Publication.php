<?php
class Publication {
    private $id;
    private $user_id;
    private $titre;
    private $contenu;
    private $categorie;
    private $tags;
    private $auteur;
    private $nombre_likes;
    private $nombre_commentaires;
    private $statut;
    private $created_at;
    private $updated_at;

    public function __construct($id = null, $user_id = null, $titre = null, $contenu = null, $categorie = null, $tags = null, $auteur = null, $statut = 'pending') {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->categorie = $categorie;
        $this->tags = $tags;
        $this->auteur = $auteur;
        $this->statut = $statut;
        $this->nombre_likes = 0;
        $this->nombre_commentaires = 0;
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getTitre() { return $this->titre; }
    public function getContenu() { return $this->contenu; }
    public function getCategorie() { return $this->categorie; }
    public function getTags() { return $this->tags; }
    public function getAuteur() { return $this->auteur; }
    public function getNombreLikes() { return $this->nombre_likes; }
    public function getNombreCommentaires() { return $this->nombre_commentaires; }
    public function getStatut() { return $this->statut; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    public function setId($id) { $this->id = $id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setContenu($contenu) { $this->contenu = $contenu; }
    public function setCategorie($categorie) { $this->categorie = $categorie; }
    public function setTags($tags) { $this->tags = $tags; }
    public function setAuteur($auteur) { $this->auteur = $auteur; }
    public function setNombreLikes($nombre_likes) { $this->nombre_likes = $nombre_likes; }
    public function setNombreCommentaires($nombre_commentaires) { $this->nombre_commentaires = $nombre_commentaires; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    public function setUpdatedAt($updated_at) { $this->updated_at = $updated_at; }

    public function getTagsArray() {
        if (empty($this->tags)) return [];
        return array_map('trim', explode(',', $this->tags));
    }

    public function getStatutBadge() {
        $badges = [
            'pending' => 'badge-pending',
            'approved' => 'badge-resolved',
            'rejected' => 'badge-high'
        ];
        return $badges[$this->statut] ?? 'badge-pending';
    }
}
?>

