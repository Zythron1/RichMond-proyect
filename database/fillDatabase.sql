USE RichMond_db;

INSERT INTO categories (category_name, category_description, category_status) VALUES
('jeans', 'Various styles of shirts', 'active'),
('pantalones', 'Different kinds of pants', 'active'),
('camisetas', 'Wide range of footwear', 'active'),
('camisas', 'Wide range of footwear', 'active'),
('sudaderas', 'Wide range of footwear', 'active'),
('Accessories', 'Fashionable accessories', 'inactive');

INSERT INTO products (product_name, product_description, stock, price, image_url, category_id) VALUES
('Casual Shirt', 'Comfortable cotton shirt', 50, 19.99, 'gorra.jpg', 1),
('Formal Pants', 'Elegant formal pants', 30, 49.99, 'gorra.jpg', 2),
('Running Shoes', 'Lightweight running shoes', 20, 59.99, 'gorra.jpg', 3),
('Leather Belt', 'Genuine leather belt', 40, 15.99, 'gorra.jpg', 4);