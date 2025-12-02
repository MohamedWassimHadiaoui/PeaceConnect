-- Script SQL pour initialiser la base peaceconnect et la table help_requests
-- Utilisateur MySQL attendu : Projet2A / 123

CREATE DATABASE IF NOT EXISTS peaceconnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE peaceconnect;

CREATE TABLE IF NOT EXISTS help_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    help_type VARCHAR(100) NOT NULL,
    urgency_level VARCHAR(50) NOT NULL,
    situation TEXT NOT NULL,
    location VARCHAR(100),
    contact_method VARCHAR(100),
    status VARCHAR(50) DEFAULT 'en_attente',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    responsable VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Exemple de données initiales (optionnel)
INSERT INTO help_requests (help_type, urgency_level, situation, location, contact_method, status, responsable)
VALUES
('sociale', 'moyen', 'Besoin d\'un accompagnement social pour une famille en difficulté.', 'Tunis', 'email', 'en_attente', 'Sara Boulifi'),
('juridique', 'eleve', 'Conseils juridiques pour dossier de regularisation.', 'Sfax', 'telephone', 'prise_en_charge', 'Houcine Ben Amor'),
('psychologique', 'faible', 'Recherche soutien psychologique ponctuel.', 'Sousse', 'email', 'orientee', 'Nada Kacem');