<?php
/**
 * Configuration File
 * Central configuration for the Stitch & Story website
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'stitch_and_story');
define('DB_USER', 'root');
define('DB_PASS', '');

// Razorpay Configuration
define('RAZORPAY_KEY_ID', 'rzp_test_YOUR_KEY_ID'); // Replace with your Razorpay Key ID
define('RAZORPAY_KEY_SECRET', 'YOUR_KEY_SECRET');   // Replace with your Razorpay Key Secret

// Site Configuration
define('SITE_NAME', 'Stitch & Story');
define('SITE_URL', 'http://localhost:8000');
define('SITE_EMAIL', 'contact@stitchandstory.com');

// Currency Configuration
define('CURRENCY', 'USD');
define('CURRENCY_SYMBOL', '$');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Timezone
date_default_timezone_set('America/New_York');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination
define('PRODUCTS_PER_PAGE', 12);

// Cart Configuration
define('MAX_CART_QUANTITY', 99);
?>

