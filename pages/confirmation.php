<?php
/**
 * Order Confirmation Page - Step 3 of 3-Step Checkout Process
 * Displays order confirmation details
 */

// Enable output buffering
ob_start();

// Prevent caching to avoid form resubmission errors on back/forward
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Include database configuration
require_once '../config/db.php';

// Start session
session_start();

// Get order details
$order_id = $_GET['order_id'] ?? 0;
$order = null;
$order_items = [];

if ($order_id > 0) {
    // Get order details
    $result = $db->query("SELECT * FROM orders WHERE id = $order_id");
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
    }
}
    
    // Get order items
    if ($order) {
        $items_result = $db->query("SELECT * FROM order_items WHERE order_id = $order_id");
        if ($items_result && $items_result->num_rows > 0) {
            while ($row = $items_result->fetch_assoc()) {
                $order_items[] = $row;
            }
        }
    }

// If order not found, redirect to home
if (!$order) {
    header('Location: ../index.php');
    ob_end_clean();
    exit;
}

// Include header after all PHP processing
require_once '../includes/header.php';
?>

<!-- Confirmation Page Header -->
<section class="bg-success text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col text-center">
                <div class="mb-3">
                    <i class="fas fa-check-circle fa-5x"></i>
                </div>
                <h1 class="mb-2">Order Confirmed!</h1>
                <p class="lead mb-0">Thank you for your purchase. Your order has been successfully placed.</p>
                
                <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success' && $order['payment_status'] === 'paid'): ?>
                    <div class="alert alert-light mt-3 mx-auto" style="max-width: 500px;">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        <strong>Payment Successful!</strong><br>
                        <small>Payment ID: <?php echo htmlspecialchars($order['razorpay_payment_id'] ?? 'N/A'); ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Progress Steps -->
<section class="bg-light py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="step-item completed">
                    <div class="step-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="step-text">
                        <small>Select Product</small>
                        <div>Completed</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-item completed">
                    <div class="step-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="step-text">
                        <small>Review & Personalize</small>
                        <div>Completed</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-item completed">
                    <div class="step-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="step-text">
                        <small>Pay & Confirm</small>
                        <div>Completed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Order Details -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Order Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Order Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Order Number:</strong><br>
                                <span class="text-success"><?php echo htmlspecialchars($order['order_number']); ?></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Order Date:</strong><br>
                                <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Customer Name:</strong><br>
                                <?php echo htmlspecialchars($order['customer_name']); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Phone Number:</strong><br>
                                <?php echo htmlspecialchars($order['customer_phone']); ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($order['customer_email'])): ?>
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Email Address:</strong><br>
                                <?php echo htmlspecialchars($order['customer_email']); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Delivery Address:</strong><br>
                                <?php echo nl2br(htmlspecialchars($order['customer_address'])); ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Payment Method:</strong><br>
                                <?php 
                                $payment_methods = [
                                    'cash_on_delivery' => 'Cash on Delivery',
                                    'bank_transfer' => 'Bank Transfer',
                                    'upi' => 'UPI Payment'
                                ];
                                echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                                ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Payment Status:</strong><br>
                                <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($order['notes'])): ?>
                        <div class="row">
                            <div class="col-12">
                                <strong>Additional Notes:</strong><br>
                                <?php echo nl2br(htmlspecialchars($order['notes'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order['payment_method'] === 'bank_transfer' && $order['payment_status'] !== 'paid'): ?>
                        <div class="alert alert-info mt-3">
                            <h6 class="fw-bold"><i class="fas fa-university me-2"></i>Bank Transfer Details</h6>
                            <p class="mb-2">Please complete your payment using these details:</p>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td><strong>Bank Name:</strong></td><td>State Bank of India</td></tr>
                                <tr><td><strong>Account Name:</strong></td><td>Adhunik Krushi Bhandar</td></tr>
                                <tr><td><strong>Account Number:</strong></td><td>12345678901</td></tr>
                                <tr><td><strong>IFSC Code:</strong></td><td>SBIN0001234</td></tr>
                                <tr><td><strong>Amount:</strong></td><td class="text-success fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td></tr>
                            </table>
                            <p class="small text-muted mt-2 mb-0">After payment, contact us with your UTR number for confirmation.</p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order['payment_method'] === 'upi' && $order['payment_status'] !== 'paid'): ?>
                        <div class="alert alert-info mt-3">
                            <h6 class="fw-bold"><i class="fas fa-mobile-alt me-2"></i>UPI Payment Details</h6>
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center">
                                    <div class="bg-white p-2 rounded border" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                        <div class="text-center">
                                            <i class="fas fa-qrcode fa-4x text-success mb-2"></i>
                                            <p class="small text-muted mb-0">Scan to Pay</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <p class="mb-1"><strong>UPI ID:</strong></p>
                                    <p class="h5 text-success mb-2">adhunik.krushi@upi</p>
                                    <p class="mb-1"><strong>Amount:</strong> <span class="text-success fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></span></p>
                                    <p class="mb-1"><strong>Order #:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
                                    <p class="small text-muted">After payment, share your UTR/Transaction ID with us.</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($order_items as $item): ?>
                        <div class="cart-item row align-items-center py-2 border-bottom">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                <small class="text-muted">Product ID: #<?php echo $item['product_id']; ?></small>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted">Qty: <?php echo $item['quantity']; ?></span>
                            </div>
                            <div class="col-md-2">
                                <span>₹<?php echo number_format($item['price'], 2); ?></span>
                            </div>
                            <div class="col-md-2 text-end">
                                <span class="fw-bold">₹<?php echo number_format($item['subtotal'], 2); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="mt-3 pt-3 border-top">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="text-success">Total Amount:</h5>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h5 class="text-success">₹<?php echo number_format($order['total_amount'], 2); ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Status & Actions -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Order Status</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-clock fa-3x text-warning"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Processing</h6>
                        <p class="text-muted small">Your order is being processed and will be shipped soon.</p>
                        
                        <div class="mt-3">
                            <span class="badge bg-warning fs-6"><?php echo ucfirst($order['status']); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Estimated Delivery -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-truck me-2 text-success"></i>Estimated Delivery
                        </h6>
                        <p class="mb-2"><strong>3-5 Business Days</strong></p>
                        <p class="text-muted small mb-0">You will receive a confirmation call before delivery.</p>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-phone me-2 text-success"></i>Need Help?
                        </h6>
                        <p class="mb-2"><strong>Customer Support:</strong></p>
                        <p class="mb-2">+91 98765 43210</p>
                        <p class="mb-0">info@adhunikkrushi.com</p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <button onclick="window.print()" class="btn btn-outline-success">
                        <i class="fas fa-print me-2"></i>Print Order
                    </button>
                    <a href="../index.php" class="btn btn-success">
                        <i class="fas fa-home me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Important Information -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>Important Information
                    </h6>
                    <ul class="mb-0">
                        <li>You will receive an order confirmation on your registered phone number.</li>
                        <li>Our delivery team will contact you before dispatching your order.</li>
                        <li>Please ensure someone is available at the delivery address during business hours.</li>
                        <li>For Cash on Delivery orders, please keep the exact amount ready.</li>
                        <li>For any queries, please contact our customer support team.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
require_once '../includes/footer.php';
?>
