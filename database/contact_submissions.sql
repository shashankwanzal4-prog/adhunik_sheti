-- SQL to create contact_submissions table
-- Run this in phpMyAdmin or MySQL to create the table for contact form submissions

CREATE TABLE IF NOT EXISTS `contact_submissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(255) NULL,
    `subject` VARCHAR(255) NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('new', 'read') DEFAULT 'new',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add index for faster searching
CREATE INDEX idx_status ON contact_submissions(status);
CREATE INDEX idx_created_at ON contact_submissions(created_at);
