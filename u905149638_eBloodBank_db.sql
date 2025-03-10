-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 10, 2025 at 07:24 AM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u905149638_eBloodBank_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'Auth', 'User logged in successfully.', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip_address\":\"103.107.36.106\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/131.0.0.0 Safari\\/537.36\",\"user_id\":1,\"email\":\"john@example.com\"}', NULL, '2024-11-27 11:24:21', '2024-11-27 11:24:21'),
(2, 'User', 'Users Retrieved', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip_address\":\"103.107.36.106\",\"user_count\":2,\"description\":\"Retrieved all users.\"}', NULL, '2024-11-27 11:26:10', '2024-11-27 11:26:10'),
(3, 'Auth', 'User logged in successfully.', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip_address\":\"103.107.36.106\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/131.0.0.0 Safari\\/537.36\",\"user_id\":1,\"email\":\"john@example.com\"}', NULL, '2024-11-27 11:28:02', '2024-11-27 11:28:02'),
(4, 'Auth', 'User logged in successfully.', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip_address\":\"103.107.36.106\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/131.0.0.0 Safari\\/537.36\",\"user_id\":1,\"email\":\"john@example.com\"}', NULL, '2024-11-28 12:02:43', '2024-11-28 12:02:43'),
(5, 'Role', 'Roles Retrieved', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip_address\":\"103.107.36.106\",\"description\":\"Retrieved all roles with permissions\",\"role_count\":0}', NULL, '2024-11-28 12:03:49', '2024-11-28 12:03:49'),
(6, 'Auth', 'Login attempt failed - Email not registered.', NULL, NULL, NULL, NULL, NULL, '{\"ip_address\":\"103.107.36.106\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/131.0.0.0 Safari\\/537.36 Edg\\/131.0.0.0\",\"email\":\"admin@nn.vom\"}', NULL, '2024-11-30 09:29:03', '2024-11-30 09:29:03'),
(7, 'Auth', 'User logged in successfully.', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip_address\":\"103.107.36.106\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/131.0.0.0 Safari\\/537.36 Edg\\/131.0.0.0\",\"user_id\":1,\"email\":\"john@example.com\"}', NULL, '2024-11-30 09:30:34', '2024-11-30 09:30:34'),
(8, 'Auth', 'User logged in successfully.', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip_address\":\"103.107.36.106\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/131.0.0.0 Safari\\/537.36 Edg\\/131.0.0.0\",\"user_id\":1,\"email\":\"john@example.com\"}', NULL, '2024-11-30 09:32:50', '2024-11-30 09:32:50'),
(9, 'Auth', 'User registration failed due to a server error.', NULL, NULL, NULL, NULL, NULL, '{\"ip_address\":\"103.107.36.106\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/131.0.0.0 Safari\\/537.36 Edg\\/131.0.0.0\",\"error_message\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'blood_group\' in \'field list\' (Connection: mysql, SQL: insert into `users` (`name`, `email`, `password`, `phone_number`, `blood_group`, `pic`, `updated_at`, `created_at`) values (Anupam Kakati, annpm1138@gmail.com, $2y$10$bz9pXjlECC0pEWRFOjJG5.5xAaWh3tdNZGcPuIasCdqBQOlTn59gm, 9365029730, A+, ?, 2024-11-30 10:03:51, 2024-11-30 10:03:51))\"}', NULL, '2024-11-30 10:03:51', '2024-11-30 10:03:51'),
(10, 'Auth', 'User registration failed due to a server error.', NULL, NULL, NULL, NULL, NULL, '{\"ip_address\":\"103.107.36.106\",\"user_agent\":\"Mozilla\\/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit\\/605.1.15 (KHTML, like Gecko) Version\\/16.6 Mobile\\/15E148 Safari\\/604.1 Edg\\/131.0.0.0\",\"error_message\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'blood_group\' in \'field list\' (Connection: mysql, SQL: insert into `users` (`name`, `email`, `password`, `phone_number`, `blood_group`, `pic`, `updated_at`, `created_at`) values (Anupam Kakati, annpm1138@gmail.com, $2y$10$HdFaSCY0hHBanRsQsJWc1ugsCRVuQ..Ervghf5u5dhfxUTfArc6R6, 9365029730, A+, ?, 2024-11-30 10:06:41, 2024-11-30 10:06:41))\"}', NULL, '2024-11-30 10:06:41', '2024-11-30 10:06:41'),
(11, 'Auth', 'User registration failed due to a server error.', NULL, NULL, NULL, NULL, NULL, '{\"ip_address\":\"103.107.36.106\",\"user_agent\":\"Mozilla\\/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit\\/605.1.15 (KHTML, like Gecko) Version\\/16.6 Mobile\\/15E148 Safari\\/604.1 Edg\\/131.0.0.0\",\"error_message\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'blood_group\' in \'field list\' (Connection: mysql, SQL: insert into `users` (`name`, `email`, `password`, `phone_number`, `blood_group`, `pic`, `updated_at`, `created_at`) values (Anupam Kakati, annpm1138@gmail.com, $2y$10$5BjqztoTls7d9ab5L1sQgOfTU\\/yWSIxaH8NlWEmwKT8kH233XwJsW, 9365029730, A+, ?, 2024-11-30 10:18:30, 2024-11-30 10:18:30))\"}', NULL, '2024-11-30 10:18:30', '2024-11-30 10:18:30'),
(12, 'Auth', 'User registration failed due to a server error.', NULL, NULL, NULL, NULL, NULL, '{\"ip_address\":\"2401:4900:1c3b:548b:68bc:3f95:4e38:bb39\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/123.0.0.0 Safari\\/537.36\",\"error_message\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'blood_group\' in \'field list\' (Connection: mysql, SQL: insert into `users` (`name`, `email`, `password`, `phone_number`, `blood_group`, `pic`, `updated_at`, `created_at`) values (John Doe, john67@example.com, $2y$10$t3caKaY9osHhHflVvCdiIe9CbjOCL97oybLWZpP1M\\/CWozWGe7Y\\/W, 1230567890, B+ve, ?, 2024-12-01 04:58:39, 2024-12-01 04:58:39))\"}', NULL, '2024-12-01 04:58:39', '2024-12-01 04:58:39'),
(13, 'Auth', 'User registration failed due to a server error.', NULL, NULL, NULL, NULL, NULL, '{\"ip_address\":\"2401:4900:1c3b:548b:68bc:3f95:4e38:bb39\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/123.0.0.0 Safari\\/537.36\",\"error_message\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'blood_group\' in \'field list\' (Connection: mysql, SQL: insert into `users` (`name`, `email`, `password`, `phone_number`, `blood_group`, `pic`, `updated_at`, `created_at`) values (John Doe, john78@example.com, $2y$10$ctJxXRp6s8s41GnWpoRGAusWro9Vv54oMrkDkAtG5hOsukkz8bV.G, 1234067890, B+ve, ?, 2024-12-01 05:00:16, 2024-12-01 05:00:16))\"}', NULL, '2024-12-01 05:00:16', '2024-12-01 05:00:16'),
(14, 'Auth', 'User registration failed due to a server error.', NULL, NULL, NULL, NULL, NULL, '{\"ip_address\":\"2401:4900:1c3b:548b:68bc:3f95:4e38:bb39\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/123.0.0.0 Safari\\/537.36\",\"error_message\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'blood_group\' in \'field list\' (Connection: mysql, SQL: insert into `users` (`name`, `email`, `password`, `phone_number`, `blood_group`, `pic`, `updated_at`, `created_at`) values (John Doe, john78@example.com, $2y$10$v6b3h946c9NEkgFR3bQwCOmldO\\/Q2j1ZkJDjhRZZTSnYqDDI5QAxK, 1234067890, B+ve, ?, 2024-12-01 05:00:24, 2024-12-01 05:00:24))\"}', NULL, '2024-12-01 05:00:24', '2024-12-01 05:00:24'),
(15, 'Auth', 'User registration failed due to a server error.', NULL, NULL, NULL, NULL, NULL, '{\"ip_address\":\"2401:4900:1c3b:548b:68bc:3f95:4e38:bb39\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/123.0.0.0 Safari\\/537.36\",\"error_message\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'blood_group\' in \'field list\' (Connection: mysql, SQL: insert into `users` (`name`, `email`, `password`, `phone_number`, `blood_group`, `pic`, `updated_at`, `created_at`) values (John Doe, john45@example.com, $2y$10$yXRxfpfFv1Q7v.RR4FXpKeREJ\\/7mYts14MG8WabmTCgvcN7gXdL7a, 1234767890, B+ve, ?, 2024-12-01 05:08:51, 2024-12-01 05:08:51))\"}', NULL, '2024-12-01 05:08:51', '2024-12-01 05:08:51'),
(16, 'Auth', 'User logged in successfully.', NULL, NULL, NULL, 'App\\Models\\User', 1, '{\"ip_address\":\"2401:4900:1c3b:eb90:482c:bcd4:1eb2:cab2\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/131.0.0.0 Mobile Safari\\/537.36\",\"user_id\":1,\"email\":\"john@example.com\"}', NULL, '2024-12-01 14:23:02', '2024-12-01 14:23:02');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `pic` varchar(255) DEFAULT NULL,
  `otp_email` varchar(255) DEFAULT NULL,
  `otp_phone` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone_number`, `status`, `pic`, `otp_email`, `otp_phone`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'john@example.com', NULL, '$2y$10$7x7TA3sh1Vqz1SSTofcTYuY4pFiBZf.wY6KNS.fULsBJVF/wtu0je', '1234567890', 1, NULL, NULL, NULL, NULL, '2024-11-27 04:09:09', '2024-11-27 04:09:09'),
(2, 'Anupam Kakati', 'qqw@example.com', NULL, '$2y$10$3N.XYlEdZBPdAGjNpiZVde/vp690DYsc1mIhWMjvdHAyHHZ8A/moe', '1234567290', 1, NULL, NULL, NULL, NULL, '2024-11-27 04:38:49', '2024-11-27 04:38:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_number_unique` (`phone_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
