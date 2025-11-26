-- =============================================================================
-- SAMPLE ORDERS + ORDER ITEMS + PAYMENTS + CART ITEMS (Realistic data)
-- =============================================================================

-- 1. ORDERS (6 completed orders from different customers)
INSERT INTO `orders` (`order_id`, `user_id`, `order_at`, `payment_status`, `status`, `total_price`, `utilize_point`, `address_id`) VALUES
(1, 'M001', '2025-10-15 08:22:10', 'Paid', 'Delivered', 289.70, 0, 1),
(2, 'M002', '2025-10-20 14:35:45', 'Paid', 'Delivered', 179.80, 1, 2),
(3, 'M004', '2025-11-01 11:10:22', 'Paid', 'Delivered', 499.70, 0, 4),
(4, 'M007', '2025-11-05 19:45:30', 'Paid', 'Shipping', 119.80, 0, 7),
(5, 'M009', '2025-11-12 09:18:55', 'Paid', 'Processing', 349.80, 1, 9),
(6, 'M010', '2025-11-18 16:27:13', 'Paid', 'Delivered', 89.90, 0, 10);

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
(2, 2, 'Bank Transfer', '2025-10-20 14:40:12'),
(3, 3, 'Credit Card', '2025-11-01 11:15:30'),
(4, 4, 'Cash', '2025-11-05 19:50:00'),
(5, 5, 'Debit Card', '2025-11-12 09:22:10'),
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