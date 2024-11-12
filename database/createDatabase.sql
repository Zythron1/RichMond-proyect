DROP DATABASE IF EXISTS RichMond_db;

CREATE DATABASE RichMond_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE RichMond_db;

CREATE TABLE users (
user_id INT NOT NULL AUTO_INCREMENT,
user_name VARCHAR(50) NOT NULL,
email_address VARCHAR(100) NOT NULL UNIQUE,
user_password VARCHAR(255) NOT NULL,
address VARCHAR(255),
phone VARCHAR(15),
user_role VARCHAR(50) DEFAULT 'customer',
created_at TIMESTAMP DEFAULT current_timestamp,
PRIMARY key(user_id)
);

CREATE TABLE categories (
category_id INT NOT NULL AUTO_INCREMENT,
category_name VARCHAR(50) NOT NULL,
category_description VARCHAR(255) NOT NULL,
category_status ENUM('active', 'inactive', 'in process') NOT NULL,
PRIMARY KEY(category_id)
);

CREATE TABLE products (
product_id INT NOT NULL AUTO_INCREMENT,
product_name VARCHAR(50) NOT NULL,
product_description TEXT NOT NULL,
stock INT NOT NULL DEFAULT 0,
price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
image_url VARCHAR(255) DEFAULT 'default_image.jpg',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
category_id INT NOT NULL,
FOREIGN KEY(category_id) REFERENCES categories(category_id) ON DELETE CASCADE ON UPDATE CASCADE,
PRIMARY KEY(product_id)
);

CREATE INDEX idx_category_id ON products(category_id);

CREATE TABLE orders (
order_id INT NOT NULL AUTO_INCREMENT,
user_id INT NOT NULL,
order_date timestamp DEFAULT CURRENT_TIMESTAMP,
total DECIMAL(10, 2) DEFAULT 0,
order_status ENUM('pending', 'shipped', 'delivered') NOT NULL DEFAULT 'pending',
FOREIGN KEY(user_id) REFERENCES users(user_id),
PRIMARY KEY(order_id)
);

CREATE INDEX idx_user_id ON orders(user_id);

CREATE TABLE shopping_bag (
shopping_bag_id INT NOT NULL AUTO_INCREMENT,
user_id INT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY(user_id) REFERENCES users(user_id),
PRIMARY KEY(shopping_bag_id)
);

CREATE INDEX idx_user_id ON shopping_bag(user_id);

CREATE TABLE bag_product (
shopping_bag_id INT NOT NULL,
product_id INT NOT NULL,
quantity INT NOT NULL DEFAULT 0,
FOREIGN KEY(shopping_bag_id) REFERENCES shopping_bag(shopping_bag_id) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY(product_id) REFERENCES products(product_id) ON DELETE CASCADE ON UPDATE CASCADE,
PRIMARY KEY(shopping_bag_id, product_id)
);

CREATE TABLE purchase_history (
    purchase_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY(purchase_id),
    FOREIGN KEY(user_id) REFERENCES users(user_id),
    FOREIGN KEY(product_id) REFERENCES products(product_id)
);


CREATE TABLE payment_methods (
payment_method_id INT NOT NULL AUTO_INCREMENT,
user_id INT NOT NULL,
type VARCHAR(50),
predetermined BOOLEAN DEFAULT FALSE,
security_details TEXT,
FOREIGN KEY(user_id) REFERENCES users(user_id),
PRIMARY KEY(payment_method_id)
);

CREATE INDEX idx_user_id ON payment_methods(user_id);

CREATE TABLE shipments (
shipment_id INT NOT NULL AUTO_INCREMENT,
order_id INT NOT NULL,
address VARCHAR(255) NOT NULL,
shipment_date DATE NOT NULL,
shipment_status ENUM('pending', 'shipped', 'delivered') NOT NULL DEFAULT 'pending',
tracking_number VARCHAR(50) NOT NULL,
FOREIGN KEY(order_id) REFERENCES orders(order_id),
PRIMARY KEY(shipment_id)
);

CREATE TABLE password_resets (
    user_id INT PRIMARY KEY,
    reset_token VARCHAR(255),
    token_expiration DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
