-- SQL: création de la table `help_requests` pour PeaceConnect
-- Exécutez ceci dans MySQL (ex: via phpMyAdmin ou mysql client)

CREATE DATABASE IF NOT EXISTS `peaceconnect` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `peaceconnect`;

-- Table des demandes / signalements
CREATE TABLE IF NOT EXISTS `help_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `help_type` VARCHAR(100) NOT NULL,
  `urgency_level` VARCHAR(50) DEFAULT NULL,
  `situation` TEXT,
  `location` VARCHAR(255) DEFAULT NULL,
  `contact_method` VARCHAR(50) DEFAULT NULL,
  `contact_value` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT 'pending',
  `attachments` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_help_type` (`help_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exemple d'utilisateur MySQL (modifiez mot de passe et droits selon votre environnement)
-- CREATE USER 'Projet2A'@'localhost' IDENTIFIED BY '123';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `peaceconnect`.* TO 'Projet2A'@'localhost';
-- FLUSH PRIVILEGES;

-- Exemple d'insertion test
-- INSERT INTO `help_requests` (`help_type`, `urgency_level`, `situation`, `location`, `contact_method`, `contact_value`) 
-- VALUES ('medical', 'high', 'Blessure suite à une chute', 'Rue Exemple, Paris', 'email', 'user@example.com');
