<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';
require_once 'includes/cart.php';
require_once 'includes/payment.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Cart object
$cart = new Cart($db);

// Get cart total
$cart_total = $cart->getCartTotal($_SESSION['user_id']);

// Check if cart is empty
if ($cart_total <= 0) {
    header("Location: cart.php");
    exit();
}

// Razorpay API Keys (Replace with your actual keys)
$razorpay_key_id = "rzp_test_RVEferDnVRYHXc"; // Replace with your Razorpay Key ID
$razorpay_key_secret = "3lxVPZxfaMNh0GvsiYJL3433"; // Replace with your Razorpay Key Secret

// Handle payment success
if (isset($_POST['razorpay_payment_id'])) {
    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    $razorpay_order_id = $_POST['razorpay_order_id'];
    $razorpay_signature = $_POST['razorpay_signature'];
    
    // Get all address fields
    $full_name = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address_line1 = $_POST['address_line1'] ?? '';
    $address_line2 = $_POST['address_line2'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $pincode = $_POST['pincode'] ?? '';
    
    // Validate required fields
    $errors = [];
    if (empty($full_name)) $errors[] = 'Full name is required';
    if (empty($phone)) $errors[] = 'Phone number is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($address_line1)) $errors[] = 'Address line 1 is required';
    if (empty($city)) $errors[] = 'City is required';
    if (empty($state)) $errors[] = 'State is required';
    if (empty($pincode)) $errors[] = 'Pincode is required';
    
    // Validate phone number (10 digits)
    if (!empty($phone) && !preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = 'Phone number must be 10 digits';
    }
    
    // Validate pincode (6 digits)
    if (!empty($pincode) && !preg_match('/^[0-9]{6}$/', $pincode)) {
        $errors[] = 'Pincode must be 6 digits';
    }
    
    // Validate email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode(', ', $errors);
        header("Location: payment.php");
        exit();
    }
    
    // Create formatted delivery address
    $delivery_address = $full_name . "\n";
    $delivery_address .= "Phone: " . $phone . "\n";
    $delivery_address .= "Email: " . $email . "\n\n";
    $delivery_address .= $address_line1 . "\n";
    if (!empty($address_line2)) {
        $delivery_address .= $address_line2 . "\n";
    }
    $delivery_address .= $city . ", " . $state . " - " . $pincode;
    
    // Verify payment signature (basic implementation)
    // In production, you should verify the signature properly
    
    // Save payment to database
    $payment = new Payment($db);
    $payment->user_id = $_SESSION['user_id'];
    $payment->amount = $cart_total;
    $payment->transaction_id = $razorpay_payment_id;
    $payment->payment_status = 'success';
    
    if ($payment->savePayment()) {
        $payment_id = $payment->payment_id;
        
        // Create order with items
        require_once 'includes/order.php';
        $order = new Order($db);
        $order->user_id = $_SESSION['user_id'];
        $order->payment_id = $payment_id;
        $order->total_amount = $cart_total;
        $order->delivery_address = $delivery_address;
        
        // Get cart items for order
        $cart_items = $cart->getCartItems($_SESSION['user_id']);
        
        if ($order->createOrder($cart_items)) {
            // Clear cart
            $cart->clearCart($_SESSION['user_id']);
            
            // Set success message
            $_SESSION['payment_success'] = true;
            $_SESSION['order_id'] = $order->order_id;
            $_SESSION['transaction_id'] = $razorpay_payment_id;
            $_SESSION['amount'] = $cart_total;
            
            // Redirect to success page
            header("Location: payment_success.php");
            exit();
        }
    }
}

$cart_count = $cart->getCartCount($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Stitch & Story</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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

    <!-- Payment Section -->
    <section class="section">
        <div class="container">
            <div class="form-container" style="max-width: 700px;">
                <h2>Complete Your Payment</h2>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-error">
                        <?php 
                        echo htmlspecialchars($_SESSION['error_message']);
                        unset($_SESSION['error_message']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="cart-summary">
                    <h3>Delivery Information</h3>
                    <form id="deliveryForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullName">Full Name <span style="color: red;">*</span></label>
                                <input type="text" id="fullName" name="full_name" 
                                       placeholder="Enter your full name" 
                                       value="<?php echo htmlspecialchars($_SESSION['username']); ?>"
                                       required>
                                <span class="error-message" id="nameError"></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group" style="flex: 1;">
                                <label for="phone">Phone Number <span style="color: red;">*</span></label>
                                <input type="tel" id="phone" name="phone" 
                                       placeholder="10-digit mobile number" 
                                       maxlength="10" 
                                       pattern="[0-9]{10}"
                                       required>
                                <span class="error-message" id="phoneError"></span>
                            </div>
                            
                            <div class="form-group" style="flex: 1;">
                                <label for="email">Email Address <span style="color: red;">*</span></label>
                                <input type="email" id="email" name="email" 
                                       placeholder="your@email.com"
                                       value="<?php echo htmlspecialchars($_SESSION['email']); ?>"
                                       required>
                                <span class="error-message" id="emailError"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="addressLine1">Address Line 1 <span style="color: red;">*</span></label>
                            <input type="text" id="addressLine1" name="address_line1" 
                                   placeholder="House No., Building Name, Street" 
                                   required>
                            <span class="error-message" id="addressLine1Error"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="addressLine2">Address Line 2 (Optional)</label>
                            <input type="text" id="addressLine2" name="address_line2" 
                                   placeholder="Landmark, Area">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group" style="flex: 2;">
                                <label for="city">City <span style="color: red;">*</span></label>
                                <input type="text" id="city" name="city" 
                                       placeholder="Enter city name" 
                                       required>
                                <span class="error-message" id="cityError"></span>
                            </div>
                            
                            <div class="form-group" style="flex: 1;">
                                <label for="state">State <span style="color: red;">*</span></label>
                                <select id="state" name="state" required>
                                    <option value="">Select State</option>
                                    <option value="Andhra Pradesh">Andhra Pradesh</option>
                                    <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                    <option value="Assam">Assam</option>
                                    <option value="Bihar">Bihar</option>
                                    <option value="Chhattisgarh">Chhattisgarh</option>
                                    <option value="Goa">Goa</option>
                                    <option value="Gujarat">Gujarat</option>
                                    <option value="Haryana">Haryana</option>
                                    <option value="Himachal Pradesh">Himachal Pradesh</option>
                                    <option value="Jharkhand">Jharkhand</option>
                                    <option value="Karnataka">Karnataka</option>
                                    <option value="Kerala">Kerala</option>
                                    <option value="Madhya Pradesh">Madhya Pradesh</option>
                                    <option value="Maharashtra">Maharashtra</option>
                                    <option value="Manipur">Manipur</option>
                                    <option value="Meghalaya">Meghalaya</option>
                                    <option value="Mizoram">Mizoram</option>
                                    <option value="Nagaland">Nagaland</option>
                                    <option value="Odisha">Odisha</option>
                                    <option value="Punjab">Punjab</option>
                                    <option value="Rajasthan">Rajasthan</option>
                                    <option value="Sikkim">Sikkim</option>
                                    <option value="Tamil Nadu">Tamil Nadu</option>
                                    <option value="Telangana">Telangana</option>
                                    <option value="Tripura">Tripura</option>
                                    <option value="Uttar Pradesh">Uttar Pradesh</option>
                                    <option value="Uttarakhand">Uttarakhand</option>
                                    <option value="West Bengal">West Bengal</option>
                                    <option value="Delhi">Delhi</option>
                                </select>
                                <span class="error-message" id="stateError"></span>
                            </div>
                            
                            <div class="form-group" style="flex: 1;">
                                <label for="pincode">Pincode <span style="color: red;">*</span></label>
                                <input type="text" id="pincode" name="pincode" 
                                       placeholder="6-digit pincode" 
                                       maxlength="6" 
                                       pattern="[0-9]{6}"
                                       required>
                                <span class="error-message" id="pincodeError"></span>
                            </div>
                        </div>
                    </form>
                    
                    <h3 style="margin-top: 30px;">Order Summary</h3>
                    <p class="cart-total">Total Amount: ₹<?php echo number_format($cart_total, 2); ?></p>
                    <p style="color: var(--text-light); margin-bottom: 20px;">
                        You are about to pay for your order using Razorpay secure payment gateway.
                    </p>
                    
                    <button id="payButton" class="btn" style="width: 100%;">Pay Now with Razorpay</button>
                    <a href="cart.php" class="btn btn-secondary" style="width: 100%; text-align: center; display: block; margin-top: 10px;">Back to Cart</a>
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

    <script>
        // Form Validation
        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('input, select').forEach(el => el.style.borderColor = '');
        }
        
        function showError(fieldId, message) {
            const errorEl = document.getElementById(fieldId + 'Error');
            const inputEl = document.getElementById(fieldId);
            if (errorEl) errorEl.textContent = message;
            if (inputEl) inputEl.style.borderColor = '#e74c3c';
        }
        
        function validateForm() {
            clearErrors();
            let isValid = true;
            
            // Get form values
            const fullName = document.getElementById('fullName').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const email = document.getElementById('email').value.trim();
            const addressLine1 = document.getElementById('addressLine1').value.trim();
            const city = document.getElementById('city').value.trim();
            const state = document.getElementById('state').value;
            const pincode = document.getElementById('pincode').value.trim();
            
            // Validate Full Name
            if (!fullName) {
                showError('fullName', 'Full name is required');
                isValid = false;
            } else if (fullName.length < 3) {
                showError('fullName', 'Name must be at least 3 characters');
                isValid = false;
            }
            
            // Validate Phone
            if (!phone) {
                showError('phone', 'Phone number is required');
                isValid = false;
            } else if (!/^[0-9]{10}$/.test(phone)) {
                showError('phone', 'Phone must be 10 digits');
                isValid = false;
            }
            
            // Validate Email
            if (!email) {
                showError('email', 'Email is required');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showError('email', 'Invalid email format');
                isValid = false;
            }
            
            // Validate Address Line 1
            if (!addressLine1) {
                showError('addressLine1', 'Address is required');
                isValid = false;
            } else if (addressLine1.length < 10) {
                showError('addressLine1', 'Address must be at least 10 characters');
                isValid = false;
            }
            
            // Validate City
            if (!city) {
                showError('city', 'City is required');
                isValid = false;
            } else if (city.length < 3) {
                showError('city', 'City name must be at least 3 characters');
                isValid = false;
            }
            
            // Validate State
            if (!state) {
                showError('state', 'Please select a state');
                isValid = false;
            }
            
            // Validate Pincode
            if (!pincode) {
                showError('pincode', 'Pincode is required');
                isValid = false;
            } else if (!/^[0-9]{6}$/.test(pincode)) {
                showError('pincode', 'Pincode must be 6 digits');
                isValid = false;
            }
            
            return isValid;
        }
        
        // Real-time validation
        document.getElementById('phone').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        document.getElementById('pincode').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Razorpay Payment Integration
        document.getElementById('payButton').onclick = function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateForm()) {
                alert('Please fill all required fields correctly before proceeding to payment.');
                return;
            }
            
            // Get form values
            const fullName = document.getElementById('fullName').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const email = document.getElementById('email').value.trim();
            const addressLine1 = document.getElementById('addressLine1').value.trim();
            const addressLine2 = document.getElementById('addressLine2').value.trim();
            const city = document.getElementById('city').value.trim();
            const state = document.getElementById('state').value;
            const pincode = document.getElementById('pincode').value.trim();
            
            var options = {
                "key": "<?php echo $razorpay_key_id; ?>",
                "amount": "<?php echo $cart_total * 100; ?>",
                "currency": "INR",
                "name": "Stitch & Story",
                "description": "Purchase of Crochet Products",
                "image": "https://via.placeholder.com/100x100/ff6b9d/ffffff?text=S&S",
                "handler": function (response) {
                    // Create a form and submit payment details
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'payment.php';
                    
                    var payment_id = document.createElement('input');
                    payment_id.type = 'hidden';
                    payment_id.name = 'razorpay_payment_id';
                    payment_id.value = response.razorpay_payment_id;
                    form.appendChild(payment_id);
                    
                    var order_id = document.createElement('input');
                    order_id.type = 'hidden';
                    order_id.name = 'razorpay_order_id';
                    order_id.value = response.razorpay_order_id;
                    form.appendChild(order_id);
                    
                    var signature = document.createElement('input');
                    signature.type = 'hidden';
                    signature.name = 'razorpay_signature';
                    signature.value = response.razorpay_signature;
                    form.appendChild(signature);
                    
                    // Add all form fields
                    const fields = [
                        {name: 'full_name', value: fullName},
                        {name: 'phone', value: phone},
                        {name: 'email', value: email},
                        {name: 'address_line1', value: addressLine1},
                        {name: 'address_line2', value: addressLine2},
                        {name: 'city', value: city},
                        {name: 'state', value: state},
                        {name: 'pincode', value: pincode}
                    ];
                    
                    fields.forEach(field => {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = field.name;
                        input.value = field.value;
                        form.appendChild(input);
                    });
                    
                    document.body.appendChild(form);
                    form.submit();
                },
                "prefill": {
                    "name": fullName,
                    "email": email,
                    "contact": phone
                },
                "theme": {
                    "color": "#ff6b9d"
                }
            };
            
            var rzp1 = new Razorpay(options);
            
            rzp1.on('payment.failed', function (response) {
                alert('Payment Failed! Error: ' + response.error.description);
            });
            
            rzp1.open();
        }
    </script>
</body>
</html>

