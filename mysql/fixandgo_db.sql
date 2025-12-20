-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 08:58 AM
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
  `address_name` varchar(20) NOT NULL DEFAULT 'Home',
  `user_id` varchar(12) NOT NULL,
  `address_one` varchar(255) NOT NULL,
  `address_two` varchar(255) DEFAULT NULL,
  `address_three` varchar(255) DEFAULT NULL,
  `state` varchar(50) NOT NULL,
  `post_code` varchar(5) NOT NULL,
  `country` varchar(40) NOT NULL DEFAULT 'Malaysia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`address_id`, `address_name`, `user_id`, `address_one`, `address_two`, `address_three`, `state`, `post_code`, `country`) VALUES
(1, 'Home', 'M001', '12 Jalan Meranti', 'Taman Bukit Indah', NULL, 'Selangor', '40100', 'Malaysia'),
(2, 'Home', 'M002', '28 Jalan Setia 5/3', 'Setia Alam', NULL, 'Selangor', '40170', 'Malaysia'),
(3, 'Home', 'M003', '55 Jalan Kempas 3', 'Taman Kempas', NULL, 'Johor', '81200', 'Malaysia'),
(4, 'Home', 'M004', '9 Jalan Lavender', 'Taman Putra Perdana', NULL, 'Selangor', '47130', 'Malaysia'),
(5, 'Home', 'M005', '21 Jalan Air Putih', NULL, NULL, 'Pahang', '25300', 'Malaysia'),
(6, 'Home', 'M006', '77 Jalan Tun Ahmad Zaidi', 'Lorong 2', NULL, 'Sarawak', '93050', 'Malaysia'),
(7, 'Home', 'M007', '35 Jalan Mawar 2A', 'Taman Sri Mawar', NULL, 'Negeri Sembilan', '71000', 'Malaysia'),
(8, 'Home', 'M008', '14 Jalan Sri Hartamas 1', NULL, NULL, 'Kuala Lumpur', '50480', 'Malaysia'),
(9, 'Home', 'M009', '68 Jalan Wong Ah Fook', 'Block B', 'Unit 12-03', 'Johor', '80000', 'Malaysia'),
(10, 'Home', 'M010', '5 Jalan Cenderawasih', 'Taman Desa Cemerlang', NULL, 'Johor', '81750', 'Malaysia'),
(11, 'Home', 'A001', '100 Jalan Persiaran Surian', 'Damansara Utama', NULL, 'Selangor', '47800', 'Malaysia'),
(12, 'Home', 'A002', '33 Jalan Bayu 4', 'Bandar Puteri', NULL, 'Selangor', '47100', 'Malaysia'),
(13, 'Home', 'A003', '9 Lorong Jelutong', NULL, NULL, 'Penang', '10250', 'Malaysia'),
(14, 'Home', 'A004', '22 Jalan Sultan Ismail', 'Menara Pintu', 'Level 10', 'Kuala Lumpur', '50250', 'Malaysia'),
(15, 'Home', 'A005', '50 Jalan Tok Janggut', NULL, NULL, 'Kelantan', '15150', 'Malaysia'),
(16, 'Home', 'M001', '12 Jalan Meranti', 'Taman Bukit Indah', NULL, 'Selangor', '40100', 'Malaysia'),
(17, 'Office', 'M001', 'Level 5, Menara Sentral', 'Jalan Tun Razak', NULL, 'Kuala Lumpur', '50400', 'Malaysia'),
(18, 'Home', 'M002', '8 Jalan Kenanga', 'Taman Melawati', NULL, 'Kuala Lumpur', '53100', 'Malaysia'),
(19, 'Home', 'M003', '22 Jalan Indah 3', 'Taman Sri Gombak', NULL, 'Selangor', '68100', 'Malaysia'),
(20, 'Shipping Address', 'M004', 'No 15, Lorong Batu Nilam', 'Bukit Tinggi', NULL, 'Selangor', '41200', 'Malaysia');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`) VALUES
(1, 'M001'),
(2, 'M002'),
(3, 'M003'),
(4, 'M004'),
(5, 'M005'),
(6, 'M006'),
(7, 'M007'),
(8, 'M008'),
(9, 'M009'),
(10, 'M010');

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
  `category_code` int(11) NOT NULL,
  `category_name` varchar(30) NOT NULL,
  `img_path` varchar(255) DEFAULT NULL,
  `description` varchar(1500) DEFAULT NULL,
  `is_show` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `category`
ADD COLUMN `is_deleted` BOOLEAN NOT NULL DEFAULT FALSE
AFTER `is_show`;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_code`, `category_name`, `img_path`, `description`, `is_show`) VALUES
(1, 'Storage', 'images/category/storage.jpg', 'Storage solutions, tool boxes, shelves and trolleys', 1),
(2, 'Stationery', 'images/category/stationary.png', 'Browse the widest range of stationery and office supplies all in one place! You will find everything from double sided tape and colored pencils, to filing folders and sticky notes. If you want the best deals available, you’re sure to find them here – highlighters, staplers, highlighters – we have it all', 1),
(3, 'Automotive', 'images/category/automotive.png', 'We here at Mr DIY know that spending time and money on your car is an important investment. That is why we have a range of automotive goods and car accessories in our store to make sure you are getting the best out of your ride! Whether it be car mats, sun shades, car covers, car polishes or even the newest tech gadgets, our website has everything you need to get more from your vehicle', 1),
(4, 'Power & Hand Tools', 'images/category/hardware_tools.png', 'Cordless screwdrivers, drills, saws, chisels hammers and measuring tapes', 1),
(5, 'Uncategorized', 'images/no-image.jpg', 'Products that have not been assigned to a specific category', 0);

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

--
-- Dumping data for table `loyaltypoint`
--

INSERT INTO `loyaltypoint` (`user_id`, `get_at`, `royalty_point`, `expired_at`) VALUES
('M001', '2025-01-10 10:00:00', 120, '2026-01-01 00:00:00'),
('M002', '2025-01-11 11:20:00', 80, '2026-01-01 00:00:00'),
('M003', '2025-01-12 09:15:00', 150, '2026-01-01 00:00:00'),
('M004', '2025-01-13 14:40:00', 60, '2026-01-01 00:00:00'),
('M005', '2025-11-14 16:05:00', 200, '2026-11-01 00:00:00'),
('M006', '2025-05-15 13:22:00', 95, '2026-05-01 00:00:00'),
('M008', '2025-03-17 17:18:00', 180, '2026-03-01 00:00:00'),
('M010', '2025-01-19 12:55:00', 140, '2026-01-01 00:00:00');

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
  `deliver_at` date DEFAULT NULL,
  `payment_status` enum('Pending','Paid','Failed','Refunded','Redeemed') NOT NULL,
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

--
-- Dumping data for table `otp`
--

INSERT INTO `otp` (`user_id`, `start_at`, `hashed_password`, `expired_at`) VALUES
('M001', '2025-01-10 10:15:00', 'otp12345', '2025-01-10 10:20:00'),
('M002', '2025-01-11 09:30:00', 'otp56789', '2025-01-11 09:35:00'),
('M003', '2025-01-12 14:10:00', 'otpabc12', '2025-01-12 14:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('Credit Card','Debit Card','PayPal','Bank Transfer','Cash','Loyalty Points') NOT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(500) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `description` varchar(10000) DEFAULT NULL,
  `short_desc` varchar(500) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `product_point` int(11) DEFAULT 0,
  `sold_number` int(11) NOT NULL DEFAULT 0,
  `category_code` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `isdeleted` tinyint(1) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) DEFAULT 10
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
  `alt` varchar(100) NOT NULL DEFAULT '',
  `is_show` tinyint(1) NOT NULL DEFAULT 1,
  `type` enum('Video','Image','Video URL','Image URL') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profilepicture`
--

CREATE TABLE `profilepicture` (
  `user_id` varchar(12) NOT NULL,
  `file_path` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profilepicture`
--

INSERT INTO `profilepicture` (`user_id`, `file_path`) VALUES
('A001', 'images/profile/A001.jpg'),
('M001', 'images/profile/1.webp');

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

--
-- Dumping data for table `userdevices`
--

INSERT INTO `userdevices` (`mac_address`, `device_name`, `user_id`) VALUES
('E0:77:2C:5B:9F:13', 'iPhone 13 Pro', 'A001'),
('F1:88:3E:7D:22:41', 'Dell XPS 15', 'A002'),
('A2:6B:5C:3A:11:92', 'iPad Mini', 'A003'),
('B5:9D:8F:1E:44:27', 'Samsung S22 Ultra', 'A004'),
('C6:2A:7B:9C:55:30', 'MacBook Air M1', 'A005'),
('A4:12:6F:9B:3C:11', 'iPhone 12', 'M001'),
('B8:4F:2A:7D:88:29', 'Samsung Galaxy S21', 'M002'),
('C0:98:3D:4A:1F:55', 'iPad Air', 'M003'),
('D1:7B:66:22:5E:90', 'Huawei Matebook D14', 'M004'),
('E3:55:AF:9C:77:01', 'Xiaomi Redmi Note 11', 'M005'),
('F7:62:1D:2E:4B:88', 'MacBook Pro 13', 'M006'),
('A1:33:4E:5F:99:77', 'Samsung Galaxy Tab A7', 'M007'),
('B2:8A:7C:3D:11:44', 'ASUS ROG Phone 5', 'M008'),
('C7:21:9D:6E:45:22', 'Lenovo IdeaPad Slim', 'M009'),
('D9:44:8F:1A:30:66', 'Oppo Reno 8', 'M010');

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

--
-- Dumping data for table `userprofile`
--

INSERT INTO `userprofile` (`user_id`, `dob`, `contact_num`, `gender`) VALUES
('A001', NULL, NULL, NULL),
('M001', NULL, '+60123456789', 'Male'),
('M002', '2002-07-22', NULL, 'Female'),
('M003', '2000-11-05', NULL, 'Male'),
('M004', '1999-09-10', '+60123456789', 'Female'),
('M005', NULL, NULL, 'Male'),
('M006', '2003-05-13', NULL, 'Female'),
('M007', NULL, '+6012309089', NULL),
('M008', '2001-08-19', '+60233456789', 'Female'),
('M009', '2000-04-09', '+60123462789', 'Male'),
('M010', '1998-06-25', NULL, 'Female');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(12) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_role` enum('Member','Admin') NOT NULL DEFAULT 'Member',
  `email` varchar(50) NOT NULL,
  `hash_password` varchar(255) NOT NULL,
  `account_status` enum('Unblock','Blocked') NOT NULL DEFAULT 'Unblock'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_role`, `email`, `hash_password`, `account_status`) VALUES
('A001', 'Daniel Brooks', 'Admin', 'daniel.brooks@example.com', '$2y$10$LljQnB2O.ukj9VkDW.B/D.ziL9GHx0aUDqZRmAUWwZkKch7AwNL82', 'Unblock'),
('A0011', 'HELLO', 'Admin', 'benjamin11.hayes@example.com', '$2y$10$hoGi1EZtx5LQGzg0tTb7AeRzTzvlso2GPQzqQhmPhgDVrz4BaVdiS', 'Unblock'),
('A002', 'Emma Collins', 'Admin', 'emma.collins@example.com', '$2y$10$4aI9qJHeY8Mu4Sc9fmVd3OEeu.uCtuuJj5JqHrbvNyGA3rmFukzyW', 'Unblock'),
('A003', 'Michael Scott', 'Admin', 'michael.scott@example.com', '$2y$10$JfxW/FXPlwpA7PM7v7EYT.nblqOs62e5Lzkra7MlXiW/Koi.Rwz5.', 'Unblock'),
('A004', 'Chloe Adams', 'Admin', 'chloe.adams@example.com', '$2y$10$OYyXMiSTRp0xYtQ97DS5SeFWif1LaC0pK/kdbY.dIG2gJlNCp7Foi', 'Unblock'),
('A005', 'Henry Watson', 'Admin', 'henry.watson@example.com', '$2y$10$kUA3gN3A.reQJpYtU0QWRud/nNGWzUFwW2C/AxyzK6PYCjbbmiLk6', 'Unblock'),
('M001', 'Evelyn Carter', 'Member', 'evelyn.carter@example.com', '$2y$10$S.9ckKf5vVV.0a/mcK6wUeHYWWvv33wRwjYtmM0ul9JK.G.FkhCqK', 'Unblock'),
('M002', 'Liam Johnson', 'Member', 'liam.johnson@example.com', '$2y$10$B2R683QQRLElyMMcCz9s2uNagC3KCq22VKueg4VEVMd7yeGI4gi6C', 'Unblock'),
('M003', 'Olivia Bennett', 'Member', 'olivia.bennett@example.com', '$2y$10$35kttDVnkLemZK7DnIEq/u80A/c.MzmmThLpULVMFPeY.ha5SOBJ.', 'Unblock'),
('M004', 'Noah Williams', 'Member', 'noah.williams@example.com', '$2y$10$rWqtd30FWksxa7NYMTbwWOSJQ5RLeKEynY1ejIhxGJ5zeDxbvD17i', 'Unblock'),
('M005', 'Ava Mitchell', 'Member', 'ava.mitchell@example.com', '$2y$10$sSvpjs.GEX62hCz/HCNOJ.9wLx1NUYygXPue4cMYB6ghM5xt166x.', 'Unblock'),
('M006', 'Mason Rivera', 'Member', 'mason.rivera@example.com', '$2y$10$0TceV9uxxbuHyoElrFkMOeQHnDgh6Nz0cqyfXVnGug3vU16e6jTG.', 'Unblock'),
('M007', 'Sophia Turner', 'Member', 'sophia.turner@example.com', '$2y$10$4UQwzHDOHjrMT8KnLDn.8OYlXRZZAoZ.XojzemA.1QV68bI6o3Bme', 'Unblock'),
('M008', 'James Parker', 'Member', 'james.parker@example.com', '$2y$10$BS7JfHTSvWEO29zFLARTueIUkqxWyJrZSXEQVd/h7FIqePo9JQ7jG', 'Unblock'),
('M009', 'Isabella Flores', 'Member', 'isabella.flores@example.com', '$2y$10$GuSL8oAuK/iGXAglOTVgtOCzfoain7q7x01TAIo5MEtNn3OdlNs82', 'Unblock'),
('M010', 'Benjamin Hayes', 'Member', 'benjamin.hayes@example.com', '$2y$10$Gc6xNqwxRwookAjwqrRHjuqPbeW2TRFnfPqNPK/XFhH/WPhppaZNa', 'Unblock');

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
  ADD PRIMARY KEY (`user_id`,`get_at`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `order_id` (`order_id`),
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
  ADD PRIMARY KEY (`user_id`,`start_at`),
  ADD KEY `user_id` (`user_id`);

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
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_id` (`user_id`);

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
  ADD PRIMARY KEY (`user_id`,`mac_address`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `userprofile`
--
ALTER TABLE `userprofile`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cartitem`
--
ALTER TABLE `cartitem`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
