-- 1. Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `library_db`;
USE `library_db`;

-- 2. Drop existing table to ensure a clean setup and clear errors
DROP TABLE IF EXISTS `users`;

-- 3. Create the table with all required columns for OTP and Roles
CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  -- Added 'admin' to the role list
  `role` enum('student','teacher','parent','admin') NOT NULL DEFAULT 'student',
  -- New columns for the verification system
  `otp_code` varchar(10) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 4. Insert an initial Admin user for testing
-- The password below is for 'admin123' (hashed)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `is_verified`) VALUES
('Library Admin', 'admin.admin@ici.edu.ph', '$2y$10$DP6mM5.EXQrAKDUDkKEkbu84z1uT0/gSgGRTt.bj5jKZMBBwucvay', 'admin', 1);