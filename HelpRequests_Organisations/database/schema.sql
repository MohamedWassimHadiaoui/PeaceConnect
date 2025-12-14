-- Module 1: Help Requests + Organisations Database Schema

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','client','mediator') DEFAULT 'client',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `help_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `help_type` VARCHAR(100) NOT NULL,
  `urgency_level` ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
  `situation` TEXT NOT NULL,
  `location` VARCHAR(255),
  `contact_method` VARCHAR(100),
  `status` ENUM('pending', 'in_progress', 'resolved', 'closed') DEFAULT 'pending',
  `responsable` VARCHAR(100),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_help_user` (`user_id`),
  CONSTRAINT `fk_help_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `organisations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `acronym` VARCHAR(50) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT NULL,
  `logo_path` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(20) DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_org_category` (`category`),
  KEY `idx_org_city` (`city`),
  KEY `idx_org_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin account (password: password)
INSERT IGNORE INTO `users` (`id`, `name`, `lastname`, `email`, `password`, `role`) VALUES
(1, 'Admin', 'User', 'admin@peaceconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

