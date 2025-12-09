<?php

class Config {
    private static $instance = null;
    private $pdo;
    
    // Paramètres de connexion
    private $host = 'localhost';
    private $dbname = 'forumm';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8';
    
    /**
     * Constructeur privé pour empêcher l'instanciation directe
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            );
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère l'instance unique de la classe
     * @return Config
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Récupère l'objet PDO
     * @return PDO
     */
    public function getPDO() {
        return $this->pdo;
    }
    
    /**
     * Empêche le clonage de l'instance
     */
    private function __clone() {}
    
    /**
     * Empêche la désérialisation de l'instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>



