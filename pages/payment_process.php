<?php
/**
 * Razorpay Payment Processing
 * Handles payment order creation and verification
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

// Start session
session_start();

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    header('Location: ../pages/cart.php');
    ob_end_clean();
    exit;
}

$order_id = intval($_GET['order_id']);

// Get order details from database
$order = null;
$order_items = [];

$stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $order = $result->fetch_assoc();
    
    // Check if order is already paid
    if ($order['payment_status'] === 'paid') {
        header("Location: confirmation.php?order_id=$order_id");
        ob_end_clean();
        exit;
    }
    
    // Get order items
    $item_stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $item_stmt->bind_param("i", $order_id);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();
    
    while ($item = $item_result->fetch_assoc()) {
        $order_items[] = $item;
    }
} else {
    header('Location: ../pages/cart.php');
    ob_end_clean();
    exit;
}

// Calculate amount in paise (Razorpay uses smallest currency unit)
$amount_paise = $order['total_amount'] * 100;

// Generate Razorpay order
$razorpay_order_id = null;

// API endpoint for creating order
$api_url = 'https://api.razorpay.com/v1/orders';

// Prepare order data
$order_data = [
    'amount' => $amount_paise,
    'currency' => 'INR',
    'receipt' => $order['order_number'],
    'notes' => [
        'order_id' => $order_id,
        'customer_name' => $order['customer_name'],
        'customer_email' => $order['customer_email'],
        'customer_phone' => $order['customer_phone']
    ]
];

// cURL request to create Razorpay order
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode(RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET)
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $razorpay_response = json_decode($response, true);
    $razorpay_order_id = $razorpay_response['id'];
    
    // Store Razorpay order ID in database
    $update_stmt = $db->prepare("UPDATE orders SET razorpay_order_id = ? WHERE id = ?");
    $update_stmt->bind_param("si", $razorpay_order_id, $order_id);
    $update_stmt->execute();
} else {
    $error = 'Failed to initialize payment. Please try again.';
}

// Include header
require_once '../includes/header.php';

// Clear any POST data to prevent resubmission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<!-- Payment Processing Page -->
<section class="bg-success text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="mb-0">Complete Payment</h1>
                <p class="mb-0">Order #<?php echo htmlspecialchars($order['order_number']); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <br>
                        <a href="checkout.php" class="btn btn-outline-danger mt-3">Try Again</a>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Payment Details</h5>
                        </div>
                        <div class="card-body">
                            <!-- Order Summary -->
                            <div class="mb-4">
                                <h6 class="fw-bold">Order Summary</h6>
                                <table class="table table-sm">
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?></td>
                                            <td class="text-end">₹<?php echo number_format($item['subtotal'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr class="fw-bold">
                                            <td>Total Amount</td>
                                            <td class="text-end text-success">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Razorpay Payment Button -->
                            <div class="text-center">
                                <button id="rzp-button" class="btn btn-success btn-lg">
                                    <i class="fas fa-credit-card me-2"></i>Pay Now ₹<?php echo number_format($order['total_amount'], 2); ?>
                                </button>
                                <p class="text-muted mt-2">
                                    <i class="fas fa-shield-alt me-1"></i>Secure payment by Razorpay
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Razorpay Checkout JS -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
// Razorpay configuration
var options = {
    "key": "<?php echo RAZORPAY_KEY_ID; ?>",
    "amount": "<?php echo $amount_paise; ?>",
    "currency": "INR",
    "name": "<?php echo RAZORPAY_COMPANY['name']; ?>",
    "description": "<?php echo RAZORPAY_COMPANY['description']; ?>",
    "image": "<?php echo RAZORPAY_COMPANY['logo']; ?>",
    "order_id": "<?php echo $razorpay_order_id; ?>",
    "handler": function (response) {
        // Redirect to verify payment
        window.location.href = 'payment_verify.php?order_id=<?php echo $order_id; ?>&razorpay_payment_id=' + response.razorpay_payment_id + '&razorpay_order_id=' + response.razorpay_order_id + '&razorpay_signature=' + response.razorpay_signature;
    },
    "prefill": {
        "name": "<?php echo htmlspecialchars($order['customer_name']); ?>",
        "email": "<?php echo htmlspecialchars($order['customer_email'] ?? ''); ?>",
        "contact": "<?php echo htmlspecialchars($order['customer_phone']); ?>"
    },
    "notes": {
        "order_id": "<?php echo $order_id; ?>",
        "order_number": "<?php echo $order['order_number']; ?>"
    },
    "theme": {
        "color": "#2e7d32"
    },
    "modal": {
        "ondismiss": function() {
            console.log('Payment modal closed');
        }
    }
};

// Initialize Razorpay
var rzp = new Razorpay(options);

// Button click handler
document.getElementById('rzp-button').onclick = function(e) {
    rzp.open();
    e.preventDefault();
};
</script>

<?php
// Include footer
require_once '../includes/footer.php';
?>
