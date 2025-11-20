
INSERT INTO `orderitem`
(`order_id`, `product_id`, `qty`, `unit_price`)
VALUES
(1, 7, 1, 10.20), 
(1, 8, 2, 13.90);

INSERT INTO `orders`
(`order_id`, `user_id`, `order_at`, `payment_status`, `status`, `total_price`, `utilize_point`, `address_id`)
VALUES
(1, 'A001', '2025-01-10 10:25:00', 'Paid', 'Delivered', 129.90, 129, 11);

INSERT INTO `payment`
(`order_id`, `payment_method`, `paid_at`)
VALUES
-- Payment for Order 1 (Delivered)
(1, 'Credit Card', '2025-01-10 14:23:55');

INSERT INTO `cart` (user_id)
VALUES
('A001');

INSERT INTO `cartitem`
(cart_id, qty, is_check, is_take, product_id)
VALUES
(1, 2, 1, 0, 7),   -- Claw Hammer
(1, 1, 0, 0, 8);

INSERT INTO `productvisualmedia` (product_id, position, file_path, alt, is_show, type) VALUES 
(7, 0, 'images/product/claw_hammer_23mm-0.jpg', 'Claw Hammer 23mm', 1, 'Image'), 
(7, 1, 'images/product/claw_hammer_23mm-1.jpg', 'Claw Hammer 23mm', 1, 'Image'), 
(7, 2, 'images/product/claw_hammer_23mm-2.jpg', 'Claw Hammer 23mm', 1, 'Image'), 

(8, 0, 'images/product/magnetic_claw_hammer-0.jpg', 'Magnetic Claw Hammer', 1, 'Image'), 
(8, 1, 'images/product/magnetic_claw_hammer-1.jpg', 'Magnetic Claw Hammer', 1, 'Image'),
 (8, 2, 'images/product/magnetic_claw_hammer-2.jpg', 'Magnetic Claw Hammer', 1, 'Image');