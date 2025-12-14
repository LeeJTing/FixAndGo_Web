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

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

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
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
    `cart_id` int(11) NOT NULL,
    `user_id` varchar(12) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

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
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
    `category_code` int(11) NOT NULL,
    `category_name` varchar(30) NOT NULL,
    `description` varchar(1500) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyaltypoint`
--

CREATE TABLE `loyaltypoint` (
    `user_id` varchar(12) NOT NULL,
    `get_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `royalty_point` int(11) NOT NULL DEFAULT 0,
    `expired_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

CREATE TABLE `orderitem` (
    `order_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `qty` int(11) NOT NULL,
    `unit_price` decimal(10, 2) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
    `order_id` int(11) NOT NULL,
    `user_id` varchar(12) NOT NULL,
    `order_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `payment_status` enum(
        'Pending',
        'Paid',
        'Failed',
        'Refunded'
    ) NOT NULL,
    `status` enum(
        'Pending',
        'Processing',
        'Shipping',
        'Delivered',
        'Cancelled'
    ) NOT NULL,
    `total_price` decimal(10, 2) NOT NULL,
    `utilize_point` tinyint(1) NOT NULL DEFAULT 0,
    `address_id` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp`
--

CREATE TABLE `otp` (
    `user_id` varchar(12) NOT NULL,
    `start_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `hashed_password` varchar(255) NOT NULL,
    `expired_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
    `payment_id` int(11) NOT NULL,
    `order_id` int(11) NOT NULL,
    `payment_method` enum(
        'Credit Card',
        'Debit Card',
        'PayPal',
        'Bank Transfer',
        'Cash'
    ) NOT NULL,
    `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

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
    `unit_price` decimal(10, 2) NOT NULL,
    `product_point` int(11) DEFAULT 0,
    `sold_number` int(11) NOT NULL DEFAULT 0,
    `category_code` int(11) NOT NULL,
    `status` varchar(50) NOT NULL DEFAULT "active",
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

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
    `type` enum(
        'Video',
        'Image',
        'Video URL',
        'Image URL'
    ) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profilepicture`
--

CREATE TABLE `profilepicture` (
    `user_id` varchar(12) NOT NULL,
    `file_path` varchar(100) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

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
    `rating` decimal(2, 1) DEFAULT NULL CHECK (
        `rating` >= 0.0
        and `rating` <= 5.0
    )
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userdevices`
--

CREATE TABLE `userdevices` (
    `mac_address` varchar(17) NOT NULL,
    `device_name` varchar(100) DEFAULT NULL,
    `user_id` varchar(12) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userprofile`
--

CREATE TABLE `userprofile` (
    `user_id` varchar(12) NOT NULL,
    `dob` date DEFAULT NULL,
    `contact_num` varchar(20) DEFAULT NULL,
    `gender` enum('Male', 'Female') DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
    `user_id` varchar(12) NOT NULL,
    `user_name` varchar(50) NOT NULL,
    `user_role` enum('Member', 'Admin') NOT NULL DEFAULT 'Member',
    `email` varchar(50) NOT NULL,
    `hash_password` varchar(255) NOT NULL,
    `account_status` enum(
        'Verified',
        'Unverified',
        'Blocked'
    ) NOT NULL DEFAULT 'Unverified'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

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
ALTER TABLE `category` ADD PRIMARY KEY (`category_code`);

--
-- Indexes for table `loyaltypoint`
--
ALTER TABLE `loyaltypoint`
ADD PRIMARY KEY (`user_id`, `get_at`),
ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orderitem`
--
ALTER TABLE `orderitem`
ADD PRIMARY KEY (`order_id`, `product_id`),
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
ADD PRIMARY KEY (`user_id`, `start_at`),
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
ADD PRIMARY KEY (`user_id`, `mac_address`),
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
MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
MODIFY `category_code` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart` MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

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

ALTER TABLE `productvisualmedia`
MODIFY COLUMN `alt` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '';

ALTER TABLE `product`
ADD COLUMN `isdeleted` BOOLEAN NOT NULL DEFAULT FALSE;

ALTER TABLE `product`
ADD COLUMN `low_stock_threshold` INT(11) DEFAULT 10;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;

INSERT INTO `users` (`user_id`, `user_name`, `user_role`, `email`, `hash_password`, `account_status`) VALUES
('A001', 'Daniel Brooks', 'Admin', 'daniel.brooks@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('A0011', 'HELLO', 'Admin', 'benjamin11.hayes@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('A002', 'Emma Collins', 'Admin', 'emma.collins@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('A003', 'Michael Scott', 'Admin', 'michael.scott@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('A004', 'Chloe Adams', 'Admin', 'chloe.adams@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('A005', 'Henry Watson', 'Admin', 'henry.watson@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('M001', 'Evelyn Carter', 'Member', 'evelyn.carter@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('M002', 'Liam Johnson', 'Member', 'liam.johnson@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('M003', 'Olivia Bennett', 'Member', 'olivia.bennett@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('M004', 'Noah Williams', 'Member', 'noah.williams@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('M005', 'Ava Mitchell', 'Member', 'ava.mitchell@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('M006', 'Mason Rivera', 'Member', 'mason.rivera@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('M007', 'Sophia Turner', 'Member', 'sophia.turner@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('M008', 'James Parker', 'Member', 'james.parker@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('M009', 'Isabella Flores', 'Member', 'isabella.flores@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified'),
('M010', 'Benjamin Hayes', 'Member', 'benjamin.hayes@example.com', 'a172ffc990129fe6f68b50f6037c54a1894ee3fd', 'Verified');

INSERT INTO
    `cart` (cart_id, user_id)
VALUES (1, 'M001'),
    (2, 'M002'),
    (3, 'M003'),
    (4, 'M004'),
    (5, 'M005'),
    (6, 'M006'),
    (7, 'M007'),
    (8, 'M008'),
    (9, 'M009'),
    (10, 'M010');

INSERT INTO
    address (
        address_id,
        user_id,
        address_name,
        address_one,
        address_two,
        address_three,
        state,
        post_code,
        country
    )
VALUES (
        1,
        'M001',
        'Home',
        '12 Jalan Meranti',
        'Taman Bukit Indah',
        NULL,
        'Selangor',
        '40100',
        'Malaysia'
    ),
    (
        2,
        'M002',
        'Home',
        '28 Jalan Setia 5/3',
        'Setia Alam',
        NULL,
        'Selangor',
        '40170',
        'Malaysia'
    ),
    (
        3,
        'M003',
        'Home',
        '55 Jalan Kempas 3',
        'Taman Kempas',
        NULL,
        'Johor',
        '81200',
        'Malaysia'
    ),
    (
        4,
        'M004',
        'Home',
        '9 Jalan Lavender',
        'Taman Putra Perdana',
        NULL,
        'Selangor',
        '47130',
        'Malaysia'
    ),
    (
        5,
        'M005',
        'Home',
        '21 Jalan Air Putih',
        NULL,
        NULL,
        'Pahang',
        '25300',
        'Malaysia'
    ),
    (
        6,
        'M006',
        'Home',
        '77 Jalan Tun Ahmad Zaidi',
        'Lorong 2',
        NULL,
        'Sarawak',
        '93050',
        'Malaysia'
    ),
    (
        7,
        'M007',
        'Home',
        '35 Jalan Mawar 2A',
        'Taman Sri Mawar',
        NULL,
        'Negeri Sembilan',
        '71000',
        'Malaysia'
    ),
    (
        8,
        'M008',
        'Home',
        '14 Jalan Sri Hartamas 1',
        NULL,
        NULL,
        'Kuala Lumpur',
        '50480',
        'Malaysia'
    ),
    (
        9,
        'M009',
        'Home',
        '68 Jalan Wong Ah Fook',
        'Block B',
        'Unit 12-03',
        'Johor',
        '80000',
        'Malaysia'
    ),
    (
        10,
        'M010',
        'Home',
        '5 Jalan Cenderawasih',
        'Taman Desa Cemerlang',
        NULL,
        'Johor',
        '81750',
        'Malaysia'
    ),
    (
        11,
        'A001',
        'Home',
        '100 Jalan Persiaran Surian',
        'Damansara Utama',
        NULL,
        'Selangor',
        '47800',
        'Malaysia'
    ),
    (
        12,
        'A002',
        'Home',
        '33 Jalan Bayu 4',
        'Bandar Puteri',
        NULL,
        'Selangor',
        '47100',
        'Malaysia'
    ),
    (
        13,
        'A003',
        'Home',
        '9 Lorong Jelutong',
        NULL,
        NULL,
        'Penang',
        '10250',
        'Malaysia'
    ),
    (
        14,
        'A004',
        'Home',
        '22 Jalan Sultan Ismail',
        'Menara Pintu',
        'Level 10',
        'Kuala Lumpur',
        '50250',
        'Malaysia'
    ),
    (
        15,
        'A005',
        'Home',
        '50 Jalan Tok Janggut',
        NULL,
        NULL,
        'Kelantan',
        '15150',
        'Malaysia'
    );

INSERT INTO
    userdevices (
        mac_address,
        device_name,
        user_id
    )
VALUES (
        'A4:12:6F:9B:3C:11',
        'iPhone 12',
        'M001'
    ),
    (
        'B8:4F:2A:7D:88:29',
        'Samsung Galaxy S21',
        'M002'
    ),
    (
        'C0:98:3D:4A:1F:55',
        'iPad Air',
        'M003'
    ),
    (
        'D1:7B:66:22:5E:90',
        'Huawei Matebook D14',
        'M004'
    ),
    (
        'E3:55:AF:9C:77:01',
        'Xiaomi Redmi Note 11',
        'M005'
    ),
    (
        'F7:62:1D:2E:4B:88',
        'MacBook Pro 13',
        'M006'
    ),
    (
        'A1:33:4E:5F:99:77',
        'Samsung Galaxy Tab A7',
        'M007'
    ),
    (
        'B2:8A:7C:3D:11:44',
        'ASUS ROG Phone 5',
        'M008'
    ),
    (
        'C7:21:9D:6E:45:22',
        'Lenovo IdeaPad Slim',
        'M009'
    ),
    (
        'D9:44:8F:1A:30:66',
        'Oppo Reno 8',
        'M010'
    ),
    (
        'E0:77:2C:5B:9F:13',
        'iPhone 13 Pro',
        'A001'
    ),
    (
        'F1:88:3E:7D:22:41',
        'Dell XPS 15',
        'A002'
    ),
    (
        'A2:6B:5C:3A:11:92',
        'iPad Mini',
        'A003'
    ),
    (
        'B5:9D:8F:1E:44:27',
        'Samsung S22 Ultra',
        'A004'
    ),
    (
        'C6:2A:7B:9C:55:30',
        'MacBook Air M1',
        'A005'
    );

INSERT INTO
    otp (
        user_id,
        start_at,
        hashed_password,
        expired_at
    )
VALUES (
        'M001',
        '2025-01-10 10:15:00',
        'otp12345',
        '2025-01-10 10:20:00'
    ),
    (
        'M002',
        '2025-01-11 09:30:00',
        'otp56789',
        '2025-01-11 09:35:00'
    ),
    (
        'M003',
        '2025-01-12 14:10:00',
        'otpabc12',
        '2025-01-12 14:15:00'
    );

INSERT INTO
    userprofile (
        user_id,
        dob,
        contact_num,
        gender
    )
VALUES (
        'M001',
        NULL,
        '012-3456789',
        'Male'
    ),
    (
        'M002',
        '2002-07-22',
        NULL,
        'Female'
    ),
    (
        'M003',
        '2000-11-05',
        NULL,
        'Male'
    ),
    (
        'M004',
        '1999-09-10',
        '017-1122334',
        'Female'
    ),
    ('M005', NULL, NULL, 'Male'),
    (
        'M006',
        '2003-05-13',
        NULL,
        'Female'
    ),
    (
        'M007',
        NULL,
        '016-7788991',
        NULL
    ),
    (
        'M008',
        '2001-08-19',
        '019-8877665',
        'Female'
    ),
    (
        'M009',
        '2000-04-09',
        '012-6677889',
        'Male'
    ),
    (
        'M010',
        '1998-06-25',
        NULL,
        'Female'
    );

INSERT INTO
    loyaltypoint (
        user_id,
        get_at,
        royalty_point,
        expired_at
    )
VALUES (
        'M001',
        '2025-01-10 10:00:00',
        120,
        '2026-01-01 00:00:00'
    ),
    (
        'M002',
        '2025-01-11 11:20:00',
        80,
        '2026-01-01 00:00:00'
    ),
    (
        'M003',
        '2025-01-12 09:15:00',
        150,
        '2026-01-01 00:00:00'
    ),
    (
        'M004',
        '2025-01-13 14:40:00',
        60,
        '2026-01-01 00:00:00'
    ),
    (
        'M005',
        '2025-11-14 16:05:00',
        200,
        '2026-11-01 00:00:00'
    ),
    (
        'M006',
        '2025-05-15 13:22:00',
        95,
        '2026-05-01 00:00:00'
    ),
    (
        'M008',
        '2025-03-17 17:18:00',
        180,
        '2026-03-01 00:00:00'
    ),
    (
        'M010',
        '2025-01-19 12:55:00',
        140,
        '2026-01-01 00:00:00'
    );

INSERT INTO
    profilepicture (user_id, file_path) VALUE (
        'M001',
        'images/profile/1.webp'
    );

ALTER TABLE category
ADD COLUMN img_path VARCHAR(255) AFTER category_name;

<<<<<<< HEAD
INSERT INTO category (category_name, description, img_path) VALUES
('No Category', 'Products without a specific category assigned', 'images/no-category.png'),
('Storage', 'Storage solutions, tool boxes, shelves and trolleys','images/storage.png'),
('Stationery', 'Browse the widest range of stationery and office supplies all in one place! You will find everything from double sided tape and colored pencils, to filing folders and sticky notes. If you want the best deals available, you're sure to find them here – highlighters, staplers, highlighters – we have it all.','images/stationary.png'),
('Automotive', 'We here at Mr DIY know that spending time and money on your car is an important investment. That is why we have a range of automotive goods and car accessories in our store to make sure you are getting the best out of your ride! Whether it be car mats, sun shades, car covers, car polishes or even the newest tech gadgets, our website has everything you need to get more from your vehicle.','images/automotive.png'),
('Power & Hand Tools', 'Cordless screwdrivers, drills, saws, chisels, hammers and measuring tapes','images/hardware_tools.png')
ON DUPLICATE KEY UPDATE 
    description = VALUES(description),
    img_path = VALUES(img_path);


=======
INSERT INTO
    category (
        category_name,
        description,
        img_path
    )
VALUES (
        'Storage',
        'Storage solutions, tool boxes, shelves and trolleys',
        'images/storage.png'
    ),
    (
        'Stationery',
        'Browse the widest range of stationery and office supplies all in one place! You will find everything from double sided tape and colored pencils, to filing folders and sticky notes. If you want the best deals available, you’re sure to find them here – highlighters, staplers, highlighters – we have it all.',
        'images/stationary.png'
    ),
    (
        'Automotive',
        'We here at Mr DIY know that spending time and money on your car is an important investment. That is why we have a range of automotive goods and car accessories in our store to make sure you are getting the best out of your ride! Whether it be car mats, sun shades, car covers, car polishes or even the newest tech gadgets, our website has everything you need to get more from your vehicle.',
        'images/automotive.png'
    ),
    (
        'Power & Hand Tools',
        'Cordless screwdrivers, drills. saws. chisels hammers and measuring tapes',
        'images/hardware_tools.png'
    )
ON DUPLICATE KEY UPDATE
    description = VALUES(description);
>>>>>>> 119145a78fc61823b23e293187eb274fbf3d0618
