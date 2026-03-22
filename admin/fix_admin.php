<?php
/**
 * Admin Login Fix Script
 * Resets admin password to plain text for testing
 */

// Include database configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/Katkar_New/config/db.php';

echo "<h1>🔧 Admin Login Fix</h1>";

// Check database connection
if (!isset($db) || !$db->conn) {
    echo "<h2>❌ Database connection failed</h2>";
    echo "<p>Please check your database configuration in config/db.php</p>";
    exit;
}

echo "<h2>✅ Database connected successfully</h2>";

// Check if admin_users table exists
$result = $db->query("SHOW TABLES LIKE 'admin_users'");
if ($result && $result->num_rows > 0) {
    echo "<h2>✅ admin_users table exists</h2>";
    
    // Update admin password to plain text for testing
    $plain_password = 'admin123';
    $username = 'admin';
    
    // First, try to update existing admin
    $update_sql = "UPDATE admin_users SET password = ? WHERE username = ?";
    $stmt = $db->prepare($update_sql);
    $stmt->bind_param("ss", $plain_password, $username);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<h2>✅ Admin password updated successfully!</h2>";
        } else {
            echo "<h2>⚠️ Admin user not found, creating new admin user...</h2>";
            
            // Create new admin user
            $insert_sql = "INSERT INTO admin_users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)";
            $email = 'admin@krushibhandar.com';
            $full_name = 'Administrator';
            $role = 'admin';
            
            $stmt = $db->prepare($insert_sql);
            $stmt->bind_param("sssss", $username, $plain_password, $email, $full_name, $role);
            
            if ($stmt->execute()) {
                echo "<h2>✅ New admin user created successfully!</h2>";
            } else {
                echo "<h2>❌ Failed to create admin user: " . $stmt->error . "</h2>";
            }
        }
    } else {
        echo "<h2>❌ Failed to update admin password: " . $stmt->error . "</h2>";
    }
    
    // Show current admin users
    echo "<h3>Current Admin Users:</h3>";
    $result = $db->query("SELECT username, email, full_name, role, created_at FROM admin_users");
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Username</th><th>Email</th><th>Full Name</th><th>Role</th><th>Created</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} else {
    echo "<h2>❌ admin_users table doesn't exist</h2>";
    echo "<p>Please import the database_setup.sql file first:</p>";
    echo "<ol>";
    echo "<li>Go to phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
    echo "<li>Create database 'krushi_bhandar'</li>";
    echo "<li>Import the database_setup.sql file</li>";
    echo "<li>Run this fix script again</li>";
    echo "</ol>";
}

echo "<h2>🔑 Login Credentials:</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<strong>Username:</strong> admin<br>";
echo "<strong>Password:</strong> admin123";
echo "</div>";

echo "<h2>🔗 Links:</h2>";
echo "<ul>";
echo "<li><a href='login.php'>🔐 Go to Admin Login</a></li>";
echo "<li><a href='../test.php'>🧪 Run Test Script</a></li>";
echo "<li><a href='../index.php'>🏠 Go to Homepage</a></li>";
echo "</ul>";

echo "<h2>🚀 Next Steps:</h2>";
echo "<ol>";
echo "<li>Try logging in with: admin / admin123</li>";
echo "<li>If it works, delete this fix_admin.php file for security</li>";
echo "<li>Consider updating to hashed passwords in production</li>";
echo "</ol>";
?>
