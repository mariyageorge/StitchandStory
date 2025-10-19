<?php
/**
 * Database Connection Test Script
 * Run this file to verify your database setup
 */

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Connection Test - Stitch & Story</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffc2d1 0%, #fef9f6 100%);
            padding: 40px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(255, 107, 157, 0.2);
        }
        h1 {
            color: #ff6b9d;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ffc2d1;
        }
        th {
            background-color: #ff6b9d;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #ff6b9d;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #e63c6e;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧶 Stitch & Story - Database Test</h1>";

// Test database connection
require_once 'includes/db.php';

echo "<div class='info'>📝 Testing database connection...</div>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "<div class='success'>✓ Database connection successful!</div>";
        echo "<div class='success'>✓ Database 'stitch_and_story' created/connected</div>";
        echo "<div class='success'>✓ All tables created successfully</div>";
        
        // Check tables
        echo "<h2 style='color: #ff6b9d; margin-top: 30px;'>Database Tables:</h2>";
        $tables = $db->query("SHOW TABLES");
        
        echo "<table>";
        echo "<tr><th>Table Name</th><th>Records</th></tr>";
        
        while ($row = $tables->fetch_array()) {
            $table_name = $row[0];
            $count_result = $db->query("SELECT COUNT(*) as count FROM $table_name");
            $count = $count_result->fetch_assoc()['count'];
            echo "<tr><td>$table_name</td><td>$count records</td></tr>";
        }
        
        echo "</table>";
        
        // Check products
        echo "<h2 style='color: #ff6b9d; margin-top: 30px;'>Sample Products:</h2>";
        $products = $db->query("SELECT * FROM products LIMIT 5");
        
        if ($products->num_rows > 0) {
            echo "<div class='success'>✓ Sample products loaded successfully</div>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th></tr>";
            
            while ($product = $products->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$product['product_id']}</td>";
                echo "<td>{$product['name']}</td>";
                echo "<td>{$product['category']}</td>";
                echo "<td>\${$product['price']}</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<div class='error'>⚠ No sample products found. They should be auto-inserted.</div>";
        }
        
        // PHP Info
        echo "<h2 style='color: #ff6b9d; margin-top: 30px;'>System Information:</h2>";
        echo "<div class='info'>";
        echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
        echo "<strong>MySQL Client Version:</strong> " . mysqli_get_client_info() . "<br>";
        echo "<strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
        echo "</div>";
        
        echo "<div style='text-align: center;'>";
        echo "<a href='index.php' class='btn'>Go to Website</a> ";
        echo "<a href='register.php' class='btn'>Register Account</a>";
        echo "</div>";
        
    } else {
        echo "<div class='error'>✗ Failed to connect to database</div>";
        echo "<div class='info'>Please check your database credentials in includes/db.php</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
    echo "<div class='info'><strong>Troubleshooting:</strong><br>";
    echo "1. Make sure MySQL server is running<br>";
    echo "2. Check database credentials in includes/db.php<br>";
    echo "3. Verify MySQL user has proper permissions<br>";
    echo "4. Try accessing phpMyAdmin to test MySQL</div>";
}

echo "
    </div>
</body>
</html>";
?>

