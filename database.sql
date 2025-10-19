
-- Create Database
CREATE DATABASE IF NOT EXISTS stitch_and_story;
USE stitch_and_story;

-- Users Table
-- Stores user account information
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products Table
-- Stores all crochet product information
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_price (price),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart Table
-- Stores user shopping cart items
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payments Table
-- Stores payment transaction details
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    payment_status ENUM('success', 'failed', 'pending') DEFAULT 'success',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_transaction_id (transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table
-- Stores complete order information with delivery address
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    payment_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_address TEXT NOT NULL,
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_order_status (order_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items Table
-- Stores individual items in each order
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Sample Products
-- This section adds default crochet products to the database
INSERT INTO products (name, category, description, price, image) VALUES
('Crochet Keychain', 'Accessories', 'Handmade crochet keychain in various colors', 5.99, 'keychain.jpg'),
('Crochet Dress', 'Clothing', 'Beautiful handmade crochet dress for girls', 49.99, 'dress.jpg'),
('Crochet Wall Hanging', 'Home Décor', 'Elegant wall hanging for home decoration', 29.99, 'wall-hanging.jpg'),
('Crochet Bag', 'Accessories', 'Stylish handmade crochet bag', 35.99, 'bag.jpg'),
('Crochet Coasters Set', 'Home Décor', 'Set of 6 colorful crochet coasters', 12.99, 'coasters.jpg'),
('Crochet Baby Blanket', 'Baby Items', 'Soft and cozy baby blanket', 39.99, 'baby-blanket.jpg'),
('Crochet Earrings', 'Accessories', 'Lightweight and colorful crochet earrings', 8.99, 'earrings.jpg'),
('Crochet Planter', 'Home Décor', 'Decorative crochet planter holder', 18.99, 'planter.jpg'),
('Crochet Scarf', 'Clothing', 'Warm and stylish winter scarf', 24.99, 'scarf.jpg'),
('Crochet Toy', 'Baby Items', 'Cute handmade crochet toy for kids', 15.99, 'toy.jpg');

-- Sample User (for testing)
-- Password: password123 (hashed)
-- INSERT INTO users (username, email, password) VALUES
-- ('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

