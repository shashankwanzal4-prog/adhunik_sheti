<?php
/**
 * Order Tracking Page for Customers
 * Track order status and shipment details
 */

// Enable output buffering
ob_start();

// Include header
require_once '../includes/header.php';

// Get order tracking parameters
$order_number = $_GET['order_number'] ?? '';
$phone = $_GET['phone'] ?? '';
$tracking_result = null;
$tracking_error = '';

// Search for order if parameters provided
if (!empty($order_number) || !empty($phone)) {
    $query = "SELECT * FROM orders WHERE 1=1";
    $params = [];
    $types = '';
    
    if (!empty($order_number)) {
        $query .= " AND order_number = ?";
        $params[] = $order_number;
        $types .= 's';
    }
    
    if (!empty($phone)) {
        $query .= " AND customer_phone = ?";
        $params[] = $phone;
        $types .= 's';
    }
    
    $query .= " ORDER BY created_at DESC LIMIT 1";
    
    $stmt = $db->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $tracking_result = $result->fetch_assoc();
        
        // Get order items
        $order_items = [];
        $item_stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $item_stmt->bind_param("i", $tracking_result['id']);
        $item_stmt->execute();
        $item_result = $item_stmt->get_result();
        while ($item = $item_result->fetch_assoc()) {
            $order_items[] = $item;
        }
        $tracking_result['items'] = $order_items;
    } else {
        $tracking_error = 'No order found with the provided details. Please check your order number and phone number.';
    }
}

$status_steps = [
    'pending' => ['label' => 'Order Placed', 'icon' => 'fa-shopping-cart', 'color' => 'warning'],
    'confirmed' => ['label' => 'Confirmed', 'icon' => 'fa-check-circle', 'color' => 'info'],
    'processing' => ['label' => 'Processing', 'icon' => 'fa-cog', 'color' => 'primary'],
    'shipped' => ['label' => 'Shipped', 'icon' => 'fa-truck', 'color' => 'success'],
    'delivered' => ['label' => 'Delivered', 'icon' => 'fa-box', 'color' => 'success'],
    'cancelled' => ['label' => 'Cancelled', 'icon' => 'fa-times-circle', 'color' => 'danger']
];

$payment_methods = [
    'online' => 'Online Payment (Razorpay)',
    'cash_on_delivery' => 'Cash on Delivery',
    'bank_transfer' => 'Bank Transfer',
    'upi' => 'UPI Payment'
];
?>

<!-- Order Tracking Header -->
<section class="bg-success text-white py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 fw-bold mb-3">Track Your Order</h1>
                <p class="lead mb-0">Enter your order number and phone number to track your order status</p>
            </div>
        </div>
    </div>
</section>

<!-- Order Tracking Form -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label for="order_number" class="form-label">Order Number</label>
                                <input type="text" class="form-control form-control-lg" id="order_number" name="order_number" 
                                       value="<?php echo htmlspecialchars($order_number); ?>" 
                                       placeholder="e.g., AKB20240001">
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control form-control-lg" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($phone); ?>" 
                                       placeholder="e.g., 9876543210">
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-search me-2"></i>Track Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($tracking_error)): ?>
<!-- Error Message -->
<section class="pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($tracking_error); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($tracking_result): ?>
<!-- Order Details -->
<section class="pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Order Info Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Order #<?php echo htmlspecialchars($tracking_result['order_number']); ?></h5>
                            <span class="badge bg-<?php echo $status_steps[$tracking_result['status']]['color']; ?> fs-6">
                                <?php echo $status_steps[$tracking_result['status']]['label']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Order Progress Timeline -->
                        <div class="order-timeline mb-4">
                            <div class="d-flex justify-content-between position-relative">
                                <?php 
                                $current_status_index = array_search($tracking_result['status'], array_keys($status_steps));
                                $step_count = 0;
                                foreach ($status_steps as $status => $step): 
                                    if ($status === 'cancelled') continue;
                                    $step_index = $step_count++;
                                    $is_completed = $step_index <= $current_status_index && $tracking_result['status'] !== 'cancelled';
                                    $is_current = $status === $tracking_result['status'];
                                ?>
                                <div class="text-center" style="width: 20%;">
                                    <div class="timeline-icon mb-2">
                                        <i class="fas <?php echo $step['icon']; ?> fa-2x <?php echo $is_completed ? 'text-' . $step['color'] : 'text-muted'; ?>"></i>
                                    </div>
                                    <small class="d-block <?php echo $is_completed ? 'text-' . $step['color'] . ' fw-bold' : 'text-muted'; ?>">
                                        <?php echo $step['label']; ?>
                                    </small>
                                    <?php if ($is_current): ?>
                                        <small class="text-muted">Current</small>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Progress Line -->
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?php echo (($current_status_index / (count($status_steps) - 2)) * 100); ?>%"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Order Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Order Date:</strong></td>
                                        <td><?php echo date('d M Y H:i', strtotime($tracking_result['created_at'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Method:</strong></td>
                                        <td><?php echo $payment_methods[$tracking_result['payment_method']] ?? $tracking_result['payment_method']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Status:</strong></td>
                                        <td>
                                            <span class="badge bg-<?php echo $tracking_result['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($tracking_result['payment_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Amount:</strong></td>
                                        <td class="text-success fw-bold">₹<?php echo number_format($tracking_result['total_amount'], 2); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Customer Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td><?php echo htmlspecialchars($tracking_result['customer_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td><?php echo htmlspecialchars($tracking_result['customer_phone']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td><?php echo htmlspecialchars($tracking_result['customer_email']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        <?php if (!empty($tracking_result['tracking_number'])): ?>
                        <div class="alert alert-info mt-3">
                            <h6 class="fw-bold mb-2"><i class="fas fa-truck me-2"></i>Shipment Information</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Courier:</strong> <?php echo htmlspecialchars($tracking_result['courier_name']); ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Tracking Number:</strong> <?php echo htmlspecialchars($tracking_result['tracking_number']); ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Estimated Delivery:</strong> 
                                    <?php echo !empty($tracking_result['estimated_delivery']) ? date('d M Y', strtotime($tracking_result['estimated_delivery'])) : 'Not specified'; ?>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="https://www.google.com/search?q=<?php echo urlencode($tracking_result['courier_name'] . ' tracking ' . $tracking_result['tracking_number']); ?>" 
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt me-2"></i>Track on Courier Website
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Delivery Address -->
                        <div class="mt-3">
                            <h6 class="fw-bold">Delivery Address</h6>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($tracking_result['customer_address'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tracking_result['items'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                        <td>₹<?php echo number_format($item['subtotal'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Total:</td>
                                        <td class="text-success">₹<?php echo number_format($tracking_result['total_amount'], 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body text-center py-4">
                        <h6 class="fw-bold mb-3">Need Help with Your Order?</h6>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="tel:+919588676848" class="btn btn-outline-success">
                                <i class="fas fa-phone me-2"></i>Call Us
                            </a>
                            <a href="https://wa.me/919588676848" target="_blank" class="btn btn-success">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Include footer
require_once '../includes/footer.php';
?>
