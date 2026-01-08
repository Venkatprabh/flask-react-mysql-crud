-- Create database
CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50),
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO users (username, email, password_hash) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere');

INSERT INTO products (name, description, price, category, stock_quantity) VALUES
('Laptop', 'High performance laptop', 999.99, 'Electronics', 10),
('Smartphone', 'Latest smartphone', 699.99, 'Electronics', 25),
('Headphones', 'Noise cancelling headphones', 199.99, 'Electronics', 50),
('Coffee Mug', 'Ceramic coffee mug', 12.99, 'Home', 100),
('T-shirt', 'Cotton t-shirt', 19.99, 'Clothing', 75);