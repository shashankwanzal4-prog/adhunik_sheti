<?php
/**
 * Shopping Cart Page
 * Handles cart operations and displays cart items
 */

// Enable output buffering to prevent headers already sent error
ob_start();

// Prevent caching to avoid form resubmission errors on back/forward
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Include database configuration first
require_once '../config/db.php';

// Start session
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    if ($action === 'add' && $product_id > 0) {
        // Check stock availability first
        $stock_result = $db->query("SELECT stock_quantity FROM products WHERE id = $product_id AND status = 'active'");
        $stock_row = $stock_result->fetch_assoc();
        $available_stock = $stock_row['stock_quantity'] ?? 0;
        
        $current_cart_qty = $_SESSION['cart'][$product_id] ?? 0;
        $requested_qty = $current_cart_qty + $quantity;
        
        if ($requested_qty > $available_stock) {
            $_SESSION['error'] = "Sorry, only $available_stock items available in stock. You already have $current_cart_qty in cart.";
            header('Location: cart.php');
            ob_end_clean();
            exit;
        }
        
        // Add product to cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        // Redirect to cart page
        header('Location: cart.php');
        ob_end_clean();
        exit;
    } elseif ($action === 'update' && $product_id > 0) {
        // Update cart quantity
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    } elseif ($action === 'remove' && $product_id > 0) {
        // Remove product from cart
        unset($_SESSION['cart'][$product_id]);
    } elseif ($action === 'clear') {
        // Clear entire cart
        $_SESSION['cart'] = [];
    }
}

// Get cart items from database
$cart_items = [];
$total_amount = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $ids_string = implode(',', $product_ids);
    
    $result = $db->query("SELECT * FROM products WHERE id IN ($ids_string) AND status = 'active'");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $quantity = $_SESSION['cart'][$row['id']];
            $subtotal = $row['price'] * $quantity;
            $total_amount += $subtotal;
            
            $cart_items[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'image' => $row['image'],
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'stock_quantity' => $row['stock_quantity'],
                'stock_error' => $quantity > $row['stock_quantity']
            ];
        }
    }
}

// Include header after all PHP processing
require_once '../includes/header.php';
?>

<!-- Cart Page Header -->
<section class="bg-success text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="mb-0">Shopping Cart</h1>
                <p class="mb-0">Review your selected products</p>
            </div>
            <div class="col-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white">Cart</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Cart Content -->
<section class="py-5">
    <div class="container">
        <?php if (empty($cart_items)): ?>
            <!-- Empty Cart -->
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
                <h3 class="mb-3">Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any products to your cart yet.</p>
                <a href="../index.php" class="btn btn-success btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <!-- Cart with Items -->
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Cart Items (<?php echo count($cart_items); ?>)</h5>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="clear">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to clear the cart?')">
                                        <i class="fas fa-trash me-1"></i>Clear Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item row align-items-center py-3 border-bottom <?php echo $item['stock_error'] ? 'bg-danger bg-opacity-10' : ''; ?>">
                                <div class="col-md-2">
                                    <img src="../assets/images/products/<?php echo $item['image'] ?: 'product-placeholder.jpg'; ?>" 
                                         class="img-fluid rounded" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-muted">Product ID: #<?php echo $item['id']; ?></small>
                                    <?php if ($item['stock_error']): ?>
                                        <div class="text-danger small">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Only <?php echo $item['stock_quantity']; ?> in stock
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-2">
                                    <span class="fw-bold text-success">₹<?php echo number_format($item['price'], 2); ?></span>
                                </div>
                                <div class="col-md-2">
                                    <form method="POST" class="d-flex align-items-center">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="<?php echo $item['stock_quantity']; ?>" class="form-control form-control-sm me-2" style="width: 60px;"
                                               <?php echo $item['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="fw-bold">₹<?php echo number_format($item['subtotal'], 2); ?></div>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger mt-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Continue Shopping -->
                    <div class="text-center">
                        <a href="../index.php" class="btn btn-outline-success">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>₹<?php echo number_format($total_amount, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery Charges:</span>
                                <span class="text-success">FREE</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>₹<?php echo number_format($total_amount * 0.18, 2); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <h5 class="mb-0">Total:</h5>
                                <h5 class="mb-0 text-success">₹<?php echo number_format($total_amount * 1.18, 2); ?></h5>
                            </div>
                            
                            <form action="checkout.php" method="POST">
                                <input type="hidden" name="total_amount" value="<?php echo $total_amount * 1.18; ?>">
                                <?php 
                                $has_stock_error = false;
                                foreach ($cart_items as $item) {
                                    if ($item['stock_error'] || $item['stock_quantity'] <= 0) {
                                        $has_stock_error = true;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($has_stock_error): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Some items in your cart are out of stock or exceed available quantity. Please adjust quantities before proceeding.
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                                        <i class="fas fa-lock me-2"></i>Cannot Checkout
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-lock me-2"></i>Proceed to Checkout
                                    </button>
                                <?php endif; ?>
                            </form>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>Secure Checkout
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Features -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Why Shop With Us?</h6>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    100% Genuine Products
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Fast Delivery
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Secure Payment
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    24/7 Support
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Include footer
require_once '../includes/footer.php';
?>
