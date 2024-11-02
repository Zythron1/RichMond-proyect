USE RichMond_db;

-- Insertar usuarios 
INSERT INTO users (user_name, email_address, user_password, address, phone, user_role) VALUES
('John Doe', 'johndoe@example.com', 'password123', '123 Main St', '555-1234', 'customer'),
('Jane Smith', 'janesmith@example.com', 'password123', '456 Oak St', '555-5678', 'customer'),
('Alice Johnson', 'alicej@example.com', 'password123', '789 Pine St', '555-9012', 'customer');


-- Insertar categorías
INSERT INTO categories (category_name, category_description, category_status) VALUES
('Shirts', 'Various styles of shirts', 'active'),
('Pants', 'Different kinds of pants', 'active'),
('Shoes', 'Wide range of footwear', 'inactive'),
('Accessories', 'Fashionable accessories', 'in process');

-- Insertar productos y asignándolos a categorías
INSERT INTO products (product_name, product_description, stock, price, image_url, category_id) VALUES
('Casual Shirt', 'Comfortable cotton shirt', 50, 19.99, 'shirt1.jpg', 1),
('Formal Pants', 'Elegant formal pants', 30, 49.99, 'pants1.jpg', 2),
('Running Shoes', 'Lightweight running shoes', 20, 59.99, 'shoes1.jpg', 3),
('Leather Belt', 'Genuine leather belt', 40, 15.99, 'belt1.jpg', 4);


-- Insertar órdenes con referencia a usuarios
INSERT INTO orders (user_id, total, order_status) VALUES
(1, 100.00, 'shipped'),
(2, 150.00, 'pending'),
(3, 200.00, 'delivered');


-- Insertar artículos en el carrito de compras (shopping_bag)
INSERT INTO shopping_bag (user_id, product_id, quantity) VALUES
(1, 1, 2),
(2, 2, 1),
(3, 3, 3);


-- Insertar elementos en la tabla bag_product, conectando shopping_bag con productos
INSERT INTO bag_product (shopping_bag_id, product_id) VALUES
(1, 1),
(2, 2),
(3, 3);

