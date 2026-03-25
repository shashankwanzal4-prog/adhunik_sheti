<?php
/**
 * Products Management Page
 * Add, Edit, Delete products
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

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $db->query("DELETE FROM products WHERE id = $product_id");
    header('Location: products.php?deleted=1');
    exit;
}

// Get products
$products = [];
$result = $db->query("SELECT * FROM products ORDER BY created_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - Admin Panel</title>
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
                            <a class="nav-link active" href="products.php">
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
                    <h1 class="h2">Products Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="add_product.php" class="btn btn-sm btn-success">
                                <i class="fas fa-plus me-2"></i>Add Product
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>Product deleted successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['added'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>Product added successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>Product updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Products Table -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">All Products</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($products)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                <h5>No products found</h5>
                                <p class="text-muted">Start by adding your first product.</p>
                                <a href="add_product.php" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Add Product
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?php echo $product['id']; ?></td>
                                            <td>
                                                <?php if ($product['image']): ?>
                                                    <img src="../assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                         style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                                <?php else: ?>
                                                    <img src="../assets/images/product-placeholder.jpg" 
                                                         alt="No image" 
                                                         style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                                            <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                            <td>
                                                <?php if ($product['stock_quantity'] > 10): ?>
                                                    <span class="text-success"><?php echo $product['stock_quantity']; ?></span>
                                                <?php elseif ($product['stock_quantity'] > 0): ?>
                                                    <span class="text-warning"><?php echo $product['stock_quantity']; ?></span>
                                                <?php else: ?>
                                                    <span class="text-danger"><?php echo $product['stock_quantity']; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $product['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($product['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($product['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="view_product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="products.php?delete=<?php echo $product['id']; ?>" 
                                                       class="btn btn-outline-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this product?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
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
</body>
</html>
