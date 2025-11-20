-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2025 at 05:56 AM
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
-- Database: `fixandgo_db`
--
CREATE DATABASE IF NOT EXISTS `fixandgo_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `fixandgo_db`;

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `address_id` int(11) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `address_one` varchar(255) NOT NULL,
  `address_two` varchar(255) DEFAULT NULL,
  `address_three` varchar(255) DEFAULT NULL,
  `state` varchar(50) NOT NULL,
  `post_code` varchar(5) NOT NULL,
  `country` varchar(40) NOT NULL DEFAULT 'Malaysia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cartitem`
--

CREATE TABLE `cartitem` (
  `item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `is_check` tinyint(1) NOT NULL DEFAULT 0,
  `is_take` tinyint(1) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_code` varchar(10) NOT NULL,
  `category_name` varchar(30) NOT NULL,
  `description` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyaltypoint`
--

CREATE TABLE `loyaltypoint` (
  `user_id` varchar(12) NOT NULL,
  `get_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `royalty_point` int(11) NOT NULL DEFAULT 0,
  `expired_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

CREATE TABLE `orderitem` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `order_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('Pending','Paid','Failed','Refunded') NOT NULL,
  `status` enum('Pending','Processing','Shipping','Delivered','Cancelled') NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `utilize_point` tinyint(1) NOT NULL DEFAULT 0,
  `address_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp`
--

CREATE TABLE `otp` (
  `user_id` varchar(12) NOT NULL,
  `start_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hashed_password` varchar(255) NOT NULL,
  `expired_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('Credit Card','Debit Card','PayPal','Bank Transfer','Cash') NOT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `description` varchar(200) DEFAULT NULL,
  `short_desc` varchar(50) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `product_point` int(11) DEFAULT 0,
  `sold_number` int(11) NOT NULL DEFAULT 0,
  `category_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `productvisualmedia`
--

CREATE TABLE `productvisualmedia` (
  `media_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(100) NOT NULL,
  `alt` varchar(50) DEFAULT NULL,
  `is_show` tinyint(1) NOT NULL DEFAULT 1,
  `type` enum('Video','Image','Video URL','Image URL') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profilepicture`
--

CREATE TABLE `profilepicture` (
  `user_id` varchar(12) NOT NULL,
  `content` varchar(200) NOT NULL,
  `mime_type` enum('jpeg','png','webp') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `product_id` int(11) NOT NULL,
  `reviewed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment` varchar(200) DEFAULT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT 1,
  `rating` decimal(2,1) DEFAULT NULL CHECK (`rating` >= 0.0 and `rating` <= 5.0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userdevices`
--

CREATE TABLE `userdevices` (
  `mac_address` varchar(17) NOT NULL,
  `device_name` varchar(100) DEFAULT NULL,
  `user_id` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userprofile`
--

CREATE TABLE `userprofile` (
  `user_id` varchar(12) NOT NULL,
  `dob` date DEFAULT NULL,
  `contact_num` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(12) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_role` enum('Member','Admin') NOT NULL DEFAULT 'Member',
  `email` varchar(30) NOT NULL,
  `hash_password` varchar(255) NOT NULL,
  `account_status` enum('Verified','Unverified','Blocked') NOT NULL DEFAULT 'Unverified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cartitem`
--
ALTER TABLE `cartitem`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_code`);

--
-- Indexes for table `loyaltypoint`
--
ALTER TABLE `loyaltypoint`
  ADD PRIMARY KEY (`user_id`,`get_at`);

--
-- Indexes for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`user_id`,`start_at`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_code` (`category_code`);

--
-- Indexes for table `productvisualmedia`
--
ALTER TABLE `productvisualmedia`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `profilepicture`
--
ALTER TABLE `profilepicture`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `userdevices`
--
ALTER TABLE `userdevices`
  ADD PRIMARY KEY (`user_id`,`mac_address`);

--
-- Indexes for table `userprofile`
--
ALTER TABLE `userprofile`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cartitem`
--
ALTER TABLE `cartitem`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `productvisualmedia`
--
ALTER TABLE `productvisualmedia`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `cartitem`
--
ALTER TABLE `cartitem`
  ADD CONSTRAINT `cartitem_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `cartitem_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `loyaltypoint`
--
ALTER TABLE `loyaltypoint`
  ADD CONSTRAINT `loyaltypoint_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD CONSTRAINT `orderitem_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `orderitem_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `otp`
--
ALTER TABLE `otp`
  ADD CONSTRAINT `otp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_code`) REFERENCES `category` (`category_code`);

--
-- Constraints for table `productvisualmedia`
--
ALTER TABLE `productvisualmedia`
  ADD CONSTRAINT `productvisualmedia_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `profilepicture`
--
ALTER TABLE `profilepicture`
  ADD CONSTRAINT `profilepicture_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `userdevices`
--
ALTER TABLE `userdevices`
  ADD CONSTRAINT `userdevices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `userprofile`
--
ALTER TABLE `userprofile`
  ADD CONSTRAINT `userprofile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

INSERT INTO users (user_id, user_name, user_role, email, hash_password, account_status)
VALUES
-- 10 Members
('M001', 'Evelyn Carter', 'Member', 'evelyn.carter@example.com', 'password123', 'Verified'),
('M002', 'Liam Johnson', 'Member', 'liam.johnson@example.com', 'password123', 'Verified'),
('M003', 'Olivia Bennett', 'Member', 'olivia.bennett@example.com', 'password123', 'Verified'),
('M004', 'Noah Williams', 'Member', 'noah.williams@example.com', 'password123', 'Verified'),
('M005', 'Ava Mitchell', 'Member', 'ava.mitchell@example.com', 'password123', 'Verified'),
('M006', 'Mason Rivera', 'Member', 'mason.rivera@example.com', 'password123', 'Verified'),
('M007', 'Sophia Turner', 'Member', 'sophia.turner@example.com', 'password123', 'Verified'),
('M008', 'James Parker', 'Member', 'james.parker@example.com', 'password123', 'Verified'),
('M009', 'Isabella Flores', 'Member', 'isabella.flores@example.com', 'password123', 'Verified'),
('M010', 'Benjamin Hayes', 'Member', 'benjamin.hayes@example.com', 'password123', 'Verified'),

-- 5 Admins
('A001', 'Daniel Brooks', 'Admin', 'daniel.brooks@example.com', 'admin123', 'Verified'),
('A002', 'Emma Collins', 'Admin', 'emma.collins@example.com', 'admin123', 'Verified'),
('A003', 'Michael Scott', 'Admin', 'michael.scott@example.com', 'admin123', 'Verified'),
('A004', 'Chloe Adams', 'Admin', 'chloe.adams@example.com', 'admin123', 'Verified'),
('A005', 'Henry Watson', 'Admin', 'henry.watson@example.com', 'admin123', 'Verified');

INSERT INTO address (address_id, user_id, address_one, address_two, address_three, state, post_code, country)
VALUES
(1, 'M001', '12 Jalan Meranti', 'Taman Bukit Indah', NULL, 'Selangor', '40100', 'Malaysia'),
(2, 'M002', '28 Jalan Setia 5/3', 'Setia Alam', NULL, 'Selangor', '40170', 'Malaysia'),
(3, 'M003', '55 Jalan Kempas 3', 'Taman Kempas', NULL, 'Johor', '81200', 'Malaysia'),
(4, 'M004', '9 Jalan Lavender', 'Taman Putra Perdana', NULL, 'Selangor', '47130', 'Malaysia'),
(5, 'M005', '21 Jalan Air Putih', NULL, NULL, 'Pahang', '25300', 'Malaysia'),
(6, 'M006', '77 Jalan Tun Ahmad Zaidi', 'Lorong 2', NULL, 'Sarawak', '93050', 'Malaysia'),
(7, 'M007', '35 Jalan Mawar 2A', 'Taman Sri Mawar', NULL, 'Negeri Sembilan', '71000', 'Malaysia'),
(8, 'M008', '14 Jalan Sri Hartamas 1', NULL, NULL, 'Kuala Lumpur', '50480', 'Malaysia'),
(9, 'M009', '68 Jalan Wong Ah Fook', 'Block B', 'Unit 12-03', 'Johor', '80000', 'Malaysia'),
(10, 'M010', '5 Jalan Cenderawasih', 'Taman Desa Cemerlang', NULL, 'Johor', '81750', 'Malaysia'),

(11, 'A001', '100 Jalan Persiaran Surian', 'Damansara Utama', NULL, 'Selangor', '47800', 'Malaysia'),
(12, 'A002', '33 Jalan Bayu 4', 'Bandar Puteri', NULL, 'Selangor', '47100', 'Malaysia'),
(13, 'A003', '9 Lorong Jelutong', NULL, NULL, 'Penang', '10250', 'Malaysia'),
(14, 'A004', '22 Jalan Sultan Ismail', 'Menara Pintu', 'Level 10', 'Kuala Lumpur', '50250', 'Malaysia'),
(15, 'A005', '50 Jalan Tok Janggut', NULL, NULL, 'Kelantan', '15150', 'Malaysia');

INSERT INTO userdevices (mac_address, device_name, user_id)
VALUES
('A4:12:6F:9B:3C:11', 'iPhone 12', 'M001'),
('B8:4F:2A:7D:88:29', 'Samsung Galaxy S21', 'M002'),
('C0:98:3D:4A:1F:55', 'iPad Air', 'M003'),
('D1:7B:66:22:5E:90', 'Huawei Matebook D14', 'M004'),
('E3:55:AF:9C:77:01', 'Xiaomi Redmi Note 11', 'M005'),
('F7:62:1D:2E:4B:88', 'MacBook Pro 13', 'M006'),
('A1:33:4E:5F:99:77', 'Samsung Galaxy Tab A7', 'M007'),
('B2:8A:7C:3D:11:44', 'ASUS ROG Phone 5', 'M008'),
('C7:21:9D:6E:45:22', 'Lenovo IdeaPad Slim', 'M009'),
('D9:44:8F:1A:30:66', 'Oppo Reno 8', 'M010'),
('E0:77:2C:5B:9F:13', 'iPhone 13 Pro', 'A001'),
('F1:88:3E:7D:22:41', 'Dell XPS 15', 'A002'),
('A2:6B:5C:3A:11:92', 'iPad Mini', 'A003'),
('B5:9D:8F:1E:44:27', 'Samsung S22 Ultra', 'A004'),
('C6:2A:7B:9C:55:30', 'MacBook Air M1', 'A005');

INSERT INTO otp (user_id, start_at, hashed_password, expired_at)
VALUES
('M001', '2025-01-10 10:15:00', 'otp12345', '2025-01-10 10:20:00'),
('M002', '2025-01-11 09:30:00', 'otp56789', '2025-01-11 09:35:00'),
('M003', '2025-01-12 14:10:00', 'otpabc12', '2025-01-12 14:15:00'),
('M004', '2025-01-13 08:50:00', 'otpxyz34', '2025-01-13 08:55:00'),
('M005', '2025-01-14 16:20:00', 'otp11223', '2025-01-14 16:25:00'),
('M006', '2025-01-15 11:05:00', 'otp44556', '2025-01-15 11:10:00'),
('M007', '2025-01-16 19:40:00', 'otp77889', '2025-01-16 19:45:00'),
('M008', '2025-01-17 07:25:00', 'otp99100', '2025-01-17 07:30:00'),
('M009', '2025-01-18 13:55:00', 'otp88990', '2025-01-18 14:00:00'),
('M010', '2025-01-19 21:10:00', 'otp33445', '2025-01-19 21:15:00'),
('A001', '2025-01-20 10:00:00', 'otpadmin1', '2025-01-20 10:05:00'),
('A002', '2025-01-21 11:45:00', 'otpadmin2', '2025-01-21 11:50:00'),
('A003', '2025-01-22 12:30:00', 'otpadmin3', '2025-01-22 12:35:00'),
('A004', '2025-01-23 17:15:00', 'otpadmin4', '2025-01-23 17:20:00'),
('A005', '2025-01-24 09:40:00', 'otpadmin5', '2025-01-24 09:45:00');

INSERT INTO userprofile (user_id, dob, contact_num, gender)
VALUES
('M001', '2001-03-15', '012-3456789', 'Male'),
('M002', '2002-07-22', '013-9876543', 'Female'),
('M003', '2000-11-05', '014-5566778', 'Male'),
('M004', '1999-09-10', '017-1122334', 'Female'),
('M005', '2001-01-28', '018-2244668', 'Male'),
('M006', '2003-05-13', '011-3344556', 'Female'),
('M007', '2002-12-02', '016-7788991', 'Male'),
('M008', '2001-08-19', '019-8877665', 'Female'),
('M009', '2000-04-09', '012-6677889', 'Male'),
('M010', '1998-06-25', '013-9988776', 'Female'),
('A001', '1995-02-14', '017-5566442', 'Male'),
('A002', '1994-10-31', '018-1122557', 'Female'),
('A003', '1996-07-07', '011-7788445', 'Male'),
('A004', '1993-12-23', '015-8899221', 'Female'),
('A005', '1997-09-17', '016-4433221', 'Male');

INSERT INTO loyaltypoint (user_id, get_at, royalty_point, expired_at)
VALUES
('M001', '2025-01-10 10:00:00', 120, '2026-01-10 10:00:00'),
('M002', '2025-01-11 11:20:00', 80,  '2026-01-11 11:20:00'),
('M003', '2025-01-12 09:15:00', 150, NULL),
('M004', '2025-01-13 14:40:00', 60,  '2026-01-13 14:40:00'),
('M005', '2025-01-14 16:05:00', 200, NULL),
('M006', '2025-01-15 13:22:00', 95,  '2026-01-15 13:22:00'),
('M007', '2025-01-16 08:33:00', 50,  NULL),
('M008', '2025-01-17 17:18:00', 180, '2026-01-17 17:18:00'),
('M009', '2025-01-18 19:45:00', 30,  NULL),
('M010', '2025-01-19 12:55:00', 140, '2026-01-19 12:55:00'),
('A001', '2025-01-20 10:10:00', 300, '2026-01-20 10:10:00'),
('A002', '2025-01-21 11:11:00', 260, NULL),
('A003', '2025-01-22 09:30:00', 220, '2026-01-22 09:30:00'),
('A004', '2025-01-23 15:45:00', 310, NULL),
('A005', '2025-01-24 18:20:00', 270, '2026-01-24 18:20:00');

INSERT INTO profilepicture (user_id, content, mime_type)
VALUES
('M001', 'uploads/profile/m001.jpeg', 'jpeg'),
('M002', 'uploads/profile/m002.png', 'png'),
('M003', 'uploads/profile/m003.webp', 'webp'),
('M004', 'uploads/profile/m004.jpeg', 'jpeg'),
('M005', 'uploads/profile/m005.png', 'png'),
('M006', 'uploads/profile/m006.webp', 'webp'),
('M007', 'uploads/profile/m007.jpeg', 'jpeg'),
('M008', 'uploads/profile/m008.png', 'png'),
('M009', 'uploads/profile/m009.webp', 'webp'),
('M010', 'uploads/profile/m010.jpeg', 'jpeg'),
('A001', 'uploads/profile/a001.png', 'png'),
('A002', 'uploads/profile/a002.jpeg', 'jpeg'),
('A003', 'uploads/profile/a003.webp', 'webp'),
('A004', 'uploads/profile/a004.png', 'png'),
('A005', 'uploads/profile/a005.jpeg', 'jpeg');

INSERT INTO category (category_code, category_name, description) VALUES
('ELEC', 'Electronics', 'Electronic devices and related products'),
('ACC', 'Accessories', 'Tech and lifestyle accessories'),
('HOME', 'Home & Living', 'Household items and home improvement products');

INSERT INTO product 
(product_id, product_name, stock_quantity, description, short_desc, unit_price, product_point, sold_number, category_code) 
VALUES
(101, 'Wireless Mouse', 120, 'Ergonomic wireless mouse with 2.4GHz connection.', '2.4G Mouse', 29.90, 10, 40, 'ELEC'),
(102, 'Mechanical Keyboard', 80, 'Blue switch mechanical keyboard with RGB lighting.', 'RGB Keyboard', 159.00, 25, 32, 'ELEC'),
(103, 'Bluetooth Earbuds', 150, 'Wireless earbuds with noise cancellation feature.', 'BT Earbuds', 89.50, 15, 55, 'ELEC'),
(104, 'USB-C Charger', 200, '20W fast charger compatible with most devices.', 'Fast Charger', 39.90, 5, 100, 'ELEC'),
(105, 'Phone Stand', 300, 'Adjustable metal stand for smartphones & tablets.', 'Phone Stand', 15.00, 3, 75, 'ACC'),
(106, 'Laptop Sleeve', 90, 'Water-resistant sleeve for 13-inch laptops.', 'Laptop Bag', 49.90, 8, 20, 'ACC'),
(107, 'Smart Watch', 70, 'Fitness tracking smartwatch with heart rate monitor.', 'Smart Watch', 199.00, 30, 22, 'ELEC'),
(108, 'Portable Speaker', 110, 'Bluetooth speaker with deep bass and long battery.', 'BT Speaker', 129.90, 18, 45, 'ELEC'),
(109, 'Webcam 1080p', 60, 'Full HD webcam with built-in microphone.', '1080p Cam', 79.00, 12, 28, 'ELEC'),
(110, 'Gaming Headset', 85, 'Surround sound headset with noise-cancelling mic.', 'Gaming Headset', 149.00, 20, 36, 'ELEC'),
(111, 'HDMI Cable', 250, 'Durable 2-meter HDMI cable supporting 4K.', '4K HDMI', 19.90, 2, 150, 'ACC'),
(112, 'Desk Lamp', 140, 'LED desk lamp with adjustable brightness.', 'LED Lamp', 59.00, 7, 48, 'HOME'),
(113, 'External HDD 1TB', 55, 'Portable USB 3.0 external hard drive.', '1TB HDD', 239.00, 35, 30, 'ELEC'),
(114, 'Power Bank 10000mAh', 130, 'Fast-charging portable power bank.', 'Power Bank', 79.90, 10, 65, 'ELEC'),
(115, 'Screen Cleaner Kit', 180, 'Cleaning spray + microfiber cloth set.', 'Cleaner Kit', 12.90, 1, 90, 'ACC');

INSERT INTO review (review_id, user_id, product_id, reviewed_at, comment, is_valid, rating)
VALUES
(1, 'M001', 101, '2025-01-12 10:15:00', 'Good quality and fast delivery.', 1, 4.5),
(2, 'M002', 102, '2025-01-13 14:22:00', 'Value for money, recommended.', 1, 4.0),
(3, 'M003', 103, '2025-01-13 18:05:00', 'Not bad but packaging can improve.', 1, 3.5),
(4, 'M004', 101, '2025-01-14 09:40:00', 'Exactly as described.', 1, 5.0),
(5, 'M005', 104, '2025-01-15 11:10:00', 'Item received in good condition.', 1, 4.2),
(6, 'M006', 105, '2025-01-15 15:00:00', NULL, 1, 3.0),
(7, 'M007', 102, '2025-01-16 20:12:00', 'Satisfied with the purchase.', 1, 4.8),
(8, 'M008', 106, '2025-01-17 08:50:00', 'Quality could be better.', 1, 2.5),
(9, 'M009', 107, '2025-01-17 16:30:00', NULL, 1, 3.7),
(10, 'M010', 103, '2025-01-18 12:15:00', 'Amazing product!', 1, 5.0),
(11, 'A001', 108, '2025-01-19 09:00:00', 'Staff review: Approved item.', 1, 4.9),
(12, 'A002', 109, '2025-01-19 10:20:00', NULL, 1, 4.3),
(13, 'A003', 110, '2025-01-19 11:45:00', 'Looks good and works fine.', 1, 4.1),
(14, 'A004', 104, '2025-01-20 13:00:00', 'Fast shipping and well packed.', 1, 4.6),
(15, 'A005', 105, '2025-01-20 15:35:00', 'Approved by admin.', 1, 5.0);

