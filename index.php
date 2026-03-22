<?php
/**
 * Homepage for Adhunik Krushi Bhandar
 * Main landing page with all sections
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include header
require_once 'includes/header.php';

// Get featured products from database
$featured_products = [];
$result = $db->query("SELECT * FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 4");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $featured_products[] = $row;
    }
}
?>

<!-- Hero Section -->
<section class="hero-section position-relative">
    <div class="hero-overlay"></div>
    <div class="container position-relative">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-7">
                <div class="hero-content" data-aos="fade-up">
                    <h1 class="display-4 fw-bold mb-4" data-aos="fade-up"><?php echo __('hero_title'); ?></h1>
                    <p class="lead mb-3" data-aos="fade-up" data-aos-delay="100"><?php echo __('hero_subtitle'); ?></p>
                    <p class="mb-4" data-aos="fade-up" data-aos-delay="200"><?php echo __('hero_description'); ?></p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start" data-aos="fade-up" data-aos-delay="300">
                        <a href="pages/products.php" class="btn btn-success btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i><?php echo __('shop_now'); ?>
                        </a>
                        <a href="pages/products.php" class="btn btn-outline-success btn-lg">
                            <i class="fas fa-leaf me-2"></i><?php echo __('explore_products'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold text-success"><?php echo __('featured_products'); ?></h2>
            <p class="lead text-muted"><?php echo __('featured_desc'); ?></p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo array_search($product, $featured_products) * 100; ?>">
                <div class="product-card card h-100 border-0 shadow-sm">
                    <div class="product-image-wrapper">
                        <a href="pages/product_detail.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                            <img src="assets/images/products/<?php echo $product['image'] ?: 'product-placeholder.jpg'; ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                        <div class="product-overlay">
                            <a href="pages/product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-eye"></i> <?php echo __('view_details'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <a href="pages/product_detail.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($product['name']); ?></h5>
                        </a>
                        <p class="text-muted small"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-success mb-0">₹<?php echo number_format($product['price'], 2); ?></span>
                            <span class="badge bg-success">In Stock</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="pages/product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-sm flex-fill">
                            <i class="fas fa-shopping-cart me-1"></i><?php echo __('add_to_cart'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="pages/products.php" class="btn btn-outline-success btn-lg">
                View All Products <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold text-success">Why Choose Us</h2>
            <p class="lead text-muted">We are committed to providing the best for our farmers</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-certificate fa-3x text-success"></i>
                    </div>
                    <h4 class="fw-bold"><?php echo __('quality_products'); ?></h4>
                    <p class="text-muted"><?php echo __('quality_desc'); ?></p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-truck fa-3x text-success"></i>
                    </div>
                    <h4 class="fw-bold"><?php echo __('fast_delivery'); ?></h4>
                    <p class="text-muted"><?php echo __('delivery_desc'); ?></p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-headset fa-3x text-success"></i>
                    </div>
                    <h4 class="fw-bold"><?php echo __('expert_support'); ?></h4>
                    <p class="text-muted"><?php echo __('support_desc'); ?></p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-tags fa-3x text-success"></i>
                    </div>
                    <h4 class="fw-bold"><?php echo __('best_prices'); ?></h4>
                    <p class="text-muted"><?php echo __('prices_desc'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Happy Farmers Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold text-success">Happy Farmers</h2>
            <p class="lead text-muted">Hear what our satisfied customers have to say</p>
        </div>
        
        <div class="swiper testimonial-swiper" data-aos="fade-up">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="testimonial-card text-center p-4 bg-white rounded shadow-sm">
                        <img src="assets/images/farmers/farmer1.jpg" class="rounded-circle mb-3" alt="Ram Singh" style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-3">"Adhunik Krushi Bhandar has transformed my farming practice. Their products are genuine and the results are amazing. My crop yield increased by 30%!"</p>
                        <h5 class="fw-bold">Ram Singh</h5>
                        <small class="text-muted">Organic Farmer, Maharashtra</small>
                    </div>
                </div>
                
                <div class="swiper-slide">
                    <div class="testimonial-card text-center p-4 bg-white rounded shadow-sm">
                        <img src="assets/images/farmers/farmer2.jpg" class="rounded-circle mb-3" alt="Anita Patel" style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-3">"Excellent service and quality products. The team is very knowledgeable and always ready to help. Highly recommend to all farmers!"</p>
                        <h5 class="fw-bold">Anita Patel</h5>
                        <small class="text-muted">Progressive Farmer, Gujarat</small>
                    </div>
                </div>
                
                <div class="swiper-slide">
                    <div class="testimonial-card text-center p-4 bg-white rounded shadow-sm">
                        <img src="assets/images/farmers/farmer3.jpg" class="rounded-circle mb-3" alt="Vijay Kumar" style="width: 80px; height: 80px; object-fit: cover;">
                        <div class="rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="mb-3">"Best agricultural products at reasonable prices. Fast delivery and excellent customer service. I've been a loyal customer for 3 years!"</p>
                        <h5 class="fw-bold">Vijay Kumar</h5>
                        <small class="text-muted">Commercial Farmer, Karnataka</small>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- 3-Stage Buying Strategy Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold text-success"><?php echo __('easy_3step'); ?></h2>
            <p class="lead text-muted"><?php echo __('hassle_free'); ?></p>
        </div>
        
        <div class="row align-items-center">
            <div class="col-md-4" data-aos="fade-right">
                <div class="buying-step text-center p-4">
                    <div class="step-icon mb-3 mx-auto" style="background: linear-gradient(135deg, #2e7d32, #66bb6a);">
                        <i class="fas fa-shopping-cart fa-2x text-white"></i>
                    </div>
                    <h4 class="fw-bold mb-3"><?php echo __('step1_title'); ?></h4>
                    <p class="text-muted"><?php echo __('step1_desc'); ?></p>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up">
                <div class="buying-step text-center p-4">
                    <div class="step-icon mb-3 mx-auto" style="background: linear-gradient(135deg, #f9a825, #fdd835);">
                        <i class="fas fa-clipboard-check fa-2x text-white"></i>
                    </div>
                    <h4 class="fw-bold mb-3"><?php echo __('step2_title'); ?></h4>
                    <p class="text-muted"><?php echo __('step2_desc'); ?></p>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-left">
                <div class="buying-step text-center p-4">
                    <div class="step-icon mb-3 mx-auto" style="background: linear-gradient(135deg, #f57c00, #ff9800);">
                        <i class="fas fa-credit-card fa-2x text-white"></i>
                    </div>
                    <h4 class="fw-bold mb-3"><?php echo __('step3_title'); ?></h4>
                    <p class="text-muted"><?php echo __('step3_desc'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="pages/products.php" class="btn btn-success btn-lg">
                <?php echo __('start_shopping'); ?> <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- About Us & R&D Section -->
<section id="about" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="assets/images/research-lab.jpg" class="img-fluid rounded shadow" alt="Research Laboratory">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="display-5 fw-bold text-success mb-4">About Us & R&D</h2>
                <p class="lead mb-4">Leading Innovation in Agriculture Since 2010</p>
                <p class="mb-3">At Adhunik Krushi Bhandar, we are committed to revolutionizing agriculture through continuous research and development. Our state-of-the-art R&D facility works tirelessly to bring you the most effective and sustainable farming solutions.</p>
                <p class="mb-4">We collaborate with agricultural scientists, farmers, and industry experts to develop products that not only increase yield but also promote sustainable farming practices.</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Advanced Research Laboratory</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Expert Agricultural Scientists</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Continuous Product Innovation</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Environmentally Friendly Solutions</li>
                </ul>
                <a href="#" class="btn btn-success btn-lg">
                    Learn More <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold text-success">Get in Touch</h2>
            <p class="lead text-muted">We're here to help you grow better</p>
        </div>
        
        <?php
        // Display success/error messages
        if (isset($_SESSION['contact_success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo '<i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($_SESSION['contact_success']);
            
            // Add WhatsApp notification button for admin
            if (isset($_SESSION['whatsapp_notification'])) {
                echo '<div class="mt-3">';
                echo '<a href="' . $_SESSION['whatsapp_notification'] . '" target="_blank" class="btn btn-success btn-sm">';
                echo '<i class="fab fa-whatsapp me-2"></i>Send WhatsApp Notification';
                echo '</a>';
                echo '</div>';
                unset($_SESSION['whatsapp_notification']);
            }
            
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
            unset($_SESSION['contact_success']);
        }
        
        if (isset($_SESSION['contact_error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo '<i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($_SESSION['contact_error']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
            unset($_SESSION['contact_error']);
        }
        ?>
        
        <div class="row">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <div class="card border-0 shadow">
                    <div class="card-body p-5">
                        <form action="contact_handler.php" method="POST" id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="9876543210" required>
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com">
                                </div>
                                <div class="col-12">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject" placeholder="How can we help you?">
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" placeholder="Tell us more about your query..." required></textarea>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
require_once 'includes/footer.php';
?>
