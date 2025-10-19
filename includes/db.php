<?php
/**
 * Database Class
 * Handles database connection and table creation
 */
class Database {
    private $host = 'localhost';
    private $db_name = 'stitch_and_story';
    private $username = 'root';
    private $password = '';
    private $conn;

    /**
     * Get database connection
     */
    public function getConnection() {
        $this->conn = null;

        try {
            // First connect without database to create it if needed
            $this->conn = new mysqli($this->host, $this->username, $this->password);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            // Create database if not exists
            $sql = "CREATE DATABASE IF NOT EXISTS {$this->db_name}";
            $this->conn->query($sql);
            
            // Select database
            $this->conn->select_db($this->db_name);
            
            // Create tables
            $this->createTables();
            
        } catch(Exception $e) {
            echo "Connection Error: " . $e->getMessage();
        }

        return $this->conn;
    }

    /**
     * Create all required tables
     */
    private function createTables() {
        // Users table
        $users_table = "CREATE TABLE IF NOT EXISTS users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->query($users_table);

        // Products table
        $products_table = "CREATE TABLE IF NOT EXISTS products (
            product_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            category VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            image VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->conn->query($products_table);

        // Cart table
        $cart_table = "CREATE TABLE IF NOT EXISTS cart (
            cart_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT DEFAULT 1,
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
        )";
        $this->conn->query($cart_table);

        // Payments table
        $payments_table = "CREATE TABLE IF NOT EXISTS payments (
            payment_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            transaction_id VARCHAR(255) NOT NULL,
            payment_status ENUM('success', 'failed', 'pending') DEFAULT 'success',
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )";
        $this->conn->query($payments_table);

        // Orders table
        $orders_table = "CREATE TABLE IF NOT EXISTS orders (
            order_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            payment_id INT,
            total_amount DECIMAL(10,2) NOT NULL,
            delivery_address TEXT NOT NULL,
            order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
            FOREIGN KEY (payment_id) REFERENCES payments(payment_id) ON DELETE SET NULL
        )";
        $this->conn->query($orders_table);

        // Order items table
        $order_items_table = "CREATE TABLE IF NOT EXISTS order_items (
            order_item_id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            product_name VARCHAR(150) NOT NULL,
            product_price DECIMAL(10,2) NOT NULL,
            quantity INT NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
        )";
        $this->conn->query($order_items_table);

        // Check and update existing tables for any missing columns
        $this->updateExistingTables();

        // Insert sample products if table is empty
        $this->insertSampleProducts();
    }

    /**
     * Update existing tables with missing columns
     */
    private function updateExistingTables() {
        // Check if orders table has created_at column
        $check = $this->conn->query("SHOW COLUMNS FROM orders LIKE 'created_at'");
        if ($check && $check->num_rows == 0) {
            // Add created_at column to orders table
            $this->conn->query("ALTER TABLE orders ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER order_status");
        }
    }

    /**
     * Insert sample products if products table is empty
     */
    private function insertSampleProducts() {
        $check = $this->conn->query("SELECT COUNT(*) as count FROM products");
        $row = $check->fetch_assoc();
        
        if ($row['count'] == 0) {
            $sample_products = [
                ['Crochet Keychain', 'Accessories', 'Handmade crochet keychain in various colors', 5.99, 'keychain.jpg'],
                ['Crochet Dress', 'Clothing', 'Beautiful handmade crochet dress for girls', 49.99, 'dress.jpg'],
                ['Crochet Wall Hanging', 'Home Décor', 'Elegant wall hanging for home decoration', 29.99, 'wall-hanging.jpg'],
                ['Crochet Bag', 'Accessories', 'Stylish handmade crochet bag', 35.99, 'bag.jpg'],
                ['Crochet Coasters Set', 'Home Décor', 'Set of 6 colorful crochet coasters', 12.99, 'coasters.jpg'],
                ['Crochet Baby Blanket', 'Baby Items', 'Soft and cozy baby blanket', 39.99, 'baby-blanket.jpg'],
                ['Crochet Earrings', 'Accessories', 'Lightweight and colorful crochet earrings', 8.99, 'earrings.jpg'],
                ['Crochet Planter', 'Home Décor', 'Decorative crochet planter holder', 18.99, 'planter.jpg']
            ];

            $stmt = $this->conn->prepare("INSERT INTO products (name, category, description, price, image) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($sample_products as $product) {
                $stmt->bind_param("sssds", $product[0], $product[1], $product[2], $product[3], $product[4]);
                $stmt->execute();
            }
            
            $stmt->close();
        }
    }
}
?>

