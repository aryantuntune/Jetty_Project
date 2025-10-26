-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2025 at 02:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demo1`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `branch_id`, `branch_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 101, 'DABHOL', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(2, 102, 'DHOPAVE', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(3, 103, 'VESHVI', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(4, 104, 'BAGMANDALE', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(5, 105, 'JAIGAD', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(6, 106, 'TAVSAL', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(7, 107, 'AGARDANDA', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(8, 108, 'DIGHI', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(9, 109, 'VASAI', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(10, 110, 'BHAYANDER', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(11, 111, 'VIRAR [MARAMBALPADA]', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16'),
(12, 112, 'SAPHALE [KHARWADASHRI]', NULL, '2025-09-19 11:47:16', '2025-09-19 11:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `branch_transfers`
--

CREATE TABLE `branch_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `from_branch_id` bigint(20) UNSIGNED NOT NULL,
  `to_branch_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_transfers`
--

INSERT INTO `branch_transfers` (`id`, `user_id`, `from_branch_id`, `to_branch_id`, `created_at`, `updated_at`) VALUES
(1, 4, 3, 1, '2025-09-30 05:30:53', '2025-09-30 05:30:53'),
(2, 4, 1, 4, '2025-09-30 05:31:24', '2025-09-30 05:31:24'),
(3, 4, 4, 1, '2025-10-01 12:27:33', '2025-10-01 12:27:33'),
(4, 3, 2, 3, '2025-10-23 02:36:27', '2025-10-23 02:36:27'),
(5, 5, 4, 3, '2025-10-23 02:36:43', '2025-10-23 02:36:43'),
(6, 3, 3, 5, '2025-10-23 02:37:38', '2025-10-23 02:37:38'),
(7, 5, 3, 5, '2025-10-23 02:37:45', '2025-10-23 02:37:45');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-maheshz@biltrax.com|127.0.0.1', 'i:1;', 1760501980),
('laravel-cache-maheshz@biltrax.com|127.0.0.1:timer', 'i:1760501980;', 1760501980),
('laravel-cache-superadmin@gmai.com|127.0.0.1', 'i:1;', 1761216694),
('laravel-cache-superadmin@gmai.com|127.0.0.1:timer', 'i:1761216694;', 1761216694);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `ferryboats`
--

CREATE TABLE `ferryboats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `number` varchar(256) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ferryboats`
--

INSERT INTO `ferryboats` (`id`, `number`, `name`, `user_id`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'RTNIV00001', 'SHANTADURGA', NULL, 1, '2025-10-23 00:04:30', '2025-10-23 00:04:30'),
(2, 'RTNIV00001', 'SHANTADURGA', NULL, 2, '2025-10-23 00:05:10', '2025-10-23 00:05:10');

-- --------------------------------------------------------

--
-- Table structure for table `ferry_schedules`
--

CREATE TABLE `ferry_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hour` int(11) NOT NULL,
  `minute` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ferry_schedules`
--

INSERT INTO `ferry_schedules` (`id`, `hour`, `minute`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 23, 55, 1, '2025-10-22 12:11:29', '2025-10-22 23:55:39'),
(3, 6, 30, 2, '2025-10-22 23:56:10', '2025-10-22 23:56:10');

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guest_categories`
--

CREATE TABLE `guest_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guest_categories`
--

INSERT INTO `guest_categories` (`id`, `name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'FAMILY', NULL, NULL, NULL),
(2, 'FRIENDS', NULL, NULL, NULL),
(3, 'SOCIAL', NULL, NULL, NULL),
(4, 'INSTITUTION', NULL, NULL, NULL),
(5, 'BUISINESS', NULL, NULL, NULL),
(6, 'CUSTOM', NULL, NULL, NULL),
(7, 'MARINE BOARD', NULL, NULL, NULL),
(8, 'POLICE', NULL, NULL, NULL),
(9, 'LOCAL', NULL, NULL, NULL),
(10, 'test', NULL, '2025-09-19 11:35:08', '2025-09-19 11:35:08');

-- --------------------------------------------------------

--
-- Table structure for table `item_categories`
--

CREATE TABLE `item_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `levy` decimal(8,2) NOT NULL DEFAULT 0.00,
  `user_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_categories`
--

INSERT INTO `item_categories` (`id`, `category_name`, `levy`, `user_id`, `location_id`, `created_at`, `updated_at`) VALUES
(1, 'CYCLE', 12.00, NULL, NULL, '2025-10-23 00:09:38', '2025-10-23 00:09:38'),
(2, 'PASSENGER ADULT ABV 12 YR', 0.00, NULL, NULL, '2025-10-23 17:43:16', '2025-10-23 17:43:16');

-- --------------------------------------------------------

--
-- Table structure for table `item_rates`
--

CREATE TABLE `item_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `item_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `item_lavy` decimal(10,2) NOT NULL DEFAULT 0.00,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `starting_date` date NOT NULL,
  `ending_date` date DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `route_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_rates`
--

INSERT INTO `item_rates` (`id`, `item_id`, `item_name`, `item_category_id`, `item_rate`, `item_lavy`, `branch_id`, `starting_date`, `ending_date`, `user_id`, `route_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'CYCLE1', 1, 14.00, 4.00, 1, '2025-10-23', NULL, 1, NULL, '2025-10-23 00:11:58', '2025-10-23 00:12:41'),
(3, 1, 'CYCLE1', 1, 14.00, 4.00, 2, '2025-10-23', NULL, 1, NULL, '2025-10-23 00:13:02', '2025-10-23 00:13:02'),
(4, 2, 'PASSENGER ADULT ABV 12 YR', 2, 18.00, 2.00, 1, '2025-10-23', NULL, 1, NULL, '2025-10-23 17:47:23', '2025-10-23 17:47:23'),
(5, 2, 'PASSENGER ADULT ABV 12 YR', 2, 18.00, 2.00, 2, '2025-10-23', NULL, 1, NULL, '2025-10-23 17:47:23', '2025-10-23 17:47:23');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
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

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_16_053630_create_item_categories_table', 1),
(5, '2025_09_16_061415_create_ferryboats_table', 1),
(6, '2025_09_16_070418_create_guest_categories_table', 1),
(7, '2025_09_19_171046_create_branches_table', 2),
(8, '2025_09_19_174133_create_guests_table', 3),
(9, '2025_09_20_034850_create_ferry_schedules_table', 4),
(10, '2025_09_25_104807_create_item_rates_table', 5),
(11, '2025_09_27_063030_create_tickets_table', 6),
(12, '2025_09_27_063356_create_tickets_table', 7),
(13, '2025_09_27_063409_create_ticket_lines_table', 7),
(14, '2025_09_27_065610_create_tickets_table', 8),
(15, '2025_09_30_103248_create_branch_transfers_table', 9),
(16, '2025_10_23_062821_create_special_charges_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('mahesh.zemse@gmail.com', '$2y$12$Bjsm.m2bk1ZgrrWjw82n1usNTIERtDhjVd6R64T67dKFLa.uEWITm', '2025-10-22 23:47:42');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Super Admin '),
(2, 'Adminstrator'),
(3, 'Manager'),
(4, 'Operator');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `route_id`, `branch_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 3),
(4, 2, 4),
(5, 3, 5),
(6, 3, 6),
(7, 4, 7),
(8, 4, 8),
(9, 5, 9),
(10, 5, 10),
(11, 6, 11),
(12, 6, 12);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7LxRP4RL0usA8Iq2r10OhxwXShpWu1dSMIzeketL', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialRzVEhCZlJaaGdPaGlDZ2VLeUdHTG44RWRYWlBNY0NKZnJyQVo4VSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fX0=', 1761243378),
('xEpCGhINZGLIMpUIT2SSEpjBZefysvteP5F3slmm', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiT1ZkbUFCOHFSd1ZuVTJ3aWVvMmE0MXFBbWpTUXI1MUxORW1zanVVZiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC90aWNrZXRzLzIvcHJpbnQ/dz01OCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzYxMjQzMzk5O319', 1761246133);

-- --------------------------------------------------------

--
-- Table structure for table `special_charges`
--

CREATE TABLE `special_charges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `special_charge` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `special_charges`
--

INSERT INTO `special_charges` (`id`, `branch_id`, `special_charge`, `created_at`, `updated_at`) VALUES
(1, 1, 400.00, '2025-10-23 01:48:13', '2025-10-23 01:49:22'),
(3, 2, 500.00, '2025-10-23 01:50:47', '2025-10-23 01:50:47');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(11) NOT NULL,
  `ferry_boat_id` int(11) NOT NULL,
  `payment_mode` varchar(255) NOT NULL,
  `ferry_time` datetime NOT NULL,
  `discount_pct` decimal(8,2) DEFAULT NULL,
  `discount_rs` decimal(12,2) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `user_id` int(11) NOT NULL,
  `ferry_type` varchar(256) DEFAULT NULL,
  `customer_name` varchar(256) DEFAULT NULL,
  `customer_mobile` varchar(256) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `branch_id`, `ferry_boat_id`, `payment_mode`, `ferry_time`, `discount_pct`, `discount_rs`, `total_amount`, `user_id`, `ferry_type`, `customer_name`, `customer_mobile`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'CASH MEMO', '2025-10-23 16:29:59', NULL, NULL, 598.00, 2, 'SPECIAL', NULL, NULL, '2025-10-23 05:29:59', '2025-10-23 05:29:59'),
(2, 1, 1, 'CASH MEMO', '2025-10-23 16:30:36', NULL, NULL, 418.00, 2, 'SPECIAL', NULL, NULL, '2025-10-23 05:30:36', '2025-10-23 05:30:36'),
(3, 1, 1, 'CASH MEMO', '2025-10-23 16:44:12', NULL, NULL, 796.00, 2, 'SPECIAL', 'Mahesh', '9975863133', '2025-10-23 05:44:12', '2025-10-23 05:44:12'),
(4, 1, 1, 'CASH MEMO', '2025-10-23 17:43:52', NULL, NULL, 1174.00, 2, 'SPECIAL', 'Test name', '9898787654', '2025-10-23 12:13:52', '2025-10-23 12:13:52'),
(5, 1, 1, 'CASH MEMO', '2025-10-23 23:55:00', NULL, NULL, 56.00, 2, 'REGULAR', 'MAHESH', '9975863133', '2025-10-23 17:48:59', '2025-10-23 17:48:59'),
(6, 1, 1, 'CASH MEMO', '2025-10-23 23:55:00', NULL, NULL, 58.00, 2, 'REGULAR', NULL, NULL, '2025-10-23 18:17:22', '2025-10-23 18:17:22'),
(7, 1, 1, 'CASH MEMO', '2025-10-24 23:55:00', NULL, NULL, 58.00, 2, 'REGULAR', 'Mahesh', '9876543212', '2025-10-23 18:50:06', '2025-10-23 18:50:06');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_lines`
--

CREATE TABLE `ticket_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `item_id` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `rate` decimal(12,2) NOT NULL,
  `levy` decimal(12,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `vehicle_name` varchar(255) DEFAULT NULL,
  `vehicle_no` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_lines`
--

INSERT INTO `ticket_lines` (`id`, `ticket_id`, `item_id`, `item_name`, `qty`, `rate`, `levy`, `amount`, `vehicle_name`, `vehicle_no`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, '1', 'CYCLE1', 11.00, 14.00, 4.00, 598.00, 'YFT', 'rer44refe', 2, '2025-10-23 05:29:59', '2025-10-23 05:29:59'),
(2, 2, '1', 'CYCLE1', 1.00, 14.00, 4.00, 418.00, NULL, NULL, 2, '2025-10-23 05:30:36', '2025-10-23 05:30:36'),
(3, 3, '1', 'CYCLE1', 22.00, 14.00, 4.00, 796.00, NULL, NULL, 2, '2025-10-23 05:44:12', '2025-10-23 05:44:12'),
(4, 4, '1', 'CYCLE1', 22.00, 14.00, 4.00, 596.00, NULL, NULL, 2, '2025-10-23 12:13:52', '2025-10-23 12:13:52'),
(5, 4, '1', 'CYCLE1', 21.00, 14.00, 4.00, 578.00, NULL, NULL, 2, '2025-10-23 12:13:52', '2025-10-23 12:13:52'),
(6, 5, '1', 'CYCLE1', 2.00, 14.00, 4.00, 36.00, NULL, NULL, 2, '2025-10-23 17:48:59', '2025-10-23 17:48:59'),
(7, 5, '2', 'PASSENGER ADULT ABV 12 YR', 1.00, 18.00, 2.00, 20.00, NULL, NULL, 2, '2025-10-23 17:48:59', '2025-10-23 17:48:59'),
(8, 6, '1', 'CYCLE1', 1.00, 14.00, 4.00, 18.00, NULL, NULL, 2, '2025-10-23 18:17:22', '2025-10-23 18:17:22'),
(9, 6, '2', 'PASSENGER ADULT ABV 12 YR', 2.00, 18.00, 2.00, 40.00, 'Pulsur', 'MH06AB7943', 2, '2025-10-23 18:17:22', '2025-10-23 18:17:22'),
(10, 7, '1', 'CYCLE1', 1.00, 14.00, 4.00, 18.00, NULL, NULL, 2, '2025-10-23 18:50:06', '2025-10-23 18:50:06'),
(11, 7, '2', 'PASSENGER ADULT ABV 12 YR', 2.00, 18.00, 2.00, 40.00, 'Pulsur', 'MH06BA7938', 2, '2025-10-23 18:50:06', '2025-10-23 18:50:06');

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
  `remember_token` varchar(100) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `ferry_boat_id` int(11) DEFAULT NULL,
  `mobile` varchar(256) DEFAULT NULL,
  `role` varchar(256) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `branch_id`, `ferry_boat_id`, `mobile`, `role`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@gmail.com', NULL, '$2y$12$OsaYq8t9wIpsDkAbYD4pjO.08jeNSZVjGfJf4mKweBotnwz7pwGZy', NULL, NULL, NULL, NULL, NULL, 1, '2025-09-27 02:54:53', '2025-09-27 02:54:53'),
(2, 'operator 1', 'dabholoperator1@gmail.com', NULL, '$2y$12$0DvlcTsKu3rVZqVGdktzKOz03Y1j3lDjE39avj.okEDsDOg9zIp4C', NULL, 1, NULL, '9898765432', NULL, 4, '2025-09-27 02:56:41', '2025-09-27 02:56:41'),
(3, 'operator 2', 'operator2@gmail.com', NULL, '$2y$12$E9pVBKbxwoBRFUbsK8eN9OP7B/iyPQO0Y4DimXP59SxB4dgPTxAyS', NULL, 8, NULL, '8767654321', NULL, 4, '2025-09-27 02:57:28', '2025-10-23 03:14:28'),
(4, 'Dabhol Manager', 'dabholmanager@gmail.com', NULL, '$2y$12$BtUv6Q1Kg822MQpjar4L.eP8zlWdfsXoN02Ug2WvmXqHW7dWiG5i6', NULL, 1, NULL, '8787654321', 'Manager', 3, '2025-09-27 14:00:29', '2025-10-01 12:27:33'),
(5, 'Dhopave manager', 'dhopavemanager@gmail.com', NULL, '$2y$12$b0eJTPtuUmOdW4Zx6JibieubmWAPCADAcOIMr061hrQosvn/yLSDC', NULL, 5, NULL, '7876543212', 'Manager', 3, '2025-09-27 14:02:57', '2025-10-23 02:37:45'),
(7, 'admin', 'admin@gmail.com', NULL, '$2y$12$7d117OMy96.552ibMZZI9.r8LFH1uicDGriz9a6nTSR1R8.swGSUy', NULL, NULL, NULL, '9898765432', 'Administrator', 2, '2025-10-03 00:04:26', '2025-10-03 00:04:26'),
(8, 'test', 'testmanger@gmail.com', NULL, '$2y$12$XoPEiqegO9HlGh1q60tS4.GEeE2b3jN44BA6JmKUVsVEUOVe/ulB2', NULL, 3, NULL, '8987654321', 'Manager', 3, '2025-10-23 03:02:27', '2025-10-23 03:02:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_branch_id_unique` (`branch_id`);

--
-- Indexes for table `branch_transfers`
--
ALTER TABLE `branch_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `ferryboats`
--
ALTER TABLE `ferryboats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ferry_schedules`
--
ALTER TABLE `ferry_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guests_category_id_foreign` (`category_id`);

--
-- Indexes for table `guest_categories`
--
ALTER TABLE `guest_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_categories`
--
ALTER TABLE `item_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_rates`
--
ALTER TABLE `item_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `special_charges`
--
ALTER TABLE `special_charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_lines`
--
ALTER TABLE `ticket_lines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `branch_transfers`
--
ALTER TABLE `branch_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ferryboats`
--
ALTER TABLE `ferryboats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ferry_schedules`
--
ALTER TABLE `ferry_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest_categories`
--
ALTER TABLE `guest_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `item_categories`
--
ALTER TABLE `item_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `item_rates`
--
ALTER TABLE `item_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `special_charges`
--
ALTER TABLE `special_charges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ticket_lines`
--
ALTER TABLE `ticket_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `guests_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `guest_categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
