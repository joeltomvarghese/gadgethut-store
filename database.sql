-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1
-- Generation Time: Oct 27, 2025 at 08:15 AM
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
CREATE DATABASE IF NOT EXISTS `m_commerce_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `m_commerce_db`;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(2048) DEFAULT 'https://placehold.co/600x400/cccccc/ffffff?text=Image+Not+Available',
  `stock` int(11) DEFAULT 0,
  `rating` decimal(3,1) DEFAULT 0.0, -- Changed rating precision to 1 decimal place
  `condition` varchar(50) DEFAULT 'Good',
  `usage_duration` varchar(100) DEFAULT NULL,
  `condition_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `stock`, `rating`, `condition`, `usage_duration`, `condition_notes`, `created_at`) VALUES
(1, 'Refurbished iPhone 13 Pro', 'Apple iPhone 13 Pro, 256GB, Sierra Blue - Unlocked. Fully tested and functional.', 899.99, 'https://images.unsplash.com/photo-1633424901929-44b4d6e9385e?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 15, 4.8, 'Pristine', '6 Months', 'Looks and functions like new. Battery health 95%.', '2025-10-25 19:00:00'),
(2, 'Refurbished Galaxy S22 Ultra', 'Samsung Galaxy S22 Ultra, 128GB, Phantom Black - Unlocked. Minor signs of wear.', 749.50, 'https://images.unsplash.com/photo-1644782973909-32d84a51e6b8?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 8, 4.7, 'Very Good', '1 Year', 'Small scratch on the bottom edge, screen is perfect. Battery health 91%.', '2025-10-25 19:00:00'),
(3, 'Refurbished MacBook Air M1', 'Apple MacBook Air (M1, 2020), 8GB RAM, 256GB SSD, Space Gray. Excellent condition.', 799.00, 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 12, 4.9, 'Pristine', '10 Months', 'No visible marks or scratches. Comes with original charger.', '2025-10-25 19:00:00'),
(4, 'Refurbished Sony WH-1000XM4', 'Sony WH-1000XM4 Wireless Noise Cancelling Headphones, Black. Industry-leading ANC.', 229.99, 'https://images.unsplash.com/photo-1604710452393-27c5ae75e6d6?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 25, 4.6, 'Very Good', '3 Months', 'Slight wear on earcups, otherwise perfect working order. Includes case.', '2025-10-25 19:00:00'),
(5, 'Refurbished Dell XPS 15', 'Dell XPS 15 (9510), Intel Core i7, 16GB RAM, 512GB SSD, NVIDIA GeForce RTX 3050 Ti. Minor cosmetic wear.', 1199.00, 'https://images.unsplash.com/photo-1606229365485-93a3c8ee6338?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 5, 4.5, 'Good', '1.5 Years', 'Small dent on lid corner, minor keyboard shine. Screen is flawless.', '2025-10-25 19:00:00'),
(6, 'Refurbished Apple Watch Series 7', 'Apple Watch Series 7 GPS, 45mm, Midnight Aluminum Case with Midnight Sport Band.', 279.00, 'https://images.unsplash.com/photo-1633190130091-62d4e0f491b4?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 18, 4.8, 'Pristine', '4 Months', 'Like new, no scratches. Battery health 98%. Includes charger.', '2025-10-25 19:00:00'),
(7, 'Refurbished Google Pixel 6 Pro', 'Google Pixel 6 Pro - 5G Android Phone - Unlocked Smartphone with Advanced Pixel Camera - 128GB - Stormy Black.', 549.99, 'https://images.unsplash.com/photo-1635817886476-854e4c935407?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 10, 4.6, 'Very Good', '8 Months', 'Micro-scratches on screen, only visible when off. Body is excellent. Battery health 92%.', '2025-10-25 19:00:00'),
(8, 'Refurbished iPad Pro 11-inch (3rd Gen)', 'Apple iPad Pro 11-inch (3rd Generation) Wi-Fi, 128GB - Space Gray.', 629.50, 'https://images.unsplash.com/photo-1587614203976-365c7d669aae?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 7, 4.9, 'Pristine', '1 Year', 'Immaculate condition. Includes original box and charger.', '2025-10-25 19:00:00'),
(9, 'Refurbished Bose QuietComfort 45', 'Bose QuietComfort 45 Bluetooth Wireless Noise Cancelling Headphones - Triple Black', 199.00, 'https://images.unsplash.com/photo-1627916533788-2a13c931e0f0?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 20, 4.7, 'Very Good', '5 Months', 'Minimal signs of use. Comes with case and charging cable.', '2025-10-25 19:00:00'),
(10, 'Refurbished Surface Laptop 4', 'Microsoft Surface Laptop 4 - 13.5\" Touchscreen - Intel Core i5 - 8GB Memory - 512GB SSD - Platinum', 699.00, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 9, 4.4, 'Good', '1 Year', 'Some light scratches on the casing, keyboard and screen are perfect.', '2025-10-25 19:00:00'),
(11, 'Refurbished DJI Mini 2 Drone Combo', 'DJI Mini 2 Fly More Combo – Ultralight Foldable Drone, 3-Axis Gimbal with 4K Camera, 12MP Photos.', 379.00, 'https://images.unsplash.com/photo-1610484830953-6a12535a2aaf?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 6, 4.8, 'Very Good', 'Used Occasionally', 'Drone body in excellent condition, controller shows minor wear. Includes all Fly More Combo accessories.', '2025-10-25 19:00:00'),
(12, 'Refurbished Oculus Quest 2 (128GB)', 'Meta Quest 2 — Advanced All-In-One Virtual Reality Headset — 128 GB', 249.99, 'https://images.unsplash.com/photo-1635863920701-a6125a176e5d?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600', 11, 4.5, 'Good', '9 Months', 'Headset is clean, controllers have some scuffs from use. Fully functional.', '2025-10-25 19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL, -- Can be NULL for guest checkout or link to users table
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`) -- Add index for user_id
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price_per_unit` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
-- UPDATED users table structure
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL, -- Increased length for hashed passwords
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`), -- Ensure usernames are unique
  UNIQUE KEY `email` (`email`)       -- Ensure emails are unique
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Add constraints after tables are created
--

-- Constraints for table `orders`
-- Ensure users table exists before adding this constraint
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE; -- Allow orders to remain if user deleted

-- Constraints for table `order_items`
-- Ensure orders and products tables exist first
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE, -- Delete items if order deleted
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE; -- Prevent deleting product if in an order

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

