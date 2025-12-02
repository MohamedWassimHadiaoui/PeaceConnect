-- create_peaceconnect.sql
-- Improved, robust script for phpMyAdmin / XAMPP environments.
-- If a statement fails in phpMyAdmin, run the script in smaller sections (see notes below).

-- NOTE: If your server uses MariaDB and phpMyAdmin shows engine errors (Aria), run the
-- statements in small chunks and repair the server tables first (instructions provided in the README).

-- ----------------------------
-- SECTION A: Create database
-- ----------------------------
CREATE DATABASE IF NOT EXISTS `peaceconnect` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database for following statements
USE `peaceconnect`;

-- ----------------------------
-- SECTION B: Create application user (safe / compatible)
-- ----------------------------
-- Some MySQL/MariaDB versions support CREATE USER IF NOT EXISTS; if this fails in phpMyAdmin,
-- create the user manually via the "User accounts" UI.
-- We perform a GRANT which will create the user if needed on many distributions.

-- Grant privileges to the application user (creates user on many servers)
GRANT ALL PRIVILEGES ON `peaceconnect`.* TO 'Projet2A'@'localhost' IDENTIFIED BY '123';
FLUSH PRIVILEGES;

-- ----------------------------
-- SECTION C: Tables (InnoDB)
-- ----------------------------
-- Main table: help_requests
CREATE TABLE IF NOT EXISTS `help_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `help_type` VARCHAR(100) NOT NULL,
  `urgency_level` VARCHAR(50) NOT NULL,
  `situation` TEXT NOT NULL,
  `location` VARCHAR(100) DEFAULT NULL,
  `contact_method` VARCHAR(100) DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'en_attente',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responsable` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_help_type` (`help_type`),
  KEY `idx_urgency` (`urgency_level`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional table: back-office users / referents
CREATE TABLE IF NOT EXISTS `pc_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `role` VARCHAR(50) DEFAULT 'staff',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional comments/notes table
CREATE TABLE IF NOT EXISTS `help_request_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_id` INT UNSIGNED NOT NULL,
  `author` VARCHAR(150) DEFAULT NULL,
  `comment` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_request` (`request_id`),
  CONSTRAINT `fk_comments_request` FOREIGN KEY (`request_id`) REFERENCES `help_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- SECTION D: Optional seed data (run only after tables created)
-- Run these INSERTs only if you want example data.
-- ----------------------------
INSERT INTO `help_requests` (`help_type`, `urgency_level`, `situation`, `location`, `contact_method`, `status`, `responsable`)
VALUES
('juridique', 'eleve', 'Besoin d aide pour signaler une agression et obtenir un accompagnement.', 'Paris', 'email', 'en_attente', NULL),
('psychologique', 'moyen', 'Difficultes de sommeil et anxiete suite a un incident', 'Lyon', 'phone', 'prise_en_charge', 'S. Boulifi');

INSERT INTO `pc_users` (`name`, `email`, `phone`, `role`) VALUES
('Sara Boulifi', 'sara@example.org', '+33123456789', 'referent'),
('Admin Local', 'admin@example.org', NULL, 'admin');

-- End of script
