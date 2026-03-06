-- Database Schema for agri-marketplace

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `agri-marketplace`;
USE `agri-marketplace`;

-- Table for users (Farmers and Buyers)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('farmer', 'buyer') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for agricultural products
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `farmer_id` INT NOT NULL,
    `product_name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `date_ready` DATE NOT NULL,
    `quantity` DECIMAL(10,2) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `status` ENUM('available', 'sold_out') DEFAULT 'available',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`farmer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Table for orders/purchases
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `buyer_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `amount_paid` DECIMAL(10,2) NOT NULL, -- For partial payments
    `total_price` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending', 'ready', 'delivered', 'confirmed') DEFAULT 'pending',
    `purchase_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);
