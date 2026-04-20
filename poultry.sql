-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 04:04 AM
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
-- Database: `poultry`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pic` varchar(255) DEFAULT 'admin.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `pic`, `created_at`) VALUES
(1, 'Castillano', 'admin@gmail.com', '$2y$10$Tn5tAnReq2R5bxSOwdwRlOi4KuLCJWWQNHjzXMQZTSsBESWniWgj2', 'admin_1.jpg', '2025-09-09 19:39:33');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pic` varchar(255) DEFAULT 'customer.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `carrier` enum('globe','tnt','smart','tm') NOT NULL DEFAULT 'tnt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `name`, `email`, `password`, `pic`, `created_at`, `phone`, `address`, `carrier`) VALUES
(4, 'Eddyza Marie Khar Carino', 'eddyza@gmail.com', '$2y$10$2Twvo5Q9Hq5EuwnRJR7rY.VUirN4UM2naf6EozNCSSL7Fv9UKKt6i', 'customer.png', '2025-09-14 13:53:07', '09635354999', 'asdadasdasdasdas', 'tnt'),
(5, 'vage andrea jayme', 'maea9658@gmail.com', '$2y$10$legLqHjAi8etLtWQhAmd8OQJFhJ4yGZvYyTE35Ln3Qf.Ttxd5GuKe', 'customer_5.png', '2025-09-26 13:08:26', '12334567789900', 'iloilo city', 'tnt'),
(6, 'james', 'james@gmail.com', '$2y$10$sIFSBxR2.mGXXo02c0ba2uj8PpH8TvsXsxU5Pwmescz87zpUbiToa', 'customer_6.jpeg', '2025-10-25 06:01:55', '9539618441', 'iloilo city', 'tnt'),
(7, 'kenth axell castilllano', 'kenth@gmail.com', '$2y$10$mMV5pN48Epu96fmYQZ/Cg.b.LgpVJneCe.R9uOM7CnBxAof/xY.W2', 'customer.png', '2025-10-28 06:13:11', NULL, NULL, 'tnt'),
(8, 'kenth gicole', 'kenth312004@gmail.com', '$2y$10$uQlvjyvKoNAJl/dMsptTUeTdYlZj64OHNzKx9rVBt9voo0bJCtkbq', 'customer.png', '2025-10-29 01:29:43', NULL, NULL, 'tnt'),
(9, 'axell ko', 'axell2004@gmail.com', '$2y$10$X/MYwYKn4Zf5kt71NsY3qeuKJwFVDCaGhFG7aKLUtTpqI/ez/HN.K', 'customer.png', '2025-10-29 01:31:02', NULL, NULL, 'tnt'),
(10, 'kenth axell castilllano', 'kenth2004@gmail.com', '$2y$10$B91NTkzi6EmLfZw3I9eBkeovF465cpSeJswha3yrL.kHDXPreOUXK', 'customer.png', '2025-10-29 02:41:17', NULL, NULL, 'tnt'),
(11, 'Gloria Subosa', 'gloria@gmail.com', '$2y$10$/cUtPebe4Pttx6r1STKCOOBpYtM8TyHS/xlnlXrEiGPWts8yySh/e', 'customer.png', '2025-10-29 02:42:15', '09071493398', 'Iloilo city', 'tnt');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `status` enum('Pending','Processing','Delivering','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `delivery_method` varchar(20) NOT NULL DEFAULT 'Pick Up'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `product_id`, `quantity`, `status`, `created_at`, `updated_at`, `delivery_method`) VALUES
(71, 2, 47, 1, 'Completed', '2025-09-16 02:12:47', '2025-09-16 02:15:00', 'Pick Up'),
(72, 2, 47, 7, 'Completed', '2025-09-18 23:43:32', '2025-09-18 23:44:06', 'Pick Up'),
(73, 2, 46, 1, 'Completed', '2025-09-18 23:58:31', '2025-09-18 23:59:06', 'Pick Up'),
(74, 2, 45, 1, 'Completed', '2025-09-18 23:58:31', '2025-09-19 00:00:58', 'Pick Up'),
(75, 5, 49, 1, 'Completed', '2025-10-01 22:29:31', '2025-10-01 22:59:37', 'Pick Up'),
(76, 5, 49, 2, 'Cancelled', '2025-10-02 11:44:28', '2025-10-02 11:48:30', 'Pick Up'),
(77, 5, 49, 2, 'Processing', '2025-10-02 19:16:42', '2025-10-02 19:19:16', 'Pick Up'),
(78, 5, 49, 3, 'Completed', '2025-10-02 19:17:00', '2025-10-02 19:19:30', 'Pick Up'),
(79, 5, 47, 1, 'Completed', '2025-10-25 13:57:34', '2025-10-25 14:06:38', 'Pick Up'),
(81, 5, 50, 2, 'Processing', '2025-10-25 13:57:34', '2025-10-25 13:58:58', 'Pick Up'),
(82, 6, 46, 1, 'Completed', '2025-10-25 14:03:35', '2025-10-25 14:06:13', 'Pick Up'),
(83, 6, 43, 1, 'Cancelled', '2025-10-25 14:03:35', '2025-10-25 14:05:27', 'Pick Up'),
(84, 5, 41, 8, 'Completed', '2025-10-28 16:00:11', '2025-10-28 16:02:33', 'Pick Up'),
(85, 5, 50, 6, 'Processing', '2025-10-28 22:41:55', '2025-11-24 00:15:09', 'Pick Up'),
(86, 5, 46, 1, 'Processing', '2025-10-28 23:01:37', '2025-10-28 23:28:59', 'Delivery'),
(87, 5, 50, 1, 'Completed', '2025-10-28 23:37:40', '2025-11-24 00:17:26', 'Delivery'),
(88, 11, 51, 1, 'Completed', '2025-10-29 10:45:05', '2025-10-29 10:48:33', 'Pick Up');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_stock` int(11) NOT NULL DEFAULT 0,
  `reduced_stock` int(11) NOT NULL DEFAULT 0,
  `is_unavailable` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `stock`, `picture`, `created_at`, `added_stock`, `reduced_stock`, `is_unavailable`) VALUES
(41, 'Fattening (per kilo)', 250.00, 'Well-nurtured live pigs offering excellent growth potential and reliability for professional livestock producers.', 100, 'product_1760462681.jpeg', '2025-09-15 18:03:02', 12, 10, 0),
(43, 'Chicken', 220.00, 'Fresh Live Chickens – Perfect for Your Farm or Backyard!\r\nHealthy, lively chickens delivered straight to you. Choose from various breeds and sizes. Order now and enjoy Backyard-fresh quality!', 200, 'product_1760460242.jpg', '2025-09-15 18:03:42', 10, 20, 0),
(44, 'Mother Pig (Sow)', 200.00, 'Healthy Live Sows – Quality Breeding Stock!\r\nStrong, well-raised sows available for your farm. Improve your herd with proven genetics. Order now for reliable livestock!', 100, 'product_1760460401.jpg', '2025-09-15 18:03:56', 100, 0, 0),
(45, 'Boar', 200.00, 'Premium Live Pigs– Strong Genetics for Your Herd!\r\nHealthy, high-quality boars ready to enhance your breeding program. Secure top genetics today!', 100, 'product_1760460718.jpg', '2025-09-15 18:04:15', 0, 9, 0),
(46, 'Pigglet', 6000.00, 'Healthy Live Piglets – Perfect for Your Farm or Homestead!\r\nLively, well-raised piglets delivered right to your door. Choose quality breeds and start your Business Today! ', 100, 'product_1760460944.jpg', '2025-09-15 18:04:30', 0, 0, 0),
(47, 'Egg (Small)', 240.00, '\"Crack Into Quality – Buy Eggs in All Sizes: Small, Medium, Large, XL!', 100, 'product_1760461124.jpg', '2025-09-15 18:06:10', 0, 22, 0),
(48, 'Egg(Meduim)', 270.00, '\"Crack Into Quality – Buy Eggs in All Sizes: Small, Medium, Large, XL!', 100, 'product_1760461234.jpg', '2025-09-18 18:17:03', 0, 0, 0),
(49, 'Egg(Large)', 300.00, '\"Crack Into Quality – Buy Eggs in All Sizes: Small, Medium, Large, XL!', 100, 'product_1760462820.jpg', '2025-09-18 18:17:16', 0, 0, 0),
(50, 'Artificial Insemination (AI)', 1700.00, 'Artificial Insemination Services – Boost Your Herd’s Genetics!\r\nReliable and efficient artificial insemination solutions to improve fertility and genetic quality. Contact us today to enhance your breeding program!', 100, 'product_1760462655.png', '2025-09-18 18:17:35', 10, 99, 0),
(51, 'Artificial Insemination (AI) Services', 1500.00, 'We provide professional Artificial Insemination (AI) services for pigs, handled directly by our trained staff. Our team carefully performs the insemination process by inserting high-quality boar semen into the sow, ensuring safe, hygienic, and effective b', 100, 'product_1761632570.jpg', '2025-10-28 06:22:50', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_cart`
--

CREATE TABLE `user_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_cart`
--

INSERT INTO `user_cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(111, 5, 49, 2, '2025-10-28 07:57:13'),
(112, 5, 48, 1, '2025-10-28 07:57:16'),
(117, 11, 43, 1, '2025-10-29 02:43:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_idx` (`customer_id`),
  ADD KEY `product_idx` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_cart`
--
ALTER TABLE `user_cart`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `user_cart`
--
ALTER TABLE `user_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
