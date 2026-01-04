-- Jetty Seed Data - Essential tables for VPS
-- Generated from demo1 backup
-- Run migrations first, then import this file

SET FOREIGN_KEY_CHECKS=0;

-- Roles
INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Super Admin'),
(2, 'Administrator'),
(3, 'Manager'),
(4, 'Operator'),
(5, 'Checker');

-- Branches
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

-- Routes (branch pairs)
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

-- Users (Staff logins)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `branch_id`, `ferry_boat_id`, `mobile`, `role`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@gmail.com', NULL, '$2y$12$OsaYq8t9wIpsDkAbYD4pjO.08jeNSZVjGfJf4mKweBotnwz7pwGZy', NULL, NULL, NULL, NULL, NULL, 1, '2025-09-27 02:54:53', '2025-09-27 02:54:53'),
(2, 'operator 1', 'dabholoperator1@gmail.com', NULL, '$2y$12$0DvlcTsKu3rVZqVGdktzKOz03Y1j3lDjE39avj.okEDsDOg9zIp4C', NULL, 1, NULL, '9898765432', NULL, 4, '2025-09-27 02:56:41', '2025-09-27 02:56:41'),
(3, 'operator 2', 'operator2@gmail.com', NULL, '$2y$12$E9pVBKbxwoBRFUbsK8eN9OP7B/iyPQO0Y4DimXP59SxB4dgPTxAyS', NULL, 8, NULL, '8767654321', NULL, 4, '2025-09-27 02:57:28', '2025-10-23 03:14:28'),
(4, 'Dabhol Manager', 'dabholmanager@gmail.com', NULL, '$2y$12$BtUv6Q1Kg822MQpjar4L.eP8zlWdfsXoN02Ug2WvmXqHW7dWiG5i6', NULL, 1, NULL, '8787654321', 'Manager', 3, '2025-09-27 14:00:29', '2025-10-01 12:27:33'),
(5, 'Dhopave manager', 'dhopavemanager@gmail.com', NULL, '$2y$12$b0eJTPtuUmOdW4Zx6JibieubmWAPCADAcOIMr061hrQosvn/yLSDC', NULL, 5, NULL, '7876543212', 'Manager', 3, '2025-09-27 14:02:57', '2025-10-23 02:37:45'),
(7, 'admin', 'admin@gmail.com', NULL, '$2y$12$7d117OMy96.552ibMZZI9.r8LFH1uicDGriz9a6nTSR1R8.swGSUy', NULL, NULL, NULL, '9898765432', 'Administrator', 2, '2025-10-03 00:04:26', '2025-10-03 00:04:26'),
(8, 'test', 'testmanger@gmail.com', NULL, '$2y$12$XoPEiqegO9HlGh1q60tS4.GEeE2b3jN44BA6JmKUVsVEUOVe/ulB2', NULL, 3, NULL, '8987654321', 'Manager', 3, '2025-10-23 03:02:27', '2025-10-23 03:02:27');

-- Ferryboats
INSERT INTO `ferryboats` (`id`, `number`, `name`, `user_id`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'RTNIV00001', 'SHANTADURGA', NULL, 1, '2025-10-23 00:04:30', '2025-10-23 00:04:30'),
(2, 'RTNIV00001', 'SHANTADURGA', NULL, 2, '2025-10-23 00:05:10', '2025-10-23 00:05:10');

-- Ferry Schedules
INSERT INTO `ferry_schedules` (`id`, `hour`, `minute`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 23, 55, 1, '2025-10-22 12:11:29', '2025-10-22 23:55:39'),
(3, 6, 30, 2, '2025-10-22 23:56:10', '2025-10-22 23:56:10');

-- Guest Categories
INSERT INTO `guest_categories` (`id`, `name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'FAMILY', NULL, NULL, NULL),
(2, 'FRIENDS', NULL, NULL, NULL),
(3, 'SOCIAL', NULL, NULL, NULL),
(4, 'INSTITUTION', NULL, NULL, NULL),
(5, 'BUSINESS', NULL, NULL, NULL),
(6, 'CUSTOM', NULL, NULL, NULL),
(7, 'MARINE BOARD', NULL, NULL, NULL),
(8, 'POLICE', NULL, NULL, NULL),
(9, 'LOCAL', NULL, NULL, NULL);

-- Item Categories
INSERT INTO `item_categories` (`id`, `category_name`, `levy`, `user_id`, `location_id`, `created_at`, `updated_at`) VALUES
(1, 'CYCLE', 12.00, NULL, NULL, '2025-10-23 00:09:38', '2025-10-23 00:09:38'),
(2, 'PASSENGER ADULT ABV 12 YR', 0.00, NULL, NULL, '2025-10-23 17:43:16', '2025-10-23 17:43:16');

-- Item Rates
INSERT INTO `item_rates` (`id`, `item_id`, `item_name`, `item_category_id`, `item_rate`, `item_lavy`, `branch_id`, `starting_date`, `ending_date`, `user_id`, `route_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'CYCLE1', 1, 14.00, 4.00, 1, '2025-10-23', NULL, 1, NULL, '2025-10-23 00:11:58', '2025-10-23 00:12:41'),
(3, 1, 'CYCLE1', 1, 14.00, 4.00, 2, '2025-10-23', NULL, 1, NULL, '2025-10-23 00:13:02', '2025-10-23 00:13:02'),
(4, 2, 'PASSENGER ADULT ABV 12 YR', 2, 18.00, 2.00, 1, '2025-10-23', NULL, 1, NULL, '2025-10-23 17:47:23', '2025-10-23 17:47:23'),
(5, 2, 'PASSENGER ADULT ABV 12 YR', 2, 18.00, 2.00, 2, '2025-10-23', NULL, 1, NULL, '2025-10-23 17:47:23', '2025-10-23 17:47:23');

-- Special Charges
INSERT INTO `special_charges` (`id`, `branch_id`, `special_charge`, `created_at`, `updated_at`) VALUES
(1, 1, 400.00, '2025-10-23 01:48:13', '2025-10-23 01:49:22'),
(3, 2, 500.00, '2025-10-23 01:50:47', '2025-10-23 01:50:47');

SET FOREIGN_KEY_CHECKS=1;
