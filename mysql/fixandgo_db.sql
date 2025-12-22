-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2025 at 06:53 AM
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
(8, 'M008');

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

--
-- Dumping data for table `cartitem`
--

INSERT INTO `cartitem` (`item_id`, `cart_id`, `qty`, `is_check`, `is_take`, `product_id`) VALUES
(1, 1, 1, 1, 0, 4),
(2, 1, 2, 1, 0, 27),
(3, 2, 1, 1, 0, 23),
(4, 2, 1, 0, 0, 19),
(5, 3, 1, 1, 0, 35),
(6, 4, 1, 1, 0, 11),
(7, 5, 2, 1, 0, 1),
(8, 6, 1, 1, 0, 28),
(9, 7, 5, 1, 0, 39),
(10, 8, 3, 1, 0, 37);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_code` int(11) NOT NULL,
  `category_name` varchar(30) NOT NULL,
  `img_path` varchar(255) DEFAULT NULL,
  `description` varchar(1500) DEFAULT NULL,
  `is_show` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_code`, `category_name`, `img_path`, `description`, `is_show`, `is_deleted`) VALUES
(1, 'Storage', 'images/category/storage.jpg', 'Storage solutions, tool boxes, shelves and trolleys', 1, 0),
(2, 'Stationery', 'images/category/stationary.png', 'Browse the widest range of stationery and office supplies all in one place! You will find everything from double sided tape and colored pencils, to filing folders and sticky notes. If you want the best deals available, you’re sure to find them here – highlighters, staplers, highlighters – we have it all', 1, 0),
(3, 'Automotive', 'images/category/automotive.png', 'We here at Mr DIY know that spending time and money on your car is an important investment. That is why we have a range of automotive goods and car accessories in our store to make sure you are getting the best out of your ride! Whether it be car mats, sun shades, car covers, car polishes or even the newest tech gadgets, our website has everything you need to get more from your vehicle', 1, 0),
(4, 'Power & Hand Tools', 'images/category/hardware_tools.png', 'Cordless screwdrivers, drills, saws, chisels hammers and measuring tapes', 1, 0),
(5, 'Uncategorized', 'images/no-image.jpg', 'Products that have not been assigned to a specific category', 0, 0);

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
('M008', '2025-03-17 17:18:00', 180, '2026-03-01 00:00:00');

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

--
-- Dumping data for table `orderitem`
--

INSERT INTO `orderitem` (`order_id`, `product_id`, `qty`, `unit_price`) VALUES
(1, 10, 1, 149.90),
(1, 16, 1, 89.90),
(1, 24, 1, 159.90),
(2, 25, 2, 19.90),
(2, 26, 1, 15.90),
(2, 29, 1, 249.90),
(3, 14, 1, 199.90),
(3, 21, 1, 289.90),
(3, 32, 1, 199.90),
(4, 8, 1, 69.90),
(4, 31, 1, 79.90),
(5, 15, 1, 179.90),
(5, 33, 1, 289.90),
(6, 6, 1, 89.90),
(7, 7, 1, 49.90),
(7, 12, 2, 29.90),
(7, 25, 1, 19.90),
(7, 26, 1, 15.90),
(7, 29, 1, 249.90),
(7, 38, 1, 12.90),
(7, 39, 1, 19.90),
(8, 17, 1, 49.90),
(8, 18, 1, 39.90),
(8, 26, 1, 15.90),
(8, 30, 2, 12.90),
(8, 35, 1, 49.90),
(8, 36, 1, 29.90),
(9, 4, 1, 59.90),
(9, 25, 2, 19.90),
(9, 27, 1, 29.90),
(9, 33, 1, 289.90),
(10, 14, 1, 199.90),
(10, 25, 1, 19.90),
(10, 26, 1, 15.90),
(10, 39, 1, 19.90),
(11, 15, 1, 179.90),
(11, 24, 1, 159.90),
(11, 31, 1, 79.90),
(11, 33, 1, 289.90),
(12, 2, 1, 32.50),
(12, 9, 2, 24.90),
(12, 12, 1, 29.90),
(12, 38, 1, 12.90),
(13, 10, 1, 149.90),
(13, 19, 1, 129.90),
(13, 21, 1, 289.90),
(13, 22, 1, 139.90),
(13, 32, 1, 199.90),
(14, 8, 1, 69.90),
(14, 16, 1, 89.90),
(14, 30, 1, 12.90),
(14, 37, 1, 24.90),
(15, 11, 1, 99.90),
(15, 18, 1, 39.90),
(15, 20, 1, 59.90),
(15, 28, 1, 49.90),
(15, 34, 2, 39.90),
(15, 35, 1, 49.90),
(16, 4, 1, 59.90),
(16, 7, 1, 49.90),
(16, 23, 1, 39.90),
(16, 25, 3, 19.90),
(16, 26, 1, 15.90),
(16, 27, 2, 29.90),
(16, 29, 1, 249.90),
(17, 5, 2, 19.90),
(17, 30, 2, 12.90),
(17, 38, 2, 12.90),
(17, 39, 1, 19.90),
(18, 17, 2, 49.90),
(18, 34, 3, 39.90),
(18, 35, 1, 49.90),
(19, 10, 1, 149.90),
(19, 24, 1, 159.90),
(19, 31, 1, 79.90),
(19, 33, 1, 289.90),
(20, 1, 1, 45.90),
(20, 12, 2, 29.90),
(20, 25, 3, 19.90),
(20, 26, 1, 15.90),
(20, 30, 1, 12.90),
(21, 8, 1, 69.90),
(21, 15, 1, 179.90),
(21, 16, 1, 89.90),
(21, 31, 1, 79.90),
(21, 39, 2, 19.90),
(22, 23, 2, 39.90),
(22, 25, 5, 19.90),
(22, 26, 2, 15.90),
(22, 27, 3, 29.90),
(23, 30, 3, 12.90),
(23, 38, 2, 12.90),
(23, 39, 1, 19.90),
(24, 4, 1, 59.90),
(24, 7, 1, 49.90),
(24, 11, 1, 99.90),
(24, 28, 1, 49.90),
(24, 33, 1, 289.90),
(25, 18, 2, 39.90),
(25, 34, 1, 39.90),
(25, 35, 1, 49.90),
(26, 17, 1, 49.90),
(26, 20, 1, 59.90),
(26, 21, 1, 289.90),
(26, 22, 1, 139.90),
(26, 32, 1, 199.90);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` varchar(12) DEFAULT NULL,
  `order_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deliver_at` date DEFAULT NULL,
  `payment_status` enum('Pending','Paid','Failed','Refunded','Redeemed') NOT NULL,
  `status` enum('Pending','Processing','Shipping','Delivered','Cancelled') NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `utilize_point` tinyint(1) NOT NULL DEFAULT 0,
  `address_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_at`, `deliver_at`, `payment_status`, `status`, `total_price`, `utilize_point`, `address_id`) VALUES
(1, 'M001', '2025-10-15 08:22:10', '2025-10-20', 'Paid', 'Delivered', 399.70, 0, 1),
(2, 'M002', '2025-10-20 14:35:45', '2025-10-25', 'Paid', 'Delivered', 285.70, 1, 2),
(3, 'M004', '2025-11-01 11:10:22', '2025-11-06', 'Paid', 'Delivered', 689.70, 0, 4),
(4, 'M007', '2025-11-05 19:45:30', '2025-11-10', 'Paid', 'Shipping', 149.80, 0, 7),
(5, NULL, '2025-11-12 09:18:55', '2025-11-17', 'Paid', 'Processing', 469.80, 1, 9),
(6, NULL, '2025-11-18 16:27:13', '2025-11-23', 'Paid', 'Delivered', 89.90, 0, 10),
(7, 'M003', '2025-11-25 10:45:33', '2025-11-30', 'Paid', 'Processing', 479.60, 0, 3),
(8, 'M005', '2025-11-28 15:20:18', '2025-12-03', 'Paid', 'Shipping', 189.60, 1, 5),
(9, 'M008', '2025-12-02 09:10:45', '2025-12-07', 'Paid', 'Delivered', 329.70, 0, 8),
(10, 'M006', '2025-12-05 14:33:27', '2025-12-10', 'Paid', 'Delivered', 249.60, 0, 6),
(11, 'M002', '2025-12-07 11:18:42', '2025-12-12', 'Paid', 'Shipping', 589.50, 1, 18),
(12, 'M001', '2025-12-09 16:45:19', '2025-12-14', 'Paid', 'Processing', 119.70, 0, 1),
(13, 'M004', '2025-12-11 09:22:55', '2025-12-16', 'Paid', 'Delivered', 899.50, 0, 4),
(14, NULL, '2025-12-13 20:15:30', '2025-12-18', 'Paid', 'Shipping', 179.80, 0, 10),
(15, 'M007', '2025-12-15 13:08:12', '2025-12-20', 'Paid', 'Processing', 349.70, 1, 7),
(16, NULL, '2025-12-18 10:55:44', '2025-12-23', 'Paid', 'Processing', 529.60, 0, 9),
(17, 'M003', '2025-12-20 18:30:22', '2025-12-25', 'Paid', 'Shipping', 99.80, 0, 19),
(18, 'M005', '2025-12-22 12:05:47', '2025-12-27', 'Paid', 'Processing', 259.60, 1, 5),
(19, 'M008', '2025-12-24 08:55:33', '2025-12-29', 'Paid', 'Processing', 679.50, 0, 8),
(20, 'M002', '2025-12-26 09:15:42', '2025-12-31', 'Paid', 'Delivered', 189.70, 0, 2),
(21, 'M004', '2025-12-27 14:30:18', '2026-01-02', 'Paid', 'Shipping', 439.60, 1, 20),
(22, 'M006', '2025-12-28 11:45:33', '2026-01-03', 'Paid', 'Processing', 299.50, 0, 6),
(23, 'M008', '2025-12-29 16:20:55', '2026-01-04', 'Paid', 'Processing', 79.80, 0, 8),
(24, NULL, '2025-12-30 10:10:10', '2026-01-05', 'Paid', 'Shipping', 519.60, 0, 10),
(25, 'M001', '2025-12-31 13:25:47', '2026-01-06', 'Paid', 'Delivered', 159.60, 1, 16),
(26, 'M003', '2026-01-02 08:40:22', '2026-01-07', 'Paid', 'Processing', 699.40, 0, 3);

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

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `order_id`, `payment_method`, `paid_at`) VALUES
(1, 1, 'Credit Card', '2025-10-15 08:25:00'),
(2, 2, 'Loyalty Points', '2025-10-20 14:40:12'),
(3, 3, 'Credit Card', '2025-11-01 11:15:30'),
(4, 4, 'Cash', '2025-11-05 19:50:00'),
(5, 5, 'Loyalty Points', '2025-11-12 09:22:10'),
(6, 6, 'Credit Card', '2025-11-18 16:30:05'),
(7, 7, 'Credit Card', '2025-11-25 10:50:22'),
(8, 8, 'Loyalty Points', '2025-11-28 15:25:05'),
(9, 9, 'Cash', '2025-12-02 09:15:30'),
(10, 10, 'Credit Card', '2025-12-05 14:40:18'),
(11, 11, 'Loyalty Points', '2025-12-07 11:25:33'),
(12, 12, 'Cash', '2025-12-09 16:50:45'),
(13, 13, 'Credit Card', '2025-12-11 09:30:20'),
(14, 14, 'Credit Card', '2025-12-13 20:22:15'),
(15, 15, 'Loyalty Points', '2025-12-15 13:15:28'),
(16, 16, 'Credit Card', '2025-12-18 11:02:10'),
(17, 17, 'Cash', '2025-12-20 18:35:40'),
(18, 18, 'Loyalty Points', '2025-12-22 12:12:25'),
(19, 19, 'Credit Card', '2025-12-24 09:03:15'),
(20, 20, 'Credit Card', '2025-12-26 09:20:15'),
(21, 21, 'Loyalty Points', '2025-12-27 14:35:42'),
(22, 22, 'Cash', '2025-12-28 11:50:33'),
(23, 23, 'Credit Card', '2025-12-29 16:25:18'),
(24, 24, 'Credit Card', '2025-12-30 10:15:27'),
(25, 25, 'Loyalty Points', '2025-12-31 13:30:55'),
(26, 26, 'Credit Card', '2026-01-02 08:45:40');

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

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `stock_quantity`, `description`, `short_desc`, `unit_price`, `product_point`, `sold_number`, `category_code`, `status`, `created_at`, `isdeleted`, `low_stock_threshold`) VALUES
(1, 'Claw Hammer 23mm', 150, 'Professional claw hammer with fiberglass handle and anti-slip grip, designed for both home and professional use. Durable steel head provides maximum impact, ideal for carpentry, construction, and DIY projects.', 'Claw Hammer 23mm', 45.90, 46, 45, 4, 'active', '2025-11-12 19:46:04', 0, 10),
(2, 'Cross Pein Pin Hammer 14mm', 200, 'Lightweight precision pin hammer with ergonomic handle, perfect for metalworking, delicate woodworking, and hobby projects. Balanced for optimal control and precision.', 'Cross Pein Pin Hammer 14mm', 32.50, 33, 60, 4, 'active', '2025-11-13 19:46:04', 0, 10),
(3, 'Cross Pein Pin Hammer 18mm', 180, 'Medium-sized cross pein hammer designed for general work tasks, including shaping metal, driving pins, and carpentry. Ergonomic handle reduces fatigue during prolonged use.', 'Cross Pein Pin Hammer 18mm', 38.90, 39, 55, 4, 'active', '2025-11-14 19:46:04', 0, 10),
(4, 'Magnetic Claw Hammer', 120, 'Claw hammer with built-in magnetic nail starter for one-handed nailing. High-strength steel head and shock-absorbing fiberglass handle for added durability.', 'Magnetic Claw Hammer', 59.90, 60, 30, 4, 'active', '2025-11-15 19:46:04', 0, 10),
(5, 'Short-Handle Mini Hammer (16 cm)', 5, 'Compact hammer for tight spaces and precision tasks. Lightweight and portable, ideal for crafts, small repairs, and DIY projects.', 'Short-Handle Mini Hammer', 19.90, 20, 2, 4, 'active', '2025-11-16 19:46:04', 0, 10),
(6, 'CHISEL SET MGZ-3PC', 80, 'Professional 3-piece woodworking chisel set made of high-quality steel. Perfect for carving, shaping, and precision woodworking.', '3-Piece Woodworking Chisel Set', 89.90, 90, 25, 4, 'active', '2025-11-17 19:46:04', 0, 10),
(7, 'Hammer with Rubber Grip', 140, 'Ergonomic hammer with non-slip rubber handle for safe and comfortable use. Ideal for construction, home repairs, and DIY projects.', 'Hammer with Rubber Grip', 49.90, 50, 40, 4, 'active', '2025-11-18 19:46:04', 0, 10),
(8, 'Precision Screwdriver Set', 200, '32-in-1 magnetic precision screwdriver kit, including flat, Phillips, and specialty bits. Perfect for electronics, watches, and small repairs.', 'Precision Screwdriver Set', 69.90, 70, 85, 4, 'active', '2025-11-19 19:46:04', 0, 10),
(9, 'INCGO Removeable Screwdriver 2in1 (19cm)', 250, 'High-quality reversible screwdriver combining flat and Phillips heads. Ideal for home, automotive, and electronic repairs.', 'INCGO 2in1 Screwdriver', 24.90, 25, 100, 4, 'active', '2025-11-20 19:46:04', 0, 10),
(10, 'EMTOP Cordless Screwdriver ECSR0403', 60, '4V rechargeable cordless screwdriver with LED light and ergonomic handle. Suitable for assembly, furniture installation, and small home projects.', 'EMTOP Cordless Screwdriver', 149.90, 150, 15, 4, 'active', '2025-11-21 19:46:04', 0, 10),
(11, 'TACTIX Insulated Screwdriver Set (6 pieces)', 90, 'VDE 1000V certified insulated screwdriver set. Provides safety while working with electrical equipment, including various common sizes.', 'TACTIX Insulated Screwdriver Set', 99.90, 100, 20, 4, 'active', '2025-11-22 19:46:04', 0, 10),
(12, 'Knife with Cutter Blade Set', 300, 'Heavy-duty utility knife with 10 spare blades. Ideal for cutting cardboard, paper, and light materials in home and workshop use.', 'Knife with Cutter Blade Set', 29.90, 30, 120, 4, 'active', '2025-11-23 19:46:04', 0, 10),
(13, 'Heavy Duty Batik Protective Hand Gloves (12 Pairs)', 100, 'Industrial cotton gloves – 12 pairs bulk pack. Provides protection against scratches, minor cuts, and dirt during manual work.', 'Protective Hand Gloves 12 Pairs', 79.90, 80, 45, 4, 'active', '2025-11-24 19:46:04', 0, 10),
(14, 'Multi-Use Foldable PVC Hand Truck Trolley (300kg)', 4, 'Portable folding trolley with 300kg capacity. Suitable for transporting boxes, luggage, and heavy items with ease. Space-saving foldable design.', 'Foldable Hand Truck 300kg', 199.90, 200, 2, 1, 'active', '2025-11-25 19:46:04', 0, 10),
(15, '31-Piece T-handle Wrench/Screwdriver Set', 70, 'Complete T-handle hex & Torx tool set for mechanical, automotive, and DIY tasks. Includes a wide range of sizes for versatility.', '31-Piece T-handle Set', 179.90, 180, 15, 4, 'active', '2025-11-26 19:46:04', 0, 10),
(16, '32-in-1 Magnetic Electron Screwdriver Set', 180, 'Magnetic precision screwdriver kit with extension rod. Perfect for electronics, computer assembly, and small maintenance tasks.', '32-in-1 Magnetic Screwdriver', 89.90, 90, 65, 4, 'active', '2025-11-27 19:46:04', 0, 10),
(17, 'Plastic Storage Drawer Rack Desk Organizer 3 Tier', 150, '3-tier plastic desk drawer organizer to store stationery, documents, and small items neatly. Durable and stackable for office or home use.', '3-Tier Desk Organizer', 49.90, 50, 50, 1, 'active', '2025-11-28 19:46:04', 0, 10),
(18, 'Wall-Mounted Tissue Box-Cyan', 200, 'Waterproof wall-mounted tissue holder in cyan color. Ideal for kitchens, bathrooms, or offices. Easy to install and clean.', 'Wall-Mounted Tissue Box Cyan', 39.90, 40, 80, 1, 'active', '2025-11-29 19:46:04', 0, 10),
(19, 'Wall-Mounted Stainless-Steel 2-Layer Bathroom Rack', 80, 'Rust-proof 2-tier stainless steel bathroom shelf for toiletries, towels, and accessories. Elegant and durable design.', 'Stainless Steel Bathroom Rack', 129.90, 130, 25, 1, 'active', '2025-11-30 19:46:04', 0, 10),
(20, 'CHANYI 3 Tier Plastic Document File Tray', 120, 'Stackable A4 document tray organizer. Keep your office or study desk tidy and organized with three levels of storage.', 'CHANYI 3-Tier File Tray', 59.90, 60, 40, 2, 'active', '2025-12-01 19:46:04', 0, 10),
(21, '3-Tier Heavy-Duty Workshop Trolley Rack', 2, 'Mobile 3-tier tool trolley with heavy-duty wheels for easy movement. Perfect for workshop, garage, or industrial use.', '3-Tier Workshop Trolley', 289.90, 290, 1, 1, 'active', '2025-12-02 19:46:04', 0, 10),
(22, '3 Shelf Multipurpose Rectangular Rack', 60, 'Sturdy rectangular storage shelf suitable for kitchen, office, or garage. Holds boxes, supplies, and tools efficiently.', '3-Shelf Rectangular Rack', 139.90, 140, 20, 1, 'active', '2025-12-03 19:46:04', 0, 10),
(23, 'Car Universal Phone Holder', 250, '360° adjustable dashboard and windshield phone mount. Securely holds smartphones during driving for hands-free use.', 'Car Universal Phone Holder', 39.90, 40, 110, 3, 'active', '2025-12-04 19:46:04', 0, 10),
(24, 'HOTAK Driver Click Extendable Ratchet 3/8\"', 90, 'Professional extendable ratchet wrench 37cm. Ideal for automotive, mechanical, and DIY repair tasks. Durable steel construction.', 'HOTAK Extendable Ratchet', 159.90, 160, 35, 4, 'active', '2025-12-05 19:46:04', 0, 10),
(25, 'Microfiber Wash Mitt Cleaning Gloves (1pc)', 400, 'Ultra-soft scratch-free car washing glove. Gently cleans surfaces without leaving scratches, perfect for automotive care.', 'Microfiber Wash Mitt', 19.90, 20, 150, 3, 'active', '2025-12-06 19:46:04', 0, 10),
(26, 'Car Dashboard Anti-Slip Mat (30x15cm)', 500, 'Non-slip mat for phone, coins, and accessories. Keeps items in place on car dashboard during travel.', 'Anti-Slip Dashboard Mat', 15.90, 16, 200, 3, 'active', '2025-12-07 19:46:04', 0, 10),
(27, 'WAXCO Auto Silicon Car Lubricant Spray (300ml)', 300, 'Silicon lubricant & protector spray. Ideal for car door seals, rubber gaskets, and household applications.', 'WAXCO Silicon Spray 300ml', 29.90, 30, 90, 3, 'active', '2025-12-08 19:46:04', 0, 10),
(28, 'ROLLINGDOG Heavy Duty Scissors (216mm)', 180, 'Titanium-coated ultra-sharp scissors for precise cutting of paper, fabric, and light materials. Durable and long-lasting.', 'ROLLINGDOG Heavy Duty Scissors', 49.90, 50, 60, 4, 'active', '2025-12-09 19:46:04', 0, 10),
(29, 'EMTOP Auto Air Compressor (35L/min)', 8, '12V portable tire inflator with LED gauge. Quickly inflates car, bike, and motorcycle tires. Compact and easy to carry.', 'EMTOP Car Air Compressor', 249.90, 250, 5, 4, 'active', '2025-12-10 19:46:04', 0, 10),
(30, 'Stainless Steel Scissor', 400, 'General-purpose rust-resistant scissors for home, office, and crafts. Comfortable handles and durable stainless steel blades.', 'Stainless Steel Scissor', 12.90, 13, 130, 2, 'active', '2025-12-11 19:46:04', 0, 10),
(31, 'TACTIX Color Quick Change Driver Bit Set (32 pcs)', 150, 'Color-coded magnetic bit set with holder. Quickly swap bits for various screwdriver applications. Durable and easy to organize.', 'TACTIX 32-Pc Bit Set', 79.90, 80, 55, 4, 'active', '2025-12-12 19:46:04', 0, 10),
(32, 'TACTIX 39-Drawers Storage Bin', 40, 'Professional parts organizer with 39 drawers. Ideal for screws, nuts, bolts, and small workshop components.', 'TACTIX 39-Drawer Storage Bin', 199.90, 200, 10, 1, 'active', '2025-12-13 19:46:04', 0, 10),
(33, 'Socket Tool Set (46 pieces)', 60, 'Complete 1/4\" & 3/8\" drive socket set for automotive, mechanical, and DIY tasks. Includes ratchets, extensions, and sockets of various sizes.', '46-Piece Socket Set', 289.90, 290, 18, 4, 'active', '2025-12-14 19:46:04', 0, 10),
(34, '2 Drawers Storage Box (32cm)', 200, 'Stackable clear plastic drawer organizer for office or home. Keeps small items neatly stored and easily accessible.', '2-Drawer Storage Box', 39.90, 40, 75, 1, 'active', '2025-12-15 19:46:04', 0, 10),
(35, 'Durable Bamboo Wooden Tissue Box (25x12cm)', 180, 'Eco-friendly bamboo tissue holder. Stylish design for home or office, keeps tissues clean and dry.', 'Bamboo Wooden Tissue Box', 49.90, 50, 45, 1, 'active', '2025-12-16 19:46:04', 0, 10),
(36, 'COMIX Marker Pen Black (12 Pcs)', 300, 'Permanent black marker pen set. Ideal for writing, labeling, and office or school projects. Durable ink with smooth flow.', 'COMIX Black Marker 12-Pack', 29.90, 30, 95, 2, 'active', '2025-12-17 19:46:04', 0, 10),
(37, 'COMIX A5 Business Notebook (122 sheets)', 250, 'Premium hardcover A5 notebook with 122 lined sheets. Perfect for office, school, or personal notes.', 'COMIX A5 Notebook', 24.90, 25, 60, 2, 'active', '2025-12-18 19:46:04', 0, 10),
(38, '2B Mechanical Pencil & Lead Set', 500, '0.5mm mechanical pencil with extra lead set. Ideal for writing, drawing, and office or school work.', '2B Mechanical Pencil Set', 12.90, 13, 200, 2, 'active', '2025-12-19 19:46:04', 0, 10),
(39, 'Sticky Notes', 400, 'Assorted color sticky notes pack (6 pads). Perfect for reminders, notes, and organization at home, school, or office.', 'Sticky Notes 6-Pack', 19.90, 20, 150, 2, 'active', '2025-12-20 19:46:04', 0, 10);

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

--
-- Dumping data for table `productvisualmedia`
--

INSERT INTO `productvisualmedia` (`media_id`, `product_id`, `position`, `created_at`, `file_path`, `alt`, `is_show`, `type`) VALUES
(1, 1, 0, '2025-12-21 19:46:04', 'images/product/claw_hammer_23mm-0.jpg', 'Claw Hammer 23mm', 1, 'Image'),
(2, 1, 1, '2025-12-21 19:46:04', 'images/product/claw_hammer_23mm-1.jpg', 'Claw Hammer 23mm', 0, 'Image'),
(3, 1, 2, '2025-12-21 19:46:04', 'images/product/claw_hammer_23mm-2.jpg', 'Claw Hammer 23mm', 0, 'Image'),
(4, 2, 0, '2025-12-21 19:46:04', 'images/product/cross_pein_pin_hammer_14mm-0.jpg', 'Cross Pein Pin Hammer 14mm', 1, 'Image'),
(5, 2, 1, '2025-12-21 19:46:04', 'images/product/cross_pein_pin_hammer_14mm-1.jpg', 'Cross Pein Pin Hammer 14mm', 0, 'Image'),
(6, 3, 0, '2025-12-21 19:46:04', 'images/product/cross_pein_pin_hammer_18mm-0.jpg', 'Cross Pein Pin Hammer 18mm', 1, 'Image'),
(7, 3, 1, '2025-12-21 19:46:04', 'images/product/cross_pein_pin_hammer_18mm-1.jpg', 'Cross Pein Pin Hammer 18mm', 0, 'Image'),
(8, 4, 0, '2025-12-21 19:46:04', 'images/product/magnetic_claw_hammer-0.jpg', 'Magnetic Claw Hammer', 1, 'Image'),
(9, 4, 1, '2025-12-21 19:46:04', 'images/product/magnetic_claw_hammer-1.jpg', 'Magnetic Claw Hammer', 0, 'Image'),
(10, 4, 2, '2025-12-21 19:46:04', 'images/product/magnetic_claw_hammer-2.jpg', 'Magnetic Claw Hammer', 0, 'Image'),
(11, 5, 0, '2025-12-21 19:46:04', 'images/product/short_handle_mini_hammer-0.jpg', 'Short-Handle Mini Hammer (16 cm)', 1, 'Image'),
(12, 5, 1, '2025-12-21 19:46:04', 'images/product/short_handle_mini_hammer-0.jpg', 'Short-Handle Mini Hammer (16 cm)', 0, 'Image'),
(13, 5, 2, '2025-12-21 19:46:04', 'images/product/short_handle_mini_hammer-0.jpg', 'Short-Handle Mini Hammer (16 cm)', 0, 'Image'),
(14, 6, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_3-piece_woodworking_chisel_set-0.png', 'CHISEL SET MGZ-3PC', 1, 'Image'),
(15, 6, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_3-piece_woodworking_chisel_set-1.png', 'CHISEL SET MGZ-3PC', 0, 'Image'),
(16, 6, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_3-piece_woodworking_chisel_set-2.png', 'CHISEL SET MGZ-3PC', 0, 'Image'),
(17, 6, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_3-piece_woodworking_chisel_set-3.png', 'CHISEL SET MGZ-3PC', 0, 'Image'),
(18, 6, 4, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_3-piece_woodworking_chisel_set-4.png', 'CHISEL SET MGZ-3PC', 0, 'Image'),
(19, 7, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Hammer_With_Rubber_Grip-0.png', 'Hammer with Rubber Grip', 1, 'Image'),
(20, 7, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Hammer_With_Rubber_Grip-1.png', 'Hammer with Rubber Grip', 0, 'Image'),
(21, 7, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Hammer_With_Rubber_Grip-2.png', 'Hammer with Rubber Grip', 0, 'Image'),
(22, 7, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Hammer_With_Rubber_Grip-3.png', 'Hammer with Rubber Grip', 0, 'Image'),
(23, 8, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Precision_Screwdriver_Set-0.png', 'Precision Screwdriver Set', 1, 'Image'),
(24, 8, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Precision_Screwdriver_Set-1.png', 'Precision Screwdriver Set', 0, 'Image'),
(25, 8, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Precision_Screwdriver_Set-2.png', 'Precision Screwdriver Set', 0, 'Image'),
(26, 8, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Precision_Screwdriver_Set-3.png', 'Precision Screwdriver Set', 0, 'Image'),
(27, 8, 4, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Precision_Screwdriver_Set-4.png', 'Precision Screwdriver Set', 0, 'Image'),
(28, 9, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_INCGO_Removeable_Screwdriver-0.png', 'INCGO Removeable Screwdriver 2in1 (19cm)', 1, 'Image'),
(29, 9, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_INCGO_Removeable_Screwdriver-1.png', 'INCGO Removeable Screwdriver 2in1 (19cm)', 0, 'Image'),
(30, 9, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_INCGO_Removeable_Screwdriver-2.png', 'INCGO Removeable Screwdriver 2in1 (19cm)', 0, 'Image'),
(31, 10, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_EMTOP_cordless_screwdriver_ECSR0403-0.png', 'EMTOP Cordless Screwdriver ECSR0403', 1, 'Image'),
(32, 10, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_EMTOP_cordless_screwdriver_ECSR0403-1.png', 'EMTOP Cordless Screwdriver ECSR0403', 0, 'Image'),
(33, 10, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_EMTOP_cordless_screwdriver_ECSR0403-2.png', 'EMTOP Cordless Screwdriver ECSR0403', 0, 'Image'),
(34, 11, 0, '2025-12-21 19:46:04', 'images/product/tactix_insulated_screwdriver-0.png', 'TACTIX Insulated Screwdriver Set (6 pieces)', 1, 'Image'),
(35, 11, 1, '2025-12-21 19:46:04', 'images/product/tactix_insulated_screwdriver-1.png', 'TACTIX Insulated Screwdriver Set (6 pieces)', 0, 'Image'),
(36, 11, 2, '2025-12-21 19:46:04', 'images/product/tactix_insulated_screwdriver-2.png', 'TACTIX Insulated Screwdriver Set (6 pieces)', 0, 'Image'),
(37, 11, 3, '2025-12-21 19:46:04', 'images/product/tactix_insulated_screwdriver-3.png', 'TACTIX Insulated Screwdriver Set (6 pieces)', 0, 'Image'),
(38, 12, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Knife_with_Cutter_Blade_Set-0.png', '(MR.DIY) Knife with Cutter Blade Set', 1, 'Image'),
(39, 12, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Knife_with_Cutter_Blade_Set-1.png', '(MR.DIY) Knife with Cutter Blade Set', 0, 'Image'),
(40, 12, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Knife_with_Cutter_Blade_Set-2.png', '(MR.DIY) Knife with Cutter Blade Set', 0, 'Image'),
(41, 12, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Knife_with_Cutter_Blade_Set-3.png', '(MR.DIY) Knife with Cutter Blade Set', 0, 'Image'),
(42, 13, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Heavy_Duty_Batik_Protective_Hand_Gloves-0.png', 'Heavy Duty Batik Protective Hand Gloves (12 Pairs)', 1, 'Image'),
(43, 13, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Heavy_Duty_Batik_Protective_Hand_Gloves-1.png', 'Heavy Duty Batik Protective Hand Gloves (12 Pairs)', 0, 'Image'),
(44, 13, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Heavy_Duty_Batik_Protective_Hand_Gloves-2.png', 'Heavy Duty Batik Protective Hand Gloves (12 Pairs)', 0, 'Image'),
(45, 13, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Heavy_Duty_Batik_Protective_Hand_Gloves-3.png', 'Heavy Duty Batik Protective Hand Gloves (12 Pairs)', 0, 'Image'),
(46, 14, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Multi-Use_Foldable_PVC_Hand_Truck_Trolley-0.png', 'Multi-Use Foldable PVC Hand Truck Trolley (300kg)', 1, 'Image'),
(47, 14, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Multi-Use_Foldable_PVC_Hand_Truck_Trolley-1.png', 'Multi-Use Foldable PVC Hand Truck Trolley (300kg)', 0, 'Image'),
(48, 14, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Multi-Use_Foldable_PVC_Hand_Truck_Trolley-2.png', 'Multi-Use Foldable PVC Hand Truck Trolley (300kg)', 0, 'Image'),
(49, 14, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Multi-Use_Foldable_PVC_Hand_Truck_Trolley-3.png', 'Multi-Use Foldable PVC Hand Truck Trolley (300kg)', 0, 'Image'),
(50, 15, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_31-Piece_T-handle_WrenchScrewdriver_Set-0.png', '(MR.DIY) 31-Piece T-handle Wrench/Screwdriver Set', 1, 'Image'),
(51, 15, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_31-Piece_T-handle_WrenchScrewdriver_Set-1.png', '(MR.DIY) 31-Piece T-handle Wrench/Screwdriver Set', 0, 'Image'),
(52, 15, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_31-Piece_T-handle_WrenchScrewdriver_Set-2.png', '(MR.DIY) 31-Piece T-handle Wrench/Screwdriver Set', 0, 'Image'),
(53, 15, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_31-Piece_T-handle_WrenchScrewdriver_Set-3.png', '(MR.DIY) 31-Piece T-handle Wrench/Screwdriver Set', 0, 'Image'),
(54, 15, 4, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_31-Piece_T-handle_WrenchScrewdriver_Set-4.png', '(MR.DIY) 31-Piece T-handle Wrench/Screwdriver Set', 0, 'Image'),
(55, 15, 5, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_31-Piece_T-handle_WrenchScrewdriver_Set-5.png', '(MR.DIY) 31-Piece T-handle Wrench/Screwdriver Set', 0, 'Image'),
(56, 16, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_32-in-1_Magnetic_Electron_Screwdriver_Tool_Set-0.png', '(MR.DIY) 32-in-1 Magnetic Electron Screwdriver Set', 1, 'Image'),
(57, 16, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_32-in-1_Magnetic_Electron_Screwdriver_Tool_Set-1.png', '(MR.DIY) 32-in-1 Magnetic Electron Screwdriver Set', 0, 'Image'),
(58, 16, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_32-in-1_Magnetic_Electron_Screwdriver_Tool_Set-2.png', '(MR.DIY) 32-in-1 Magnetic Electron Screwdriver Set', 0, 'Image'),
(59, 16, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_32-in-1_Magnetic_Electron_Screwdriver_Tool_Set-3.png', '(MR.DIY) 32-in-1 Magnetic Electron Screwdriver Set', 0, 'Image'),
(60, 16, 4, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_32-in-1_Magnetic_Electron_Screwdriver_Tool_Set-4.png', '(MR.DIY) 32-in-1 Magnetic Electron Screwdriver Set', 0, 'Image'),
(61, 16, 5, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_32-in-1_Magnetic_Electron_Screwdriver_Tool_Set-5.png', '(MR.DIY) 32-in-1 Magnetic Electron Screwdriver Set', 0, 'Image'),
(62, 17, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Plastic_storage_drawer_rack_desk_organizer_3-tier-0.png', '(MR.DIY) Plastic Storage Drawer Rack Desk Organizer 3 Tier (25.5 x 18 x 24cm)', 1, 'Image'),
(63, 17, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Plastic_storage_drawer_rack_desk_organizer_3-tier-1.png', '(MR.DIY) Plastic Storage Drawer Rack Desk Organizer 3 Tier (25.5 x 18 x 24cm)', 0, 'Image'),
(64, 17, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Plastic_storage_drawer_rack_desk_organizer_3-tier-2.png', '(MR.DIY) Plastic Storage Drawer Rack Desk Organizer 3 Tier (25.5 x 18 x 24cm)', 0, 'Image'),
(65, 17, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_Plastic_storage_drawer_rack_desk_organizer_3-tier-3.png', '(MR.DIY) Plastic Storage Drawer Rack Desk Organizer 3 Tier (25.5 x 18 x 24cm)', 0, 'Image'),
(66, 18, 0, '2025-12-21 19:46:04', 'images/product/plastic_wall-Mounted_waterproof_tissue_storage_box_cyan-0.png', '(MR.DIY) Wall-Mounted Tissue Box-Cyan', 1, 'Image'),
(67, 18, 1, '2025-12-21 19:46:04', 'images/product/plastic_wall-Mounted_waterproof_tissue_storage_box_cyan-1.png', '(MR.DIY) Wall-Mounted Tissue Box-Cyan', 0, 'Image'),
(68, 19, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_wall-mounted_stainless-steel_2-layer_bathroom_rack-0.png', '(MR.DIY) Wall-Mounted Stainless-Steel 2-Layer Bathroom Rack (29cm x 35cm)', 1, 'Image'),
(69, 19, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_wall-mounted_stainless-steel_2-layer_bathroom_rack-1.png', '(MR.DIY) Wall-Mounted Stainless-Steel 2-Layer Bathroom Rack (29cm x 35cm)', 0, 'Image'),
(70, 19, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_wall-mounted_stainless-steel_2-layer_bathroom_rack-2.png', '(MR.DIY) Wall-Mounted Stainless-Steel 2-Layer Bathroom Rack (29cm x 35cm)', 0, 'Image'),
(71, 19, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_wall-mounted_stainless-steel_2-layer_bathroom_rack-3.png', '(MR.DIY) Wall-Mounted Stainless-Steel 2-Layer Bathroom Rack (29cm x 35cm)', 0, 'Image'),
(72, 20, 0, '2025-12-21 19:46:04', 'images/product/CHANYI-3_Tier_Plastic_Document_File_Tray-0.png', '(MR.DIY) CHANYI 3 Tier Plastic Document File Tray', 1, 'Image'),
(73, 20, 1, '2025-12-21 19:46:04', 'images/product/CHANYI-3_Tier_Plastic_Document_File_Tray-1.png', '(MR.DIY) CHANYI 3 Tier Plastic Document File Tray', 0, 'Image'),
(74, 20, 2, '2025-12-21 19:46:04', 'images/product/CHANYI-3_Tier_Plastic_Document_File_Tray-2.png', '(MR.DIY) CHANYI 3 Tier Plastic Document File Tray', 0, 'Image'),
(75, 21, 0, '2025-12-21 19:46:04', 'images/product/3-tier_multifunction_heavy-duty_workshop_trolley_rack_with_plastic_wheels-0.png', '(MR.DIY) 3-Tier Multifunction Heavy-Duty Workshop Trolley Rack With Plastic Wheels', 1, 'Image'),
(76, 21, 1, '2025-12-21 19:46:04', 'images/product/3-tier_multifunction_heavy-duty_workshop_trolley_rack_with_plastic_wheels-1.png', '(MR.DIY) 3-Tier Multifunction Heavy-Duty Workshop Trolley Rack With Plastic Wheels', 0, 'Image'),
(77, 21, 2, '2025-12-21 19:46:04', 'images/product/3-tier_multifunction_heavy-duty_workshop_trolley_rack_with_plastic_wheels-2.png', '(MR.DIY) 3-Tier Multifunction Heavy-Duty Workshop Trolley Rack With Plastic Wheels', 0, 'Image'),
(78, 21, 3, '2025-12-21 19:46:04', 'images/product/3-tier_multifunction_heavy-duty_workshop_trolley_rack_with_plastic_wheels-3.png', '(MR.DIY) 3-Tier Multifunction Heavy-Duty Workshop Trolley Rack With Plastic Wheels', 0, 'Image'),
(79, 21, 4, '2025-12-21 19:46:04', 'images/product/3-tier_multifunction_heavy-duty_workshop_trolley_rack_with_plastic_wheels-4.png', '(MR.DIY) 3-Tier Multifunction Heavy-Duty Workshop Trolley Rack With Plastic Wheels', 0, 'Image'),
(80, 21, 5, '2025-12-21 19:46:04', 'images/product/3-tier_multifunction_heavy-duty_workshop_trolley_rack_with_plastic_wheels-5.png', '(MR.DIY) 3-Tier Multifunction Heavy-Duty Workshop Trolley Rack With Plastic Wheels', 0, 'Image'),
(81, 22, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_3_shelf_multipurpose_rectangular_rack-0.png', '(MR.DIY) 3 Shelf Multipurpose Rectangular Rack', 1, 'Image'),
(82, 22, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_3_shelf_multipurpose_rectangular_rack-1.png', '(MR.DIY) 3 Shelf Multipurpose Rectangular Rack', 0, 'Image'),
(83, 23, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_car_universal_phone_holder-0.png', '(MR.DIY) Car Universal Phone Holder', 1, 'Image'),
(84, 23, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_car_universal_phone_holder-1.png', '(MR.DIY) Car Universal Phone Holder', 0, 'Image'),
(85, 23, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_car_universal_phone_holder-2.png', '(MR.DIY) Car Universal Phone Holder', 0, 'Image'),
(86, 24, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_HOTAK_Driver_Click_Extendable_Ratchet-0.png', '(MR.DIY) HOTAK Driver Click Extendable Ratchet 3/8\" YJTS-3365 (37cm)', 1, 'Image'),
(87, 24, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_HOTAK_Driver_Click_Extendable_Ratchet-1.png', '(MR.DIY) HOTAK Driver Click Extendable Ratchet 3/8\" YJTS-3365 (37cm)', 0, 'Image'),
(88, 24, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_HOTAK_Driver_Click_Extendable_Ratchet-2.png', '(MR.DIY) HOTAK Driver Click Extendable Ratchet 3/8\" YJTS-3365 (37cm)', 0, 'Image'),
(89, 24, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_HOTAK_Driver_Click_Extendable_Ratchet-3.png', '(MR.DIY) HOTAK Driver Click Extendable Ratchet 3/8\" YJTS-3365 (37cm)', 0, 'Image'),
(90, 25, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_microfiber_eash_mitt-0.png', '(MR.DIY) Microfiber Wash Mitt Cleaning Gloves (1pc)', 1, 'Image'),
(91, 26, 0, '2025-12-21 19:46:04', 'images/product/car_dashboard_anti-slip_mat-0.png', '(MR.DIY) Car Dashboard Anti-Slip Mat (30cm x 15cm)', 1, 'Image'),
(92, 26, 1, '2025-12-21 19:46:04', 'images/product/car_dashboard_anti-slip_mat-1.png', '(MR.DIY) Car Dashboard Anti-Slip Mat (30cm x 15cm)', 0, 'Image'),
(93, 26, 2, '2025-12-21 19:46:04', 'images/product/car_dashboard_anti-slip_mat-2.png', '(MR.DIY) Car Dashboard Anti-Slip Mat (30cm x 15cm)', 0, 'Image'),
(94, 26, 3, '2025-12-21 19:46:04', 'images/product/car_dashboard_anti-slip_mat-3.png', '(MR.DIY) Car Dashboard Anti-Slip Mat (30cm x 15cm)', 0, 'Image'),
(95, 27, 0, '2025-12-21 19:46:04', 'images/product/WAXCO_auto_silicon_car_lubricant_cleaner_protect_auto_parts_spray-0.png', '(MR.DIY) WAXCO Auto Silicon Car Lubricant Cleaner Protect Auto Parts Spray (300ml)', 1, 'Image'),
(96, 27, 1, '2025-12-21 19:46:04', 'images/product/WAXCO_auto_silicon_car_lubricant_cleaner_protect_auto_parts_spray-1.png', '(MR.DIY) WAXCO Auto Silicon Car Lubricant Cleaner Protect Auto Parts Spray (300ml)', 0, 'Image'),
(97, 27, 2, '2025-12-21 19:46:04', 'images/product/WAXCO_auto_silicon_car_lubricant_cleaner_protect_auto_parts_spray-2.png', '(MR.DIY) WAXCO Auto Silicon Car Lubricant Cleaner Protect Auto Parts Spray (300ml)', 0, 'Image'),
(98, 27, 3, '2025-12-21 19:46:04', 'images/product/WAXCO_auto_silicon_car_lubricant_cleaner_protect_auto_parts_spray-3.png', '(MR.DIY) WAXCO Auto Silicon Car Lubricant Cleaner Protect Auto Parts Spray (300ml)', 0, 'Image'),
(99, 28, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_ROLLINGDOG_heavy_duty_scissors-0.png', '(MR.DIY) ROLLINGDOG Heavy Duty Scissors (216mm)', 1, 'Image'),
(100, 28, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_ROLLINGDOG_heavy_duty_scissors-1.png', '(MR.DIY) ROLLINGDOG Heavy Duty Scissors (216mm)', 0, 'Image'),
(101, 28, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_ROLLINGDOG_heavy_duty_scissors-2.png', '(MR.DIY) ROLLINGDOG Heavy Duty Scissors (216mm)', 0, 'Image'),
(102, 28, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_ROLLINGDOG_heavy_duty_scissors-3.png', '(MR.DIY) ROLLINGDOG Heavy Duty Scissors (216mm)', 0, 'Image'),
(103, 28, 4, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_ROLLINGDOG_heavy_duty_scissors-4.png', '(MR.DIY) ROLLINGDOG Heavy Duty Scissors (216mm)', 0, 'Image'),
(104, 28, 5, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_ROLLINGDOG_heavy_duty_scissors-5.png', '(MR.DIY) ROLLINGDOG Heavy Duty Scissors (216mm)', 0, 'Image'),
(105, 29, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_EMTOP_auto_air_compressor-0.png', '(MR.DIY) EMTOP Auto Air Compressor (35L/min) - EAAC3501', 1, 'Image'),
(106, 29, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_EMTOP_auto_air_compressor-1.png', '(MR.DIY) EMTOP Auto Air Compressor (35L/min) - EAAC3501', 0, 'Image'),
(107, 29, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_EMTOP_auto_air_compressor-2.png', '(MR.DIY) EMTOP Auto Air Compressor (35L/min) - EAAC3501', 0, 'Image'),
(108, 30, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_stainless_steel_scissor-0.png', '(MR.DIY) Stainless Steel Scissor', 1, 'Image'),
(109, 30, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_stainless_steel_scissor-1.png', '(MR.DIY) Stainless Steel Scissor', 0, 'Image'),
(110, 30, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_stainless_steel_scissor-2.png', '(MR.DIY) Stainless Steel Scissor', 0, 'Image'),
(111, 31, 0, '2025-12-21 19:46:04', 'images/product/tactix_color_quick_change_driver_bit_set-0.png', '(MR.DIY) TACTIX Color Quick Change Driver Bit Set (32 pieces)', 1, 'Image'),
(112, 31, 1, '2025-12-21 19:46:04', 'images/product/tactix_color_quick_change_driver_bit_set-1.png', '(MR.DIY) TACTIX Color Quick Change Driver Bit Set (32 pieces)', 0, 'Image'),
(113, 31, 2, '2025-12-21 19:46:04', 'images/product/tactix_color_quick_change_driver_bit_set-2.png', '(MR.DIY) TACTIX Color Quick Change Driver Bit Set (32 pieces)', 0, 'Image'),
(114, 32, 0, '2025-12-21 19:46:04', 'images/product/TACTIX_39-Drawers_Storage_Bin-0.png', '(MR.DIY) TACTIX 39-Drawers Storage Bin', 1, 'Image'),
(115, 32, 1, '2025-12-21 19:46:04', 'images/product/TACTIX_39-Drawers_Storage_Bin-1.png', '(MR.DIY) TACTIX 39-Drawers Storage Bin', 0, 'Image'),
(116, 32, 2, '2025-12-21 19:46:04', 'images/product/TACTIX_39-Drawers_Storage_Bin-2.png', '(MR.DIY) TACTIX 39-Drawers Storage Bin', 0, 'Image'),
(117, 32, 3, '2025-12-21 19:46:04', 'images/product/TACTIX_39-Drawers_Storage_Bin-3.png', '(MR.DIY) TACTIX 39-Drawers Storage Bin', 0, 'Image'),
(118, 32, 4, '2025-12-21 19:46:04', 'images/product/TACTIX_39-Drawers_Storage_Bin-4.png', '(MR.DIY) TACTIX 39-Drawers Storage Bin', 0, 'Image'),
(119, 33, 0, '2025-12-21 19:46:04', 'images/product/scoket_tools_set-0.png', '(MR.DIY) Socket Tool Set (46 pieces)', 1, 'Image'),
(120, 33, 1, '2025-12-21 19:46:04', 'images/product/scoket_tools_set-1.png', '(MR.DIY) Socket Tool Set (46 pieces)', 0, 'Image'),
(121, 33, 2, '2025-12-21 19:46:04', 'images/product/scoket_tools_set-2.png', '(MR.DIY) Socket Tool Set (46 pieces)', 0, 'Image'),
(122, 33, 3, '2025-12-21 19:46:04', 'images/product/scoket_tools_set-3.png', '(MR.DIY) Socket Tool Set (46 pieces)', 0, 'Image'),
(123, 34, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_2_drawers_storage_box-0.png', '(MR.DIY) 2 Drawers Storage Box (32cm)', 1, 'Image'),
(124, 34, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_2_drawers_storage_box-1.png', '(MR.DIY) 2 Drawers Storage Box (32cm)', 0, 'Image'),
(125, 34, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_2_drawers_storage_box-2.png', '(MR.DIY) 2 Drawers Storage Box (32cm)', 0, 'Image'),
(126, 34, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_2_drawers_storage_box-3.png', '(MR.DIY) 2 Drawers Storage Box (32cm)', 0, 'Image'),
(127, 35, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_durable_bamboo_wooden_tissue_box-0.png', '(MR.DIY) Durable Bamboo Wooden Tissue Box (25 x 12cm)', 1, 'Image'),
(128, 35, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_durable_bamboo_wooden_tissue_box-1.png', '(MR.DIY) Durable Bamboo Wooden Tissue Box (25 x 12cm)', 0, 'Image'),
(129, 35, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_durable_bamboo_wooden_tissue_box-2.png', '(MR.DIY) Durable Bamboo Wooden Tissue Box (25 x 12cm)', 0, 'Image'),
(130, 35, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_durable_bamboo_wooden_tissue_box-3.png', '(MR.DIY) Durable Bamboo Wooden Tissue Box (25 x 12cm)', 0, 'Image'),
(131, 35, 4, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_durable_bamboo_wooden_tissue_box-4.png', '(MR.DIY) Durable Bamboo Wooden Tissue Box (25 x 12cm)', 0, 'Image'),
(132, 36, 0, '2025-12-21 19:46:04', 'images/product/comix_marker_pen_black-0.png', '(MR.DIY) COMIX Marker Pen Black (0.5-1.5mm/12 Pcs)', 1, 'Image'),
(133, 36, 1, '2025-12-21 19:46:04', 'images/product/comix_marker_pen_black-1.png', '(MR.DIY) COMIX Marker Pen Black (0.5-1.5mm/12 Pcs)', 0, 'Image'),
(134, 36, 2, '2025-12-21 19:46:04', 'images/product/comix_marker_pen_black-2.png', '(MR.DIY) COMIX Marker Pen Black (0.5-1.5mm/12 Pcs)', 0, 'Image'),
(135, 36, 3, '2025-12-21 19:46:04', 'images/product/comix_marker_pen_black-3.png', '(MR.DIY) COMIX Marker Pen Black (0.5-1.5mm/12 Pcs)', 0, 'Image'),
(136, 37, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_A5_business_notebook-0.png', '(MR.DIY) COMIX A5 Business Notebook (122 sheets)', 1, 'Image'),
(137, 38, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_2B_Mechanical_Pencil&Lead_Set-0.png', '(MR.DIY) 2B Mechanical Pencil & Lead Set', 1, 'Image'),
(138, 38, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_2B_Mechanical_Pencil&Lead_Set-1.png', '(MR.DIY) 2B Mechanical Pencil & Lead Set', 0, 'Image'),
(139, 38, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_2B_Mechanical_Pencil&Lead_Set-2.png', '(MR.DIY) 2B Mechanical Pencil & Lead Set', 0, 'Image'),
(140, 39, 0, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_sticky_notes-0.png', '(MR.DIY) Sticky Notes', 1, 'Image'),
(141, 39, 1, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_sticky_notes-1.png', '(MR.DIY) Sticky Notes', 0, 'Image'),
(142, 39, 2, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_sticky_notes-2.png', '(MR.DIY) Sticky Notes', 0, 'Image'),
(143, 39, 3, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_sticky_notes-3.png', '(MR.DIY) Sticky Notes', 0, 'Image'),
(144, 39, 4, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_sticky_notes-4.png', '(MR.DIY) Sticky Notes', 0, 'Image'),
(145, 39, 5, '2025-12-21 19:46:04', 'images/product/(MR.DIY)_sticky_notes-5.png', '(MR.DIY) Sticky Notes', 0, 'Image');

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

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `user_id`, `product_id`, `reviewed_at`, `comment`, `is_valid`, `rating`) VALUES
(1, 'M003', 1, '2025-10-18 10:15:22', 'Solid hammer, great weight balance. Highly recommended!', 1, 4.8),
(2, 'M007', 1, '2025-11-10 14:30:11', 'Very sturdy, used it for framing work – no issues', 1, 5.0),
(3, 'M001', 4, '2025-10-16 12:44:55', 'The magnetic nail starter is a game changer! One-hand nailing FTW', 1, 5.0),
(4, 'M001', 10, '2025-10-17 09:20:33', 'Battery lasts long, LED light is super useful in dark corners', 1, 4.9),
(6, 'M004', 14, '2025-11-03 18:12:44', 'Moved 200kg of tiles easily. Folds flat – perfect for my van', 1, 5.0),
(7, 'M004', 21, '2025-11-04 11:30:00', 'Best purchase this year. My garage finally organized!', 1, 5.0),
(8, 'M002', 29, '2025-10-22 13:25:18', 'Fast inflation, digital gauge is accurate. Love the LED light', 1, 4.7),
(11, 'M005', 8, '2025-11-15 15:22:10', 'Fixed my laptop and PS5 controller with this. Must-have!', 1, 5.0),
(12, 'M008', 8, '2025-11-20 09:18:44', 'Magnetic tips are strong – no more dropped tiny screws', 1, 5.0),
(13, 'M006', 19, '2025-11-08 17:33:21', 'Looks elegant, no rust after 2 weeks in humid bathroom', 1, 4.8),
(14, 'M002', 23, '2025-10-25 11:11:11', 'Strong grip, doesn’t block air vent. Finally found the perfect one', 1, 5.0),
(15, 'M003', 35, '2025-11-16 19:44:55', 'Beautiful natural look, matches my Scandinavian theme', 1, 4.7),
(16, 'M002', 25, '2025-10-23 08:30:00', 'No scratches, absorbs water like crazy. My car shines!', 1, 5.0),
(17, 'M008', 37, '2025-11-21 14:22:33', 'Thick paper, no bleed-through with fountain pen', 1, 4.9),
(18, 'M007', 39, '2025-11-06 10:10:10', 'Bright colors, stick really well. Bought 5 packs already', 1, 5.0),
(20, 'M005', 16, '2025-11-17 16:40:22', 'Best electronics screwdriver set I ever owned', 1, 5.0),
(21, 'M001', 27, '2025-10-19 09:11:44', 'Silicon spray works great on rubber seals', 1, 4.6),
(22, 'M003', 32, '2025-11-18 13:20:15', '39 drawers = all my screws finally organized!', 1, 5.0),
(24, 'M006', 18, '2025-11-09 20:45:10', 'Nice cyan color, waterproof as advertised', 1, 4.7),
(25, 'M007', 11, '2025-11-07 11:11:11', 'VDE certified = peace of mind for electrical work', 1, 5.0);

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE `token` (
  `user_id` varchar(12) NOT NULL,
  `start_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `token` varchar(255) NOT NULL,
  `used_for` enum('Remember','Register') NOT NULL,
  `expired_at` timestamp NULL DEFAULT NULL
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
('M008', '2001-08-19', '+60233456789', 'Female');

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
  `account_status` enum('Unblock','Blocked','Unverify') NOT NULL DEFAULT 'Unblock'
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
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`start_at`),
  ADD KEY `start_at` (`start_at`),
  ADD KEY `token_ibfk_1` (`user_id`);

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
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `cartitem`
--
ALTER TABLE `cartitem`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `productvisualmedia`
--
ALTER TABLE `productvisualmedia`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cartitem`
--
ALTER TABLE `cartitem`
  ADD CONSTRAINT `cartitem_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cartitem_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `loyaltypoint`
--
ALTER TABLE `loyaltypoint`
  ADD CONSTRAINT `loyaltypoint_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `otp`
--
ALTER TABLE `otp`
  ADD CONSTRAINT `otp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `profilepicture_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `userprofile`
--
ALTER TABLE `userprofile`
  ADD CONSTRAINT `userprofile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
