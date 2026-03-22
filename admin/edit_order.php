<?php
/**
 * Edit Order Page
 * Edit existing orders
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
if ($order_id > 0) {
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
    }
}

// If order not found, redirect
if (!$order) {
    header('Location: orders.php');
    exit;
}

// Handle form submission
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_address = trim($_POST['customer_address'] ?? '');
    $status = $_POST['status'] ?? $order['status'];
    $payment_status = $_POST['payment_status'] ?? $order['payment_status'];
    $notes = trim($_POST['notes'] ?? '');
    
    // Validation
    if (empty($customer_name)) $errors[] = 'Customer name is required';
    if (empty($customer_phone)) $errors[] = 'Phone number is required';
    if (empty($customer_address)) $errors[] = 'Address is required';
    
    // If no errors, update order
    if (empty($errors)) {
        $update_sql = "UPDATE orders SET 
            customer_name = ?, customer_email = ?, customer_phone = ?, 
            customer_address = ?, status = ?, payment_status = ?, notes = ?, 
            updated_at = NOW() WHERE id = ?";
        
        $stmt = $db->prepare($update_sql);
        $stmt->bind_param("sssssssi", $customer_name, $customer_email, $customer_phone, 
                         $customer_address, $status, $payment_status, $notes, $order_id);
        
        if ($stmt->execute()) {
            header('Location: view_order.php?id=' . $order_id . '&updated=1');
            exit;
        } else {
            $errors[] = 'Failed to update order. Please try again.';
        }
    }
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
    <title>Edit Order - Admin Panel</title>
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
                    <h1 class="h2">Edit Order</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-eye me-2"></i>View Order
                        </a>
                        <a href="orders.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                    </div>
                </div>

                <!-- Error Messages -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Edit Form -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Edit Order #<?php echo htmlspecialchars($order['order_number']); ?></h6>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3">Customer Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="customer_name" class="form-label">Customer Name *</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                               value="<?php echo htmlspecialchars($order['customer_name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="customer_email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                               value="<?php echo htmlspecialchars($order['customer_email']); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="customer_phone" class="form-label">Phone Number *</label>
                                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                               value="<?php echo htmlspecialchars($order['customer_phone']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="customer_address" class="form-label">Delivery Address *</label>
                                        <textarea class="form-control" id="customer_address" name="customer_address" rows="4" required><?php echo htmlspecialchars($order['customer_address']); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5 class="mb-3">Order Information</h5>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Order Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                            <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>✅ Confirmed</option>
                                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>🔧 Processing</option>
                                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>🚚 Shipped</option>
                                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>📦 Delivered</option>
                                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>❌ Cancelled</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="payment_status" class="form-label">Payment Status</label>
                                        <select class="form-select" id="payment_status" name="payment_status">
                                            <option value="pending" <?php echo $order['payment_status'] === 'pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                            <option value="paid" <?php echo $order['payment_status'] === 'paid' ? 'selected' : ''; ?>>✅ Paid</option>
                                            <option value="failed" <?php echo $order['payment_status'] === 'failed' ? 'selected' : ''; ?>>❌ Failed</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Payment Method</label>
                                        <input type="text" class="form-control" value="<?php echo $payment_methods[$order['payment_method']] ?? $order['payment_method']; ?>" readonly>
                                        <small class="text-muted">Payment method cannot be changed</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Order Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($order['notes']); ?></textarea>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <strong>Order Total:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?><br>
                                        <strong>Order Date:</strong> <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Update Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
