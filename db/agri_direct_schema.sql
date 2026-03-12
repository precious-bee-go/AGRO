-- AgriDirect Professional Database Schema

CREATE DATABASE IF NOT EXISTS `agri_direct`;
USE `agri_direct`;

-- 1. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `slug` VARCHAR(100) UNIQUE NOT NULL,
    `parent_id` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
);

-- 2. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'farmer', 'buyer') NOT NULL DEFAULT 'buyer',
    `profile_pic` VARCHAR(255) DEFAULT 'default_user.png',
    `is_verified` BOOLEAN DEFAULT FALSE,
    `last_login` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Profiles (Specific info for Farmer/Buyer)
CREATE TABLE IF NOT EXISTS `profiles` (
    `user_id` INT PRIMARY KEY,
    `phone` VARCHAR(20),
    `address` TEXT,
    `city` VARCHAR(100),
    `state` VARCHAR(100),
    `farm_name` VARCHAR(150), -- Farmer specific
    `farm_story` TEXT,        -- Farmer specific
    `kyc_status` ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- 4. Products Table
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `farmer_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `price_per_unit` DECIMAL(10,2) NOT NULL,
    `unit` VARCHAR(20) NOT NULL DEFAULT 'kg',
    `cultivation_method` ENUM('organic', 'hydroponic', 'traditional', 'greenhouse') DEFAULT 'traditional',
    `status` ENUM('draft', 'published', 'sold_out', 'hidden') DEFAULT 'draft',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`farmer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
);

-- 5. Product Batches (Time-based availability)
CREATE TABLE IF NOT EXISTS `product_batches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `batch_name` VARCHAR(100),
    `quantity_available` DECIMAL(10,2) NOT NULL,
    `min_order_quantity` DECIMAL(10,2) DEFAULT 1.00,
    `harvest_date` DATE NOT NULL,
    `booking_deadline` DATE NOT NULL,
    `quality_grade` ENUM('premium', 'grade_a', 'grade_b') DEFAULT 'grade_a',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- 6. Product Images
CREATE TABLE IF NOT EXISTS `product_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `is_primary` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- 7. Orders Table
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `buyer_id` INT NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `advance_amount` DECIMAL(10,2) NOT NULL, -- 20% booking
    `remaining_amount` DECIMAL(10,2) NOT NULL,
    `payment_status` ENUM('partial', 'paid', 'refunded') DEFAULT 'partial',
    `order_status` ENUM('booked', 'growing', 'harvested', 'dispatched', 'delivered', 'cancelled') DEFAULT 'booked',
    `delivery_address` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`)
);

-- 8. Order Items
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `batch_id` INT NOT NULL,
    `quantity` DECIMAL(10,2) NOT NULL,
    `price_at_booking` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`batch_id`) REFERENCES `product_batches`(`id`)
);

-- 9. Notifications
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('order', 'harvest', 'system', 'payment') DEFAULT 'system',
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- 10. Reviews
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `buyer_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `rating` TINYINT CHECK (rating BETWEEN 1 AND 5),
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);
