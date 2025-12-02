-- =====================================================
-- Base de données complète PeaceConnect avec CRUD
-- Utilisateur: Projet2A / Mot de passe: 123
-- =====================================================

CREATE DATABASE IF NOT EXISTS peaceconnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE peaceconnect;

-- =====================================================
-- Table 1: UTILISATEURS (Users)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    role ENUM('admin', 'moderator', 'volunteer', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    avatar_path VARCHAR(255) DEFAULT NULL,
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED DEFAULT NULL,
    updated_by INT UNSIGNED DEFAULT NULL,
    KEY idx_email (email),
    KEY idx_role (role),
    KEY idx_status (status),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 2: ORGANISATIONS
-- =====================================================
CREATE TABLE IF NOT EXISTS organisations (
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
    country VARCHAR(100) DEFAULT 'Tunisia',
    logo_path VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    mission TEXT DEFAULT NULL,
    vision TEXT DEFAULT NULL,
    responsable_id INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED DEFAULT NULL,
    updated_by INT UNSIGNED DEFAULT NULL,
    KEY idx_name (name),
    KEY idx_category (category),
    KEY idx_status (status),
    KEY idx_responsable (responsable_id),
    FOREIGN KEY (responsable_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 3: DEMANDES D'AIDE (Help Requests)
-- =====================================================
CREATE TABLE IF NOT EXISTS help_requests (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    help_type VARCHAR(100) NOT NULL,
    urgency_level ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    situation TEXT NOT NULL,
    location VARCHAR(255) DEFAULT NULL,
    contact_method VARCHAR(100) DEFAULT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'rejected') DEFAULT 'pending',
    responsable_id INT UNSIGNED DEFAULT NULL,
    organisation_id INT UNSIGNED DEFAULT NULL,
    requester_id INT UNSIGNED DEFAULT NULL,
    priority INT DEFAULT 0,
    estimated_hours INT DEFAULT NULL,
    attachments TEXT DEFAULT NULL COMMENT 'JSON array of file paths',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    completed_at DATETIME DEFAULT NULL,
    created_by INT UNSIGNED DEFAULT NULL,
    updated_by INT UNSIGNED DEFAULT NULL,
    KEY idx_status (status),
    KEY idx_help_type (help_type),
    KEY idx_urgency (urgency_level),
    KEY idx_responsable (responsable_id),
    KEY idx_organisation (organisation_id),
    KEY idx_requester (requester_id),
    KEY idx_created_at (created_at),
    FOREIGN KEY (responsable_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (organisation_id) REFERENCES organisations(id) ON DELETE SET NULL,
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 4: ACTIVITÉS & AUDIT LOG
-- =====================================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id INT UNSIGNED DEFAULT NULL,
    description TEXT DEFAULT NULL,
    old_values JSON DEFAULT NULL,
    new_values JSON DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_user_id (user_id),
    KEY idx_entity (entity_type, entity_id),
    KEY idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 5: PERMISSIONS & ROLES
-- =====================================================
CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS role_permissions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(50) NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role_permission (role, permission_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table 6: CATÉGORIES DE DEMANDES
-- =====================================================
CREATE TABLE IF NOT EXISTS help_categories (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    icon VARCHAR(50) DEFAULT NULL,
    color VARCHAR(7) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATA INITIALIZATION
-- =====================================================

-- Insérer les utilisateurs par défaut
INSERT INTO users (name, email, password, role, status) VALUES
('Admin Sara', 'admin@peaceconnect.tn', SHA2('admin123', 256), 'admin', 'active'),
('Modérateur Houcine', 'moderator@peaceconnect.tn', SHA2('mod123', 256), 'moderator', 'active'),
('Bénévole Nada', 'volunteer@peaceconnect.tn', SHA2('vol123', 256), 'volunteer', 'active'),
('Utilisateur Test', 'user@peaceconnect.tn', SHA2('user123', 256), 'user', 'active');

-- Insérer les catégories de demandes
INSERT INTO help_categories (name, description, icon, color) VALUES
('Aide Sociale', 'Assistance sociales et aide d\'urgence', '🤝', '#10b981'),
('Aide Juridique', 'Conseils juridiques et assistance légale', '⚖️', '#3b82f6'),
('Aide Psychologique', 'Soutien psychologique et counseling', '💭', '#8b5cf6'),
('Formation/Éducation', 'Cours et formation professionnelle', '📚', '#f59e0b'),
('Santé', 'Assistance médicale et de santé', '⚕️', '#ef4444'),
('Logement', 'Aide pour le logement et hébergement', '🏠', '#ec4899'),
('Emploi', 'Aide à la recherche d\'emploi et formation', '💼', '#06b6d4'),
('Autre', 'Autres types d\'assistance', '❓', '#6b7280');

-- Insérer les permissions
INSERT INTO permissions (name, description) VALUES
('view_requests', 'Voir les demandes d\'aide'),
('create_request', 'Créer une nouvelle demande'),
('edit_request', 'Modifier les demandes'),
('delete_request', 'Supprimer les demandes'),
('view_organisations', 'Voir les organisations'),
('create_organisation', 'Créer une organisation'),
('edit_organisation', 'Modifier les organisations'),
('delete_organisation', 'Supprimer les organisations'),
('view_users', 'Voir la liste des utilisateurs'),
('manage_users', 'Gérer les utilisateurs'),
('view_reports', 'Voir les rapports et statistiques'),
('manage_system', 'Gérer le système et les paramètres');

-- Assigner les permissions par défaut aux rôles
INSERT INTO role_permissions (role, permission_id) SELECT 'admin', id FROM permissions;
INSERT INTO role_permissions (role, permission_id) SELECT 'moderator', id FROM permissions WHERE name IN ('view_requests', 'edit_request', 'view_organisations', 'view_users', 'view_reports');
INSERT INTO role_permissions (role, permission_id) SELECT 'volunteer', id FROM permissions WHERE name IN ('view_requests', 'view_organisations');
INSERT INTO role_permissions (role, permission_id) SELECT 'user', id FROM permissions WHERE name IN ('create_request', 'view_requests');

-- Insérer les organisations initiales
INSERT INTO organisations (name, acronym, description, category, email, phone, website, address, city, status, mission, responsable_id, created_by) VALUES
('ONG Paix et Solidarité', 'OPAS', 'Organisation dédiée à la paix et la solidarité sociale', 'ONG', 'contact@ong-paix.tn', '+216 71 123 456', 'www.ong-paix.tn', '123 Rue de la Paix', 'Tunis', 'active', 'Promouvoir la paix et l\'entraide', 1, 1),
('Centre Juridique National', 'CJN', 'Assistance juridique gratuite', 'Justice', 'info@cjn.tn', '+216 71 234 567', 'www.cjn.tn', '456 Avenue des Droits', 'Sfax', 'active', 'Accès à la justice pour tous', 1, 1),
('Fondation Santé Sociale', 'FSS', 'Services de santé et soutien social', 'Santé', 'sante@fss.tn', '+216 71 345 678', 'www.fss.tn', '789 Boulevard Santé', 'Sousse', 'active', 'Accès aux services de santé', 1, 1);

-- Insérer des demandes d'aide initiales
INSERT INTO help_requests (title, help_type, urgency_level, situation, location, contact_method, status, responsable_id, organisation_id, requester_id, priority, created_by) VALUES
('Aide d\'urgence pour famille en difficulté', 'Aide Sociale', 'urgent', 'Famille nécessitant aide d\'urgence suite à perte d\'emploi', 'Tunis', 'email', 'pending', 2, 1, 4, 10, 4),
('Consultation juridique demande de régularisation', 'Aide Juridique', 'high', 'Besoin assistance pour régularisation administrative', 'Sfax', 'telephone', 'in_progress', 2, 2, 4, 8, 4),
('Soutien psychologique ponctuel', 'Aide Psychologique', 'normal', 'Consultation psychologique pour stress et anxiété', 'Sousse', 'email', 'completed', 3, 3, 4, 5, 4),
('Formation professionnelle demandée', 'Formation/Éducation', 'normal', 'Recherche formation en informatique', 'Tunis', 'telephone', 'pending', 2, 1, 4, 6, 4),
('Assistance médicale urgente', 'Santé', 'urgent', 'Accès aux soins médicaux d\'urgence', 'Sfax', 'email', 'pending', 3, 3, 4, 10, 4);

-- =====================================================
-- INDEXES ADDITIONNELS POUR PERFORMANCE
-- =====================================================
CREATE INDEX idx_help_requests_status ON help_requests(status);
CREATE INDEX idx_help_requests_urgency ON help_requests(urgency_level);
CREATE INDEX idx_help_requests_created ON help_requests(created_at);
CREATE INDEX idx_organisations_status ON organisations(status);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_activity_user ON activity_logs(user_id);
CREATE INDEX idx_activity_entity ON activity_logs(entity_type, entity_id);

-- =====================================================
-- VIEWS POUR FACILITER LES REQUÊTES
-- =====================================================

-- Vue pour les demandes avec détails complets
CREATE OR REPLACE VIEW v_help_requests_detailed AS
SELECT 
    hr.id,
    hr.title,
    hr.help_type,
    hr.urgency_level,
    hr.situation,
    hr.location,
    hr.status,
    hr.priority,
    hr.created_at,
    hr.updated_at,
    CONCAT(requester.name, ' (', requester.email, ')') AS requester_info,
    CONCAT(responsable.name, ' (', responsable.email, ')') AS responsable_info,
    org.name AS organisation_name,
    hc.name AS category_name
FROM help_requests hr
LEFT JOIN users requester ON hr.requester_id = requester.id
LEFT JOIN users responsable ON hr.responsable_id = responsable.id
LEFT JOIN organisations org ON hr.organisation_id = org.id
LEFT JOIN help_categories hc ON hr.help_type = hc.name;

-- Vue pour les statistiques
CREATE OR REPLACE VIEW v_statistics AS
SELECT 
    (SELECT COUNT(*) FROM help_requests) AS total_requests,
    (SELECT COUNT(*) FROM help_requests WHERE status = 'pending') AS pending_requests,
    (SELECT COUNT(*) FROM help_requests WHERE status = 'in_progress') AS in_progress_requests,
    (SELECT COUNT(*) FROM help_requests WHERE status = 'completed') AS completed_requests,
    (SELECT COUNT(*) FROM organisations WHERE status = 'active') AS active_organisations,
    (SELECT COUNT(*) FROM users) AS total_users,
    (SELECT COUNT(*) FROM users WHERE role = 'admin') AS admin_count;

-- =====================================================
-- FIN DU SCRIPT
-- =====================================================
