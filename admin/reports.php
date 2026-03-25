<?php
/**
 * Reports Page
 * Generate and view business reports
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

// Get report data
$total_revenue = 0;
$total_orders = 0;
$monthly_sales = [];
$top_products = [];

// Get total revenue
$result = $db->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_revenue = $row['total'] ?? 0;
}

// Get total orders
$result = $db->query("SELECT COUNT(*) as total FROM orders");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_orders = $row['total'];
}

// Get monthly sales (last 6 months)
$result = $db->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as revenue, COUNT(*) as orders FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $monthly_sales[] = $row;
    }
}

// Get top selling products
$result = $db->query("SELECT p.name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as revenue FROM order_items oi JOIN products p ON oi.product_id = p.id JOIN orders o ON oi.order_id = o.id WHERE o.status != 'cancelled' GROUP BY oi.product_id ORDER BY total_sold DESC LIMIT 5");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $top_products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel</title>
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
                            <a class="nav-link" href="orders.php">
                                <i class="fas fa-shopping-cart me-2"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="customers.php">
                                <i class="fas fa-users me-2"></i>Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="reports.php">
                                <i class="fas fa-chart-bar me-2"></i>Reports
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Business Reports</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf me-1"></i>Export PDF
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="exportToExcel()">
                                <i class="fas fa-file-excel me-1"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-xl-6 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($total_revenue, 2); ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-rupee-sign fa-2x text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_orders; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Sales Report -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Monthly Sales Report (Last 6 Months)</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($monthly_sales)): ?>
                            <p class="text-muted">No sales data available for the last 6 months.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Orders</th>
                                            <th>Revenue</th>
                                            <th>Average Order Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($monthly_sales as $sale): ?>
                                        <tr>
                                            <td><?php echo date('F Y', strtotime($sale['month'] . '-01')); ?></td>
                                            <td><?php echo $sale['orders']; ?></td>
                                            <td>₹<?php echo number_format($sale['revenue'], 2); ?></td>
                                            <td>₹<?php echo number_format($sale['revenue'] / $sale['orders'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Top Products Report -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($top_products)): ?>
                            <p class="text-muted">No product sales data available.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Units Sold</th>
                                            <th>Revenue</th>
                                            <th>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($top_products as $product): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                            <td><?php echo $product['total_sold']; ?></td>
                                            <td>₹<?php echo number_format($product['revenue'], 2); ?></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: <?php echo min(100, ($product['total_sold'] / $top_products[0]['total_sold']) * 100); ?>%">
                                                        <?php echo round(($product['total_sold'] / $top_products[0]['total_sold']) * 100); ?>%
                                                    </div>
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
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
    // Export to Excel function
    function exportToExcel() {
        // Create workbook
        const wb = XLSX.utils.book_new();
        wb.Props = {
            Title: "Business Reports",
            Subject: "Sales Report",
            Author: "Adhunik Krushi Bhandar",
            CreatedDate: new Date()
        };
        
        // Summary Sheet
        const summaryData = [
            ["Report", "Value"],
            ["Total Revenue", "₹<?php echo number_format($total_revenue, 2); ?>"],
            ["Total Orders", "<?php echo $total_orders; ?>"],
            ["Report Generated", new Date().toLocaleString()]
        ];
        const summaryWs = XLSX.utils.aoa_to_sheet(summaryData);
        XLSX.utils.book_append_sheet(wb, summaryWs, "Summary");
        
        // Monthly Sales Sheet
        const monthlyData = [
            ["Month", "Orders", "Revenue", "Average Order Value"]
        ];
        <?php foreach ($monthly_sales as $sale): ?>
        monthlyData.push([
            "<?php echo date('F Y', strtotime($sale['month'] . '-01')); ?>",
            <?php echo $sale['orders']; ?>,
            "₹<?php echo number_format($sale['revenue'], 2); ?>",
            "₹<?php echo number_format($sale['revenue'] / $sale['orders'], 2); ?>"
        ]);
        <?php endforeach; ?>
        const monthlyWs = XLSX.utils.aoa_to_sheet(monthlyData);
        XLSX.utils.book_append_sheet(wb, monthlyWs, "Monthly Sales");
        
        // Top Products Sheet
        const productsData = [
            ["Product Name", "Units Sold", "Revenue"]
        ];
        <?php foreach ($top_products as $product): ?>
        productsData.push([
            "<?php echo htmlspecialchars($product['name']); ?>",
            <?php echo $product['total_sold']; ?>,
            "₹<?php echo number_format($product['revenue'], 2); ?>"
        ]);
        <?php endforeach; ?>
        const productsWs = XLSX.utils.aoa_to_sheet(productsData);
        XLSX.utils.book_append_sheet(wb, productsWs, "Top Products");
        
        // Save file
        XLSX.writeFile(wb, "Business_Report_<?php echo date('Y-m-d'); ?>.xlsx");
    }
    
    // Export to PDF function (using print to PDF)
    function exportToPDF() {
        // Hide buttons before printing
        const buttons = document.querySelectorAll('.btn-toolbar');
        buttons.forEach(btn => btn.style.display = 'none');
        
        // Add print header
        const header = document.createElement('div');
        header.innerHTML = '<h2 class="text-center mb-4">Adhunik Krushi Bhandar - Business Report</h2><p class="text-center text-muted">Generated on: ' + new Date().toLocaleString() + '</p>';
        header.className = 'print-header';
        document.querySelector('main').insertBefore(header, document.querySelector('main').firstChild);
        
        // Print
        window.print();
        
        // Restore after printing
        buttons.forEach(btn => btn.style.display = '');
        header.remove();
    }
    </script>
</body>
</html>
