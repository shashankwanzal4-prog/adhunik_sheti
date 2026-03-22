<?php
/**
 * Language Configuration for Adhunik Krushi Bhandar
 * Supports English and Marathi (मराठी)
 */

class Language {
    private $current_lang = 'en';
    private $translations = [];
    
    public function __construct() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check for language switch request
        if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'mr'])) {
            $this->setLanguage($_GET['lang']);
        }
        
        // Get saved language or default to English
        $this->current_lang = $_SESSION['lang'] ?? 'en';
        
        // Load translations
        $this->loadTranslations();
    }
    
    public function setLanguage($lang) {
        if (in_array($lang, ['en', 'mr'])) {
            $this->current_lang = $lang;
            $_SESSION['lang'] = $lang;
            
            // Set cookie for 30 days
            setcookie('lang', $lang, time() + (30 * 24 * 60 * 60), '/');
        }
    }
    
    public function getCurrentLanguage() {
        return $this->current_lang;
    }
    
    public function translate($key) {
        return $this->translations[$this->current_lang][$key] ?? $key;
    }
    
    public function getLanguageName($lang = null) {
        $lang = $lang ?? $this->current_lang;
        $names = [
            'en' => 'English',
            'mr' => 'मराठी (Marathi)'
        ];
        return $names[$lang] ?? 'English';
    }
    
    private function loadTranslations() {
        $this->translations = [
            'en' => [
                // Navigation
                'home' => 'Home',
                'products' => 'Products',
                'about_us' => 'About Us',
                'contact' => 'Contact',
                'cart' => 'Cart',
                'admin' => 'Admin',
                
                // Hero Section
                'hero_title' => 'Adhunik Krushi Bhandar',
                'hero_subtitle' => 'Your One-Stop Solution for Quality Agricultural Products',
                'hero_description' => 'Premium quality seeds, fertilizers, pesticides, and farming equipment delivered to your doorstep. Empowering farmers with modern agricultural solutions.',
                'shop_now' => 'Shop Now',
                'explore_products' => 'Explore Products',
                
                // Features
                'quality_products' => 'Quality Products',
                'quality_desc' => 'Certified premium agricultural products from trusted brands',
                'fast_delivery' => 'Fast Delivery',
                'delivery_desc' => 'Quick and reliable delivery to your farm',
                'expert_support' => 'Expert Support',
                'support_desc' => 'Guidance from agricultural experts',
                'best_prices' => 'Best Prices',
                'prices_desc' => 'Competitive prices with seasonal discounts',
                
                // Products Section
                'featured_products' => 'Featured Products',
                'featured_desc' => 'Premium quality agricultural products for better yield',
                'view_details' => 'View Details',
                'add_to_cart' => 'Add to Cart',
                'in_stock' => 'In Stock',
                'out_of_stock' => 'Out of Stock',
                
                // 3-Step Process
                'easy_3step' => 'Easy 3-Step Buying Process',
                'hassle_free' => 'Simple, secure, and hassle-free shopping experience',
                'step1_title' => 'Step 1: Select Product',
                'step1_desc' => 'Browse our wide range of quality agricultural products and choose what you need',
                'step2_title' => 'Step 2: Review & Personalize',
                'step2_desc' => 'Review your cart, update quantities, and provide delivery details',
                'step3_title' => 'Step 3: Pay & Confirm',
                'step3_desc' => 'Secure payment processing and instant order confirmation',
                'start_shopping' => 'Start Shopping Now',
                
                // About Section
                'about_title' => 'About Us',
                'about_subtitle' => 'Leading Agricultural Solutions Provider',
                'about_desc' => 'Adhunik Krushi Bhandar has been serving farmers for over a decade. We provide high-quality agricultural products, expert guidance, and innovative farming solutions to help you achieve better yields.',
                'our_mission' => 'Our Mission',
                'mission_desc' => 'To empower farmers with quality products and modern agricultural knowledge',
                'our_vision' => 'Our Vision',
                'vision_desc' => 'To be the most trusted agricultural partner for farmers across the region',
                'years_experience' => 'Years Experience',
                'happy_farmers' => 'Happy Farmers',
                'products_available' => 'Products Available',
                'delivery_locations' => 'Delivery Locations',
                
                // Contact Section
                'contact_title' => 'Get in Touch',
                'contact_subtitle' => 'We are here to help you grow better',
                'send_message' => 'Send Message',
                'your_name' => 'Your Name',
                'phone_number' => 'Phone Number',
                'email_address' => 'Email Address',
                'your_message' => 'Your Message',
                'call_us' => 'Call Us',
                'visit_us' => 'Visit Us',
                'business_hours' => 'Business Hours',
                
                // Footer
                'quick_links' => 'Quick Links',
                'customer_service' => 'Customer Service',
                'connect_with_us' => 'Connect With Us',
                'all_rights_reserved' => 'All rights reserved',
                
                // Cart
                'shopping_cart' => 'Shopping Cart',
                'cart_empty' => 'Your cart is empty',
                'continue_shopping' => 'Continue Shopping',
                'product' => 'Product',
                'price' => 'Price',
                'quantity' => 'Quantity',
                'total' => 'Total',
                'subtotal' => 'Subtotal',
                'tax' => 'Tax (18%)',
                'delivery' => 'Delivery',
                'grand_total' => 'Grand Total',
                'proceed_checkout' => 'Proceed to Checkout',
                'remove' => 'Remove',
                'update' => 'Update',
                
                // Checkout
                'checkout' => 'Checkout',
                'billing_details' => 'Billing Details',
                'full_name' => 'Full Name',
                'address' => 'Address',
                'payment_method' => 'Payment Method',
                'cash_on_delivery' => 'Cash on Delivery',
                'order_notes' => 'Order Notes (Optional)',
                'place_order' => 'Place Order',
                'order_summary' => 'Order Summary',
                
                // Order Confirmation
                'thank_you' => 'Thank You!',
                'order_placed' => 'Your order has been placed successfully',
                'order_number' => 'Order Number',
                'order_date' => 'Order Date',
                'customer_details' => 'Customer Details',
                'order_items' => 'Order Items',
                'print_receipt' => 'Print Receipt',
                'continue_shopping' => 'Continue Shopping',
                
                // Products Page
                'all_products' => 'All Products',
                'filter_by_category' => 'Filter by Category',
                'search_products' => 'Search products...',
                'search' => 'Search',
                'no_products_found' => 'No products found',
                
                // Admin
                'dashboard' => 'Dashboard',
                'orders_management' => 'Orders Management',
                'products_management' => 'Products Management',
                'add_product' => 'Add Product',
                'edit_product' => 'Edit Product',
                'delete_product' => 'Delete Product',
                'logout' => 'Logout',
                'login' => 'Login',
                'username' => 'Username',
                'password' => 'Password',
                'remember_me' => 'Remember Me',
                'forgot_password' => 'Forgot Password?',
                
                // Language
                'select_language' => 'Select Language',
                'language' => 'Language'
            ],
            
            'mr' => [
                // Navigation
                'home' => 'मुख्यपृष्ठ',
                'products' => 'उत्पादने',
                'about_us' => 'आमच्याबद्दल',
                'contact' => 'संपर्क',
                'cart' => 'कार्ट',
                'admin' => 'प्रशासक',
                
                // Hero Section
                'hero_title' => 'आधुनिक कृषी भांडार',
                'hero_subtitle' => 'गुणवत्तापूर्ण कृषी उत्पादनांसाठी एकच ठिकाण',
                'hero_description' => 'दारावर पोहोचवलेली प्रीमियम दर्जाची बियाणे, खते, कीटकनाशके आणि शेती साधने. शेतकऱ्यांना आधुनिक शेती उपायांनी सशक्त बनवणे.',
                'shop_now' => 'आता खरेदी करा',
                'explore_products' => 'उत्पादने पहा',
                
                // Features
                'quality_products' => 'गुणवत्तापूर्ण उत्पादने',
                'quality_desc' => 'विश्वसनीय ब्रांड्सकडून प्रमाणित प्रीमियम कृषी उत्पादने',
                'fast_delivery' => 'जलद डिलिव्हरी',
                'delivery_desc' => 'आपल्या शेतात जलद आणि विश्वसनीय डिलिव्हरी',
                'expert_support' => 'तज्ञ सहाय्य',
                'support_desc' => 'कृषी तज्ञांकडून मार्गदर्शन',
                'best_prices' => 'सर्वोत्तम किंमती',
                'prices_desc' => 'हंगामी सवलतींसह स्पर्धात्मक किंमती',
                
                // Products Section
                'featured_products' => 'वैशिष्ट्यपूर्ण उत्पादने',
                'featured_desc' => 'अधिक उत्पादनासाठी प्रीमियम गुणवत्तेची कृषी उत्पादने',
                'view_details' => 'तपशील पहा',
                'add_to_cart' => 'कार्टमध्ये जोडा',
                'in_stock' => 'स्टॉकमध्ये आहे',
                'out_of_stock' => 'स्टॉक संपले',
                
                // 3-Step Process
                'easy_3step' => 'सोपी 3-पायरी खरेदी प्रक्रिया',
                'hassle_free' => 'साधी, सुरक्षित आणि त्रासहीन खरेदी अनुभव',
                'step1_title' => 'पायरी 1: उत्पादन निवडा',
                'step1_desc' => 'आमच्या गुणवत्तापूर्ण कृषी उत्पादनांची विस्तृत श्रेणी ब्राउझर करा आणि आपल्याला आवश्यक असलेले निवडा',
                'step2_title' => 'पायरी 2: पुनरावलोकन आणि वैयक्तिकृत करा',
                'step2_desc' => 'आपली कार्ट पुनरावलोकन करा, प्रमाण अद्यतनित करा आणि डिलिव्हरी तपशील प्रदान करा',
                'step3_title' => 'पायरी 3: पेमेंट आणि पुष्टी',
                'step3_desc' => 'सुरक्षित पेमेंट प्रक्रिया आणि तात्काळ ऑर्डर पुष्टी',
                'start_shopping' => 'आता खरेदी सुरू करा',
                
                // About Section
                'about_title' => 'आमच्याबद्दल',
                'about_subtitle' => 'अग्रगण्य कृषी उपाय प्रदाता',
                'about_desc' => 'आधुनिक कृषी भांडार दोन दशकांहून अधिक काळ शेतकऱ्यांना सेवा देत आहे. आम्ही उच्च-गुणवत्तेची कृषी उत्पादने, तज्ञ मार्गदर्शन आणि अभिनव शेती उपाय प्रदान करतो जेणेकरून आपण अधिक उत्पादन मिळवू शकता.',
                'our_mission' => 'आमचे ध्येय',
                'mission_desc' => 'गुणवत्तापूर्ण उत्पादनांनी आणि आधुनिक कृषी ज्ञानाने शेतकऱ्यांना सशक्त बनवणे',
                'our_vision' => 'आमचे दृष्टीकोन',
                'vision_desc' => 'प्रदेशातील शेतकऱ्यांसाठी सर्वात विश्वसनीय कृषी भागीदार बनणे',
                'years_experience' => 'वर्षे अनुभव',
                'happy_farmers' => 'समाधानी शेतकरी',
                'products_available' => 'उपलब्ध उत्पादने',
                'delivery_locations' => 'डिलिव्हरी स्थाने',
                
                // Contact Section
                'contact_title' => 'संपर्क साधा',
                'contact_subtitle' => 'आम्ही आपल्याला अधिक चांगले उत्पादन घेण्यास मदत करण्यासाठी येथे आहोत',
                'send_message' => 'संदेश पाठवा',
                'your_name' => 'आपले नाव',
                'phone_number' => 'फोन नंबर',
                'email_address' => 'ईमेल पत्ता',
                'your_message' => 'आपला संदेश',
                'call_us' => 'आम्हाला कॉल करा',
                'visit_us' => 'आमच्या भेट द्या',
                'business_hours' => 'व्यवसायाची वेळ',
                
                // Footer
                'quick_links' => 'द्रुत दुवे',
                'customer_service' => 'ग्राहक सेवा',
                'connect_with_us' => 'आमच्याशी जोडा',
                'all_rights_reserved' => 'सर्व हक्क राखीव',
                
                // Cart
                'shopping_cart' => 'शॉपिंग कार्ट',
                'cart_empty' => 'आपली कार्ट रिकामी आहे',
                'continue_shopping' => 'खरेदी सुरू ठेवा',
                'product' => 'उत्पादन',
                'price' => 'किंमत',
                'quantity' => 'प्रमाण',
                'total' => 'एकूण',
                'subtotal' => 'उपएकूण',
                'tax' => 'कर (18%)',
                'delivery' => 'डिलिव्हरी',
                'grand_total' => 'एकूण रक्कम',
                'proceed_checkout' => 'चेकआउट करा',
                'remove' => 'काढून टाका',
                'update' => 'अद्यतनित करा',
                
                // Checkout
                'checkout' => 'चेकआउट',
                'billing_details' => 'बिलिंग तपशील',
                'full_name' => 'पूर्ण नाव',
                'address' => 'पत्ता',
                'payment_method' => 'पेमेंट पद्धत',
                'cash_on_delivery' => 'डिलिव्हरीवर रोख',
                'order_notes' => 'ऑर्डर नोट्स (पर्यायी)',
                'place_order' => 'ऑर्डर द्या',
                'order_summary' => 'ऑर्डर सारांश',
                
                // Order Confirmation
                'thank_you' => 'धन्यवाद!',
                'order_placed' => 'आपली ऑर्डर यशस्वीरित्या देण्यात आली आहे',
                'order_number' => 'ऑर्डर नंबर',
                'order_date' => 'ऑर्डर दिनांक',
                'customer_details' => 'ग्राहक तपशील',
                'order_items' => 'ऑर्डर आयटम्स',
                'print_receipt' => 'पावती प्रिंट करा',
                
                // Products Page
                'all_products' => 'सर्व उत्पादने',
                'filter_by_category' => 'वर्गानुसार फिल्टर करा',
                'search_products' => 'उत्पादने शोधा...',
                'search' => 'शोधा',
                'no_products_found' => 'कोणतीही उत्पादने सापडली नाहीत',
                
                // Admin
                'dashboard' => 'डॅशबोर्ड',
                'orders_management' => 'ऑर्डर व्यवस्थापन',
                'products_management' => 'उत्पादने व्यवस्थापन',
                'add_product' => 'उत्पादन जोडा',
                'edit_product' => 'उत्पादन संपादित करा',
                'delete_product' => 'उत्पादन हटवा',
                'logout' => 'बाहेर पडा',
                'login' => 'लॉग इन',
                'username' => 'वापरकर्तानाव',
                'password' => 'पासवर्ड',
                'remember_me' => 'मला लक्षात ठेवा',
                'forgot_password' => 'पासवर्ड विसरलात?',
                
                // Language
                'select_language' => 'भाषा निवडा',
                'language' => 'भाषा'
            ]
        ];
    }
}

// Initialize global language object
$lang = new Language();

// Helper function for translations
function __($key) {
    global $lang;
    return $lang->translate($key);
}
?>
