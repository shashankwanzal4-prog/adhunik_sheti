<?php
/**
 * Orders Management Page
 * View and manage customer orders
 */

// Include database configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/Adhunik_Sheti/config/db.php';

// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    
    header('Location: orders.php?updated=1');
    exit;
}

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    // Get all orders
    $export_result = $db->query("SELECT * FROM orders ORDER BY created_at DESC");
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=orders_export_' . date('Y-m-d') . '.csv');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, ['Order ID', 'Customer Name', 'Phone', 'Email', 'Address', 'Total Amount', 'Status', 'Order Date']);
    
    // Add data rows
    if ($export_result && $export_result->num_rows > 0) {
        while ($row = $export_result->fetch_assoc()) {
            fputcsv($output, [
                $row['id'],
                $row['customer_name'],
                $row['phone'],
                $row['email'],
                $row['address'],
                $row['total_amount'],
                $row['status'],
                $row['created_at']
            ]);
        }
    }
    
    fclose($output);
    exit;
}

// Get orders
$orders = [];
$result = $db->query("SELECT * FROM orders ORDER BY created_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Admin Panel</title>
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
                    <h1 class="h2">Orders Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="orders.php?export=csv" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download me-1"></i>Export CSV
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>Order status updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Orders Table -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">All Orders</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5>No orders found</h5>
                                <p class="text-muted">When customers place orders, they will appear here.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Phone</th>
                                            <th>Amount</th>
                                            <th>Payment</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                                            <td>
                                                <strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <?php 
                                                    $payment_methods = [
                                                        'online' => 'Razorpay',
                                                        'cash_on_delivery' => 'COD',
                                                        'bank_transfer' => 'Bank Transfer',
                                                        'upi' => 'UPI Manual'
                                                    ];
                                                    echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                                                    ?>
                                                </small>
                                                <?php if (!empty($order['razorpay_payment_id'])): ?>
                                                    <br><small class="text-success"><?php echo htmlspecialchars(substr($order['razorpay_payment_id'], 0, 15)) . '...'; ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                        <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                        <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-outline-success" onclick="window.print()">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Statistics -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-center border-warning">
                            <div class="card-body">
                                <h5 class="card-title text-warning">Pending</h5>
                                <h3 class="card-text">
                                    <?php 
                                    $pending = array_filter($orders, fn($o) => $o['status'] === 'pending');
                                    echo count($pending);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-info">
                            <div class="card-body">
                                <h5 class="card-title text-info">Processing</h5>
                                <h3 class="card-text">
                                    <?php 
                                    $processing = array_filter($orders, fn($o) => $o['status'] === 'processing');
                                    echo count($processing);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-primary">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Shipped</h5>
                                <h3 class="card-text">
                                    <?php 
                                    $shipped = array_filter($orders, fn($o) => $o['status'] === 'shipped');
                                    echo count($shipped);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-success">
                            <div class="card-body">
                                <h5 class="card-title text-success">Delivered</h5>
                                <h3 class="card-text">
                                    <?php 
                                    $delivered = array_filter($orders, fn($o) => $o['status'] === 'delivered');
                                    echo count($delivered);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
