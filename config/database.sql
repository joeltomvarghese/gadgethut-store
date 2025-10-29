-- Create database
CREATE DATABASE gadgethut;
USE gadgethut;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    product_condition VARCHAR(50),
    usage_duration VARCHAR(100),
    condition_notes TEXT,
    rating DECIMAL(3,2) DEFAULT 0.00,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'pending',
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
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample users
INSERT INTO users (username, email, password) VALUES 
('johndoe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('janedoe', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample products
INSERT INTO products (name, description, price, product_condition, usage_duration, condition_notes, rating) VALUES 
('iPhone 13 Pro', 'Latest iPhone with advanced camera system', 899.99, 'Pristine', '6 months', 'Like new condition with original box', 4.8),
('Samsung Galaxy S21', 'Powerful Android smartphone', 699.99, 'Very Good', '1 year', 'Minor scratches on screen', 4.5),
('MacBook Air M1', 'Lightweight and powerful laptop', 999.99, 'Excellent', '3 months', 'Perfect condition with warranty', 4.9),
('Sony WH-1000XM4', 'Noise cancelling headphones', 299.99, 'Good', '8 months', 'Works perfectly, includes case', 4.3),
('iPad Air', 'Versatile tablet for work and play', 549.99, 'Pristine', '2 months', 'Brand new condition', 4.7);