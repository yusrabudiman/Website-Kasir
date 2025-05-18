-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2025 at 06:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` varchar(36) NOT NULL,
  `user_id` varchar(36) DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `module`, `action`, `details`, `ip_address`, `created_at`) VALUES
('', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'update', 'Updated Low Stock Threshold from 10 to 11', NULL, '2025-04-21 13:15:41'),
('03affcd5-52cf-4df0-9000-9aecd70d4f20', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:37:45'),
('069ba693-e236-4ad8-af9c-f8ee7877745b', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:43:42'),
('06e94a73-f1b3-4733-bfa2-0c85c745c45d', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:11:58'),
('0c03419b-053f-493c-b7b4-d137fdfc7133', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 12:17:55'),
('11f933e9-c2f3-45e5-ab3a-cc900be08042', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:30:31'),
('139b3409-ef1d-44ef-8d42-a85705770147', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:53:04'),
('183592aa-7d4f-4858-a8a6-6fffa4c7f759', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:37:00'),
('1fb9a684-d4f6-4f9d-a8ca-9e8cbbca0efe', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 12:07:11'),
('2538c389-997a-48e7-8b32-d4fab73af6f6', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:32:05'),
('27a50526-f64e-4ad9-a837-d4c2542ef07c', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:47:05'),
('2b065483-d808-41b5-ac33-e203b1600aa5', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:08:23'),
('2feb16d5-1e69-403a-86f5-c65377e6bac1', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:57:10'),
('30ecea46-fc2b-4e3c-9c47-6e01c927e8ba', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:13:51'),
('3f497873-48dc-4bc9-8c45-4eb767c75a4a', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 12:18:20'),
('43ccfb5a-8829-426d-9caa-deecab184bf0', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:15:41'),
('4881a86d-49cd-4449-86cf-a59d17de4a5b', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:06:20'),
('4cef8fd4-4705-4041-a989-cc4face47a87', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:53:11'),
('504b9b15-9695-4200-80c4-b922652592f5', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:30:59'),
('53483a5d-0d29-4750-bd09-9895e06e62cf', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:34:23'),
('5acf75f6-7dde-4ee7-9048-6d7373304799', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:13:03'),
('5d013d99-214c-4a76-ad06-0a1fb90d1e1f', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 12:07:19'),
('622acb34-a803-440c-92b6-554f267a5a98', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:42:59'),
('6b66f23f-3e88-481d-b00d-d60083119d0b', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 12:07:15'),
('6ddc1b9d-0daa-4a23-a46c-4c517608218b', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:43:34'),
('73e4a74a-0a14-490a-ba2f-867606c073f9', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:52:25'),
('748d5570-acf0-4939-8041-69125e3c943b', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:08:40'),
('7bbceedb-4d50-41d5-91e1-54996635d664', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:37:54'),
('7e5c1cb8-7f24-4f39-8083-e6576a642c9e', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:31:55'),
('7f00ab5f-770c-42e3-ae90-e07d3daddca7', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:59:25'),
('7f056eb5-af89-4087-b4bc-b049b2487315', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:34:16'),
('8205d5aa-d5a5-4a44-acd1-01fdb362e457', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:42:01'),
('87aaefa5-f85f-41e9-88b5-99c28e17c47d', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:37:05'),
('8c234fb3-08a0-46a4-b0e5-12fc8a0b0ed0', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:34:28'),
('8e489993-5be3-4aeb-b674-93869887ae75', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:11:46'),
('920729b0-ec60-40b5-9db4-8d5f01473c4a', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:37:30'),
('927df661-4bf9-4672-9331-e29aa08dc72a', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 12:02:30'),
('93f17954-bc76-4e5a-a796-03ef5f1cd2b3', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:37:24'),
('9ebcc0e7-564f-4a05-b6d1-c94858568ad7', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:45:40'),
('9f60c729-6733-434f-84cf-2501a22ffd22', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:55:21'),
('a47ee058-8947-4b16-ae1e-f0b52427795c', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:55:34'),
('abe7c8a6-7ca7-429f-a466-18408d9c6a9a', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:53:50'),
('ad729d23-27f8-44b9-a73c-d7dec939f3aa', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 12:07:07'),
('afed7104-1a8f-4bfb-9d09-693b34340a1f', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:11:53'),
('b9fde120-331f-4f54-b088-764597d6cf7d', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:50:47'),
('bb6bb1f8-2d30-4749-8d4b-fc3d5d7d53fe', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:55:16'),
('bbd2674e-4d2a-44c9-bc56-d5de5d201616', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:59:34'),
('bf050c15-ae57-43b7-8f2c-9e675d9d6df7', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:06:14'),
('c893ade4-a1e6-4659-b392-49bf37ae95e1', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 12:11:13'),
('d5f92cf8-0b39-40ab-b2bf-04f5a12e409a', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:49:47'),
('d6211ee5-ff5a-4676-8bc9-ad44d64d4104', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:43:09'),
('e26442d8-80a4-4b0f-977a-27eac53eaf78', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 08:53:36'),
('ed63c68c-ea57-43ca-8224-011addd678f9', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:31:41'),
('ef789940-aa98-4ae6-88bf-dd03e64ecfdc', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:34:36'),
('f3c17975-7a26-4eb2-8824-6660278505ca', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:59:30'),
('f5f6f9b1-fcb4-4641-b263-b682dda2e3fe', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 14:19:14'),
('f7d51056-551c-45e6-a299-ec96dc6de101', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:53:46'),
('fa07e6d3-a260-4bc4-a079-44e818f28705', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:12:02'),
('fd7736b7-634a-4a26-bfc5-546fc1f0ff17', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 13:08:31'),
('fdae416e-19c0-44e2-b405-162b27309041', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 11:51:55'),
('fde09eca-d585-49fa-92d3-cd62b62c2244', '0937c433-4c5b-41cb-bd8e-f915a6e59186', 'settings', 'Store settings updated', '', '::1', '2025-04-21 08:53:43');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` varchar(36) NOT NULL,
  `invoice_number` varchar(20) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `tax_amount` decimal(12,2) NOT NULL,
  `final_amount` decimal(12,2) NOT NULL,
  `payment_amount` decimal(12,2) NOT NULL,
  `change_amount` decimal(12,2) NOT NULL,
  `created_by` varchar(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` varchar(36) NOT NULL,
  `order_id` varchar(36) NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` varchar(36) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `min_stock` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `code`, `name`, `description`, `price`, `stock`, `min_stock`, `created_at`, `updated_at`) VALUES
('180bc774-5739-486f-965a-fcdcb3c5b248', '10201', 'cincau', 'Produk cincau merupakan produk yang memiliki ciri khas yang menyenangkan', 5000.00, 15, 10, '2025-04-21 13:49:48', '2025-04-21 13:49:48'),
('443a1294-4c7f-4a6b-b0ee-d4c307daf14c', '1102', 'aqua', 'AQUA adds', 2000.00, 12, 10, '2025-04-21 08:44:19', '2025-04-21 08:44:19');

-- --------------------------------------------------------

--
-- Table structure for table `stock_mutations`
--

CREATE TABLE `stock_mutations` (
  `id` varchar(36) NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `type` enum('purchase','sale','adjustment','return') NOT NULL,
  `quantity` int(11) NOT NULL,
  `before_stock` int(11) NOT NULL,
  `after_stock` int(11) NOT NULL,
  `reference_id` varchar(36) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` varchar(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_settings`
--

CREATE TABLE `store_settings` (
  `id` int(11) NOT NULL,
  `store_name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `currency_symbol` varchar(10) DEFAULT 'Rp',
  `low_stock_threshold` int(11) DEFAULT 10,
  `tax_rate` decimal(5,2) DEFAULT 0.00,
  `service_charge` decimal(5,2) DEFAULT 0.00,
  `printer_name` varchar(100) DEFAULT NULL,
  `printer_type` enum('thermal','regular') DEFAULT 'thermal',
  `thank_you_message` text DEFAULT 'Terima kasih telah berbelanja di toko kami. Kami menghargai kepercayaan Anda dan berharap dapat melayani Anda kembali.',
  `created_by` varchar(36) DEFAULT NULL,
  `updated_by` varchar(36) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_settings`
--

INSERT INTO `store_settings` (`id`, `store_name`, `address`, `phone`, `email`, `logo`, `currency_symbol`, `low_stock_threshold`, `tax_rate`, `service_charge`, `printer_name`, `printer_type`, `thank_you_message`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Website Kasir', 'google', '123456781', 'g@gmail.com', '', '$', 11, 15.01, 12.11, 'Windows 11 edition limited', 'regular', 'Terima kasih telah berbelanja di toko kami semoga belanjanya nyaman', '88fb91a6-1e8c-11f0-90ca-e5f621c269c5', '0937c433-4c5b-41cb-bd8e-f915a6e59186', '2025-04-21 08:42:19', '2025-04-21 14:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(36) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','cashier') NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `role`, `last_login`, `created_at`, `updated_at`) VALUES
('0937c433-4c5b-41cb-bd8e-f915a6e59186', 'adminstock', '$2y$10$4deKAj3xn5.hGEMiunbKued9OSlZW2ZhmTkvJEzUkdGVQl3.Rf6EC', 'kom', 'adminstock@gmail.com', 'admin', NULL, '2025-04-21 08:53:23', '2025-04-21 08:53:23'),
('7f4062bf-8bee-4cf4-9f1b-79f778eda39c', 'userstock', '$2y$10$xXqG1lMejsfsSaQq.dZbievPPgBOh0iwvcTExJQpUcsuah6r7Ik/y', 'user stock', 'userstock@gmail.com', 'cashier', NULL, '2025-04-21 16:09:38', '2025-04-21 16:09:38'),
('88fb91a6-1e8c-11f0-90ca-e5f621c269c5', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@example.com', 'admin', NULL, '2025-04-21 08:42:19', '2025-04-21 08:42:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `stock_mutations`
--
ALTER TABLE `stock_mutations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `store_settings`
--
ALTER TABLE `store_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `store_settings`
--
ALTER TABLE `store_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `stock_mutations`
--
ALTER TABLE `stock_mutations`
  ADD CONSTRAINT `stock_mutations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `stock_mutations_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `store_settings`
--
ALTER TABLE `store_settings`
  ADD CONSTRAINT `store_settings_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `store_settings_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

--
-- Table structure for table `stock_mutations`
--

CREATE TABLE `stock_mutations` (
  `id` varchar(36) NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `type` enum('purchase','sale','adjustment','return') NOT NULL,
  `quantity` int(11) NOT NULL,
  `before_stock` int(11) NOT NULL,
  `after_stock` int(11) NOT NULL,
  `reference_id` varchar(36) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` varchar(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `stock_mutations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_mutations_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_mutations`
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
