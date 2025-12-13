-- Module 5: Reports + Sessions Database Schema

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

CREATE TABLE IF NOT EXISTS `mediators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `expertise` varchar(100) DEFAULT NULL,
  `availability` enum('available','busy','unavailable') NOT NULL DEFAULT 'available',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `incident_date` date DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','assigned','in_mediation','resolved') DEFAULT 'pending',
  `mediator_id` int(11) DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_report_mediator` (`mediator_id`),
  KEY `idx_report_user` (`user_id`),
  CONSTRAINT `fk_report_mediator` FOREIGN KEY (`mediator_id`) REFERENCES `mediators` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_report_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `mediation_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL,
  `mediator_id` int(11) NOT NULL,
  `session_date` date NOT NULL,
  `session_time` time NOT NULL,
  `session_type` enum('online','in_person') NOT NULL DEFAULT 'in_person',
  `location` varchar(255) DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_session_report` (`report_id`),
  KEY `fk_session_mediator` (`mediator_id`),
  CONSTRAINT `fk_session_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_session_mediator` FOREIGN KEY (`mediator_id`) REFERENCES `mediators` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `ai_flags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL,
  `flag_type` enum('violence','urgency','harassment','discrimination','general') NOT NULL DEFAULT 'general',
  `severity` enum('low','medium','high','critical') NOT NULL DEFAULT 'low',
  `confidence_score` decimal(3,2) DEFAULT 0.50,
  `keywords_detected` text DEFAULT NULL,
  `ai_summary` text DEFAULT NULL,
  `suggested_priority` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_flag_report` (`report_id`),
  KEY `idx_severity` (`severity`),
  KEY `idx_flag_type` (`flag_type`),
  CONSTRAINT `fk_flag_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Default admin account (password: password)
INSERT IGNORE INTO `users` (`id`, `name`, `lastname`, `email`, `password`, `role`) VALUES
(1, 'Admin', 'User', 'admin@peaceconnect.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

