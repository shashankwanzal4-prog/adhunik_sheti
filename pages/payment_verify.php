<?php
/**
 * Razorpay Payment Verification
 * Verifies payment signature and updates order status
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prevent caching to avoid form resubmission errors
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Enable output buffering
ob_start();

// Include configurations
require_once '../config/db.php';
require_once '../config/razorpay_config.php';
require_once '../config/payment_config.php';

// Start session
session_start();

// Get parameters
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$razorpay_payment_id = isset($_GET['razorpay_payment_id']) ? $_GET['razorpay_payment_id'] : '';
$razorpay_order_id = isset($_GET['razorpay_order_id']) ? $_GET['razorpay_order_id'] : '';
$razorpay_signature = isset($_GET['razorpay_signature']) ? $_GET['razorpay_signature'] : '';

$payment_success = false;
$error_message = '';

if ($order_id && $razorpay_payment_id && $razorpay_order_id && $razorpay_signature) {
    // Verify signature
    $generated_signature = hash_hmac('sha256', $razorpay_order_id . '|' . $razorpay_payment_id, RAZORPAY_KEY_SECRET);
    
    if (hash_equals($generated_signature, $razorpay_signature)) {
        // Signature verified - payment is successful
        $payment_success = true;
        
        // Update order status in database
        $payment_status = 'paid';
        $order_status = 'confirmed';
        
        $update_stmt = $db->prepare("UPDATE orders SET 
            payment_status = ?, 
            order_status = ?,
            razorpay_payment_id = ?,
            updated_at = NOW()
            WHERE id = ?");
        
        $update_stmt->bind_param("sssi", $payment_status, $order_status, $razorpay_payment_id, $order_id);
        
        if ($update_stmt->execute()) {
            // Store payment details for receipt
            $_SESSION['payment_success'] = true;
            $_SESSION['payment_id'] = $razorpay_payment_id;
            
            // Redirect to confirmation page
            header("Location: confirmation.php?order_id=$order_id&payment=success");
            ob_end_clean();
            exit;
        } else {
            $error_message = 'Failed to update order status. Please contact support.';
        }
    } else {
        $error_message = 'Payment verification failed. Signature mismatch.';
    }
} else {
    $error_message = 'Invalid payment response. Missing required parameters.';
}

// If verification failed, show error
require_once '../includes/header.php';
?>

<!-- Payment Verification Error -->
<section class="bg-danger text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="mb-0">Payment Failed</h1>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                        <h4 class="mb-3">Payment Verification Failed</h4>
                        <p class="text-muted"><?php echo htmlspecialchars($error_message); ?></p>
                        
                        <div class="mt-4">
                            <a href="payment_process.php?order_id=<?php echo $order_id; ?>" class="btn btn-success me-2">
                                <i class="fas fa-redo me-2"></i>Try Again
                            </a>
                            <a href="cart.php" class="btn btn-outline-secondary">
                                <i class="fas fa-shopping-cart me-2"></i>Back to Cart
                            </a>
                        </div>
                        
                        <div class="alert alert-info mt-4">
                            <h6 class="fw-bold">Need Help?</h6>
                            <p class="mb-1">If amount was deducted, it will be refunded within 5-7 business days.</p>
                            <p class="mb-0">Contact us: <?php echo PAYMENT_CONFIG['contact']['phone'] ?? '+91-9588676848'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>
