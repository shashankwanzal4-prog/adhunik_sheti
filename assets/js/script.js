/**
 * Custom JavaScript for Adhunik Krushi Bhandar
 * Main website functionality
 */

// Initialize AOS (Animate On Scroll)
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });

    // Initialize Swiper for testimonials
    const testimonialSwiper = new Swiper('.testimonial-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });

    // Initialize other components
    initializeBackToTop();
    initializeCart();
    initializeProductQuickView();
    initializeContactForm();
    initializeSearch();
    initializeSmoothScroll();
});

// ======== BACK TO TOP BUTTON ========
function initializeBackToTop() {
    const backToTopButton = document.getElementById('backToTop');
    
    if (backToTopButton) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.style.display = 'flex';
            } else {
                backToTopButton.style.display = 'none';
            }
        });

        // Smooth scroll to top
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// ======== CART FUNCTIONALITY ========
function initializeCart() {
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('form[action*="cart.php"]');
    
    addToCartButtons.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const productId = formData.get('product_id');
            const quantity = formData.get('quantity');
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            submitButton.disabled = true;
            
            // Simulate API call (replace with actual AJAX call)
            setTimeout(() => {
                // Update cart count
                updateCartCount();
                
                // Show success message
                showNotification('Product added to cart successfully!', 'success');
                
                // Reset button
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
                
                // Redirect to cart page
                window.location.href = 'pages/cart.php';
            }, 1000);
        });
    });
}

// ======== CART COUNT UPDATE ========
function updateCartCount() {
    // This would typically make an AJAX call to get the current cart count
    // For now, we'll simulate it
    const cartCountElements = document.querySelectorAll('.badge');
    cartCountElements.forEach(element => {
        if (element.textContent.match(/^\d+$/)) {
            const currentCount = parseInt(element.textContent);
            element.textContent = currentCount + 1;
        }
    });
}

// ======== PRODUCT QUICK VIEW ========
function initializeProductQuickView() {
    const quickViewButtons = document.querySelectorAll('.quick-view-btn');
    
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            showProductQuickView(productId);
        });
    });
}

// ======== SHOW PRODUCT QUICK VIEW MODAL ========
function showProductQuickView(productId) {
    // Create modal HTML
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Quick View</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="spinner"></div>
                        <p class="mt-2">Loading product details...</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Show modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Load product details (replace with actual AJAX call)
    setTimeout(() => {
        const modalBody = modal.querySelector('.modal-body');
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <img src="assets/images/products/product-placeholder.jpg" class="img-fluid rounded" alt="Product">
                </div>
                <div class="col-md-6">
                    <h4>Product Name</h4>
                    <p class="text-muted">Product description goes here...</p>
                    <div class="h4 text-success mb-3">₹450.00</div>
                    <div class="mb-3">
                        <label class="form-label">Quantity:</label>
                        <input type="number" class="form-control" value="1" min="1" max="10" style="width: 100px;">
                    </div>
                    <button class="btn btn-success">
                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                    </button>
                </div>
            </div>
        `;
    }, 1000);
    
    // Remove modal when hidden
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// ======== CONTACT FORM ========
function initializeContactForm() {
    const contactForm = document.querySelector('form[action="contact_handler.php"]');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            submitButton.disabled = true;
            
            // Simulate form submission
            setTimeout(() => {
                showNotification('Message sent successfully! We will contact you soon.', 'success');
                this.reset();
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }, 2000);
        });
    }
}

// ======== SEARCH FUNCTIONALITY ========
function initializeSearch() {
    const searchInput = document.querySelector('input[type="search"], input[name="search"]');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length > 2) {
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 500);
            }
        });
    }
}

// ======== PERFORM SEARCH ========
function performSearch(query) {
    // This would typically make an AJAX call to search products
    console.log('Searching for:', query);
    
    // Show loading state
    showNotification('Searching...', 'info');
    
    // Simulate search results
    setTimeout(() => {
        showNotification(`Found 5 results for "${query}"`, 'success');
    }, 1000);
}

// ======== SMOOTH SCROLL ========
function initializeSmoothScroll() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// ======== NOTIFICATION SYSTEM ========
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease-out;';
    
    // Set icon based on type
    let icon = '';
    switch(type) {
        case 'success':
            icon = '<i class="fas fa-check-circle me-2"></i>';
            break;
        case 'error':
        case 'danger':
            icon = '<i class="fas fa-exclamation-triangle me-2"></i>';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-circle me-2"></i>';
            break;
        default:
            icon = '<i class="fas fa-info-circle me-2"></i>';
    }
    
    notification.innerHTML = `
        ${icon}${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }
    }, 5000);
}

// ======== IMAGE LAZY LOADING ========
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// ======== PRODUCT FILTER ========
function initializeProductFilter() {
    const filterButtons = document.querySelectorAll('[data-filter]');
    const productCards = document.querySelectorAll('.product-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active state
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter products
            productCards.forEach(card => {
                if (filter === 'all' || card.dataset.category === filter) {
                    card.style.display = 'block';
                    card.classList.add('fade-in');
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// ======== WISHLIST FUNCTIONALITY ========
function initializeWishlist() {
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const icon = this.querySelector('i');
            
            if (icon.classList.contains('far')) {
                // Add to wishlist
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-danger');
                showNotification('Added to wishlist!', 'success');
            } else {
                // Remove from wishlist
                icon.classList.remove('fas', 'text-danger');
                icon.classList.add('far');
                showNotification('Removed from wishlist!', 'info');
            }
        });
    });
}

// ======== PRODUCT COMPARISON ========
function initializeComparison() {
    const compareButtons = document.querySelectorAll('.compare-btn');
    
    compareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            
            // Add to comparison (max 4 products)
            let compareList = JSON.parse(localStorage.getItem('compareList') || '[]');
            
            if (compareList.includes(productId)) {
                // Remove from comparison
                compareList = compareList.filter(id => id !== productId);
                showNotification('Removed from comparison', 'info');
            } else if (compareList.length >= 4) {
                showNotification('You can compare maximum 4 products', 'warning');
                return;
            } else {
                // Add to comparison
                compareList.push(productId);
                showNotification('Added to comparison', 'success');
            }
            
            localStorage.setItem('compareList', JSON.stringify(compareList));
            updateCompareCount();
        });
    });
}

// ======== UPDATE COMPARISON COUNT ========
function updateCompareCount() {
    const compareList = JSON.parse(localStorage.getItem('compareList') || '[]');
    const countElements = document.querySelectorAll('.compare-count');
    
    countElements.forEach(element => {
        element.textContent = compareList.length;
    });
}

// ======== PRINT FUNCTIONALITY ========
function initializePrint() {
    const printButtons = document.querySelectorAll('[onclick*="print()"]');
    
    printButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Hide unnecessary elements
            const elementsToHide = document.querySelectorAll('.navbar, .footer, .btn, .sidebar');
            elementsToHide.forEach(el => el.style.display = 'none');
            
            // Print
            window.print();
            
            // Show elements again
            setTimeout(() => {
                elementsToHide.forEach(el => el.style.display = '');
            }, 100);
        });
    });
}

// ======== FORM VALIDATION ========
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    // Remove error on input
                    field.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                    });
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required fields', 'warning');
            }
        });
    });
}

// ======== UTILITY FUNCTIONS ========
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ======== ERROR HANDLING ========
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    showNotification('An unexpected error occurred. Please try again.', 'error');
});

// ======== PERFORMANCE MONITORING ========
if (window.performance && window.performance.timing) {
    window.addEventListener('load', function() {
        const loadTime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
        console.log('Page load time:', loadTime + 'ms');
    });
}
