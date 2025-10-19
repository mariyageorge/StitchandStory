<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';
require_once 'includes/order.php';
require_once 'includes/cart.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Order object
$order = new Order($db);

// Get user's orders
$orders = $order->getUserOrders($_SESSION['user_id']);

// Get cart count
$cart = new Cart($db);
$cart_count = $cart->getCartCount($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Stitch & Story</title>
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
                    <li><a href="orders.php" class="active">My Orders</a></li>
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

    <!-- Orders Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">My Orders</h2>
            
            <?php if ($orders->num_rows > 0): ?>
                <div class="orders-list">
                    <?php while ($order_data = $orders->fetch_assoc()): ?>
                        <?php
                        // Get order items
                        $order_items = $order->getOrderItems($order_data['order_id']);
                        ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <h3>Order #<?php echo $order_data['order_id']; ?></h3>
                                    <p class="order-date">Placed on <?php echo date('F j, Y, g:i a', strtotime($order_data['created_at'])); ?></p>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-<?php echo $order_data['order_status']; ?>">
                                        <?php echo ucfirst($order_data['order_status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="order-items">
                                <?php while ($item = $order_items->fetch_assoc()): ?>
                                    <div class="order-item">
                                        <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                        <div class="item-details">
                                            <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                                            <p class="item-price">₹<?php echo number_format($item['product_price'], 2); ?></p>
                                        </div>
                                        <div class="item-subtotal">
                                            <p>₹<?php echo number_format($item['subtotal'], 2); ?></p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            
                            <div class="order-footer">
                                <div class="delivery-address">
                                    <h4>Delivery Address:</h4>
                                    <p><?php echo nl2br(htmlspecialchars($order_data['delivery_address'])); ?></p>
                                </div>
                                <div class="order-total">
                                    <h4>Total Amount:</h4>
                                    <p class="total-price">₹<?php echo number_format($order_data['total_amount'], 2); ?></p>
                                    <?php if (isset($order_data['transaction_id'])): ?>
                                        <p class="transaction-id">Transaction ID: <?php echo htmlspecialchars($order_data['transaction_id']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="order-actions">
                                <a href="generate_receipt.php?order_id=<?php echo $order_data['order_id']; ?>" 
                                   class="btn" target="_blank">
                                    📄 Print Receipt
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-cart">
                    <h2>No orders yet</h2>
                    <p>You haven't placed any orders yet. Start shopping!</p>
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

