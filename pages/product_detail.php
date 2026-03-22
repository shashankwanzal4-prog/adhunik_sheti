<?php
/**
 * Product Detail Page
 * Shows detailed product information with add to cart
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include header
require_once '../includes/header.php';

// Get product ID
$product_id = $_GET['id'] ?? 0;

if ($product_id <= 0) {
    header('Location: products.php');
    exit;
}

// Get product details
$product = null;
$result = $db->query("SELECT * FROM products WHERE id = $product_id AND status = 'active'");
if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    header('Location: products.php');
    exit;
}

// Get related products
$related_products = [];
$category = $db->escape($product['category']);
$related_result = $db->query("SELECT * FROM products WHERE category = '$category' AND id != $product_id AND status = 'active' LIMIT 4");
if ($related_result && $related_result->num_rows > 0) {
    while ($row = $related_result->fetch_assoc()) {
        $related_products[] = $row;
    }
}
?>

<!-- Product Detail Header -->
<section class="bg-success text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="mb-0"><?php echo htmlspecialchars($product['category']); ?></p>
            </div>
            <div class="col-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item"><a href="products.php" class="text-white">Products</a></li>
                        <li class="breadcrumb-item active text-white"><?php echo htmlspecialchars($product['name']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Product Detail -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Product Image -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <img src="../assets/images/products/<?php echo $product['image'] ?: 'product-placeholder.jpg'; ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="max-height: 500px; object-fit: cover;">
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info">
                    <span class="badge bg-success mb-3"><?php echo htmlspecialchars($product['category']); ?></span>
                    <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="mb-3">
                        <span class="h2 text-success">₹<?php echo number_format($product['price'], 2); ?></span>
                    </div>
                    
                    <div class="mb-4">
                        <span class="badge bg-<?php echo $product['stock_quantity'] > 0 ? 'success' : 'danger'; ?> fs-6">
                            <?php echo $product['stock_quantity'] > 0 ? 'In Stock (' . $product['stock_quantity'] . ' available)' : 'Out of Stock'; ?>
                        </span>
                    </div>
                    
                    <p class="lead text-muted mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                    
                    <!-- Add to Cart Form -->
                    <form action="cart.php" method="POST" class="mb-4">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-auto">
                                <label for="quantity" class="form-label fw-bold">Quantity:</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" id="quantity" name="quantity" value="1" min="1" 
                                       max="<?php echo min(10, $product['stock_quantity']); ?>" 
                                       class="form-control form-control-lg" style="width: 100px;"
                                       <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-success btn-lg flex-fill" 
                                    <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                <i class="fas fa-cart-plus me-2"></i>
                                <?php echo $product['stock_quantity'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                            </button>
                            <a href="cart.php" class="btn btn-outline-success btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>View Cart
                            </a>
                        </div>
                    </form>
                    
                    <!-- Product Features -->
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>Product Features
                            </h5>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Premium quality agricultural product</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Trusted by thousands of farmers</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Fast and reliable delivery</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Expert support available</li>
                                <li><i class="fas fa-check text-success me-2"></i>Money-back guarantee</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3-Step Process Reminder -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold text-success">Easy 3-Step Buying Process</h2>
            <p class="lead text-muted">Continue your shopping journey</p>
        </div>
        
        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <div class="p-4 bg-white rounded shadow-sm">
                    <div class="step-icon mb-3 mx-auto" style="background: linear-gradient(135deg, #2e7d32, #66bb6a); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check fa-lg text-white"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Step 1: Select Product</h5>
                    <p class="text-muted small mb-0">✅ You've selected this product</p>
                </div>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="p-4 bg-white rounded shadow-sm">
                    <div class="step-icon mb-3 mx-auto" style="background: linear-gradient(135deg, #f9a825, #fdd835); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-arrow-right fa-lg text-white"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Step 2: Add to Cart</h5>
                    <p class="text-muted small mb-0">Click "Add to Cart" above</p>
                </div>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="p-4 bg-white rounded shadow-sm">
                    <div class="step-icon mb-3 mx-auto" style="background: linear-gradient(135deg, #f57c00, #ff9800); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-credit-card fa-lg text-white"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Step 3: Checkout</h5>
                    <p class="text-muted small mb-0">Review and confirm your order</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="cart.php" class="btn btn-success btn-lg">
                <i class="fas fa-shopping-cart me-2"></i>Go to Cart
            </a>
        </div>
    </div>
</section>

<?php if (!empty($related_products)): ?>
<!-- Related Products -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold text-success">Related Products</h2>
            <p class="lead text-muted">You might also like these</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($related_products as $related): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="../assets/images/products/<?php echo $related['image'] ?: 'product-placeholder.jpg'; ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($related['name']); ?>"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($related['name']); ?></h5>
                        <p class="text-success fw-bold mb-0">₹<?php echo number_format($related['price'], 2); ?></p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="product_detail.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-success btn-sm w-100">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
require_once '../includes/footer.php';
?>
