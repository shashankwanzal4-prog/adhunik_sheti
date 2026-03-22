<?php
/**
 * Database Check Script
 * Checks if database is properly set up
 */

echo "<h1>🗄️ Database Status Check</h1>";

try {
    // Include database configuration
    require_once 'config/db.php';
    
    echo "<h2>✅ Database Configuration: Connected</h2>";
    
    // Check if database exists
    $result = $db->query("SELECT DATABASE() as current_db");
    $row = $result->fetch_assoc();
    $current_db = $row['current_db'];
    
    echo "<h3>Current Database: <strong>$current_db</strong></h3>";
    
    // Check if required tables exist
    $required_tables = ['products', 'orders', 'admin_users', 'order_items'];
    $existing_tables = [];
    $missing_tables = [];
    
    echo "<h3>📋 Table Status:</h3>";
    
    foreach ($required_tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            $existing_tables[] = $table;
            echo "✅ Table '$table': <strong>EXISTS</strong><br>";
            
            // Show record count
            $count_result = $db->query("SELECT COUNT(*) as count FROM $table");
            $count_row = $count_result->fetch_assoc();
            echo "   └─ Records: " . $count_row['count'] . "<br>";
        } else {
            $missing_tables[] = $table;
            echo "❌ Table '$table': <strong>MISSING</strong><br>";
        }
    }
    
    // Check admin user
    echo "<h3>👤 Admin User Check:</h3>";
    $result = $db->query("SELECT username, password FROM admin_users WHERE username = 'admin'");
    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo "✅ Admin user found: <strong>" . $admin['username'] . "</strong><br>";
        echo "   └─ Password type: " . (strlen($admin['password']) < 20 ? "Plain text (testing)" : "Hashed (production)") . "<br>";
    } else {
        echo "❌ Admin user NOT found<br>";
    }
    
    // Summary
    echo "<h2>📊 Summary:</h2>";
    
    if (empty($missing_tables)) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
        echo "<h3>✅ Database is properly set up!</h3>";
        echo "<p>All required tables exist. You don't need to update the database.</p>";
        echo "</div>";
        
        echo "<h3>🔗 What you can do now:</h3>";
        echo "<ul>";
        echo "<li><a href='admin/login.php'>🔐 Try Admin Login</a> (admin/admin123)</li>";
        echo "<li><a href='index.php'>🏠 Visit Homepage</a></li>";
        echo "<li><a href='test.php'>🧪 Run Full Test</a></li>";
        echo "</ul>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "<h3>⚠️ Database needs to be set up!</h3>";
        echo "<p>Missing tables: " . implode(', ', $missing_tables) . "</p>";
        echo "</div>";
        
        echo "<h3>🔧 To fix this:</h3>";
        echo "<ol>";
        echo "<li>Open <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>";
        echo "<li>Make sure database 'krushi_bhandar' exists</li>";
        echo "<li>Click on the 'krushi_bhandar' database</li>";
        echo "<li>Click 'Import' tab</li>";
        echo "<li>Choose file: <code>database_setup.sql</code></li>";
        echo "<li>Click 'Go' button</li>";
        echo "</ol>";
        
        echo "<p><strong>After importing:</strong> <a href='check_database.php'>Refresh this page</a></p>";
    }
    
    // Quick test links
    echo "<h3>🚀 Quick Links:</h3>";
    echo "<ul>";
    echo "<li><a href='admin/fix_admin.php'>🔧 Fix Admin Login</a></li>";
    echo "<li><a href='admin/login.php'>🔐 Admin Login</a></li>";
    echo "<li><a href='index.php'>🏠 Homepage</a></li>";
    echo "<li><a href='pages/products.php'>📦 Products</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Database Connection Failed</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    
    echo "<h3>🔧 To fix this:</h3>";
    echo "<ol>";
    echo "<li>Make sure XAMPP is running (Apache + MySQL)</li>";
    echo "<li>Check MySQL service in XAMPP Control Panel</li>";
    echo "<li>Verify database settings in config/db.php</li>";
    echo "<li>Create database 'krushi_bhandar' in phpMyAdmin</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<p><small><em>Run this script anytime to check database status</em></small></p>";
?>
