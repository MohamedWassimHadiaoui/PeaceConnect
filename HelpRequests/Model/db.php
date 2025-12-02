<?php
/**
 * Model/db.php
 * Gestion simple de la BD MySQL sans classes complexes
 */

// Configuration
const DB_HOST = 'localhost';
const DB_USER = 'Projet2A';
const DB_PASS = '123';
const DB_NAME = 'peaceconnect';

/**
 * Retourne une connexion PDO à la BD
 */
function getDb() {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        
        // Créer la BD si elle n'existe pas
        $pdo->exec('CREATE DATABASE IF NOT EXISTS ' . DB_NAME . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        
        // Reconnecter à la BD
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        
        // Ensure required tables exist (create if missing).
        // This keeps the app usable when the DB hasn't been initialized yet.
        $pdo->exec("CREATE TABLE IF NOT EXISTS help_requests (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            help_type VARCHAR(100) NOT NULL,
            urgency_level VARCHAR(50) DEFAULT NULL,
            situation TEXT,
            location VARCHAR(255) DEFAULT NULL,
            contact_method VARCHAR(100) DEFAULT NULL,
            responsable VARCHAR(100) DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'en_attente',
            attachments TEXT DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_status (status),
            KEY idx_help_type (help_type),
            KEY idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS organisations (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE,
            acronym VARCHAR(50) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            category VARCHAR(100) DEFAULT NULL,
            email VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            website VARCHAR(255) DEFAULT NULL,
            address VARCHAR(255) DEFAULT NULL,
            city VARCHAR(100) DEFAULT NULL,
            postal_code VARCHAR(10) DEFAULT NULL,
            country VARCHAR(100) DEFAULT NULL,
            logo_path VARCHAR(255) DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'active',
            mission TEXT DEFAULT NULL,
            vision TEXT DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            created_by VARCHAR(100) DEFAULT NULL,
            updated_by VARCHAR(100) DEFAULT NULL,
            KEY idx_name (name),
            KEY idx_category (category),
            KEY idx_status (status),
            KEY idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        return $pdo;
    } catch (PDOException $e) {
        die('Erreur BD: ' . $e->getMessage());
    }
}

/**
 * Récupère toutes les demandes
 */
function getAllRequests() {
    $db = getDb();
    $stmt = $db->query('SELECT * FROM help_requests ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

/**
 * Récupère une demande par ID
 */
function getRequestById($id) {
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM help_requests WHERE id = ?');
    $stmt->execute([(int)$id]);
    return $stmt->fetch();
}

/**
 * Crée une nouvelle demande
 */
function createRequest($data) {
    if (empty($data['help_type']) || empty($data['urgency_level']) || empty($data['situation'])) {
        throw new Exception('Champs obligatoires manquants: help_type, urgency_level, situation');
    }
    
    $db = getDb();
    $stmt = $db->prepare('
        INSERT INTO help_requests (help_type, urgency_level, situation, location, contact_method, status, responsable)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    
    $stmt->execute([
        substr($data['help_type'], 0, 100),
        substr($data['urgency_level'], 0, 50),
        $data['situation'],
        $data['location'] ?? null,
        $data['contact_method'] ?? null,
        $data['status'] ?? 'en_attente',
        $data['responsable'] ?? null,
    ]);
    
    return $db->lastInsertId();
}

/**
 * Met à jour une demande
 */
function updateRequest($id, $data) {
    if (empty($data['help_type']) || empty($data['urgency_level']) || empty($data['situation'])) {
        throw new Exception('Champs obligatoires manquants: help_type, urgency_level, situation');
    }
    
    $db = getDb();
    $stmt = $db->prepare('
        UPDATE help_requests 
        SET help_type = ?, urgency_level = ?, situation = ?, location = ?, contact_method = ?, status = ?, responsable = ?
        WHERE id = ?
    ');
    
    $stmt->execute([
        substr($data['help_type'], 0, 100),
        substr($data['urgency_level'], 0, 50),
        $data['situation'],
        $data['location'] ?? null,
        $data['contact_method'] ?? null,
        $data['status'] ?? 'en_attente',
        $data['responsable'] ?? null,
        (int)$id,
    ]);
    
    return $stmt->rowCount() > 0;
}

/**
 * Supprime une demande
 */
function deleteRequest($id) {
    $db = getDb();
    $stmt = $db->prepare('DELETE FROM help_requests WHERE id = ?');
    return $stmt->execute([(int)$id]);
}

/**
 * Recherche des demandes
 */
function searchRequests($criteria) {
    $db = getDb();
    $sql = 'SELECT * FROM help_requests WHERE 1=1';
    $params = [];
    
    if (!empty($criteria['help_type'])) {
        $sql .= ' AND help_type LIKE ?';
        $params[] = '%' . $criteria['help_type'] . '%';
    }
    if (!empty($criteria['status'])) {
        $sql .= ' AND status = ?';
        $params[] = $criteria['status'];
    }
    if (!empty($criteria['urgency_level'])) {
        $sql .= ' AND urgency_level = ?';
        $params[] = $criteria['urgency_level'];
    }
    
    $sql .= ' ORDER BY created_at DESC';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?>
