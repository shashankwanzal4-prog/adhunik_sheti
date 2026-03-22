<?php
/**
 * Footer Component for Adhunik Krushi Bhandar
 * Contains contact information, links, and social media
 */
?>
<!-- Footer -->
<footer class="bg-dark text-white pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-seedling text-success me-2 fs-3"></i>
                    <div>
                        <div class="fw-bold">Adhunik Krushi</div>
                        <small class="text-muted">Bhandar</small>
                    </div>
                </div>
                <p class="text-light">Your trusted partner for quality agricultural products. Empowering farmers with innovative solutions for sustainable farming.</p>
                <div class="social-links">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f fs-5"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter fs-5"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram fs-5"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-linkedin-in fs-5"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-whatsapp fs-5"></i></a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="mb-3 text-success">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="pages/products.php" class="text-white text-decoration-none">All Products</a></li>
                    <li class="mb-2"><a href="#about" class="text-white text-decoration-none">About Us</a></li>
                    <li class="mb-2"><a href="#contact" class="text-white text-decoration-none">Contact</a></li>
                    <li class="mb-2"><a href="pages/cart.php" class="text-white text-decoration-none">Cart</a></li>
                </ul>
            </div>
            
            <!-- Product Categories -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3 text-success">Categories</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="pages/products.php?category=Insecticides" class="text-white text-decoration-none">Insecticides</a></li>
                    <li class="mb-2"><a href="pages/products.php?category=Fungicides" class="text-white text-decoration-none">Fungicides</a></li>
                    <li class="mb-2"><a href="pages/products.php?category=PGR" class="text-white text-decoration-none">PGR</a></li>
                    <li class="mb-2"><a href="pages/products.php?category=Bio-pesticides" class="text-white text-decoration-none">Bio-pesticides</a></li>
                    <li class="mb-2"><a href="pages/products.php?category=Fertilizers" class="text-white text-decoration-none">Fertilizers</a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3 text-success">Contact Us</h5>
                <div class="contact-info">
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-success"></i>
                        123, Agricultural Market,<br>
                        Near Bus Stand, Pune - 411001
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-phone me-2 text-success"></i>
                        +91 9588676848
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-envelope me-2 text-success"></i>
                        info@adhunikkrushi.com
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-clock me-2 text-success"></i>
                        Mon-Sat: 9:00 AM - 6:00 PM
                    </p>
                </div>
            </div>
        </div>
        
        <hr class="bg-secondary my-4">
        
        <!-- Bottom Footer -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-light">&copy; <?php echo date('Y'); ?> Adhunik Krushi Bhandar. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-light">
                    Designed with <i class="fas fa-heart text-danger"></i> for Farmers
                </small>
            </div>
        </div>
    </div>
</footer>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/919588676848" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>

<!-- Back to Top Button -->
<button id="backToTop" class="back-to-top">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS Animation JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<!-- Custom JavaScript -->
<script src="assets/js/script.js"></script>

</body>
</html>
