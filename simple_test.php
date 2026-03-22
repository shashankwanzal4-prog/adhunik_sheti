<?php
echo "<h1>🧪 Simple Test</h1>";

// Test 1: Basic PHP
echo "<h2>✅ PHP is working</h2>";

// Test 2: Database connection
try {
    require_once 'config/db.php';
    echo "<h2>✅ Database connected</h2>";
    
    // Test 3: Check admin users
    $result = $db->query("SELECT username, password FROM admin_users");
    if ($result && $result->num_rows > 0) {
        echo "<h2>✅ Found " . $result->num_rows . " admin users:</h2>";
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['username'] . " (password: " . substr($row['password'], 0, 10) . "...)<br>";
        }
    } else {
        echo "<h2>❌ No admin users found</h2>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Database error: " . $e->getMessage() . "</h2>";
}

// Test 4: Links
echo "<h2>🔗 Test Links:</h2>";
echo "<a href='index.php'>Homepage</a><br>";
echo "<a href='admin/login.php'>Admin Login</a><br>";
echo "<a href='admin/fix_admin.php'>Fix Admin</a><br>";

echo "<hr>";
echo "<p><small>If you see this, XAMPP is working!</small></p>";
?>
