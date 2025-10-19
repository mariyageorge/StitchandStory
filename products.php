<?php
session_start();

// Check if user is logged in - products only visible after login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?message=" . urlencode("Please login to view products"));
    exit();
}

require_once 'includes/db.php';
require_once 'includes/product.php';
require_once 'includes/cart.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Product object
$product = new Product($db);

// Get search and sort parameters
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'name';
$order = $_GET['order'] ?? 'ASC';

// Get all products
$products = $product->getAllProducts($search, $sort, $order);

// Get categories
$categories = $product->getAllCategories();

// Get cart count if user is logged in
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cart = new Cart($db);
    $cart_count = $cart->getCartCount($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Stitch & Story</title>
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
                        <li><a href="cart.php" class="cart-icon">
                            🛒 Cart
                            <?php if ($cart_count > 0): ?>
                                <span class="cart-count"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a></li>
                        <li><a href="logout.php" class="logout-link">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Products Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Our Products</h2>
            
            <!-- Search and Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <form method="GET" action="">
                        <input type="text" 
                               name="search" 
                               placeholder="Search products by name, category, or description..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                        <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
                    </form>
                </div>
                
                <div class="sort-box">
                    <form method="GET" action="" id="sortForm">
                        <select name="sort" onchange="document.getElementById('sortForm').submit()">
                            <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Sort by Name</option>
                            <option value="price" <?php echo $sort === 'price' ? 'selected' : ''; ?>>Sort by Price</option>
                            <option value="category" <?php echo $sort === 'category' ? 'selected' : ''; ?>>Sort by Category</option>
                            <option value="created_at" <?php echo $sort === 'created_at' ? 'selected' : ''; ?>>Sort by Date</option>
                        </select>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
                    </form>
                </div>
                
                <div class="sort-box">
                    <form method="GET" action="" id="orderForm">
                        <select name="order" onchange="document.getElementById('orderForm').submit()">
                            <option value="ASC" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                            <option value="DESC" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                        </select>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-grid">
                <?php if ($products->num_rows > 0): ?>
                    <?php while ($row = $products->fetch_assoc()): ?>
                        <div class="product-card">
                            <img src="assets/images/<?php echo htmlspecialchars($row['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                                 onclick="openProductModal(<?php echo htmlspecialchars(json_encode($row)); ?>)"
                                 style="cursor: pointer;">
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="product-category"><?php echo htmlspecialchars($row['category']); ?></p>
                                <p class="product-price">Rs.<?php echo number_format($row['price'], 2); ?></p>
                                
                                <form method="POST" action="add_to_cart.php" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn">Add to Cart</button>
                                </form>
                                <button onclick="openProductModal(<?php echo htmlspecialchars(json_encode($row)); ?>)" 
                                        class="btn btn-secondary" style="margin-top: 5px;">View Details</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-cart">
                        <h2>No products found</h2>
                        <p>Try searching for something else</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeProductModal()">&times;</span>
            <div class="modal-body">
                <div class="modal-image">
                    <img id="modalImage" src="" alt="">
                </div>
                <div class="modal-details">
                    <h2 id="modalTitle"></h2>
                    <p class="modal-category" id="modalCategory"></p>
                    <p class="modal-description" id="modalDescription"></p>
                    <p class="modal-price" id="modalPrice"></p>
                    <form method="POST" action="add_to_cart.php" id="modalCartForm">
                        <input type="hidden" name="product_id" id="modalProductId">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn" style="width: 100%;">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Stitch & Story. All rights reserved. Made with love and crochet hooks 💗</p>
        </div>
    </footer>
    
    <script src="assets/js/main.js"></script>
</body>
</html>

