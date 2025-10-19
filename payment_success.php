<?php
session_start();

// Check if payment was successful
if (!isset($_SESSION['payment_success']) || !$_SESSION['payment_success']) {
    header("Location: index.php");
    exit();
}

$order_id = $_SESSION['order_id'] ?? 0;
$transaction_id = $_SESSION['transaction_id'] ?? '';
$amount = $_SESSION['amount'] ?? 0;

// Clear payment session variables
unset($_SESSION['payment_success']);
unset($_SESSION['order_id']);
unset($_SESSION['transaction_id']);
unset($_SESSION['amount']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - Stitch & Story</title>
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="orders.php">My Orders</a></li>
                        <li class="user-welcome">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
                        <li><a href="cart.php">🛒 Cart</a></li>
                        <li><a href="logout.php" class="logout-link">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Payment Success Section -->
    <section class="section">
        <div class="container">
            <div class="payment-success">
                <div class="icon">✓</div>
                <h2>Payment Successful!</h2>
                <p style="color: var(--text-light); margin-bottom: 20px;">
                    Thank you for your purchase! Your order has been placed successfully.
                </p>
                
                <div class="cart-summary" style="max-width: 500px; margin: 0 auto;">
                    <h3>Transaction Details</h3>
                    <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($order_id); ?></p>
                    <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($transaction_id); ?></p>
                    <p><strong>Amount Paid:</strong> ₹<?php echo number_format($amount, 2); ?></p>
                    <p style="color: var(--text-light); margin-top: 20px;">
                        Your order will be processed and shipped soon. We'll send you an email confirmation shortly.
                    </p>
                    
                    <a href="orders.php" class="btn" style="width: 100%; text-align: center; display: block; margin-top: 20px;">View My Orders</a>
                    <a href="products.php" class="btn btn-secondary" style="width: 100%; text-align: center; display: block; margin-top: 10px;">Continue Shopping</a>
                    <a href="index.php" class="btn btn-secondary" style="width: 100%; text-align: center; display: block; margin-top: 10px;">Back to Home</a>
                </div>
            </div>
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

