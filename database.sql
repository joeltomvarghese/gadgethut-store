-- This is a SQL script to create your database and tables.
-- You will import this file into phpMyAdmin.

-- 1. Create the database
-- This line is commented out because XAMPP/phpMyAdmin often prefers you to create
-- the database manually. If import fails, create a database named 'm_commerce_db'
-- and then import this file again.
CREATE DATABASE IF NOT EXISTS `m_commerce_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `m_commerce_db`;

-- 2. Create the `products` table
-- This table stores all the items you are selling.
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(2083) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 100,
  `rating` decimal(3,2) DEFAULT 4.50,
  `reviews` int(11) DEFAULT 150,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Insert sample data into `products` table
-- These are the sample products for your store.
INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `stock`, `rating`, `reviews`) VALUES
(1, 'Smartphone X1', 'A flagship smartphone with 128GB storage and a 108MP camera. 6.7 inch AMOLED display.', '799.99', 'https://placehold.co/600x400/000000/FFFFFF?text=Smartphone+X1', 100, 4.70, 182),
(2, 'Noise-Canceling Headphones', 'Wireless earbuds with 30-hour battery life and spatial audio.', '199.99', 'https://placehold.co/600x400/333333/FFFFFF?text=Headphones', 100, 4.80, 256),
(3, 'Smartwatch 2', 'A water-resistant smartwatch with heart rate, SpO2, and sleep tracking. 1.9-inch display.', '249.99', 'https://placehold.co/600x400/222222/FFFFFF?text=Smartwatch+2', 100, 4.50, 130

