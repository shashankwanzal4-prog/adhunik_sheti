<?php
/**
 * Header Component for Adhunik Krushi Bhandar
 * Contains navigation, cart, and common header elements
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prevent caching to avoid form resubmission errors on back/forward
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/Adhunik_Sheti/config/db.php';
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Include language configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/Adhunik_Sheti/config/language.php';

// Define base path for navigation links
$base_path = '/Adhunik_Sheti/';

// Get current language for display
$current_lang = $lang->getCurrentLanguage();
$current_lang_name = $lang->getLanguageName();

// Helper function to build language switch URL preserving existing parameters
function getLanguageUrl($lang_code) {
    $params = $_GET;
    $params['lang'] = $lang_code;
    return '?' . http_build_query($params);
}

// Get cart item count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adhunik Krushi Bhandar - Empowering Farmers with Quality Agro Products</title>
    <meta name="description" content="Adhunik Krushi Bhandar - Your trusted partner for quality agricultural products, insecticides, fungicides, and farming solutions.">
    <meta name="keywords" content="agriculture, farming, insecticide, fungicide, PGR, organic farming, krushi bhandar">
    
    <!-- Google Fonts Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar bg-success text-white py-2">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <small><i class="fas fa-phone me-2"></i> +91 98765 43210</small>
                    <small class="ms-3"><i class="fas fa-envelope me-2"></i> info@adhunikkrushi.com</small>
                </div>
                <div class="col-md-6 text-end">
                    <small><i class="fas fa-clock me-2"></i> Mon-Sat: 9AM-6PM</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $base_path; ?>index.php">
                <img src="<?php echo $base_path; ?>assets/images/logo.png" alt="Adhunik Krushi Bhandar" height="70">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $base_path; ?>index.php"><?php echo __('home'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>pages/products.php"><?php echo __('products'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>index.php#about"><?php echo __('about_us'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>index.php#contact"><?php echo __('contact'); ?></a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <!-- Language Selector -->
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe me-1"></i><?php echo $current_lang == 'en' ? 'English' : 'मराठी'; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                            <li><a class="dropdown-item <?php echo $current_lang == 'en' ? 'active' : ''; ?>" href="<?php echo getLanguageUrl('en'); ?>">English</a></li>
                            <li><a class="dropdown-item <?php echo $current_lang == 'mr' ? 'active' : ''; ?>" href="<?php echo getLanguageUrl('mr'); ?>">मराठी (Marathi)</a></li>
                        </ul>
                    </div>
                    
                    <a href="<?php echo $base_path; ?>pages/cart.php" class="btn btn-outline-success position-relative me-3">
                        <i class="fas fa-shopping-cart"></i>
                        <?php echo __('cart'); ?>
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo $base_path; ?>admin/" class="btn btn-success">
                        <i class="fas fa-user me-2"></i><?php echo __('admin'); ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>
