<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';
require_once 'includes/cart.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    if ($product_id > 0 && $quantity > 0) {
        $database = new Database();
        $db = $database->getConnection();
        
        $cart = new Cart($db);
        $cart->user_id = $_SESSION['user_id'];
        $cart->product_id = $product_id;
        $cart->quantity = $quantity;
        
        if ($cart->addToCart()) {
            $_SESSION['cart_message'] = 'Product added to cart successfully!';
        } else {
            $_SESSION['cart_message'] = 'Failed to add product to cart!';
        }
    }
}

// Redirect back to previous page or products page
$redirect = $_SERVER['HTTP_REFERER'] ?? 'products.php';
header("Location: $redirect");
exit();
?>

