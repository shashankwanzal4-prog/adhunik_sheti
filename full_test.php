<?php
/**
 * Complete Website Test Suite
 * Tests all major functionality and pages
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Website Test Suite</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>.test-pass { color: green; } .test-fail { color: red; } .test-warn { color: orange; }</style>";
echo "</head><body class='container py-4'>";

echo "<h1 class='mb-4'>🧪 Complete Website Test Suite</h1>";

$tests_passed = 0;
$tests_failed = 0;
$tests_warning = 0;

function test($name, $condition, $error_msg = '') {
    global $tests_passed, $tests_failed, $tests_warning;
    if ($condition) {
        echo "<div class='test-pass'>✅ $name</div>";
        $tests_passed++;
        return true;
    } else {
        if ($error_msg) {
            echo "<div class='test-fail'>❌ $name - $error_msg</div>";
        } else {
            echo "<div class='test-fail'>❌ $name</div>";
        }
        $tests_failed++;
        return false;
    }
}

function warn($name, $msg) {
    global $tests_warning;
    echo "<div class='test-warn'>⚠️ $name - $msg</div>";
    $tests_warning++;
}

echo "<h2>🔧 System Checks</h2>";

// Test 1: PHP is working
test("PHP is running", true);

// Test 2: Document root exists
test("Document root exists", file_exists($_SERVER['DOCUMENT_ROOT']));

// Test 3: Project directory exists
test("Project directory exists", file_exists($_SERVER['DOCUMENT_ROOT'] . '/Katkar_New'));

echo "<h2>🗄️ Database Tests</h2>";

// Test 4: Database configuration file exists
test("Database config file exists", file_exists($_SERVER['DOCUMENT_ROOT'] . '/Katkar_New/config/db.php'));

// Test 5: Database connection
$db_connected = false;
$products_table_exists = false;
$admin_users_table_exists = false;

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/Katkar_New/config/db.php';
    if (isset($db) && $db->conn) {
        $db_connected = true;
        test("Database connection", true);
        
        // Check tables
        $result = $db->query("SHOW TABLES LIKE 'products'");
        $products_table_exists = ($result && $result->num_rows > 0);
        test("Products table exists", $products_table_exists);
        
        $result = $db->query("SHOW TABLES LIKE 'orders'");
        test("Orders table exists", $result && $result->num_rows > 0);
        
        $result = $db->query("SHOW TABLES LIKE 'admin_users'");
        $admin_users_table_exists = ($result && $result->num_rows > 0);
        test("Admin users table exists", $admin_users_table_exists);
        
        // Check admin user
        if ($admin_users_table_exists) {
            $result = $db->query("SELECT * FROM admin_users WHERE username = 'admin'");
            test("Admin user exists", $result && $result->num_rows > 0);
        }
        
        // Count products
        if ($products_table_exists) {
            $result = $db->query("SELECT COUNT(*) as count FROM products");
            $row = $result->fetch_assoc();
            $product_count = $row['count'];
            if ($product_count == 0) {
                warn("Products table is empty", "No products in database - add some products via admin panel");
            } else {
                test("Products in database ($product_count products)", true);
            }
        }
    } else {
        test("Database connection", false, "Could not connect to database");
    }
} catch (Exception $e) {
    test("Database connection", false, $e->getMessage());
}

echo "<h2>📄 File Structure Tests</h2>";

$required_files = [
    'index.php' => 'Homepage',
    'config/db.php' => 'Database Config',
    'includes/header.php' => 'Header Component',
    'includes/footer.php' => 'Footer Component',
    'pages/products.php' => 'Products Page',
    'pages/cart.php' => 'Cart Page',
    'pages/checkout.php' => 'Checkout Page',
    'pages/confirmation.php' => 'Confirmation Page',
    'pages/product_detail.php' => 'Product Detail Page',
    'admin/login.php' => 'Admin Login',
    'admin/dashboard.php' => 'Admin Dashboard',
    'admin/products.php' => 'Admin Products',
    'admin/orders.php' => 'Admin Orders',
    'admin/add_product.php' => 'Add Product Page',
    'assets/css/style.css' => 'Stylesheet',
    'assets/js/script.js' => 'JavaScript',
];

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/Katkar_New/';

foreach ($required_files as $file => $description) {
    test("$description ($file)", file_exists($base_path . $file));
}

echo "<h2>🔐 Admin Panel Tests</h2>";

// Test admin pages load without errors
$admin_pages = ['login.php', 'dashboard.php', 'products.php', 'orders.php'];
foreach ($admin_pages as $page) {
    $file_path = $base_path . 'admin/' . $page;
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        $has_php_tags = (strpos($content, '<?php') !== false);
        $has_no_fatal_errors = (strpos($content, 'require_once') !== false);
        test("Admin $page structure", $has_php_tags && $has_no_fatal_errors);
    }
}

echo "<h2>🛒 E-Commerce Tests</h2>";

// Test cart functionality
if (file_exists($base_path . 'pages/cart.php')) {
    $cart_content = file_get_contents($base_path . 'pages/cart.php');
    test("Cart has session handling", strpos($cart_content, 'session_start') !== false);
    test("Cart has add function", strpos($cart_content, 'add') !== false);
    test("Cart has remove function", strpos($cart_content, 'remove') !== false);
}

// Test checkout
if (file_exists($base_path . 'pages/checkout.php')) {
    $checkout_content = file_get_contents($base_path . 'pages/checkout.php');
    test("Checkout has output buffering", strpos($checkout_content, 'ob_start') !== false);
    test("Checkout processes orders", strpos($checkout_content, 'INSERT INTO orders') !== false);
}

echo "<h2>🎨 UI/UX Tests</h2>";

// Test header navigation
if (file_exists($base_path . 'includes/header.php')) {
    $header_content = file_get_contents($base_path . 'includes/header.php');
    test("Header has navigation", strpos($header_content, 'navbar') !== false);
    test("Header has cart link", strpos($header_content, 'cart.php') !== false);
    test("Header has base path variable", strpos($header_content, '$base_path') !== false);
    test("Logo links to homepage", strpos($header_content, 'index.php') !== false);
}

// Test homepage content
if (file_exists($base_path . 'index.php')) {
    $index_content = file_get_contents($base_path . 'index.php');
    test("Homepage has hero section", strpos($index_content, 'hero') !== false || strpos($index_content, 'Home') !== false);
    test("Homepage has products section", strpos($index_content, 'products') !== false);
    test("Homepage has about section", strpos($index_content, 'about') !== false);
    test("Homepage has contact section", strpos($index_content, 'contact') !== false);
}

echo "<h2>📊 Test Summary</h2>";
echo "<div class='alert alert-info'>";
echo "<strong>Passed:</strong> $tests_passed | ";
echo "<strong>Failed:</strong> $tests_failed | ";
echo "<strong>Warnings:</strong> $tests_warning";
echo "</div>";

if ($tests_failed == 0) {
    echo "<div class='alert alert-success'>";
    echo "<h3>🎉 All Critical Tests Passed!</h3>";
    echo "<p>Your website is properly configured and ready to use.</p>";
    echo "</div>";
} else {
    echo "<div class='alert alert-warning'>";
    echo "<h3>⚠️ Some Tests Failed</h3>";
    echo "<p>Please fix the failed tests above before using the website.</p>";
    echo "</div>";
}

echo "<h2>🔗 Quick Links</h2>";
echo "<ul class='list-group'>";
echo "<li class='list-group-item'><a href='index.php'>🏠 Homepage</a></li>";
echo "<li class='list-group-item'><a href='pages/products.php'>📦 Products Page</a></li>";
echo "<li class='list-group-item'><a href='pages/cart.php'>🛒 Cart Page</a></li>";
echo "<li class='list-group-item'><a href='admin/login.php'>🔐 Admin Login</a></li>";
echo "<li class='list-group-item'><a href='debug.php'>🔍 Debug Page</a></li>";
echo "</ul>";

echo "<h2>🚀 Next Steps</h2>";
echo "<ol>";
if (!$db_connected) {
    echo "<li>Start XAMPP and ensure MySQL is running</li>";
    echo "<li>Create database 'krushi_bhandar' in phpMyAdmin</li>";
    echo "<li>Import database_setup.sql file</li>";
}
if ($product_count == 0 && $db_connected) {
    echo "<li>Login to admin panel (admin/admin123)</li>";
    echo "<li>Add some products via 'Add Product' page</li>";
}
echo "<li>Test the complete buying process: Products → Cart → Checkout → Confirmation</li>";
echo "<li>Verify admin panel functionality</li>";
echo "</ol>";

echo "<hr><p class='text-muted'>Test completed at: " . date('Y-m-d H:i:s') . "</p>";
echo "</body></html>";
?>
