-- Drop existing database and start completely fresh
DROP DATABASE IF EXISTS gadgethut_store;

-- Create new database
CREATE DATABASE gadgethut_store;
USE gadgethut_store;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table (with corrected column names)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(500),
    product_condition VARCHAR(50),
    usage_duration VARCHAR(100),
    condition_notes TEXT,
    rating DECIMAL(3,2),
    stock_quantity INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'pending',
    shipping_address TEXT,
    customer_name VARCHAR(255),
    customer_email VARCHAR(255),
    customer_phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample users (password for all: "password123")
INSERT INTO users (username, email, password_hash) VALUES 
('demo_user', 'demo@gadgethut.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('mike_wilson', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample products
INSERT INTO products (name, description, price, image_url, product_condition, usage_duration, condition_notes, rating, stock_quantity) VALUES
('iPhone 14 Pro', 'Latest iPhone with advanced camera system, A16 Bionic chip, and Dynamic Island. 128GB storage.', 999.99, 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=400&h=300&fit=crop', 'Pristine', '6 months', 'Like new condition with original box and accessories. Battery health 100%.', 4.8, 15),
('Samsung Galaxy S23', 'Premium Android smartphone with AMOLED display, Snapdragon processor, and professional camera system. 256GB storage.', 849.99, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=300&fit=crop', 'Very Good', '1 year', 'Minor scratches on back glass. Screen is flawless. Includes original charger.', 4.5, 12),
('Google Pixel 7', 'Best camera phone with AI features, Google Tensor processor, and pure Android experience. 128GB storage.', 699.99, 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=400&h=300&fit=crop', 'Excellent', '3 months', 'Factory refurbished with new battery. Looks and works like new.', 4.7, 8),
('Wireless Earbuds Pro', 'Noise cancelling wireless earbuds with 30hr battery life, water resistance, and premium sound quality.', 199.99, 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=400&h=300&fit=crop', 'Pristine', '2 months', 'Unused, sealed package with warranty. Latest model.', 4.9, 20),
('Smart Watch Series 5', 'Fitness tracking and notifications with always-on display, heart rate monitoring, and GPS.', 299.99, 'https://images.unsplash.com/photo-1579586337278-3f4364269b5a?w=400&h=300&fit=crop', 'Very Good', '8 months', 'Light wear on strap. Screen protector applied since day one.', 4.3, 10),
('MacBook Air M2', 'Apple MacBook Air with M2 chip, 13.6" Liquid Retina display, 8GB RAM, 256GB SSD.', 1199.99, 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=400&h=300&fit=crop', 'Excellent', '4 months', 'Refurbished by Apple. Includes 1-year warranty. No scratches or dents.', 4.6, 6),
('iPad Pro 12.9"', 'iPad Pro with M1 chip, 12.9" Liquid Retina XDR display, 128GB, Wi-Fi + Cellular.', 1099.99, 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=300&fit=crop', 'Pristine', '1 month', 'Like new with Apple Pencil compatibility. Original packaging.', 4.8, 7),
('Gaming Laptop RTX 4060', 'High-performance gaming laptop with RTX 4060, Intel i7 processor, 16GB RAM, 1TB SSD.', 1499.99, 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400&h=300&fit=crop', 'Very Good', '6 months', 'Excellent condition. Used for light gaming. All original accessories included.', 4.4, 5),
('Wireless Charging Pad', 'Fast wireless charging pad compatible with all Qi-enabled devices. Includes adapter.', 49.99, 'https://images.unsplash.com/photo-1558618666-fcd25856cd63?w=400&h=300&fit=crop', 'Pristine', 'Brand New', 'Never used. Original packaging with 18-month warranty.', 4.2, 25),
('Bluetooth Speaker', 'Portable Bluetooth speaker with 20W output, waterproof, and 24-hour battery life.', 129.99, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&h=300&fit=crop', 'Excellent', '3 months', 'Like new condition. Perfect sound quality. Includes carrying case.', 4.5, 15);

-- Insert sample orders
INSERT INTO orders (user_id, total_amount, status, customer_name, customer_email, customer_phone) VALUES
(1, 1849.98, 'completed', 'Demo User', 'demo@gadgethut.com', '+1234567890'),
(2, 499.98, 'pending', 'John Doe', 'john@example.com', '+1234567891'),
(3, 1299.98, 'shipped', 'Jane Smith', 'jane@example.com', '+1234567892');

-- Insert sample order items
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 999.99),
(1, 3, 1, 849.99),
(2, 4, 2, 199.99),
(2, 10, 1, 99.99),
(3, 6, 1, 1199.99),
(3, 9, 2, 49.99);

-- Create indexes for better performance
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_products_condition ON products(product_condition);
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_order_items_order_id ON order_items(order_id);
CREATE INDEX idx_order_items_product_id ON order_items(product_id);

-- Display success message and counts
SELECT '✅ Database created successfully with fresh sample data!' as status;

SELECT 
    (SELECT COUNT(*) FROM users) as user_count,
    (SELECT COUNT(*) FROM products) as product_count,
    (SELECT COUNT(*) FROM orders) as order_count,
    (SELECT COUNT(*) FROM order_items) as order_item_count;