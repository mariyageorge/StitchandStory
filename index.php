<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/product.php';
require_once 'includes/cart.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Product object
$product = new Product($db);

// Get products for image slider
$slider_products = $product->getAllProducts('', 'created_at', 'DESC');

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
    <title>Stitch & Story - Handmade Crochet Products</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .image-slider-container {
            position: relative;
            max-width: 100%;
            margin: 40px 0;
            overflow: hidden;
        }
        
        .image-slider {
            display: flex;
            transition: transform 0.5s ease;
        }
        
        .slider-image {
            min-width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            z-index: 10;
            transition: background 0.3s;
        }
        
        .slider-btn:hover {
            background: rgba(255, 255, 255, 1);
        }
        
        .slider-btn.prev {
            left: 20px;
        }
        
        .slider-btn.next {
            right: 20px;
        }
        
        .content-section {
            padding: 60px 0;
            text-align: center;
        }
        
        .content-section h2 {
            font-size: 32px;
            color: var(--primary-pink);
            margin-bottom: 20px;
        }
        
        .content-section p {
            font-size: 18px;
            color: var(--text-dark);
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto 20px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        
        .feature-card {
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .feature-card h3 {
            color: var(--primary-pink);
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .feature-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Welcome to Stitch & Story</h1>
            <p>Handcrafted with love, every stitch tells a story</p>
            <p>Discover beautiful handmade crochet products for your home and wardrobe</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="products.php" class="btn">Shop Now</a>
            <?php else: ?>
                <a href="login.php" class="btn">Login to Start Shopping</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Image Slider Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Our Creations</h2>
            <div class="image-slider-container">
                <button class="slider-btn prev" onclick="slideImage(-1)">❮</button>
                <div class="image-slider" id="imageSlider">
                    <?php 
                    $slider_products->data_seek(0);
                    while ($product_item = $slider_products->fetch_assoc()): 
                    ?>
                        <img src="assets/images/<?php echo htmlspecialchars($product_item['image']); ?>" 
                             alt="Handmade crochet product" 
                             class="slider-image">
                    <?php endwhile; ?>
                </div>
                <button class="slider-btn next" onclick="slideImage(1)">❯</button>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="content-section">
        <div class="container">
            <h2>About Stitch & Story</h2>
            <p>
                Welcome to our world of handcrafted crochet artistry! Each piece in our collection is 
                lovingly created by skilled artisans who pour their heart into every stitch. We believe 
                in the beauty of slow fashion and the warmth of handmade treasures.
            </p>
            <p>
                From cozy blankets to adorable amigurumi, stylish accessories to home décor, our products 
                are made with premium yarns and meticulous attention to detail. Every item is unique and 
                carries the personal touch of the maker.
            </p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Why Choose Us</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🧶</div>
                    <h3>Handmade Quality</h3>
                    <p>Every product is carefully handcrafted with love and attention to detail</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💝</div>
                    <h3>Unique Designs</h3>
                    <p>Original patterns and designs you won't find anywhere else</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌿</div>
                    <h3>Quality Materials</h3>
                    <p>Premium yarns and materials for lasting beauty and comfort</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎁</div>
                    <h3>Perfect Gifts</h3>
                    <p>Thoughtful, handmade gifts that show you care</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="content-section" style="background: var(--background-light);">
        <div class="container">
            <h2>Start Your Crochet Journey</h2>
            <p>
                Browse our collection and find the perfect handmade piece for yourself or your loved ones.
            </p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="products.php" class="btn" style="margin-top: 20px;">Browse Products</a>
            <?php else: ?>
                <a href="register.php" class="btn" style="margin: 20px 10px 0 0;">Register Now</a>
                <a href="login.php" class="btn btn-secondary" style="margin-top: 20px;">Login</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Stitch & Story. All rights reserved. Made with love and crochet hooks 💗</p>
        </div>
    </footer>
    
    <script>
        let currentSlide = 0;
        const slider = document.getElementById('imageSlider');
        const totalSlides = slider.children.length;

        function slideImage(direction) {
            currentSlide += direction;
            
            if (currentSlide < 0) {
                currentSlide = totalSlides - 1;
            } else if (currentSlide >= totalSlides) {
                currentSlide = 0;
            }
            
            slider.style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        // Auto-slide every 5 seconds
        setInterval(() => {
            slideImage(1);
        }, 5000);
    </script>
</body>
</html>