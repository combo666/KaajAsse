-- Minimal KaajAsse schema to reproduce the main tables used by the app
-- NOTE: Adjust types and constraints to match your production needs.

CREATE DATABASE IF NOT EXISTS KaajAsse;
USE KaajAsse;

-- Users table
CREATE TABLE IF NOT EXISTS `user` (
	`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`first_name` VARCHAR(100) NOT NULL,
	`last_name` VARCHAR(100) DEFAULT NULL,
	`user_email` VARCHAR(255) NOT NULL UNIQUE,
	`user_pass` VARCHAR(255) NOT NULL,
	`user_role` CHAR(1) DEFAULT 'u', -- u: user, a: admin, s: super-admin
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`user_id`)
);

-- Tasks table
CREATE TABLE IF NOT EXISTS `task_calendar` (
	`task_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`task_name` VARCHAR(255) NOT NULL,
	`task_description` TEXT,
	`task_start_date` DATE DEFAULT NULL,
	`task_duration` INT DEFAULT 1,
	`task_priority` VARCHAR(20) DEFAULT 'normal',
	`task_status` VARCHAR(30) DEFAULT 'backlog',
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`task_id`)
);

-- Task to user assignment (many-to-many)
CREATE TABLE IF NOT EXISTS `task_user` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`task_id` INT UNSIGNED NOT NULL,
	`user_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	INDEX (`task_id`),
	INDEX (`user_id`),
	FOREIGN KEY (`task_id`) REFERENCES `task_calendar`(`task_id`) ON DELETE CASCADE,
	FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE
);

-- Simple leaderboard table used by the kanban logic
CREATE TABLE IF NOT EXISTS `task_leaderboard` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT UNSIGNED NOT NULL,
	`points` INT DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`user_id`),
	FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE
);

-- Optional: sample admin user (password: change-me)
-- Use PHP password_hash to generate a proper hash before inserting into production.
-- INSERT INTO `user` (first_name, last_name, user_email, user_pass, user_role) VALUES ('Admin','User','admin@example.com','$2y$10$examplehashreplace', 'a');

-- End of schema
