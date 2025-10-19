<?php
/**
 * Database Update Script
 * Run this file once to update the orders table with created_at column
 */

require_once 'includes/db.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Update - Stitch & Story</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #fef9f6; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #ff6b9d; }
        .success { color: #27ae60; padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0; }
        .error { color: #e74c3c; padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0; }
        .info { color: #004085; padding: 10px; background: #cce5ff; border-radius: 5px; margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 30px; background: #ff6b9d; color: white; text-decoration: none; border-radius: 25px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧶 Stitch & Story - Database Update</h1>
        <p>This script will update your database tables with any missing columns.</p>
";

// Check if orders table exists
$check_table = "SHOW TABLES LIKE 'orders'";
$result = $db->query($check_table);

if ($result->num_rows == 0) {
    echo "<div class='error'>❌ Orders table does not exist. Please run the main application first to create tables.</div>";
} else {
    echo "<div class='success'>✅ Orders table exists</div>";
    
    // Check if created_at column exists
    $check_column = "SHOW COLUMNS FROM orders LIKE 'created_at'";
    $result = $db->query($check_column);
    
    if ($result->num_rows == 0) {
        echo "<div class='info'>⚠️ created_at column is missing. Adding it now...</div>";
        
        // Add the created_at column
        $add_column = "ALTER TABLE orders ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER order_status";
        
        if ($db->query($add_column)) {
            echo "<div class='success'>✅ Successfully added created_at column to orders table!</div>";
        } else {
            echo "<div class='error'>❌ Error adding column: " . $db->error . "</div>";
        }
    } else {
        echo "<div class='success'>✅ created_at column already exists</div>";
    }
}

// Verify all required columns
echo "<h2>Verifying Orders Table Structure:</h2>";
$columns = $db->query("SHOW COLUMNS FROM orders");

if ($columns) {
    echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
            <tr style='background: #ff6b9d; color: white;'>
                <th style='padding: 10px; border: 1px solid #ddd;'>Field</th>
                <th style='padding: 10px; border: 1px solid #ddd;'>Type</th>
                <th style='padding: 10px; border: 1px solid #ddd;'>Default</th>
            </tr>";
    
    while ($col = $columns->fetch_assoc()) {
        echo "<tr>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$col['Field']}</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$col['Type']}</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$col['Default']}</td>
              </tr>";
    }
    
    echo "</table>";
}

echo "
        <div class='success'>
            <strong>✅ Database update completed!</strong><br>
            Your orders table now has all required columns.
        </div>
        
        <a href='index.php' class='btn'>← Back to Home</a>
        <a href='orders.php' class='btn' style='background: #27ae60;'>View Orders</a>
    </div>
</body>
</html>
";

$db->close();
?>

