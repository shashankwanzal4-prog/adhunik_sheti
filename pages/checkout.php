<?php
/**
 * Checkout Page - Step 2 of 3-Step Checkout Process
 * Collects customer information and order details
 */

// Enable output buffering to prevent headers already sent error
ob_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prevent caching to avoid form resubmission errors on back/forward
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Start session and include database first (before any output)
require_once '../config/db.php';
session_start();

// Check if cart is empty - redirect BEFORE any output
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    ob_end_clean();
    exit;
}

// Get cart items and calculate total
$cart_items = [];
$total_amount = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $ids_string = implode(',', $product_ids);
    
    $result = $db->query("SELECT * FROM products WHERE id IN ($ids_string) AND status = 'active'");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $quantity = $_SESSION['cart'][$row['id']];
            $subtotal = $row['price'] * $quantity;
            $total_amount += $subtotal;
            
            $cart_items[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'image' => $row['image'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_address = trim($_POST['customer_address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    $notes = trim($_POST['notes'] ?? '');
    
    // Basic validation
    $errors = [];
    if (empty($customer_name)) $errors[] = 'Name is required';
    if (empty($customer_phone)) $errors[] = 'Phone number is required';
    if (empty($customer_address)) $errors[] = 'Address is required';
    if (empty($payment_method)) $errors[] = 'Payment method is required';
    
    if (empty($errors)) {
        // Generate order number
        $order_number = 'AKB' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Insert order into database
        $order_sql = "INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, customer_address, total_amount, payment_method, notes) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($order_sql);
        $stmt->bind_param("sssssdss", $order_number, $customer_name, $customer_email, $customer_phone, $customer_address, $total_amount, $payment_method, $notes);
        
        if ($stmt->execute()) {
            $order_id = $db->insert_id();
            
            // Insert order items and reduce stock
            foreach ($cart_items as $item) {
                // Insert order item
                $item_sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal) 
                             VALUES (?, ?, ?, ?, ?, ?)";
                $item_stmt = $db->prepare($item_sql);
                $item_stmt->bind_param("isiddd", $order_id, $item['id'], $item['name'], $item['quantity'], $item['price'], $item['subtotal']);
                $item_stmt->execute();
                
                // Reduce product stock
                $update_stock_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
                $stock_stmt = $db->prepare($update_stock_sql);
                $stock_stmt->bind_param("isi", $item['quantity'], $item['id'], $item['quantity']);
                $stock_stmt->execute();
            }
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            // Redirect based on payment method
            if ($payment_method === 'online') {
                // Redirect to Razorpay payment page
                header("Location: payment_process.php?order_id=$order_id");
            } else {
                // Redirect to confirmation page for COD/Bank Transfer/UPI
                header("Location: confirmation.php?order_id=$order_id");
            }
            ob_end_clean();
            exit;
        } else {
            $errors[] = 'Failed to place order. Please try again.';
        }
    }
}

// Include payment configuration
require_once '../config/payment_config.php';

// Include header (after all PHP processing and redirects)
require_once '../includes/header.php';
?>

<!-- Checkout Page Header -->
<section class="bg-success text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="mb-0">Checkout</h1>
                <p class="mb-0">Step 2 of 3: Review & Personalize</p>
            </div>
            <div class="col-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item"><a href="cart.php" class="text-white">Cart</a></li>
                        <li class="breadcrumb-item active text-white">Checkout</li>
                    </ol>
                </nav>
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
                <div class="step-item active">
                    <div class="step-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="step-text">
                        <small>Review & Personalize</small>
                        <div>Current Step</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-item">
                    <div class="step-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="step-text">
                        <small>Pay & Confirm</small>
                        <div>Next</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Checkout Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Order Details -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Order Details</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item row align-items-center py-2 border-bottom">
                            <div class="col-md-2">
                                <img src="../assets/images/products/<?php echo $item['image'] ?: 'product-placeholder.jpg'; ?>" 
                                     class="img-fluid rounded" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="fw-bold">₹<?php echo number_format($item['subtotal'], 2); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="mt-3 text-end">
                            <h5 class="text-success">Total: ₹<?php echo number_format($total_amount, 2); ?></h5>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Information Form -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="customer_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                           value="<?php echo htmlspecialchars($_POST['customer_name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="customer_phone" class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                           value="<?php echo htmlspecialchars($_POST['customer_phone'] ?? ''); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="customer_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                           value="<?php echo htmlspecialchars($_POST['customer_email'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label for="customer_address" class="form-label">Delivery Address *</label>
                                    <textarea class="form-control" id="customer_address" name="customer_address" rows="3" required><?php echo htmlspecialchars($_POST['customer_address'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="payment_method" class="form-label">Payment Method *</label>
                                    <select class="form-select" id="payment_method" name="payment_method" required onchange="showPaymentInstructions()">
                                        <option value="">Select Payment Method</option>
                                        <option value="online" <?php echo ($_POST['payment_method'] ?? '') === 'online' ? 'selected' : ''; ?>>💳 Online Payment (Card/UPI/NetBanking)</option>
                                        <option value="cash_on_delivery" <?php echo ($_POST['payment_method'] ?? '') === 'cash_on_delivery' ? 'selected' : ''; ?>>💵 Cash on Delivery</option>
                                        <option value="bank_transfer" <?php echo ($_POST['payment_method'] ?? '') === 'bank_transfer' ? 'selected' : ''; ?>>🏦 Bank Transfer</option>
                                        <option value="upi" <?php echo ($_POST['payment_method'] ?? '') === 'upi' ? 'selected' : ''; ?>>📱 UPI Payment (Manual)</option>
                                    </select>
                                    <small class="text-muted">Online payment is secure and instant</small>
                                </div>
                                
                                <!-- Payment Instructions -->
                                <div class="col-12" id="payment_instructions" style="display: none;">
                                    <div class="alert alert-info">
                                        <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>Payment Instructions</h6>
                                        
                                        <!-- Bank Transfer Instructions -->
                                        <div id="bank_instructions" style="display: none;">
                                            <p class="mb-2">Please transfer the amount to the following bank account:</p>
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr><td><strong>Bank Name:</strong></td><td><?php echo PAYMENT_CONFIG['bank_transfer']['bank_name']; ?></td></tr>
                                                <tr><td><strong>Account Name:</strong></td><td><?php echo PAYMENT_CONFIG['bank_transfer']['account_name']; ?></td></tr>
                                                <tr><td><strong>Account Number:</strong></td><td><?php echo PAYMENT_CONFIG['bank_transfer']['account_number']; ?></td></tr>
                                                <tr><td><strong>IFSC Code:</strong></td><td><?php echo PAYMENT_CONFIG['bank_transfer']['ifsc_code']; ?></td></tr>
                                                <tr><td><strong>Branch:</strong></td><td><?php echo PAYMENT_CONFIG['bank_transfer']['branch']; ?></td></tr>
                                            </table>
                                            <p class="small text-muted mt-2 mb-0">After payment, please share the screenshot/UTR number in the notes below or contact us at <?php echo PAYMENT_CONFIG['contact']['phone']; ?></p>
                                        </div>
                                        
                                        <!-- UPI Instructions -->
                                        <div id="upi_instructions" style="display: none;">
                                            <p class="mb-2">Scan QR code or use UPI ID to pay:</p>
                                            <div class="row align-items-center">
                                                <div class="col-md-4 text-center">
                                                    <div class="bg-white p-2 rounded border" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center;">
                                                        <?php if (file_exists(PAYMENT_CONFIG['upi']['qr_code_path'])): ?>
                                                            <img src="<?php echo $base_path . PAYMENT_CONFIG['upi']['qr_code_path']; ?>" alt="UPI QR Code" style="max-width: 100%; max-height: 100%;">
                                                        <?php else: ?>
                                                            <div class="text-center">
                                                                <i class="fas fa-qrcode fa-4x text-success mb-2"></i>
                                                                <p class="small text-muted mb-0">UPI QR</p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <p class="mb-1"><strong>UPI ID:</strong></p>
                                                    <p class="h5 text-success mb-2"><?php echo PAYMENT_CONFIG['upi']['upi_id']; ?></p>
                                                    <p class="mb-1"><strong>Amount:</strong> ₹<?php echo number_format($total_amount, 2); ?></p>
                                                    <p class="small text-muted">After payment, please share the UTR/Transaction ID in the notes below.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <label for="notes" class="form-label">Additional Notes / Payment Reference (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="For UPI/Bank transfer: Enter transaction ID or UTR number"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="cart.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Back to Cart
                                        </a>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-check me-2"></i>Place Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₹<?php echo number_format($total_amount / 1.18, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (18%):</span>
                            <span>₹<?php echo number_format($total_amount - ($total_amount / 1.18), 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery:</span>
                            <span class="text-success">FREE</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">Total:</h5>
                            <h5 class="mb-0 text-success">₹<?php echo number_format($total_amount, 2); ?></h5>
                        </div>
                        
                        <!-- Delivery Info -->
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Estimated Delivery:</strong> 3-5 business days
                        </div>
                        
                        <!-- Security Badge -->
                        <div class="text-center">
                            <div class="mb-2">
                                <i class="fas fa-shield-alt fa-2x text-success"></i>
                            </div>
                            <small class="text-muted">
                                Secure Checkout<br>
                                Your information is protected
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function showPaymentInstructions() {
    var paymentMethod = document.getElementById('payment_method').value;
    var instructionsDiv = document.getElementById('payment_instructions');
    var bankInstructions = document.getElementById('bank_instructions');
    var upiInstructions = document.getElementById('upi_instructions');
    
    if (paymentMethod === 'bank_transfer' || paymentMethod === 'upi') {
        instructionsDiv.style.display = 'block';
        
        if (paymentMethod === 'bank_transfer') {
            bankInstructions.style.display = 'block';
            upiInstructions.style.display = 'none';
        } else if (paymentMethod === 'upi') {
            bankInstructions.style.display = 'none';
            upiInstructions.style.display = 'block';
        }
    } else {
        // Hide instructions for online and COD
        instructionsDiv.style.display = 'none';
        bankInstructions.style.display = 'none';
        upiInstructions.style.display = 'none';
    }
}

// Run on page load to show instructions if a method is already selected
document.addEventListener('DOMContentLoaded', function() {
    showPaymentInstructions();
});
</script>

<?php
// Include footer
require_once '../includes/footer.php';
?>
