<?php
/**
 * Payment Configuration File
 * Update your payment details here
 */

// Bank Transfer Details
$payment_config = [
    'bank_transfer' => [
        'bank_name' => 'State Bank of India',
        'account_name' => 'Adhunik Krushi Bhandar',
        'account_number' => '12345678901',
        'ifsc_code' => 'SBIN0001234',
        'branch' => 'Main Branch, Pune'
    ],
    
    'upi' => [
        'upi_id' => '9881398919@ybl',
        'qr_code_path' => 'assets/images/payment/upi-qr.png', // Upload your QR code image here
        'display_name' => 'Shashank Wanzal'
    ],
    
    // Contact for payment confirmation
    'contact' => [
        'phone' => '+91-9588676848',
        'whatsapp' => '+91-9588676848',
        'email' => 'shashankwanzal4@gmail.com'
    ]
];

// Function to get payment details
define('PAYMENT_CONFIG', $payment_config);
?>
