<?php
/**
 * Add Product Page
 * Form to add new products
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

// Handle form submission
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    
    // Validation
    if (empty($name)) $errors[] = 'Product name is required';
    if (empty($description)) $errors[] = 'Description is required';
    if ($price <= 0) $errors[] = 'Valid price is required';
    if (empty($category)) $errors[] = 'Category is required';
    if ($stock_quantity < 0) $errors[] = 'Stock quantity must be 0 or greater';
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = 'Invalid image type. Only JPG, PNG, GIF, and WebP are allowed.';
        } elseif ($_FILES['image']['size'] > $max_size) {
            $errors[] = 'Image size must be less than 5MB.';
        } else {
            $upload_dir = '../assets/images/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $filename = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $filename;
            } else {
                $errors[] = 'Failed to upload image.';
            }
        }
    }
    
    if (empty($errors)) {
        // Insert product into database
        $sql = "INSERT INTO products (name, description, price, image, category, stock_quantity, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssdssis", $name, $description, $price, $image, $category, $stock_quantity, $status);
        
        if ($stmt->execute()) {
            header('Location: products.php?added=1');
            exit;
        } else {
            $errors[] = 'Failed to add product. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Panel</title>
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
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Add New Product</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="products.php" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Products
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:
                        </h6>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Add Product Form -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Product Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description *</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Price (₹) *</label>
                                                <input type="number" class="form-control" id="price" name="price" 
                                                       step="0.01" min="0" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                                                       min="0" value="<?php echo htmlspecialchars($_POST['stock_quantity'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Category *</label>
                                                <select class="form-select" id="category" name="category" required>
                                                    <option value="">Select Category</option>
                                                    <option value="Insecticide" <?php echo ($_POST['category'] ?? '') === 'Insecticide' ? 'selected' : ''; ?>>Insecticide</option>
                                                    <option value="Fungicide" <?php echo ($_POST['category'] ?? '') === 'Fungicide' ? 'selected' : ''; ?>>Fungicide</option>
                                                    <option value="PGR" <?php echo ($_POST['category'] ?? '') === 'PGR' ? 'selected' : ''; ?>>PGR</option>
                                                    <option value="Bio-pesticide" <?php echo ($_POST['category'] ?? '') === 'Bio-pesticide' ? 'selected' : ''; ?>>Bio-pesticide</option>
                                                    <option value="Fertilizer" <?php echo ($_POST['category'] ?? '') === 'Fertilizer' ? 'selected' : ''; ?>>Fertilizer</option>
                                                    <option value="Nutrients" <?php echo ($_POST['category'] ?? '') === 'Nutrients' ? 'selected' : ''; ?>>Nutrients</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="active" <?php echo ($_POST['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Product Image</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <small class="text-muted">Allowed: JPG, PNG, GIF, WebP (Max 5MB)</small>
                                    </div>
                                    
                                    <div id="imagePreview" class="text-center">
                                        <img src="../assets/images/product-placeholder.jpg" 
                                             alt="Product preview" 
                                             class="img-fluid rounded" 
                                             style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="products.php" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-2"></i>Add Product
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').innerHTML = 
                        '<img src="' + e.target.result + '" alt="Product preview" class="img-fluid rounded" style="max-height: 200px;">';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
