<?php
/**
 * Model/organisation_logic.php
 * Fonctions CRUD pour gérer les organisations
 */

require_once __DIR__ . '/db.php';

/**
 * Récupère toutes les organisations classées par date décroissante
 */
function org_get_all()
{
    $pdo = getDb();
    $stmt = $pdo->query('SELECT * FROM organisations ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

/**
 * Récupère les organisations filtrées par statut
 */
function org_get_by_status($status)
{
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM organisations WHERE status = ? ORDER BY created_at DESC');
    $stmt->execute([$status]);
    return $stmt->fetchAll();
}

/**
 * Récupère une organisation par ID
 */
function org_get($id)
{
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM organisations WHERE id = ?');
    $stmt->execute([(int)$id]);
    return $stmt->fetch();
}

/**
 * Crée une nouvelle organisation et retourne l'ID
 */
function org_create(array $data)
{
    $pdo = getDb();
    $payload = org_prepare_payload($data);

    $stmt = $pdo->prepare('
        INSERT INTO organisations (name, acronym, description, category, email, phone, website, address, city, postal_code, country, logo_path, status, mission, vision, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    
    $stmt->execute([
        $payload['name'],
        $payload['acronym'],
        $payload['description'],
        $payload['category'],
        $payload['email'],
        $payload['phone'],
        $payload['website'],
        $payload['address'],
        $payload['city'],
        $payload['postal_code'],
        $payload['country'],
        $payload['logo_path'],
        $payload['status'],
        $payload['mission'],
        $payload['vision'],
        $payload['created_by'],
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Met à jour une organisation
 */
function org_update($id, array $data)
{
    $pdo = getDb();
    $payload = org_prepare_payload($data);

    $stmt = $pdo->prepare('
        UPDATE organisations 
        SET name = ?, acronym = ?, description = ?, category = ?, email = ?, phone = ?, website = ?, 
            address = ?, city = ?, postal_code = ?, country = ?, logo_path = ?, status = ?, mission = ?, vision = ?, updated_by = ?
        WHERE id = ?
    ');
    
    return $stmt->execute([
        $payload['name'],
        $payload['acronym'],
        $payload['description'],
        $payload['category'],
        $payload['email'],
        $payload['phone'],
        $payload['website'],
        $payload['address'],
        $payload['city'],
        $payload['postal_code'],
        $payload['country'],
        $payload['logo_path'],
        $payload['status'],
        $payload['mission'],
        $payload['vision'],
        $payload['updated_by'],
        (int)$id,
    ]);
}

/**
 * Supprime une organisation
 */
function org_delete($id)
{
    $pdo = getDb();
    $stmt = $pdo->prepare('DELETE FROM organisations WHERE id = ?');
    return $stmt->execute([(int)$id]);
}

/**
 * Recherche les organisations par critères
 */
function org_search(array $criteria)
{
    $pdo = getDb();
    $sql = 'SELECT * FROM organisations WHERE 1=1';
    $params = [];

    if (!empty($criteria['name'])) {
        $sql .= ' AND name LIKE ?';
        $params[] = '%' . $criteria['name'] . '%';
    }
    if (!empty($criteria['category'])) {
        $sql .= ' AND category = ?';
        $params[] = $criteria['category'];
    }
    if (!empty($criteria['status'])) {
        $sql .= ' AND status = ?';
        $params[] = $criteria['status'];
    }
    if (!empty($criteria['city'])) {
        $sql .= ' AND city LIKE ?';
        $params[] = '%' . $criteria['city'] . '%';
    }

    $sql .= ' ORDER BY created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Nettoie les données POST brutes
 */
function org_get_post_data()
{
    $fields = ['name', 'acronym', 'description', 'category', 'email', 'phone', 'website', 'address', 'city', 'postal_code', 'country', 'logo_path', 'status', 'mission', 'vision', 'created_by', 'updated_by'];
    $data = [];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = trim($_POST[$field]);
        }
    }
    return $data;
}

/**
 * Valide et tronque les données avant insertion/update
 */
function org_prepare_payload(array $data)
{
    // Champs obligatoires
    if (empty($data['name'])) {
        throw new InvalidArgumentException('Le nom de l\'organisation est obligatoire.');
    }

    $payload = [
        'name' => substr($data['name'], 0, 255),
        'acronym' => isset($data['acronym']) && $data['acronym'] !== '' ? substr($data['acronym'], 0, 50) : null,
        'description' => isset($data['description']) && $data['description'] !== '' ? $data['description'] : null,
        'category' => isset($data['category']) && $data['category'] !== '' ? substr($data['category'], 0, 100) : null,
        'email' => isset($data['email']) && $data['email'] !== '' && filter_var($data['email'], FILTER_VALIDATE_EMAIL) ? substr($data['email'], 0, 255) : null,
        'phone' => isset($data['phone']) && $data['phone'] !== '' ? substr($data['phone'], 0, 20) : null,
        'website' => isset($data['website']) && $data['website'] !== '' ? substr($data['website'], 0, 255) : null,
        'address' => isset($data['address']) && $data['address'] !== '' ? substr($data['address'], 0, 255) : null,
        'city' => isset($data['city']) && $data['city'] !== '' ? substr($data['city'], 0, 100) : null,
        'postal_code' => isset($data['postal_code']) && $data['postal_code'] !== '' ? substr($data['postal_code'], 0, 10) : null,
        'country' => isset($data['country']) && $data['country'] !== '' ? substr($data['country'], 0, 100) : null,
        'logo_path' => isset($data['logo_path']) && $data['logo_path'] !== '' ? substr($data['logo_path'], 0, 255) : null,
        'status' => isset($data['status']) && $data['status'] !== '' ? substr($data['status'], 0, 50) : 'active',
        'mission' => isset($data['mission']) && $data['mission'] !== '' ? $data['mission'] : null,
        'vision' => isset($data['vision']) && $data['vision'] !== '' ? $data['vision'] : null,
        'created_by' => isset($data['created_by']) && $data['created_by'] !== '' ? substr($data['created_by'], 0, 100) : null,
        'updated_by' => isset($data['updated_by']) && $data['updated_by'] !== '' ? substr($data['updated_by'], 0, 100) : null,
    ];

    return $payload;
}

?>
