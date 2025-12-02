<?php
/**
 * help_request_logic.php (Model)
 * Fonctions utilitaires pour gérer les CRUD de la table help_requests.
 * Réutilisable par n'importe quel controller PHP.
 */

// Utilise la connexion centralisée de Model/db.php
require_once __DIR__ . '/db.php';

/**
 * Retourne une instance PDO initialisée (singleton basique).
 */
// getDb() defined in Model/db.php will be used for DB access; migrations are handled separately

/**
 * Récupère toutes les demandes classées par date de création décroissante.
 */
function hr_get_all()
{
    $pdo = getDb();
    $stmt = $pdo->query('SELECT * FROM help_requests ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

/**
 * Récupère une seule demande.
 */
function hr_get($id)
{
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT * FROM help_requests WHERE id = ?');
    $stmt->execute([(int)$id]);
    return $stmt->fetch();
}

/**
 * Ajoute une demande et retourne l'identifiant créé.
 */
function hr_create(array $data)
{
    $pdo = getDb();
    $payload = hr_prepare_payload($data);

    $stmt = $pdo->prepare('INSERT INTO help_requests (help_type, urgency_level, situation, location, contact_method, status, responsable) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $payload['help_type'],
        $payload['urgency_level'],
        $payload['situation'],
        $payload['location'],
        $payload['contact_method'],
        $payload['status'],
        $payload['responsable'],
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Met à jour une demande.
 */
function hr_update($id, array $data)
{
    $pdo = getDb();
    $payload = hr_prepare_payload($data);

    $stmt = $pdo->prepare('UPDATE help_requests SET help_type = ?, urgency_level = ?, situation = ?, location = ?, contact_method = ?, status = ?, responsable = ? WHERE id = ?');
    return $stmt->execute([
        $payload['help_type'],
        $payload['urgency_level'],
        $payload['situation'],
        $payload['location'],
        $payload['contact_method'],
        $payload['status'],
        $payload['responsable'],
        (int)$id,
    ]);
}

/**
 * Supprime une demande.
 */
function hr_delete($id)
{
    $pdo = getDb();
    $stmt = $pdo->prepare('DELETE FROM help_requests WHERE id = ?');
    return $stmt->execute([(int)$id]);
}

/**
 * Nettoie les données POST brutes.
 */
function hr_get_post_data()
{
    $fields = ['help_type', 'urgency_level', 'situation', 'location', 'contact_method', 'status', 'responsable'];
    $data = [];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = trim($_POST[$field]);
        }
    }
    return $data;
}

/**
 * Valide & tronque les données avant insertion/update.
 */
function hr_prepare_payload(array $data)
{
    $required = ['help_type', 'urgency_level', 'situation'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new InvalidArgumentException('Le champ obligatoire "' . $field . '" est manquant.');
        }
    }

    $payload = [
        'help_type' => substr($data['help_type'], 0, 100),
        'urgency_level' => substr($data['urgency_level'], 0, 50),
        'situation' => $data['situation'],
        'location' => isset($data['location']) && $data['location'] !== '' ? substr($data['location'], 0, 100) : null,
        'contact_method' => isset($data['contact_method']) && $data['contact_method'] !== '' ? substr($data['contact_method'], 0, 100) : null,
        'status' => isset($data['status']) && $data['status'] !== '' ? substr($data['status'], 0, 50) : 'en_attente',
        'responsable' => isset($data['responsable']) && $data['responsable'] !== '' ? substr($data['responsable'], 0, 100) : null,
    ];

    return $payload;
}

?>