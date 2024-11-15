USE RichMond_db;

INSERT INTO categories (category_name, category_description, category_status) VALUES
('Shirts', 'Various styles of shirts', 'active'),
('Pants', 'Different kinds of pants', 'active'),
('Shoes', 'Wide range of footwear', 'inactive'),
('Accessories', 'Fashionable accessories', 'in process');

INSERT INTO products (product_name, product_description, stock, price, image_url, category_id) VALUES
('Casual Shirt', 'Comfortable cotton shirt', 50, 19.99, 'shirt1.jpg', 1),
('Formal Pants', 'Elegant formal pants', 30, 49.99, 'pants1.jpg', 2),
('Running Shoes', 'Lightweight running shoes', 20, 59.99, 'shoes1.jpg', 3),
('Leather Belt', 'Genuine leather belt', 40, 15.99, 'belt1.jpg', 4);

INSERT INTO orders (user_id, total, order_status) VALUES
(1, 100.00, 'shipped'),
(2, 150.00, 'pending'),
(3, 200.00, 'delivered');

INSERT INTO shopping_bag (user_id) VALUES
(1),
(2), 
(3);

INSERT INTO bag_product (shopping_bag_id, product_id, quantity) VALUES
(1, 1, 2), 
(2, 2, 1), 
(3, 3, 3);



