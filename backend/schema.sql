-- backend/schema.sql
-- Media Share database schema

-- Media table
CREATE TABLE IF NOT EXISTS `media` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `filename` VARCHAR(255) NOT NULL,
    `thumbnail` VARCHAR(255),
    `type` ENUM('video', 'audio', 'image') NOT NULL,
    `caption` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tags table
CREATE TABLE IF NOT EXISTS `tags` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Media_Tags relation table
CREATE TABLE IF NOT EXISTS `media_tags` (
    `media_id` INT NOT NULL,
    `tag_id` INT NOT NULL,
    PRIMARY KEY (`media_id`, `tag_id`),
    FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Analytics table
CREATE TABLE IF NOT EXISTS `analytics` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `media_id` INT NOT NULL,
    `user_name` VARCHAR(50) NOT NULL,
    `event_type` ENUM('view', 'play', 'pause', 'seek', 'progress', 'ended', 'download') NOT NULL,
    `position` FLOAT,
    `percentage` FLOAT,
    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin users table
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table for app configuration
CREATE TABLE IF NOT EXISTS `settings` (
    `key` VARCHAR(50) PRIMARY KEY,
    `value` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial settings
INSERT INTO `settings` (`key`, `value`) VALUES
('app_title', 'Media Share'),
('app_description', 'Share media with tracking capabilities'),
('frontend_url', '../'),
('max_upload_size', '104857600'),
('allowed_users', 'charl,nade');