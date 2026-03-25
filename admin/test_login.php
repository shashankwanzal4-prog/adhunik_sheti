<?php
/**
 * Quick Admin Login Test
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/Adhunik_Sheti/config/db.php';

echo "<h1>🔐 Admin Login Test</h1>";

// Check if admin user exists
$result = $db->query("SELECT * FROM admin_users WHERE username = 'admin'");
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<h2>✅ Admin user found</h2>";
    echo "<p>Username: " . $user['username'] . "</p>";
    echo "<p>Password hash: " . substr($user['password'], 0, 20) . "...</p>";
    echo "<p>Password length: " . strlen($user['password']) . "</p>";
    
    // Test password
    $test_password = 'admin123';
    if (password_verify($test_password, $user['password'])) {
        echo "<h2>✅ Password verify works (hashed)</h2>";
    } elseif ($test_password === $user['password']) {
        echo "<h2>✅ Plain text password match</h2>";
    } else {
        echo "<h2>❌ Password doesn't match</h2>";
        echo "<p>Updating password to 'admin123'...</p>";
        
        // Update password to plain text for testing
        $update = $db->query("UPDATE admin_users SET password = 'admin123' WHERE username = 'admin'");
        if ($update) {
            echo "<h2>✅ Password updated! Try logging in now.</h2>";
        }
    }
} else {
    echo "<h2>❌ Admin user not found</h2>";
    echo "<p>Creating admin user...</p>";
    
    $create = $db->query("INSERT INTO admin_users (username, password, email, full_name, role) VALUES ('admin', 'admin123', 'admin@krushibhandar.com', 'Administrator', 'admin')");
    if ($create) {
        echo "<h2>✅ Admin user created! Try logging in now.</h2>";
    } else {
        echo "<h2>❌ Error: " . $db->conn->error . "</h2>";
    }
}

echo "<hr>";
echo "<h3>🔗 Next Steps:</h3>";
echo "<ol>";
echo "<li><a href='login.php'>Try Admin Login</a> (admin / admin123)</li>";
echo "<li>If it works, <a href='dashboard.php'>Go to Dashboard</a></li>";
echo "<li>Or <a href='../test.php'>Run Full Test</a></li>";
echo "</ol>";
?>
