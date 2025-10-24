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
    CREATE DATABASE IF NOT EXISTS `m_commerce_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    USE `m_commerce_db`;

    -- --------------------------------------------------------

    --
    -- Table structure for table `products`
    --

    CREATE TABLE `products` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `description` text DEFAULT NULL,
      `price` decimal(10,2) NOT NULL,
      `image_url` varchar(2083) DEFAULT NULL, -- Increased length for potential long URLs
      `stock` int(11) NOT NULL DEFAULT 0,
      `rating` decimal(2,1) DEFAULT 4.5,     -- Added rating
      `condition` enum('Pristine','Very Good','Good') NOT NULL DEFAULT 'Good', -- Refurbished condition
      `usage_duration` varchar(100) DEFAULT NULL, -- E.g., '6 months', '1 year'
      `condition_notes` text DEFAULT NULL,    -- Specific notes about condition
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    --
    -- Dumping data for table `products` (12 products with real images and details)
    --

    INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `stock`, `rating`, `condition`, `usage_duration`, `condition_notes`) VALUES
    (1, 'Refurbished iPhone 13 Pro', 'Apple iPhone 13 Pro, 256GB, Sierra Blue - Unlocked. Fully tested and functional.', '899.99', 'https://images.unsplash.com/photo-1632701784920-aa37a4e5c8e3?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 50, 4.8, 'Pristine', '3 months', 'Looks and functions like new. Battery health 95%.'),
    (2, 'Refurbished Galaxy S22 Ultra', 'Samsung Galaxy S22 Ultra, 128GB, Phantom Black - Unlocked. Minor signs of wear.', '749.50', 'https://images.unsplash.com/photo-1644342551628-98e3b7914f17?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 35, 4.7, 'Very Good', '8 months', 'Minor scratches on the back panel, screen is perfect. Battery health 91%.'),
    (3, 'Refurbished MacBook Air M1', 'Apple MacBook Air Laptop with M1 Chip, 13-inch, 8GB RAM, 256GB SSD Storage, Space Gray.', '799.00', 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 20, 4.9, 'Pristine', '1 year', 'Minimal use, excellent condition. Battery cycle count: 45.'),
    (4, 'Refurbished Sony WH-1000XM4', 'Sony WH-1000XM4 Wireless Noise Cancelling Headphones, Black. Industry-leading ANC.', '229.99', 'https://images.unsplash.com/photo-1627910398246-a4c3fef6b0c6?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 60, 4.6, 'Very Good', '10 months', 'Slight wear on earpads, otherwise perfect working order.'),
    (5, 'Refurbished Dell XPS 15', 'Dell XPS 15 9510 Laptop, 15.6" FHD+, Intel Core i7, 16GB RAM, 512GB SSD, NVIDIA GeForce RTX 3050 Ti.', '1199.00', 'https://images.unsplash.com/photo-1588872657578-7efd1f155540?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 15, 4.5, 'Good', '1.5 years', 'Visible scratch on lid, small dent near corner. Fully functional, screen is perfect.'),
    (6, 'Refurbished Apple Watch Series 7', 'Apple Watch Series 7 GPS, 45mm, Midnight Aluminum Case with Midnight Sport Band.', '279.00', 'https://images.unsplash.com/photo-1634045546955-39a7d3c00427?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 40, 4.8, 'Pristine', '4 months', 'Like new condition, comes with original charger. Battery health 98%.'),
    (7, 'Refurbished Google Pixel 6 Pro', 'Google Pixel 6 Pro - 5G Android Phone - Unlocked Smartphone with Advanced Pixel Camera - 128GB - Stormy Black.', '549.00', 'https://images.unsplash.com/photo-1635869719942-01c5f35d21fe?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 25, 4.6, 'Very Good', '6 months', 'Minor scuffs on the frame, screen immaculate. Battery health 92%.'),
    (8, 'Refurbished iPad Pro 11-inch (3rd Gen)', 'Apple iPad Pro 11-inch (3rd Generation) Wi-Fi, 128GB - Space Gray.', '629.50', 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 30, 4.9, 'Pristine', '9 months', 'Excellent condition, barely used. Includes original box and charger.'),
    (9, 'Refurbished Bose QuietComfort 45', 'Bose QuietComfort 45 Wireless Noise Cancelling Headphones - Triple Black.', '199.00', 'https://images.unsplash.com/photo-1625800043818-d7b380a49ccf?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 55, 4.7, 'Very Good', '1 year', 'Minor signs of use on headband, earcups are clean. Excellent sound quality.'),
    (10, 'Refurbished Samsung Galaxy Tab S8', 'Samsung Galaxy Tab S8 Android Tablet, 11” LCD Screen, 128GB Storage, Wi-Fi 6E, Graphite.', '449.99', 'https://images.unsplash.com/photo-1644342551628-98e3b7914f17?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 22, 4.5, 'Good', '14 months', 'Small scratch on the screen (not visible when on), minor scuffs on back. Includes S Pen.'),
    (11, 'Refurbished DJI Mini 2 Drone', 'DJI Mini 2 Fly More Combo – Ultralight Foldable Drone, 3-Axis Gimbal with 4K Camera, 10km Video Transmission.', '379.00', 'https://images.unsplash.com/photo-1606990471954-080a37c5f884?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 18, 4.8, 'Very Good', '7 months', 'Fully functional, includes all Fly More Combo accessories. Minor scuffs on propellers.'),
    (12, 'Refurbished Oculus Quest 2', 'Meta Quest 2 — Advanced All-In-One Virtual Reality Headset — 128 GB.', '249.00', 'https://images.unsplash.com/photo-1641887343637-2521c3c9d721?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80', 33, 4.7, 'Very Good', '1 year', 'Headset and controllers cleaned and tested. Slight discoloration on head strap.');


    -- --------------------------------------------------------

    --
    -- Table structure for table `users` (Simplified)
    --

    CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `password_hash` varchar(255) NOT NULL, -- Store hashed passwords only
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Add a dummy user if needed for testing orders
    INSERT INTO `users` (`id`, `name`, `email`, `password_hash`) VALUES
    (1, 'Test User', 'test@example.com', 'dummy_hash');


    -- --------------------------------------------------------

    --
    -- Table structure for table `orders`
    --

    CREATE TABLE `orders` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) DEFAULT NULL, -- Can be NULL for guest checkouts, or link to users table
      `total_amount` decimal(10,2) NOT NULL,
      `order_status` varchar(50) NOT NULL DEFAULT 'Pending', -- e.g., Pending, Processing, Shipped, Delivered, Cancelled
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
       CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL -- Optional: Link to users table
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    -- --------------------------------------------------------

    --
    -- Table structure for table `order_items`
    --

    CREATE TABLE `order_items` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `order_id` int(11) NOT NULL,
      `product_id` int(11) NOT NULL,
      `quantity` int(11) NOT NULL,
      `price_per_unit` decimal(10,2) NOT NULL, -- Price at the time of order
      PRIMARY KEY (`id`),
      KEY `order_id` (`order_id`),
      KEY `product_id` (`product_id`),
      CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE, -- If order is deleted, delete items
      CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE -- Or ON DELETE RESTRICT if product deletion shouldn't be allowed if ordered
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    COMMIT;

    /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
    /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
    /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
    

