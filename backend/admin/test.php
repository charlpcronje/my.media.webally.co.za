<?php
// Simple test file to check if PHP is executing properly
echo "<h1>PHP Test Page</h1>";
echo "<p>PHP is working in the admin directory!</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";

// Check session functionality
session_start();
if (!isset($_SESSION['test_count'])) {
    $_SESSION['test_count'] = 1;
    echo "<p>Session started for the first time.</p>";
} else {
    $_SESSION['test_count']++;
    echo "<p>Session count: " . $_SESSION['test_count'] . "</p>";
}

// Check database connection
require_once('../config.php');
echo "<h2>Database Connection Test</h2>";
try {
    $conn = getDbConnection();
    if ($conn) {
        echo "<p style='color:green'>Database connection successful!</p>";
        
        // Check if admin user exists
        $stmt = $conn->prepare("SELECT id, username FROM admin_users WHERE username = ?");
        $stmt->execute(['admin']);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<p>Admin user found with ID: " . $user['id'] . "</p>";
        } else {
            echo "<p style='color:red'>Admin user not found. You may need to run the database initialization script.</p>";
        }
    } else {
        echo "<p style='color:red'>Database connection failed.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Show server information
echo "<h2>Server Information</h2>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
?>
