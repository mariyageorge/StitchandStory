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

// Console log for debugging
console.log('Stitch & Story - Website loaded successfully!');

