<?php
/**
 * Products Listing Page
 * Display all products with filtering and search
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include header (which handles session and database)
require_once '../includes/header.php';

// Get products from database
$products = [];
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM products WHERE status = 'active'";

if (!empty($category)) {
    $safe_category = $db->escape($category);
    $query .= " AND category = '$safe_category'";
}

if (!empty($search)) {
    $safe_search = $db->escape($search);
    $query .= " AND (name LIKE '%$safe_search%' OR description LIKE '%$safe_search%')";
}

$query .= " ORDER BY created_at DESC";

// Execute query
$result = $db->query($query);

// Check for query errors
if (!$result) {
    echo '<div class="alert alert-danger">Database error: ' . $db->conn->error . '</div>';
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get categories for filter
$categories = [];
$cat_result = $db->query("SELECT DISTINCT category FROM products WHERE status = 'active' ORDER BY category");
if ($cat_result && $cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}
?>

<!-- Products Page Header -->
<section class="bg-success text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="mb-0">Our Products</h1>
                <p class="mb-0">Quality agricultural products for better farming</p>
            </div>
            <div class="col-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white">Products</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Products Content -->
<section class="py-5">
    <div class="container">
        <!-- Search and Filter -->
        <div class="row mb-4 g-3">
            <div class="col-12 col-md-6 col-lg-6">
                <form method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg" name="search" 
                               placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <?php if (!empty($search)): ?>
                        <a href="products.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-12 col-md-6 col-lg-6">
                <form method="GET" class="d-flex gap-2">
                    <select class="form-select form-select-lg" name="category" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" 
                                    <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($search)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <?php endif; ?>
                    <?php if (!empty($category)): ?>
                        <a href="products.php<?php echo !empty($search) ? '?search=' . urlencode($search) : ''; ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Product Count -->
        <div class="row mb-3">
            <div class="col-12">
                <p class="text-muted">
                    <?php if (!empty($search) || !empty($category)): ?>
                        Showing <?php echo count($products); ?> result(s)
                        <?php if (!empty($search)): ?>for "<strong><?php echo htmlspecialchars($search); ?></strong>"<?php endif; ?>
                        <?php if (!empty($category)): ?>in <strong><?php echo htmlspecialchars($category); ?></strong><?php endif; ?>
                    <?php else: ?>
                        Showing all <?php echo count($products); ?> products
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <!-- No Products Found -->
            <div class="text-center py-5">
                <i class="fas fa-search fa-5x text-muted mb-4"></i>
                <h3 class="mb-3">No products found</h3>
                <p class="text-muted mb-4">
                    <?php if (!empty($search) || !empty($category)): ?>
                        We couldn't find any products matching your criteria.
                        <br>Try adjusting your search or browse all products.
                    <?php else: ?>
                        No products are available at the moment.
                        <br>Please check back later or contact us for assistance.
                    <?php endif; ?>
                </p>
                <a href="products.php" class="btn btn-success btn-lg">
                    <i class="fas fa-th me-2"></i>View All Products
                </a>
            </div>
        <?php else: ?>
            <!-- Debug: Products found: <?php echo count($products); ?> -->
            <!-- Products Grid -->
            <div class="row g-4">
                <?php foreach ($products as $index => $product): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="product-card card h-100 border-0 shadow-sm">
                        <div class="product-image-wrapper">
                            <img src="../assets/images/products/<?php echo $product['image'] ?: 'product-placeholder.jpg'; ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="product-overlay">
                                <button class="btn btn-success btn-sm quick-view-btn" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-eye"></i> Quick View
                                </button>
                            </div>
                            <?php if ($product['stock_quantity'] <= 5): ?>
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-warning">Low Stock</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted"><?php echo htmlspecialchars($product['category']); ?></small>
                            </div>
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="text-muted small"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-success mb-0">₹<?php echo number_format($product['price'], 2); ?></span>
                                <span class="badge bg-<?php echo $product['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                                    <?php echo $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <form action="cart.php" method="POST" class="d-flex gap-2">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo min(10, $product['stock_quantity']); ?>" 
                                       class="form-control form-control-sm" style="width: 70px;" 
                                       <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                <button type="submit" class="btn btn-success btn-sm flex-fill" 
                                        <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                    <i class="fas fa-cart-plus me-1"></i>
                                    <?php echo $product['stock_quantity'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="row mt-5">
                <div class="col-12">
                    <nav aria-label="Product pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Product Categories Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-5 fw-bold text-success">Shop by Category</h2>
            <p class="lead text-muted">Find products by category</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($categories as $cat): ?>
            <div class="col-lg-2 col-md-4 col-6" data-aos="fade-up">
                <a href="products.php?category=<?php echo urlencode($cat); ?>" class="text-decoration-none">
                    <div class="category-card text-center p-3 bg-white rounded shadow-sm h-100">
                        <div class="category-icon mb-2">
                            <i class="fas fa-seedling fa-2x text-success"></i>
                        </div>
                        <h6 class="fw-bold text-dark"><?php echo htmlspecialchars($cat); ?></h6>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Include footer
require_once '../includes/footer.php';
?>
