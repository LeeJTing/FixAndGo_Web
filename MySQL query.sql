CREATE TABLE IF NOT EXISTS Users (
    user_id VARCHAR(12) PRIMARY KEY,
    user_name VARCHAR(50) NOT NULL,
    user_role ENUM('Member', 'Admin') DEFAULT 'Member',
    email VARCHAR(30) UNIQUE NOT NULL,
    hash_password VARCHAR(255) NOT NULL,
    account_status ENUM('Verified', 'Unverified', 'Blocked') DEFAULT 'Unverified'
);

CREATE TABLE IF NOT EXISTS Address (
    address_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(12) NOT NULL,
    address_one VARCHAR(255) NOT NULL,
    address_two VARCHAR(255) NULL,
    address_three VARCHAR(255) NULL,
    state VARCHAR(50) NOT NULL,
    post_code VARCHAR(5) NOT NULL,
    country VARCHAR(40) DEFAULT 'Malaysia',
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE IF NOT EXISTS UserDevices (
    mac_address VARCHAR(17) NOT NULL,
    device_name VARCHAR(100) NULL,
    user_id VARCHAR(12) NOT NULL,
    PRIMARY KEY (user_id, mac_address),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE IF NOT EXISTS LoyaltyPoint (
    user_id VARCHAR(12) NOT NULL,
    get_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    royalty_point INT DEFAULT 0 NOT NULL,
    expired_at TIMESTAMP NULL,
    PRIMARY KEY (user_id, get_at),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE IF NOT EXISTS OTP (
    user_id VARCHAR(12) NOT NULL,
    start_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    hashed_password VARCHAR(255) NOT NULL,
    expired_at TIMESTAMP NULL,
    PRIMARY KEY (user_id, start_at),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE IF NOT EXISTS UserProfile (
    user_id VARCHAR(12) PRIMARY KEY,
    dob DATE NULL,
    contact_num VARCHAR(20) NULL,
    gender ENUM('Male', 'Female') NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE IF NOT EXISTS Cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(12) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE IF NOT EXISTS ProfilePicture (
    user_id VARCHAR(12) PRIMARY KEY,
    content VARCHAR(200) NOT NULL,
    mime_type ENUM('jpeg', 'png', 'webp') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE IF NOT EXISTS Category (
    category_code VARCHAR(10) PRIMARY KEY,
    category_name VARCHAR(30) NOT NULL,
    description VARCHAR(200)
);

CREATE TABLE IF NOT EXISTS Product (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(50) NOT NULL,
    stock_quantity INT DEFAULT 0 NOT NULL,
    description VARCHAR(200) NULL,
    short_desc VARCHAR(50) NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    product_point INT DEFAULT 0 NULL,
    sold_number INT DEFAULT 0 NOT NULL,
    category_code VARCHAR(50) NOT NULL,
    FOREIGN KEY (category_code) REFERENCES Category(category_code)
);

CREATE TABLE IF NOT EXISTS ImageLink (
    link_id INT PRIMARY KEY AUTO_INCREMENT,
    url VARCHAR(500) NOT NULL,
    position INT DEFAULT 0 NOT NULL,
    alt VARCHAR(50) NULL,
    product_id INT NOT NULL,
    is_show BOOLEAN DEFAULT TRUE NOT NULL,
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);

CREATE TABLE IF NOT EXISTS CartItem (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    qty INT DEFAULT 1 NOT NULL,
    is_check BOOLEAN DEFAULT FALSE NOT NULL,
    is_tick BOOLEAN DEFAULT FALSE NOT NULL,
    product_id INT NOT NULL,
    FOREIGN KEY (cart_id) REFERENCES Cart(cart_id),
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);

CREATE TABLE IF NOT EXISTS ProductVisualMedia (
    media_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    position INT DEFAULT 0 NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    file_path VARCHAR(100) NOT NULL,
    alt VARCHAR(50) NULL,
    is_show BOOLEAN DEFAULT TRUE NOT NULL,
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);

CREATE TABLE IF NOT EXISTS Review (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(12) NOT NULL,
    product_id INT NOT NULL,
    reviewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comment VARCHAR(200),
    is_valid BOOLEAN DEFAULT TRUE,
    rating DECIMAL(2,1) CHECK (rating >= 0.0 AND rating <= 5.0),
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);

CREATE TABLE IF NOT EXISTS Orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(12) NOT NULL,
    order_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    payment_status ENUM('Pending', 'Paid', 'Failed', 'Refunded') NOT NULL,
    status ENUM('Pending', 'Processing', 'Shipping', 'Delivered', 'Cancelled') NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    utilize_point BOOLEAN DEFAULT FALSE NOT NULL,
    address_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE IF NOT EXISTS OrderItem (
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (order_id, product_id),
    FOREIGN KEY (order_id) REFERENCES Orders(order_id),
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);

CREATE TABLE IF NOT EXISTS OrderAdjustment (
    adjust_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('TAX', 'SHIPPING_FEE', 'DISCOUNT') NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id)
);

CREATE TABLE IF NOT EXISTS Invoice (
    invoice_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    invoiced_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id)
);

CREATE TABLE IF NOT EXISTS Payment (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    payment_method ENUM('Credit Card', 'Debit Card', 'PayPal', 'Bank Transfer', 'Cash') NOT NULL,
    paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES Invoice(invoice_id)
);
