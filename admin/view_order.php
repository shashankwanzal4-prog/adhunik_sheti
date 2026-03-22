<?php
/**
 * View Order Page
 * Display order details
 */

// Include database configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/Katkar_New/config/db.php';

// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get order ID
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get order details
$order = null;
$order_items = [];

if ($order_id > 0) {
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        
        // Get order items
        $item_stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $item_stmt->bind_param("i", $order_id);
        $item_stmt->execute();
        $item_result = $item_stmt->get_result();
        while ($item = $item_result->fetch_assoc()) {
            $order_items[] = $item;
        }
    }
}

// If order not found, redirect
if (!$order) {
    header('Location: orders.php');
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'] ?? $order['status'];
    $tracking_number = $_POST['tracking_number'] ?? '';
    $courier_name = $_POST['courier_name'] ?? '';
    $estimated_delivery = $_POST['estimated_delivery'] ?? '';
    
    // Update order with tracking info
    $update_sql = "UPDATE orders SET status = ?, tracking_number = ?, courier_name = ?, estimated_delivery = ?, updated_at = NOW() WHERE id = ?";
    $update_stmt = $db->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $new_status, $tracking_number, $courier_name, $estimated_delivery, $order_id);
    $update_stmt->execute();
    
    // If status is shipped, send notification to customer (placeholder for now)
    if ($new_status === 'shipped' && $order['status'] !== 'shipped') {
        // Notification logic will be added here
        $_SESSION['notification_sent'] = true;
    }
    
    // Refresh order data
    header("Location: view_order.php?id=$order_id&updated=1");
    exit;
}

$payment_methods = [
    'online' => 'Razorpay',
    'cash_on_delivery' => 'Cash on Delivery',
    'bank_transfer' => 'Bank Transfer',
    'upi' => 'UPI Manual'
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <!-- Admin Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-seedling me-2"></i>Admin Panel
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['admin_name']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">
                                <i class="fas fa-box me-2"></i>Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="orders.php">
                                <i class="fas fa-shopping-cart me-2"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="customers.php">
                                <i class="fas fa-users me-2"></i>Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="fas fa-chart-bar me-2"></i>Reports
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-success" href="../index.php" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>Back to Website
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Order Details</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="orders.php" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-print me-2"></i>Print Order
                        </button>
                    </div>
                </div>

                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>Order status updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Order Info -->
                    <div class="col-md-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Order #<?php echo htmlspecialchars($order['order_number']); ?></h6>
                                <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                    Payment: <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Customer Information</h6>
                                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                                        <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">Order Information</h6>
                                        <p class="mb-1"><strong>Order Date:</strong> <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
                                        <p class="mb-1"><strong>Payment Method:</strong> <?php echo $payment_methods[$order['payment_method']] ?? $order['payment_method']; ?></p>
                                        <?php if (!empty($order['razorpay_payment_id'])): ?>
                                            <p class="mb-1"><strong>Payment ID:</strong> <span class="text-success"><?php echo htmlspecialchars($order['razorpay_payment_id']); ?></span></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold">Delivery Address</h6>
                                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($order['customer_address'])); ?></p>
                                </div>

                                <?php if (!empty($order['notes'])): ?>
                                    <div class="mb-4">
                                        <h6 class="fw-bold">Customer Notes</h6>
                                        <p class="mb-1 text-muted"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                            <td>₹<?php echo number_format($item['subtotal'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr class="fw-bold">
                                            <td colspan="3" class="text-end">Total Amount:</td>
                                            <td class="text-success">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status -->
                    <div class="col-md-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Order Status</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="update_status" value="1">
                                    <div class="mb-3">
                                        <label class="form-label">Current Status</label>
                                        <select name="status" class="form-select" onchange="toggleTrackingFields(this.value)">
                                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                            <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>✅ Confirmed</option>
                                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>🔧 Processing</option>
                                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>🚚 Shipped</option>
                                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>📦 Delivered</option>
                                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>❌ Cancelled</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Tracking Fields (shown when shipped) -->
                                    <div id="trackingFields" style="display: <?php echo $order['status'] === 'shipped' || $order['status'] === 'delivered' ? 'block' : 'none'; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Courier Name</label>
                                            <select name="courier_name" class="form-select">
                                                <option value="">Select Courier</option>
                                                <option value="Delhivery" <?php echo ($order['courier_name'] ?? '') === 'Delhivery' ? 'selected' : ''; ?>>Delhivery</option>
                                                <option value="BlueDart" <?php echo ($order['courier_name'] ?? '') === 'BlueDart' ? 'selected' : ''; ?>>BlueDart</option>
                                                <option value="FedEx" <?php echo ($order['courier_name'] ?? '') === 'FedEx' ? 'selected' : ''; ?>>FedEx</option>
                                                <option value="DTDC" <?php echo ($order['courier_name'] ?? '') === 'DTDC' ? 'selected' : ''; ?>>DTDC</option>
                                                <option value="India Post" <?php echo ($order['courier_name'] ?? '') === 'India Post' ? 'selected' : ''; ?>>India Post</option>
                                                <option value="Other" <?php echo ($order['courier_name'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Tracking Number</label>
                                            <input type="text" class="form-control" name="tracking_number" 
                                                   value="<?php echo htmlspecialchars($order['tracking_number'] ?? ''); ?>" 
                                                   placeholder="Enter tracking number">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Estimated Delivery</label>
                                            <input type="date" class="form-control" name="estimated_delivery" 
                                                   value="<?php echo htmlspecialchars($order['estimated_delivery'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-save me-2"></i>Update Status
                                    </button>
                                </form>
                                
                                <!-- Tracking Link -->
                                <?php if (!empty($order['tracking_number']) && !empty($order['courier_name'])): ?>
                                    <div class="mt-3">
                                        <a href="https://www.google.com/search?q=<?php echo urlencode($order['courier_name'] . ' tracking ' . $order['tracking_number']); ?>" 
                                           target="_blank" class="btn btn-outline-primary w-100 btn-sm">
                                            <i class="fas fa-search-location me-2"></i>Track Shipment
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mt-4">
                                    <h6 class="fw-bold">Status History</h6>
                                    <ul class="list-unstyled small text-muted">
                                        <li><i class="fas fa-check text-success me-2"></i>Order Placed: <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></li>
                                        <?php if ($order['status'] !== 'pending'): ?>
                                            <li><i class="fas fa-check text-success me-2"></i>Status Updated: <?php echo date('d M Y H:i', strtotime($order['updated_at'])); ?></li>
                                        <?php endif; ?>
                                        <?php if (!empty($order['tracking_number'])): ?>
                                            <li><i class="fas fa-truck text-info me-2"></i>Shipped: <?php echo htmlspecialchars($order['courier_name']); ?></li>
                                            <li><i class="fas fa-barcode text-info me-2"></i>Tracking: <?php echo htmlspecialchars($order['tracking_number']); ?></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-edit me-2"></i>Edit Order
                                    </a>
                                    <button onclick="window.print()" class="btn btn-outline-success">
                                        <i class="fas fa-print me-2"></i>Print Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Send Notification -->
                        <div class="card shadow mt-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Customer Notification</h6>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-3">Send order status update to customer via WhatsApp/SMS</p>
                                <?php
                                // Build WhatsApp message
                                $whatsapp_msg = "Dear " . $order['customer_name'] . ",\n\n";
                                $whatsapp_msg .= "Your order #" . $order['order_number'] . " is now " . strtoupper($order['status']) . ".\n\n";
                                if (!empty($order['tracking_number'])) {
                                    $whatsapp_msg .= "Track your shipment:\n";
                                    $whatsapp_msg .= "Courier: " . $order['courier_name'] . "\n";
                                    $whatsapp_msg .= "Tracking: " . $order['tracking_number'] . "\n\n";
                                }
                                $whatsapp_msg .= "Thank you for shopping with Adhunik Krushi Bhandar!\n";
                                $whatsapp_msg .= "Contact: +91 9588676848";
                                
                                $clean_phone = preg_replace('/[^0-9]/', '', $order['customer_phone']);
                                $whatsapp_url = "https://wa.me/91" . $clean_phone . "?text=" . urlencode($whatsapp_msg);
                                ?>
                                <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="btn btn-success w-100 btn-sm">
                                    <i class="fab fa-whatsapp me-2"></i>Send WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleTrackingFields(status) {
        const trackingFields = document.getElementById('trackingFields');
        if (status === 'shipped' || status === 'delivered') {
            trackingFields.style.display = 'block';
        } else {
            trackingFields.style.display = 'none';
        }
    }
    </script>
</body>
</html>
