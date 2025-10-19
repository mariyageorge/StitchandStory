<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';
require_once 'includes/cart.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Cart object
$cart = new Cart($db);

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $cart_id = $_POST['cart_id'] ?? 0;
                $quantity = $_POST['quantity'] ?? 1;
                if ($cart_id > 0 && $quantity > 0) {
                    $cart->updateQuantity($cart_id, $quantity);
                }
                break;
                
            case 'remove':
                $cart_id = $_POST['cart_id'] ?? 0;
                if ($cart_id > 0) {
                    $cart->removeItem($cart_id);
                }
                break;
        }
        
        // Redirect to avoid form resubmission
        header("Location: cart.php");
        exit();
    }
}

// Get cart items
$cart_items = $cart->getCartItems($_SESSION['user_id']);
$cart_total = $cart->getCartTotal($_SESSION['user_id']);
$cart_count = $cart->getCartCount($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Stitch & Story</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="container">
            <a href="index.php" class="logo">🧶 Stitch & Story</a>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="orders.php">My Orders</a></li>
                    <li class="user-welcome">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
                    <li><a href="cart.php" class="cart-icon">
                        🛒 Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a></li>
                    <li><a href="logout.php" class="logout-link">Logout</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Cart Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Your Shopping Cart</h2>
            
            <?php if (isset($_SESSION['cart_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo htmlspecialchars($_SESSION['cart_message']);
                    unset($_SESSION['cart_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if ($cart_items->num_rows > 0): ?>
                <div class="cart-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $cart_items->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <input type="number" 
                                                   name="quantity" 
                                                   value="<?php echo $item['quantity']; ?>" 
                                                   min="1" 
                                                   class="quantity-input"
                                                   onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    <td>
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <button type="submit" class="btn-remove">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="cart-summary">
                    <h3>Cart Summary</h3>
                    <p class="cart-total">Total: ₹<?php echo number_format($cart_total, 2); ?></p>
                    <a href="payment.php" class="btn" style="width: 100%; text-align: center; display: block;">Proceed to Payment</a>
                    <a href="products.php" class="btn btn-secondary" style="width: 100%; text-align: center; display: block; margin-top: 10px;">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="empty-cart">
                    <h2>Your cart is empty</h2>
                    <p>Start adding some beautiful crochet products!</p>
                    <a href="products.php" class="btn">Browse Products</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Stitch & Story. All rights reserved. Made with love and crochet hooks 💗</p>
        </div>
    </footer>
</body>
</html>

