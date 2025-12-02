-- Migration 001: create help_requests table

CREATE DATABASE IF NOT EXISTS `peaceconnect` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `peaceconnect`;

CREATE TABLE IF NOT EXISTS `help_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `help_type` VARCHAR(100) NOT NULL,
  `urgency_level` VARCHAR(50) DEFAULT NULL,
  `situation` TEXT,
  `location` VARCHAR(255) DEFAULT NULL,
  `contact_method` VARCHAR(50) DEFAULT NULL,
  `responsable` VARCHAR(100) DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT 'en_attente',
  `attachments` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_help_type` (`help_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
