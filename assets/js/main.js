// =========================
// Stitch & Story - JavaScript
// =========================

// Product Card Slider Functionality (Horizontal)
let productSliderPosition = 0;
const cardWidth = 345; // Card width (320px) + gap (25px)

function slideProducts(direction) {
    const slider = document.getElementById('productSlider');
    if (!slider) return;
    
    const sliderWrapper = slider.parentElement;
    const maxScroll = slider.scrollWidth - sliderWrapper.offsetWidth;
    
    // Calculate new position
    productSliderPosition += (direction * cardWidth);
    
    // Limit the scroll
    if (productSliderPosition < 0) {
        productSliderPosition = 0;
    } else if (productSliderPosition > maxScroll) {
        productSliderPosition = maxScroll;
    }
    
    // Apply transform
    slider.style.transform = `translateX(-${productSliderPosition}px)`;
}

// Auto-slide products every 4 seconds
let autoSlideInterval;

function startAutoSlideProducts() {
    const slider = document.getElementById('productSlider');
    if (!slider) return;
    
    autoSlideInterval = setInterval(() => {
        const sliderWrapper = slider.parentElement;
        const maxScroll = slider.scrollWidth - sliderWrapper.offsetWidth;
        
        productSliderPosition += cardWidth;
        
        // Reset to start if reached end
        if (productSliderPosition >= maxScroll) {
            productSliderPosition = 0;
        }
        
        slider.style.transform = `translateX(-${productSliderPosition}px)`;
    }, 4000);
}

// Stop auto-slide when user interacts
function stopAutoSlideProducts() {
    if (autoSlideInterval) {
        clearInterval(autoSlideInterval);
    }
}

// Initialize product slider on page load
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('productSlider');
    if (slider) {
        startAutoSlideProducts();
        
        // Stop auto-slide when user hovers over slider
        const sliderContainer = document.querySelector('.product-slider-container');
        if (sliderContainer) {
            sliderContainer.addEventListener('mouseenter', stopAutoSlideProducts);
            sliderContainer.addEventListener('mouseleave', startAutoSlideProducts);
        }
    }
});

// Product Modal Functionality
function openProductModal(product) {
    const modal = document.getElementById('productModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalCategory = document.getElementById('modalCategory');
    const modalDescription = document.getElementById('modalDescription');
    const modalPrice = document.getElementById('modalPrice');
    const modalProductId = document.getElementById('modalProductId');
    
    // Set modal content
    modalImage.src = 'assets/images/' + product.image;
    modalImage.alt = product.name;
    modalTitle.textContent = product.name;
    modalCategory.textContent = product.category;
    modalDescription.textContent = product.description;
    modalPrice.textContent = '₹' + parseFloat(product.price).toFixed(2);
    modalProductId.value = product.product_id;
    
    // Show modal
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeProductModal() {
    const modal = document.getElementById('productModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto'; // Re-enable scrolling
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target == modal) {
        closeProductModal();
    }
}

// Close modal on Escape key press
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeProductModal();
    }
});

// Form Validation (if needed in future)
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = '#e74c3c';
        } else {
            input.style.borderColor = '';
        }
    });
    
    return isValid;
}

// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Add to cart with animation
function addToCartAnimation(button) {
    button.textContent = 'Added! ✓';
    button.style.backgroundColor = '#27ae60';
    
    setTimeout(() => {
        button.textContent = 'Add to Cart';
        button.style.backgroundColor = '';
    }, 2000);
}

// =========================
// Form Validation Functions
// =========================

// Username validation
function validateUsername(username) {
    const errors = [];
    
    if (username.length < 3) {
        errors.push('Username must be at least 3 characters');
    }
    
    if (username.length > 16) {
        errors.push('Username must not exceed 16 characters');
    }
    
    // Check if only alphanumeric (letters and numbers only)
    const alphanumericRegex = /^[a-zA-Z0-9]+$/;
    if (!alphanumericRegex.test(username)) {
        errors.push('Username can only contain letters and numbers');
    }
    
    return errors;
}

// Email validation
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Password validation
function validatePassword(password) {
    const errors = [];
    
    if (password.length < 8) {
        errors.push('Password must be at least 8 characters');
    }
    
    return errors;
}

// Check if email exists (AJAX)
function checkEmailExists(email, callback) {
    if (!validateEmail(email)) {
        return;
    }
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'check_email.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                callback(response.exists);
            } catch (e) {
                console.error('Error parsing response:', e);
            }
        }
    };
    
    xhr.send('email=' + encodeURIComponent(email));
}

// Display validation message
function showValidationMessage(input, message, isError = true) {
    // Remove existing message
    const existingMessage = input.parentElement.querySelector('.validation-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    if (message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'validation-message ' + (isError ? 'error' : 'success');
        messageDiv.textContent = message;
        input.parentElement.appendChild(messageDiv);
        
        // Update input border color
        input.style.borderColor = isError ? '#e74c3c' : '#27ae60';
    } else {
        input.style.borderColor = '';
    }
}

// Initialize register form validation
function initRegisterValidation() {
    const usernameInput = document.querySelector('input[name="username"]');
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
    
    if (!usernameInput || !emailInput || !passwordInput || !confirmPasswordInput) {
        return; // Not on register page
    }
    
    // Username validation
    let usernameTimeout;
    usernameInput.addEventListener('input', function() {
        clearTimeout(usernameTimeout);
        usernameTimeout = setTimeout(() => {
            const errors = validateUsername(this.value);
            if (errors.length > 0) {
                showValidationMessage(this, errors[0], true);
            } else if (this.value.length > 0) {
                showValidationMessage(this, '✓ Username is valid', false);
            } else {
                showValidationMessage(this, '');
            }
        }, 300);
    });
    
    // Email validation
    let emailTimeout;
    emailInput.addEventListener('input', function() {
        clearTimeout(emailTimeout);
        const email = this.value;
        
        emailTimeout = setTimeout(() => {
            if (email.length === 0) {
                showValidationMessage(this, '');
                return;
            }
            
            if (!validateEmail(email)) {
                showValidationMessage(this, 'Invalid email format', true);
                return;
            }
            
            // Check if email exists
            checkEmailExists(email, (exists) => {
                if (exists) {
                    showValidationMessage(emailInput, 'Email is already registered', true);
                } else {
                    showValidationMessage(emailInput, '✓ Email is available', false);
                }
            });
        }, 500);
    });
    
    // Password validation
    passwordInput.addEventListener('input', function() {
        const errors = validatePassword(this.value);
        if (errors.length > 0) {
            showValidationMessage(this, errors[0], true);
        } else if (this.value.length > 0) {
            showValidationMessage(this, '✓ Password is strong enough', false);
        } else {
            showValidationMessage(this, '');
        }
        
        // Also check confirm password if it has a value
        if (confirmPasswordInput.value.length > 0) {
            confirmPasswordInput.dispatchEvent(new Event('input'));
        }
    });
    
    // Confirm password validation
    confirmPasswordInput.addEventListener('input', function() {
        if (this.value.length === 0) {
            showValidationMessage(this, '');
            return;
        }
        
        if (this.value !== passwordInput.value) {
            showValidationMessage(this, 'Passwords do not match', true);
        } else {
            showValidationMessage(this, '✓ Passwords match', false);
        }
    });
    
    // Form submission validation
    const form = usernameInput.closest('form');
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate username
        const usernameErrors = validateUsername(usernameInput.value);
        if (usernameErrors.length > 0) {
            showValidationMessage(usernameInput, usernameErrors[0], true);
            isValid = false;
        }
        
        // Validate email
        if (!validateEmail(emailInput.value)) {
            showValidationMessage(emailInput, 'Invalid email format', true);
            isValid = false;
        }
        
        // Validate password
        const passwordErrors = validatePassword(passwordInput.value);
        if (passwordErrors.length > 0) {
            showValidationMessage(passwordInput, passwordErrors[0], true);
            isValid = false;
        }
        
        // Validate confirm password
        if (confirmPasswordInput.value !== passwordInput.value) {
            showValidationMessage(confirmPasswordInput, 'Passwords do not match', true);
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
}

// Initialize login form validation
function initLoginValidation() {
    const emailInput = document.querySelector('input[name="email"]');
    const passwordInput = document.querySelector('input[name="password"]');
    
    if (!emailInput || !passwordInput) {
        return; // Not on login page
    }
    
    // Check if we're on login page (not register page)
    if (document.querySelector('input[name="username"]')) {
        return; // This is register page
    }
    
    // Email/Username validation (just check if not empty)
    emailInput.addEventListener('input', function() {
        if (this.value.length === 0) {
            showValidationMessage(this, 'Email or username is required', true);
        } else {
            showValidationMessage(this, '');
        }
    });
    
    // Password validation (just check if not empty)
    passwordInput.addEventListener('input', function() {
        if (this.value.length === 0) {
            showValidationMessage(this, 'Password is required', true);
        } else {
            showValidationMessage(this, '');
        }
    });
}

// Initialize validations on page load
document.addEventListener('DOMContentLoaded', function() {
    initRegisterValidation();
    initLoginValidation();
});

// Console log for debugging
console.log('Stitch & Story - Website loaded successfully!');

