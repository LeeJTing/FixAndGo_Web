-- =============================================================================
-- SAMPLE ORDERS + ORDER ITEMS + PAYMENTS + CART ITEMS (Realistic data)
-- =============================================================================

-- 1. ORDERS (6 completed orders from different customers)
INSERT INTO `orders` 
(`order_id`, `user_id`, `order_at`, `deliver_at`, `payment_status`, `status`, `total_price`, `utilize_point`, `address_id`) 
VALUES
(1, 'M001', '2025-10-15 08:22:10', '2025-10-20', 'Paid', 'Delivered', 399.70, 0, 1),
(2, 'M002', '2025-10-20 14:35:45', '2025-10-25', 'Paid', 'Delivered', 285.70, 1, 2),
(3, 'M004', '2025-11-01 11:10:22', '2025-11-06', 'Paid', 'Delivered', 689.70, 0, 4),
(4, 'M007', '2025-11-05 19:45:30', '2025-11-10', 'Paid', 'Shipping', 149.80, 0, 7),
(5, 'M009', '2025-11-12 09:18:55', '2025-11-17', 'Paid', 'Processing', 469.80, 1, 9),
(6, 'M010', '2025-11-18 16:27:13', '2025-11-23', 'Paid', 'Delivered', 89.90, 0, 10);


-- 2. ORDER ITEMS (what they actually bought)
INSERT INTO `orderitem` (`order_id`, `product_id`, `qty`, `unit_price`) VALUES
-- Order 1 (M001)
(1, 10, 1, 149.90),  -- EMTOP Cordless Screwdriver
(1, 24, 1, 159.90),  -- HOTAK Ratchet
(1, 16, 1, 89.90),   -- 32-in-1 Magnetic Screwdriver Set → total 289.70

-- Order 2 (M002)
(2, 29, 1, 249.90),  -- EMTOP Air Compressor
(2, 25, 2, 19.90),   -- Microfiber Mitt ×2
(2, 26, 1, 15.90),   -- Anti-Slip Mat → total 249.90 + 39.80 + 15.90 = 305.60 - 125.80 points used = 179.80

-- Order 3 (M004)
(3, 14, 1, 199.90),  -- Foldable Hand Truck 300kg
(3, 21, 1, 289.90),  -- 3-Tier Workshop Trolley
(3, 32, 1, 199.90),  -- TACTIX 39-Drawer Bin → total 689.70 - 190 points = 499.70 (points used)

-- Order 4 (M007)
(4, 8, 1, 69.90),    -- Precision Screwdriver Set
(4, 31, 1, 79.90),   -- TACTIX 32-Pc Bit Set → total 119.80

-- Order 5 (M009)
(5, 33, 1, 289.90),  -- 46-Piece Socket Set
(5, 15, 1, 179.90),  -- 31-Piece T-Handle Set → total 469.80 - 120 points = 349.80

-- Order 6 (M010)
(6, 6, 1, 89.90);    -- 3-Piece Chisel Set

-- 3. PAYMENTS (all paid successfully)
INSERT INTO `payment` (`payment_id`, `order_id`, `payment_method`, `paid_at`) VALUES
(1, 1, 'Credit Card', '2025-10-15 08:25:00'),
(2, 2, 'Loyalty Points', '2025-10-20 14:40:12'),
(3, 3, 'Credit Card', '2025-11-01 11:15:30'),
(4, 4, 'Cash', '2025-11-05 19:50:00'),
(5, 5, 'Loyalty Points', '2025-11-12 09:22:10'),
(6, 6, 'Credit Card', '2025-11-18 16:30:05');

-- 4. CURRENT CART ITEMS (what people still have in cart — not yet ordered)
INSERT INTO `cartitem` (`cart_id`, `product_id`, `qty`, `is_check`, `is_take`) VALUES
(1, 4, 1, 1, 0),   -- M001 has Magnetic Claw Hammer in cart
(1, 27, 2, 1, 0),  -- + 2× WAXCO Silicon Spray

(2, 23, 1, 1, 0),  -- M002 has Car Phone Holder
(2, 19, 1, 0, 0),  -- Bathroom Rack (not checked)

(3, 35, 1, 1, 0),  -- M003 has Bamboo Tissue Box
(4, 11, 1, 1, 0),  -- M004 has TACTIX Insulated Screwdrivers

(5, 1, 2, 1, 0),   -- M005 wants 2× Claw Hammer 23mm
(6, 28, 1, 1, 0),  -- M006 has ROLLINGDOG Scissors

(7, 39, 5, 1, 0),  -- M007 buying 5 packs of Sticky Notes
(8, 37, 3, 1, 0);  -- M008 buying 3× COMIX A5 Notebook

-- =============================================================================
-- SAMPLE REVIEWS (25 realistic reviews from real customers)
-- =============================================================================

INSERT INTO `review` (`review_id`, `user_id`, `product_id`, `reviewed_at`, `comment`, `is_valid`, `rating`) VALUES

-- Product 1 - Claw Hammer 23mm
(1, 'M003', 1, '2025-10-18 10:15:22', 'Solid hammer, great weight balance. Highly recommended!', 1, 4.8),
(2, 'M007', 1, '2025-11-10 14:30:11', 'Very sturdy, used it for framing work – no issues', 1, 5.0),

-- Product 4 - Magnetic Claw Hammer
(3, 'M001', 4, '2025-10-16 12:44:55', 'The magnetic nail starter is a game changer! One-hand nailing FTW', 1, 5.0),

-- Product 10 - EMTOP Cordless Screwdriver
(4, 'M001', 10, '2025-10-17 09:20:33', 'Battery lasts long, LED light is super useful in dark corners', 1, 4.9),
(5, 'M009', 10, '2025-11-14 16:55:10', 'Worth every sen. Assembled 3 IKEA cabinets non-stop', 1, 5.0),

-- Product 14 - Foldable Hand Truck 300kg
(6, 'M004', 14, '2025-11-03 18:12:44', 'Moved 200kg of tiles easily. Folds flat – perfect for my van', 1, 5.0),

-- Product 21 - 3-Tier Workshop Trolley
(7, 'M004', 21, '2025-11-04 11:30:00', 'Best purchase this year. My garage finally organized!', 1, 5.0),

-- Product 29 - EMTOP Air Compressor
(8, 'M002', 29, '2025-10-22 13:25:18', 'Fast inflation, digital gauge is accurate. Love the LED light', 1, 4.7),

-- Product 6 - 3-Piece Chisel Set
(9, 'M010', 6, '2025-11-19 10:40:22', 'Sharp out of the box, wooden handles feel premium', 1, 4.8),

-- Product 33 - 46-Piece Socket Set
(10, 'M009', 33, '2025-11-13 20:11:33', 'Complete set, good quality chrome vanadium. Case is solid', 1, 4.9),

-- Product 8 - Precision Screwdriver Set
(11, 'M005', 8, '2025-11-15 15:22:10', 'Fixed my laptop and PS5 controller with this. Must-have!', 1, 5.0),
(12, 'M008', 8, '2025-11-20 09:18:44', 'Magnetic tips are strong – no more dropped tiny screws', 1, 5.0),

-- Product 19 - Stainless-Steel Bathroom Rack
(13, 'M006', 19, '2025-11-08 17:33:21', 'Looks elegant, no rust after 2 weeks in humid bathroom', 1, 4.8),

-- Product 23 - Car Universal Phone Holder
(14, 'M002', 23, '2025-10-25 11:11:11', 'Strong grip, doesn’t block air vent. Finally found the perfect one', 1, 5.0),

-- Product 35 - Bamboo Tissue Box
(15, 'M003', 35, '2025-11-16 19:44:55', 'Beautiful natural look, matches my Scandinavian theme', 1, 4.7),

-- Product 25 - Microfiber Wash Mitt
(16, 'M002', 25, '2025-10-23 08:30:00', 'No scratches, absorbs water like crazy. My car shines!', 1, 5.0),

-- Product 37 - COMIX A5 Notebook
(17, 'M008', 37, '2025-11-21 14:22:33', 'Thick paper, no bleed-through with fountain pen', 1, 4.9),

-- Product 39 - Sticky Notes
(18, 'M007', 39, '2025-11-06 10:10:10', 'Bright colors, stick really well. Bought 5 packs already', 1, 5.0),

-- More mixed reviews
(19, 'M010', 24, '2025-11-20 12:55:30', 'Smooth ratchet action, extension is very useful', 1, 4.8),
(20, 'M005', 16, '2025-11-17 16:40:22', 'Best electronics screwdriver set I ever owned', 1, 5.0),
(21, 'M001', 27, '2025-10-19 09:11:44', 'Silicon spray works great on rubber seals', 1, 4.6),
(22, 'M003', 32, '2025-11-18 13:20:15', '39 drawers = all my screws finally organized!', 1, 5.0),
(23, 'M009', 28, '2025-11-15 18:33:21', 'Super sharp scissors, cuts through anything', 1, 4.9),
(24, 'M006', 18, '2025-11-09 20:45:10', 'Nice cyan color, waterproof as advertised', 1, 4.7),
(25, 'M007', 11, '2025-11-07 11:11:11', 'VDE certified = peace of mind for electrical work', 1, 5.0);

-- NEW ORDERS STARTING FROM ID 7
INSERT INTO `orders` 
(`order_id`, `user_id`, `order_at`, `deliver_at`, `payment_status`, `status`, `total_price`, `utilize_point`, `address_id`) 
VALUES
(7, 'M003', '2025-11-25 10:45:33', '2025-11-30', 'Paid', 'Processing', 479.60, 0, 3),
(8, 'M005', '2025-11-28 15:20:18', '2025-12-03', 'Paid', 'Shipping', 189.60, 1, 5),
(9, 'M008', '2025-12-02 09:10:45', '2025-12-07', 'Paid', 'Delivered', 329.70, 0, 8),
(10, 'M006', '2025-12-05 14:33:27', '2025-12-10', 'Paid', 'Delivered', 249.60, 0, 6),
(11, 'M002', '2025-12-07 11:18:42', '2025-12-12', 'Paid', 'Shipping', 589.50, 1, 18),
(12, 'M001', '2025-12-09 16:45:19', '2025-12-14', 'Paid', 'Processing', 119.70, 0, 1),
(13, 'M004', '2025-12-11 09:22:55', '2025-12-16', 'Paid', 'Delivered', 899.50, 0, 4),
(14, 'M010', '2025-12-13 20:15:30', '2025-12-18', 'Paid', 'Shipping', 179.80, 0, 10),
(15, 'M007', '2025-12-15 13:08:12', '2025-12-20', 'Paid', 'Processing', 349.70, 1, 7),
(16, 'M009', '2025-12-18 10:55:44', '2025-12-23', 'Paid', 'Processing', 529.60, 0, 9),
(17, 'M003', '2025-12-20 18:30:22', '2025-12-25', 'Paid', 'Shipping', 99.80, 0, 19),
(18, 'M005', '2025-12-22 12:05:47', '2025-12-27', 'Paid', 'Processing', 259.60, 1, 5),
(19, 'M008', '2025-12-24 08:55:33', '2025-12-29', 'Paid', 'Processing', 679.50, 0, 8),
(20, 'M002', '2025-12-26 09:15:42', '2025-12-31', 'Paid', 'Delivered', 189.70, 0, 2),
(21, 'M004', '2025-12-27 14:30:18', '2026-01-02', 'Paid', 'Shipping', 439.60, 1, 20),
(22, 'M006', '2025-12-28 11:45:33', '2026-01-03', 'Paid', 'Processing', 299.50, 0, 6),
(23, 'M008', '2025-12-29 16:20:55', '2026-01-04', 'Paid', 'Processing', 79.80, 0, 8),
(24, 'M010', '2025-12-30 10:10:10', '2026-01-05', 'Paid', 'Shipping', 519.60, 0, 10),
(25, 'M001', '2025-12-31 13:25:47', '2026-01-06', 'Paid', 'Delivered', 159.60, 1, 16),
(26, 'M003', '2026-01-02 08:40:22', '2026-01-07', 'Paid', 'Processing', 699.40, 0, 3);

-- ORDER ITEMS FOR ORDERS 7-26
INSERT INTO `orderitem` (`order_id`, `product_id`, `qty`, `unit_price`) VALUES
-- Order 7 (M003) - Total: 479.60
(7, 29, 1, 249.90),  -- EMTOP Air Compressor
(7, 7, 1, 49.90),    -- Hammer with Rubber Grip
(7, 12, 2, 29.90),   -- Knife with Cutter Blade Set ×2
(7, 39, 1, 19.90),   -- Sticky Notes
(7, 38, 1, 12.90),   -- 2B Mechanical Pencil Set
(7, 26, 1, 15.90),   -- Anti-Slip Mat
(7, 25, 1, 19.90),   -- Microfiber Wash Mitt

-- Order 8 (M005) - Total: 189.60 (before points)
(8, 17, 1, 49.90),   -- 3-Tier Desk Organizer
(8, 35, 1, 49.90),   -- Bamboo Wooden Tissue Box
(8, 30, 2, 12.90),   -- Stainless Steel Scissor ×2
(8, 36, 1, 29.90),   -- COMIX Black Marker 12-Pack
(8, 18, 1, 39.90),   -- Wall-Mounted Tissue Box Cyan
(8, 26, 1, 15.90),   -- Anti-Slip Mat

-- Order 9 (M008) - Total: 329.70
(9, 4, 1, 59.90),    -- Magnetic Claw Hammer
(9, 33, 1, 289.90),  -- 46-Piece Socket Set
(9, 27, 1, 29.90),   -- WAXCO Silicon Spray
(9, 25, 2, 19.90),   -- Microfiber Wash Mitt ×2

-- Order 10 (M006) - Total: 249.60
(10, 14, 1, 199.90), -- Foldable Hand Truck 300kg
(10, 26, 1, 15.90),  -- Anti-Slip Mat
(10, 25, 1, 19.90),  -- Microfiber Wash Mitt
(10, 39, 1, 19.90),  -- Sticky Notes

-- Order 11 (M002) - Total: 589.50 (before points)
(11, 33, 1, 289.90), -- 46-Piece Socket Set
(11, 24, 1, 159.90), -- HOTAK Extendable Ratchet
(11, 31, 1, 79.90),  -- TACTIX 32-Pc Bit Set
(11, 15, 1, 179.90), -- 31-Piece T-handle Set

-- Order 12 (M001) - Total: 119.70
(12, 2, 1, 32.50),   -- Cross Pein Pin Hammer 14mm
(12, 9, 2, 24.90),   -- INCGO 2in1 Screwdriver ×2
(12, 12, 1, 29.90),  -- Knife with Cutter Blade Set
(12, 38, 1, 12.90),  -- 2B Mechanical Pencil Set

-- Order 13 (M004) - Total: 899.50
(13, 21, 1, 289.90), -- 3-Tier Workshop Trolley
(13, 32, 1, 199.90), -- TACTIX 39-Drawer Storage Bin
(13, 10, 1, 149.90), -- EMTOP Cordless Screwdriver
(13, 19, 1, 129.90), -- Stainless Steel Bathroom Rack
(13, 22, 1, 139.90), -- 3-Shelf Rectangular Rack

-- Order 14 (M010) - Total: 179.80
(14, 8, 1, 69.90),   -- Precision Screwdriver Set
(14, 16, 1, 89.90),  -- 32-in-1 Magnetic Screwdriver
(14, 30, 1, 12.90),  -- Stainless Steel Scissor
(14, 37, 1, 24.90),  -- COMIX A5 Notebook

-- Order 15 (M007) - Total: 349.70 (before points)
(15, 11, 1, 99.90),  -- TACTIX Insulated Screwdriver Set
(15, 28, 1, 49.90),  -- ROLLINGDOG Heavy Duty Scissors
(15, 34, 2, 39.90),  -- 2-Drawer Storage Box ×2
(15, 20, 1, 59.90),  -- CHANYI 3-Tier File Tray
(15, 35, 1, 49.90),  -- Bamboo Wooden Tissue Box
(15, 18, 1, 39.90),  -- Wall-Mounted Tissue Box Cyan

-- Order 16 (M009) - Total: 529.60
(16, 29, 1, 249.90), -- EMTOP Air Compressor
(16, 7, 1, 49.90),   -- Hammer with Rubber Grip
(16, 4, 1, 59.90),   -- Magnetic Claw Hammer
(16, 23, 1, 39.90),  -- Car Universal Phone Holder
(16, 27, 2, 29.90),  -- WAXCO Silicon Spray ×2
(16, 25, 3, 19.90),  -- Microfiber Wash Mitt ×3
(16, 26, 1, 15.90),  -- Anti-Slip Mat

-- Order 17 (M003) - Total: 99.80
(17, 5, 2, 19.90),   -- Short-Handle Mini Hammer ×2
(17, 30, 2, 12.90),  -- Stainless Steel Scissor ×2
(17, 38, 2, 12.90),  -- 2B Mechanical Pencil Set ×2
(17, 39, 1, 19.90),  -- Sticky Notes

-- Order 18 (M005) - Total: 259.60 (before points)
(18, 17, 2, 49.90),  -- 3-Tier Desk Organizer ×2
(18, 34, 3, 39.90),  -- 2-Drawer Storage Box ×3
(18, 35, 1, 49.90),  -- Bamboo Wooden Tissue Box

-- Order 19 (M008) - Total: 679.50
(19, 33, 1, 289.90), -- 46-Piece Socket Set
(19, 24, 1, 159.90), -- HOTAK Extendable Ratchet
(19, 10, 1, 149.90), -- EMTOP Cordless Screwdriver
(19, 31, 1, 79.90),  -- TACTIX 32-Pc Bit Set

-- Order 20 (M002) - Total: 189.70
(20, 1, 1, 45.90),   -- Claw Hammer 23mm
(20, 12, 2, 29.90),  -- Knife with Cutter Blade Set ×2
(20, 25, 3, 19.90),  -- Microfiber Wash Mitt ×3
(20, 26, 1, 15.90),  -- Anti-Slip Mat
(20, 30, 1, 12.90),  -- Stainless Steel Scissor

-- Order 21 (M004) - Total: 439.60 (before points)
(21, 15, 1, 179.90), -- 31-Piece T-handle Set
(21, 16, 1, 89.90),  -- 32-in-1 Magnetic Screwdriver
(21, 8, 1, 69.90),   -- Precision Screwdriver Set
(21, 31, 1, 79.90),  -- TACTIX 32-Pc Bit Set
(21, 39, 2, 19.90),  -- Sticky Notes ×2

-- Order 22 (M006) - Total: 299.50
(22, 23, 2, 39.90),  -- Car Universal Phone Holder ×2
(22, 26, 2, 15.90),  -- Anti-Slip Mat ×2
(22, 27, 3, 29.90),  -- WAXCO Silicon Spray ×3
(22, 25, 5, 19.90),  -- Microfiber Wash Mitt ×5

-- Order 23 (M008) - Total: 79.80
(23, 30, 3, 12.90),  -- Stainless Steel Scissor ×3
(23, 38, 2, 12.90),  -- 2B Mechanical Pencil Set ×2
(23, 39, 1, 19.90),  -- Sticky Notes

-- Order 24 (M010) - Total: 519.60
(24, 11, 1, 99.90),  -- TACTIX Insulated Screwdriver Set
(24, 28, 1, 49.90),  -- ROLLINGDOG Heavy Duty Scissors
(24, 4, 1, 59.90),   -- Magnetic Claw Hammer
(24, 7, 1, 49.90),   -- Hammer with Rubber Grip
(24, 33, 1, 289.90), -- 46-Piece Socket Set

-- Order 25 (M001) - Total: 159.60 (before points)
(25, 18, 2, 39.90),  -- Wall-Mounted Tissue Box Cyan ×2
(25, 35, 1, 49.90),  -- Bamboo Wooden Tissue Box
(25, 34, 1, 39.90),  -- 2-Drawer Storage Box

-- Order 26 (M003) - Total: 699.40
(26, 21, 1, 289.90), -- 3-Tier Workshop Trolley
(26, 32, 1, 199.90), -- TACTIX 39-Drawer Storage Bin
(26, 22, 1, 139.90), -- 3-Shelf Rectangular Rack
(26, 17, 1, 49.90),  -- 3-Tier Desk Organizer
(26, 20, 1, 59.90);  -- CHANYI 3-Tier File Tray

-- PAYMENTS FOR ORDERS 7-26
INSERT INTO `payment` (`payment_id`, `order_id`, `payment_method`, `paid_at`) VALUES
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