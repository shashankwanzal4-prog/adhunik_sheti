<?php
/**
 * Admin Index - Redirect to Dashboard
 */

// Include database configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/Katkar_New/config/db.php';

// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Redirect to dashboard
header('Location: dashboard.php');
exit;
?>
