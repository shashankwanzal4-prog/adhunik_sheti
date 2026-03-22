<?php
/**
 * Razorpay Payment Gateway Configuration
 * Get your API keys from: https://dashboard.razorpay.com/
 */

$razorpay_config = [
    // Test Mode (Use these for testing)
    'test' => [
        'key_id' => 'rzp_test_YOUR_KEY_ID_HERE',      // ← Replace with your Test Key ID
        'key_secret' => 'YOUR_TEST_KEY_SECRET_HERE',   // ← Replace with your Test Key Secret
    ],
    
    // Live Mode (Use these for production)
    'live' => [
        'key_id' => 'rzp_live_YOUR_KEY_ID_HERE',       // ← Replace with your Live Key ID
        'key_secret' => 'YOUR_LIVE_KEY_SECRET_HERE',   // ← Replace with your Live Key Secret
    ],
    
    // Set to 'live' for production, 'test' for testing
    'mode' => 'test',
    
    // Company details for receipts
    'company' => [
        'name' => 'Adhunik Krushi Bhandar',
        'description' => 'Agricultural Products Store',
        'logo' => 'https://yourdomain.com/assets/images/logo.png', // Your logo URL
    ]
];

// Get current mode settings
$current_mode = $razorpay_config['mode'];
define('RAZORPAY_KEY_ID', $razorpay_config[$current_mode]['key_id']);
define('RAZORPAY_KEY_SECRET', $razorpay_config[$current_mode]['key_secret']);
define('RAZORPAY_MODE', $current_mode);
define('RAZORPAY_COMPANY', $razorpay_config['company']);

/**
 * How to get Razorpay API Keys:
 * 
 * 1. Sign up at https://dashboard.razorpay.com/
 * 2. Complete your KYC verification
 * 3. Go to Settings → API Keys
 * 4. Generate new keys (Test and Live)
 * 5. Copy Key ID and Key Secret
 * 6. Paste them above
 * 
 * For testing, use Test Mode keys (no real money)
 * For production, switch mode to 'live' and use Live keys
 */
?>
