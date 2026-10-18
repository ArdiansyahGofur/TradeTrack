-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 18, 2026 at 04:30 AM
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
-- Database: `tradetrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `id` bigint UNSIGNED NOT NULL,
  `penjualan_id` bigint UNSIGNED NOT NULL,
  `menu_id` bigint UNSIGNED NOT NULL,
  `harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `jumlah` int UNSIGNED NOT NULL DEFAULT '1',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`id`, `penjualan_id`, `menu_id`, `harga`, `jumlah`, `subtotal`, `created_at`) VALUES
(8, 6, 1, '15000.00', 2, '30000.00', '2026-10-07 00:42:27'),
(9, 6, 2, '5000.00', 2, '10000.00', '2026-10-07 00:42:27'),
(10, 7, 6, '18000.00', 1, '18000.00', '2026-10-07 00:42:44'),
(11, 7, 7, '7000.00', 1, '7000.00', '2026-10-07 00:42:44'),
(12, 8, 5, '14000.00', 2, '28000.00', '2026-10-07 00:43:04'),
(13, 8, 2, '5000.00', 1, '5000.00', '2026-10-07 00:43:04'),
(14, 9, 8, '8000.00', 1, '8000.00', '2026-10-07 00:43:27'),
(15, 9, 1, '15000.00', 1, '15000.00', '2026-10-07 00:43:27'),
(16, 10, 9, '12000.00', 2, '24000.00', '2026-10-07 00:46:02'),
(17, 11, 11, '15000.00', 1, '15000.00', '2026-10-07 00:46:17'),
(18, 11, 14, '12000.00', 1, '12000.00', '2026-10-07 00:46:17'),
(19, 12, 13, '10000.00', 2, '20000.00', '2026-10-07 00:46:30'),
(20, 12, 10, '8000.00', 1, '8000.00', '2026-10-07 00:46:30'),
(21, 13, 12, '13000.00', 1, '13000.00', '2026-10-07 00:46:46'),
(22, 13, 9, '12000.00', 1, '12000.00', '2026-10-07 00:46:46'),
(23, 14, 15, '12000.00', 1, '12000.00', '2026-10-07 00:48:58'),
(24, 14, 20, '5000.00', 1, '5000.00', '2026-10-07 00:48:58'),
(31, 19, 21, '15000.00', 2, '30000.00', '2026-10-07 00:51:54'),
(32, 20, 22, '18000.00', 1, '18000.00', '2026-10-07 00:52:13'),
(33, 20, 26, '16000.00', 1, '16000.00', '2026-10-07 00:52:13'),
(34, 21, 25, '8000.00', 2, '16000.00', '2026-10-07 00:52:26'),
(35, 21, 23, '10000.00', 1, '10000.00', '2026-10-07 00:52:26'),
(36, 22, 21, '15000.00', 1, '15000.00', '2026-10-07 00:52:38'),
(37, 22, 24, '14000.00', 1, '14000.00', '2026-10-07 00:52:38'),
(38, 23, 4, '15000.00', 2, '30000.00', '2026-10-07 00:55:23'),
(39, 24, 3, '15000.00', 1, '15000.00', '2026-10-07 00:55:38'),
(40, 24, 31, '5000.00', 1, '5000.00', '2026-10-07 00:55:38'),
(41, 25, 27, '18000.00', 1, '18000.00', '2026-10-07 00:55:50'),
(42, 25, 32, '7000.00', 1, '7000.00', '2026-10-07 00:55:50'),
(43, 26, 29, '20000.00', 1, '20000.00', '2026-10-07 00:56:00'),
(44, 27, 16, '15000.00', 2, '30000.00', '2026-10-07 00:58:36'),
(45, 28, 18, '8000.00', 2, '16000.00', '2026-10-07 00:58:50'),
(46, 28, 20, '5000.00', 1, '5000.00', '2026-10-07 00:58:50'),
(47, 29, 17, '20000.00', 1, '20000.00', '2026-10-07 00:58:59'),
(48, 29, 19, '10000.00', 1, '10000.00', '2026-10-07 00:58:59'),
(49, 30, 1, '15000.00', 1, '15000.00', '2026-10-09 08:39:33'),
(50, 30, 2, '5000.00', 1, '5000.00', '2026-10-09 08:39:33'),
(51, 30, 6, '18000.00', 2, '36000.00', '2026-10-09 08:39:33'),
(52, 30, 5, '14000.00', 1, '14000.00', '2026-10-09 08:39:33'),
(53, 30, 7, '7000.00', 1, '7000.00', '2026-10-09 08:39:33'),
(54, 30, 8, '8000.00', 1, '8000.00', '2026-10-09 08:39:33'),
(55, 30, 2, '5000.00', 1, '5000.00', '2026-10-09 08:39:33');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Makanan', 'aktif', '2026-10-02 03:55:12', '2026-10-02 03:55:12'),
(2, 'Minuman', 'aktif', '2026-10-02 03:55:12', '2026-10-02 03:55:12'),
(3, 'Snack', 'aktif', '2026-10-02 03:55:12', '2026-10-02 03:55:12'),
(4, 'Dessert', 'aktif', '2026-10-02 03:55:12', '2026-10-02 03:55:12'),
(5, 'Lainnya', 'aktif', '2026-10-02 03:55:12', '2026-10-02 03:55:12'),
(6, 'Kopi', 'aktif', '2026-10-02 04:52:26', '2026-10-02 04:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `kategori_id` bigint UNSIGNED NOT NULL,
  `nama_menu` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `user_id`, `kategori_id`, `nama_menu`, `harga`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Nasi Goreng', '15000.00', 'aktif', '2026-10-02 09:25:44', '2026-10-02 09:25:44'),
(2, 2, 2, 'Es Teh', '5000.00', 'aktif', '2026-10-02 09:48:50', '2026-10-07 00:38:38'),
(3, 3, 1, 'Mie Ayam', '15000.00', 'aktif', '2026-10-06 00:43:24', '2026-10-07 00:54:06'),
(4, 3, 1, 'Bakso Biasa', '15000.00', 'aktif', '2026-10-06 00:43:36', '2026-10-07 00:55:15'),
(5, 2, 1, 'Mie Goreng', '14000.00', 'aktif', '2026-10-06 07:42:51', '2026-10-07 00:38:28'),
(6, 2, 1, 'Ayam Geprek', '18000.00', 'aktif', '2026-10-07 00:38:51', '2026-10-07 00:38:51'),
(7, 2, 2, 'Es Jeruk', '7000.00', 'aktif', '2026-10-07 00:39:09', '2026-10-07 00:39:09'),
(8, 2, 3, 'Pisang Goreng', '8000.00', 'aktif', '2026-10-07 00:39:21', '2026-10-07 00:39:21'),
(9, 4, 2, 'Es Kopi Susu', '12000.00', 'aktif', '2026-10-07 00:44:23', '2026-10-07 00:44:23'),
(10, 4, 2, 'Kopi Hitam', '8000.00', 'aktif', '2026-10-07 00:44:35', '2026-10-07 00:44:35'),
(11, 4, 2, 'Matcha Latte', '15000.00', 'aktif', '2026-10-07 00:44:54', '2026-10-07 00:44:54'),
(12, 4, 2, 'Cokelat', '13000.00', 'aktif', '2026-10-07 00:45:09', '2026-10-07 00:45:09'),
(13, 4, 2, 'Lemon Tea', '10000.00', 'aktif', '2026-10-07 00:45:21', '2026-10-07 00:45:21'),
(14, 4, 3, 'Roti Bakar Cokelat', '12000.00', 'aktif', '2026-10-07 00:45:39', '2026-10-07 00:45:39'),
(15, 5, 1, 'Seblak Original', '12000.00', 'aktif', '2026-10-07 00:47:36', '2026-10-07 00:47:36'),
(16, 5, 1, 'Seblak Ceker', '15000.00', 'aktif', '2026-10-07 00:47:48', '2026-10-07 00:47:48'),
(17, 5, 1, 'Seblak Komplit', '20000.00', 'aktif', '2026-10-07 00:48:04', '2026-10-07 00:48:04'),
(18, 5, 3, 'Cireng', '8000.00', 'aktif', '2026-10-07 00:48:15', '2026-10-07 00:48:15'),
(19, 5, 3, 'Kentang Goreng', '10000.00', 'aktif', '2026-10-07 00:48:28', '2026-10-07 00:48:28'),
(20, 5, 2, 'Es Teh', '5000.00', 'aktif', '2026-10-07 00:48:38', '2026-10-07 00:48:38'),
(21, 6, 4, 'Brownies', '15000.00', 'aktif', '2026-10-07 00:50:05', '2026-10-07 00:50:05'),
(22, 6, 4, 'Cheesecake', '18000.00', 'aktif', '2026-10-07 00:50:19', '2026-10-07 00:50:19'),
(23, 6, 4, 'Pudding Cokelat', '10000.00', 'aktif', '2026-10-07 00:50:32', '2026-10-07 00:50:32'),
(24, 6, 3, 'Croissant', '14000.00', 'aktif', '2026-10-07 00:50:47', '2026-10-07 00:50:47'),
(25, 6, 3, 'Donat Cokelat', '8000.00', 'aktif', '2026-10-07 00:51:00', '2026-10-07 00:51:00'),
(26, 6, 2, 'Milkshake Vanilla', '16000.00', 'aktif', '2026-10-07 00:51:12', '2026-10-07 00:51:12'),
(27, 3, 1, 'Bakso Urat', '18000.00', 'aktif', '2026-10-07 00:53:50', '2026-10-07 00:53:50'),
(28, 3, 1, 'Bakso Komplit', '22000.00', 'aktif', '2026-10-07 00:53:59', '2026-10-07 00:53:59'),
(29, 3, 1, 'Mie Ayam Bakso', '20000.00', 'aktif', '2026-10-07 00:54:16', '2026-10-07 00:54:16'),
(30, 3, 1, 'Mie Ayam Ceker', '18000.00', 'aktif', '2026-10-07 00:54:28', '2026-10-07 00:54:28'),
(31, 3, 2, 'Es Teh', '5000.00', 'aktif', '2026-10-07 00:54:38', '2026-10-07 00:54:38'),
(32, 3, 2, 'Es Jeruk', '7000.00', 'aktif', '2026-10-07 00:54:50', '2026-10-07 00:54:50');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id`, `user_id`, `tanggal`, `total`, `catatan`, `created_at`, `updated_at`) VALUES
(6, 2, '2026-10-01 00:00:00', '40000.00', NULL, '2026-10-07 00:42:27', '2026-10-07 00:42:27'),
(7, 2, '2026-10-01 00:00:00', '25000.00', NULL, '2026-10-07 00:42:44', '2026-10-07 00:42:44'),
(8, 2, '2026-10-01 00:00:00', '33000.00', NULL, '2026-10-07 00:43:04', '2026-10-07 00:43:04'),
(9, 2, '2026-10-01 00:00:00', '23000.00', NULL, '2026-10-07 00:43:27', '2026-10-07 00:43:27'),
(10, 4, '2026-10-01 00:00:00', '24000.00', NULL, '2026-10-07 00:46:02', '2026-10-07 00:46:02'),
(11, 4, '2026-10-01 00:00:00', '27000.00', NULL, '2026-10-07 00:46:17', '2026-10-07 00:46:17'),
(12, 4, '2026-10-01 00:00:00', '28000.00', NULL, '2026-10-07 00:46:30', '2026-10-07 00:46:30'),
(13, 4, '2026-10-01 00:00:00', '25000.00', NULL, '2026-10-07 00:46:46', '2026-10-07 00:46:46'),
(14, 5, '2026-10-01 00:00:00', '17000.00', NULL, '2026-10-07 00:48:58', '2026-10-07 00:48:58'),
(19, 6, '2026-10-01 00:00:00', '30000.00', NULL, '2026-10-07 00:51:54', '2026-10-07 00:51:54'),
(20, 6, '2026-10-01 00:00:00', '34000.00', NULL, '2026-10-07 00:52:13', '2026-10-07 00:52:13'),
(21, 6, '2026-10-01 00:00:00', '26000.00', NULL, '2026-10-07 00:52:26', '2026-10-07 00:52:26'),
(22, 6, '2026-10-01 00:00:00', '29000.00', NULL, '2026-10-07 00:52:38', '2026-10-07 00:52:38'),
(23, 3, '2026-10-01 00:00:00', '30000.00', NULL, '2026-10-07 00:55:23', '2026-10-07 00:55:23'),
(24, 3, '2026-10-01 00:00:00', '20000.00', NULL, '2026-10-07 00:55:38', '2026-10-07 00:55:38'),
(25, 3, '2026-10-01 00:00:00', '25000.00', NULL, '2026-10-07 00:55:50', '2026-10-07 00:55:50'),
(26, 3, '2026-10-01 00:00:00', '20000.00', NULL, '2026-10-07 00:56:00', '2026-10-07 00:56:00'),
(27, 5, '2026-10-01 00:00:00', '30000.00', NULL, '2026-10-07 00:58:36', '2026-10-07 00:58:36'),
(28, 5, '2026-10-01 00:00:00', '21000.00', NULL, '2026-10-07 00:58:50', '2026-10-07 00:58:50'),
(29, 5, '2026-10-01 00:00:00', '30000.00', NULL, '2026-10-07 00:58:59', '2026-10-07 00:58:59'),
(30, 2, '2026-10-02 00:00:00', '90000.00', NULL, '2026-10-09 08:39:33', '2026-10-09 08:39:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','pedagang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pedagang',
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `no_hp`, `alamat`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', '$2y$10$sljELHAQcECgWy9vK1Dpa.JhIZhx5Eqt3tima6wqHXTmdzM6FBRS6', 'admin', NULL, NULL, 'aktif', '2026-10-02 03:54:02', '2026-10-02 03:54:43'),
(2, 'Budi Santoso', 'budi', '$2y$10$qDRU42uFLl3f3rnIyZ9VpuO0EkeH3SsXgjErjRFjLk4ima8ncN2n.', 'pedagang', '081234567890', 'Malang', 'aktif', '2026-10-02 04:25:03', '2026-10-02 04:47:24'),
(3, 'Syamsul Arifin', 'Syamsul', '$2y$10$5nuz6YzoTss1zuNrHSYwru4yuoiO/ZYNhcccmGb0FziyzSuzF6d5m', 'pedagang', '081809876543', 'Bangkalan', 'aktif', '2026-10-06 00:42:18', '2026-10-06 00:42:18'),
(4, 'Siti Aminah', 'siti', '$2y$10$hT9vEvB7SVwbalvYdVhCt.FG1A1KknwrXL5TFeH8kXWM24qfgEBs2', 'pedagang', '081234567891', 'Jl. Mawar No. 10', 'aktif', '2026-10-07 00:31:55', '2026-10-07 00:31:55'),
(5, 'Andi Pratama', 'andi', '$2y$10$Esw6WOhO2ICdRI6mwTBXW./EjRKBYsYmfIIvbsB5ReWaI594vynV.', 'pedagang', '081234567892', 'Jl. Melati No. 15', 'aktif', '2026-10-07 00:32:24', '2026-10-07 00:32:24'),
(6, 'Rina Wulandari', 'rina', '$2y$10$24WfHoWr1VA9u7bJkmcF3Oknt1F/iUrjjhHmhd76NM.WibhrOfzYm', 'pedagang', '081234567893', 'Jl. Kenanga No. 20', 'aktif', '2026-10-07 00:32:52', '2026-10-07 00:32:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detail_penjualan` (`penjualan_id`),
  ADD KEY `fk_detail_menu` (`menu_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_menu_user` (`user_id`),
  ADD KEY `fk_menu_kategori` (`kategori_id`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_penjualan_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `fk_detail_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_penjualan` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `fk_menu_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_menu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `fk_penjualan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
