-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2025 at 01:00 AM
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
-- Database: `m_commerce_db`
--
CREATE DATABASE IF NOT EXISTS `m_commerce_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `m_commerce_db`;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(2083) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 100,
  `rating` decimal(3,2) NOT NULL DEFAULT 4.50
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `stock`, `rating`) VALUES
(1, 'Smartphone X1', 'A flagship smartphone with 128GB storage and a 108MP camera. 6.7 inch AMOLED display.', '799.99', 'https://placehold.co/600x400/333/fff?text=Smartphone+X1', 100, 4.70),
(2, 'Noise-Canceling Headphones', 'Wireless over-ear headphones with 40-hour battery life and spatial audio.', '349.99', 'https://placehold.co/600x400/444/fff?text=Headphones', 100, 4.80),
(3, 'Smartwatch S2', 'A water-resistant smartwatch with heart rate, SpO2, and sleep tracking. 1.9-inch display.', '249.50', 'https://placehold.co/600x400/555/fff?text=Smartwatch+S2', 100, 4.50),
(4, '4K Ultra HD Smart TV', '55-inch QLED Smart TV with 4K resolution, HDR10+, and built-in streaming apps.', '699.00', 'https://placehold.co/600x400/666/fff?text=Smart+TV', 100, 4.60),
(5, 'Pro Gaming Laptop', '16-inch gaming laptop with 165Hz display, 16GB RAM, 1TB SSD, and a high-end graphics card.', '1499.99', 'https://placehold.co/600x400/2a2a2a/fff?text=Gaming+Laptop', 100, 4.90),
(6, 'Wireless Earbuds TWS', 'True wireless earbuds with active noise-cancellation, 8-hour battery, and a wireless charging case.', '199.99', 'https://placehold.co/600x400/3a3a3a/fff?text=Wireless+Earbuds', 100, 4.40),
(7, 'Ultra-Slim Tablet', '11-inch tablet with a high-resolution liquid retina display, powerful processor, and stylus support.', '599.00', 'https://placehold.co/600x400/4a4a4a/fff?text=Slim+Tablet', 100, 4.70),
(8, '4K Action Camera', 'Waterproof action camera that shoots 4K video at 60fps, with advanced image stabilization.', '399.99', 'https://placehold.co/600x400/5a5a5a/fff?text=Action+Camera', 100, 4.60),
(9, 'Smart Home Hub', 'Voice-controlled smart speaker with a built-in display to manage all your smart home devices.', '129.00', 'https://placehold.co/600x400/6a6a6a/fff?text=Smart+Hub', 100, 4.30),
(10, 'VR Headset Pro', 'All-in-one virtual reality headset with 5K resolution, 120Hz refresh rate, and inside-out tracking.', '799.00', 'https://placehold.co/600x400/7a7a7a/fff?text=VR+Headset', 100, 4.80),
(11, 'Portable SSD 2TB', 'A super-fast, rugged portable 2TB SSD with read/write speeds up to 1050MB/s.', '229.99', 'https://placehold.co/600x400/8a8a8a/fff?text=Portable+SSD', 100, 4.90),
(12, 'GPS Drone with 4K Camera', 'A foldable GPS drone with a 3-axis gimbal camera, 4K video, and a 30-minute flight time.', '549.00', 'https://placehold.co/600x400/9a9a9a/fff?text=4K+Drone', 100, 4.70);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

