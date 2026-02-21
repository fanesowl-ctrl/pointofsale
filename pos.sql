-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 20, 2026 at 02:21 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `kasir`
--

CREATE TABLE `kasir` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kasir`
--

INSERT INTO `kasir` (`id`, `name`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'juan', 'juan@gmail.com', 'juan123456', '2026-01-07 02:32:51', '2026-01-24 01:16:21'),
(3, 'faness', 'kasir2', 'kasir123', '2026-01-25 05:46:26', '2026-01-25 06:19:42');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_harian`
--

CREATE TABLE `laporan_harian` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `total_transaksi` int NOT NULL DEFAULT '0',
  `barang_terjual` int NOT NULL DEFAULT '0',
  `total_penjualan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `laporan_harian`
--

INSERT INTO `laporan_harian` (`id`, `tanggal`, `total_transaksi`, `barang_terjual`, `total_penjualan`, `created_at`, `updated_at`) VALUES
(8, '2026-01-26', 2, 5, '320000.00', '2026-01-26 04:33:40', '2026-01-26 04:34:33'),
(9, '2026-01-27', 1, 5, '175000.00', '2026-01-27 09:51:24', '2026-01-27 09:51:24'),
(10, '2026-02-04', 5, 5, '288000.00', '2026-02-03 22:28:03', '2026-02-03 22:28:03'),
(11, '2026-02-14', 1, 12, '420000.00', '2026-02-15 04:27:12', '2026-02-15 04:27:12'),
(12, '2026-02-15', 1, 3, '130000.00', '2026-02-15 04:27:12', '2026-02-15 04:27:12');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `code`, `name`, `phone`, `discount_percentage`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'MBR-456483', 'nonenk', '08218331824', '20.00', 1, '2026-02-12 06:02:14', '2026-02-12 06:02:14');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_reports`
--

CREATE TABLE `monthly_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `month` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Format YYYY-MM',
  `sold_items` int NOT NULL DEFAULT '0',
  `total_sales` decimal(15,2) NOT NULL DEFAULT '0.00',
  `profit_loss` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_settings`
--

CREATE TABLE `payment_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment_settings`
--

INSERT INTO `payment_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(3, 'qris_image', 'payment_images/ybu12HoiRzNGiHGKm3b7hKjENWz2VQlrC11llHfZ.jpg', NULL, '2026-01-25 19:13:53');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_price` decimal(15,2) NOT NULL,
  `selling_price` decimal(15,2) NOT NULL,
  `discount_percentage` decimal(5,2) DEFAULT '0.00',
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `final_price` decimal(15,2) DEFAULT '0.00',
  `discount_stock` int DEFAULT NULL COMMENT 'Kuota Stok Promo',
  `discount_start` datetime DEFAULT NULL COMMENT 'Waktu mulai diskon',
  `discount_end` datetime DEFAULT NULL COMMENT 'Waktu berakhir diskon',
  `stock` int NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_code`, `name`, `category`, `cost_price`, `selling_price`, `discount_percentage`, `discount_amount`, `final_price`, `discount_stock`, `discount_start`, `discount_end`, `stock`, `image`, `created_at`, `updated_at`) VALUES
(1, 'a5', 'popok bayi', NULL, '50000.00', '60000.00', '0.00', '0.00', '60000.00', NULL, NULL, NULL, 30, 'products/1769405427_popok.jfif', '2026-01-25 21:30:27', '2026-02-15 04:27:03'),
(2, 'a1', 'berass', NULL, '60000.00', '70000.00', '0.00', '0.00', '70000.00', NULL, NULL, NULL, 46, 'products/1769405638_berass.jfif', '2026-01-25 21:33:58', '2026-02-14 07:03:24'),
(3, 'a3', 'Galon Le Minerale', NULL, '30000.00', '35000.00', '0.00', '0.00', '35000.00', NULL, NULL, NULL, 15, 'products/1769480698_galon.jfif', '2026-01-26 18:24:58', '2026-02-15 04:27:03'),
(4, 'a2', 'rokok marlboro', NULL, '50000.00', '53000.00', '0.00', '0.00', '53000.00', NULL, NULL, NULL, 34, 'products/1769482147_marlboro.jfif', '2026-01-26 18:49:07', '2026-02-15 04:25:04'),
(10, 'a6', 'aqua', 'minuman', '6000.00', '8000.00', '0.00', '0.00', '8000.00', NULL, NULL, NULL, 27, 'products/1770985305_download.jpeg', '2026-02-13 04:21:46', '2026-02-13 04:21:46');

-- --------------------------------------------------------

--
-- Table structure for table `stock_batches`
--

CREATE TABLE `stock_batches` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL COMMENT 'Sisa stok',
  `original_quantity` int NOT NULL COMMENT 'Stok awal',
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `received_at` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `batch_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stock_batches`
--

INSERT INTO `stock_batches` (`id`, `product_id`, `quantity`, `original_quantity`, `cost_price`, `received_at`, `expiry_date`, `batch_code`, `created_at`, `updated_at`) VALUES
(1, 1, 30, 31, '50000.00', '2026-02-08', NULL, 'INITIAL-STOCK-20260209', '2026-02-08 23:40:39', '2026-02-15 04:27:03'),
(2, 2, 36, 36, '60000.00', '2026-02-08', NULL, 'INITIAL-STOCK-20260209', '2026-02-08 23:40:39', '2026-02-08 23:40:39'),
(3, 3, 5, 19, '30000.00', '2026-02-08', NULL, 'INITIAL-STOCK-20260209', '2026-02-08 23:40:39', '2026-02-15 04:27:03'),
(4, 4, 34, 34, '50000.00', '2026-02-08', NULL, 'INITIAL-STOCK-20260209', '2026-02-08 23:40:39', '2026-02-08 23:40:39'),
(5, 2, 10, 10, '75000.00', '2026-02-09', NULL, 'a1', '2026-02-08 23:49:56', '2026-02-08 23:49:56'),
(6, 3, 10, 10, '30000.00', '2026-02-15', NULL, 'INITIAL-STOCK-20260209', '2026-02-14 21:23:03', '2026-02-14 21:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` bigint UNSIGNED NOT NULL,
  `nomor_transaksi` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `sub_total` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('cash','qris') NOT NULL DEFAULT 'cash',
  `kasir_name` varchar(255) DEFAULT NULL,
  `member_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `nomor_transaksi`, `tanggal`, `sub_total`, `discount_amount`, `grand_total`, `payment_method`, `kasir_name`, `member_id`, `created_at`, `updated_at`) VALUES
(13, 'TRX-1769430817-7JuX', '2026-01-26', '140000.00', '0.00', '0.00', 'cash', 'juan', NULL, '2026-01-26 04:33:37', '2026-01-26 04:33:37'),
(14, 'TRX-1769430869-3FEZ', '2026-01-26', '180000.00', '0.00', '0.00', 'cash', 'juan', NULL, '2026-01-26 04:34:29', '2026-01-26 04:34:29'),
(15, 'TRX-1769480740-jlGl', '2026-01-27', '175000.00', '0.00', '0.00', 'cash', 'juan', NULL, '2026-01-26 18:25:40', '2026-01-26 18:25:40'),
(16, 'TRX-1770186373-p2m6', '2026-02-04', '60000.00', '0.00', '0.00', 'cash', 'juan', NULL, '2026-02-03 22:26:13', '2026-02-03 22:26:13'),
(17, 'TRX-1770186395-qBm8', '2026-02-04', '70000.00', '0.00', '0.00', 'cash', 'juan', NULL, '2026-02-03 22:26:35', '2026-02-03 22:26:35'),
(18, 'TRX-1770186418-MFvB', '2026-02-04', '53000.00', '0.00', '0.00', 'cash', 'juan', NULL, '2026-02-03 22:26:58', '2026-02-03 22:26:58'),
(19, 'TRX-1770186436-SAwI', '2026-02-04', '35000.00', '0.00', '0.00', 'cash', 'juan', NULL, '2026-02-03 22:27:16', '2026-02-03 22:27:16'),
(20, 'TRX-1770186462-SZqF', '2026-02-04', '70000.00', '0.00', '0.00', 'cash', 'juan', NULL, '2026-02-03 22:27:42', '2026-02-03 22:27:42'),
(21, 'TRX-1771080936-2J49', '2026-02-14', '420000.00', '0.00', '420000.00', 'cash', 'juan', NULL, '2026-02-14 06:55:36', '2026-02-14 06:55:36'),
(22, 'TRX-1771158423-DWvx', '2026-02-15', '130000.00', '26000.00', '104000.00', 'qris', 'juan', 1, '2026-02-15 04:27:03', '2026-02-15 04:27:03');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_details`
--

CREATE TABLE `transaksi_details` (
  `id` bigint UNSIGNED NOT NULL,
  `transaksi_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaksi_details`
--

INSERT INTO `transaksi_details` (`id`, `transaksi_id`, `product_id`, `quantity`, `price`, `total`, `created_at`, `updated_at`) VALUES
(10, 13, 2, 2, '70000.00', '140000.00', '2026-01-26 04:33:37', '2026-01-26 04:33:37'),
(11, 14, 1, 3, '60000.00', '180000.00', '2026-01-26 04:34:29', '2026-01-26 04:34:29'),
(12, 15, 3, 5, '35000.00', '175000.00', '2026-01-26 18:25:40', '2026-01-26 18:25:40'),
(13, 16, 1, 1, '60000.00', '60000.00', '2026-02-03 22:26:13', '2026-02-03 22:26:13'),
(14, 17, 2, 1, '70000.00', '70000.00', '2026-02-03 22:26:35', '2026-02-03 22:26:35'),
(15, 18, 4, 1, '53000.00', '53000.00', '2026-02-03 22:26:58', '2026-02-03 22:26:58'),
(16, 19, 3, 1, '35000.00', '35000.00', '2026-02-03 22:27:16', '2026-02-03 22:27:16'),
(17, 20, 2, 1, '70000.00', '70000.00', '2026-02-03 22:27:42', '2026-02-03 22:27:42'),
(18, 21, 3, 12, '35000.00', '420000.00', '2026-02-14 06:55:36', '2026-02-14 06:55:36'),
(19, 22, 3, 2, '35000.00', '70000.00', '2026-02-15 04:27:03', '2026-02-15 04:27:03'),
(20, 22, 1, 1, '60000.00', '60000.00', '2026-02-15 04:27:03', '2026-02-15 04:27:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(7, 'jo', 'jo@gmail.com', NULL, 'jo123456', NULL, '2026-01-07 02:53:06', '2026-01-07 02:53:06'),
(8, 'ko', 'ko@gmail.com', NULL, 'ko123456', NULL, '2026-01-07 02:56:03', '2026-01-07 02:56:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kasir`
--
ALTER TABLE `kasir`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kasir_username_unique` (`username`);

--
-- Indexes for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tanggal` (`tanggal`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `members_code_unique` (`code`);

--
-- Indexes for table `monthly_reports`
--
ALTER TABLE `monthly_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_settings`
--
ALTER TABLE `payment_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_settings_setting_key_unique` (`setting_key`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`product_code`);

--
-- Indexes for table `stock_batches`
--
ALTER TABLE `stock_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_batches_product_id_foreign` (`product_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_transaksi` (`nomor_transaksi`),
  ADD KEY `transaksi_member_id_foreign` (`member_id`);

--
-- Indexes for table `transaksi_details`
--
ALTER TABLE `transaksi_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `product_id` (`product_id`);

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
-- AUTO_INCREMENT for table `kasir`
--
ALTER TABLE `kasir`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `monthly_reports`
--
ALTER TABLE `monthly_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_settings`
--
ALTER TABLE `payment_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `stock_batches`
--
ALTER TABLE `stock_batches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `transaksi_details`
--
ALTER TABLE `transaksi_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stock_batches`
--
ALTER TABLE `stock_batches`
  ADD CONSTRAINT `stock_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transaksi_details`
--
ALTER TABLE `transaksi_details`
  ADD CONSTRAINT `transaksi_details_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
