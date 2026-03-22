<?php
/**
 * Debug Page - Check what's causing blank screens
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Debugging Blank Page Issue</h1>";

echo "<h2>Step 1: Basic PHP Check</h2>";
echo "<p>✅ PHP is working - you can see this message</p>";

echo "<h2>Step 2: Check Document Root</h2>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current File: " . __FILE__ . "</p>";

echo "<h2>Step 3: Test Database Connection</h2>";
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/Katkar_New/config/db.php';
    echo "<p>✅ Database configuration loaded</p>";
    
    if (isset($db) && $db->conn) {
        echo "<p>✅ Database connection successful</p>";
        
        // Test query
        $result = $db->query("SELECT 1 as test");
        if ($result) {
            echo "<p>✅ Database query works</p>";
        }
    } else {
        echo "<p>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Step 4: Check Products Table</h2>";
if (isset($db)) {
    $result = $db->query("SHOW TABLES LIKE 'products'");
    if ($result && $result->num_rows > 0) {
        echo "<p>✅ Products table exists</p>";
        
        $count_result = $db->query("SELECT COUNT(*) as count FROM products");
        $count_row = $count_result->fetch_assoc();
        echo "<p>Products count: " . $count_row['count'] . "</p>";
        
        if ($count_row['count'] == 0) {
            echo "<p>⚠️ Products table is EMPTY - need to add products</p>";
        }
    } else {
        echo "<p>❌ Products table does NOT exist</p>";
        echo "<p>💡 You need to import database_setup.sql in phpMyAdmin</p>";
    }
}

echo "<h2>Step 5: Check Header File</h2>";
$header_path = $_SERVER['DOCUMENT_ROOT'] . '/Katkar_New/includes/header.php';
if (file_exists($header_path)) {
    echo "<p>✅ Header file exists: $header_path</p>";
} else {
    echo "<p>❌ Header file NOT found: $header_path</p>";
}

echo "<h2>Step 6: Test Include Header</h2>";
try {
    // Don't actually include it as it will output HTML, just check if it loads
    $header_content = file_get_contents($header_path);
    if ($header_content !== false) {
        echo "<p>✅ Header file readable</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Cannot read header file: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>🔗 Quick Links:</h2>";
echo "<ul>";
echo "<li><a href='index.php'>🏠 Try Homepage</a> (should show errors now)</li>";
echo "<li><a href='pages/products.php'>📦 Try Products Page</a></li>";
echo "<li><a href='admin/login.php'>🔐 Try Admin Login</a></li>";
echo "<li><a href='simple_test.php'>🧪 Run Simple Test</a></li>";
echo "</ul>";

echo "<h2>🔧 Common Fixes:</h2>";
echo "<ol>";
echo "<li>Make sure XAMPP is running (Apache + MySQL)</li>";
echo "<li>Import database_setup.sql in phpMyAdmin</li>";
echo "<li>Check that database 'krushi_bhandar' exists</li>";
echo "<li>Verify config/db.php has correct credentials</li>";
echo "</ol>";
?>
