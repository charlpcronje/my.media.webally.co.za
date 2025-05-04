<?php
// First, make sure error reporting is enabled
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the backend config file which has our enhanced error handling
require_once('./backend/config.php');

echo "<h1>PHP Error Testing Page</h1>";
echo "<p>This page intentionally generates errors to test error reporting.</p>";

// Test 1: Undefined variable (Notice)
echo "<h2>Test 1: Undefined Variable (Notice)</h2>";
echo "Attempting to use undefined variable: $undefined_variable";

// Test 2: Division by zero (Warning)
echo "<h2>Test 2: Division by Zero (Warning)</h2>";
$division = 10 / 0;
echo "Result of division by zero: $division";

// Test 3: Call to undefined function (Fatal Error)
echo "<h2>Test 3: Call to Undefined Function (Fatal Error)</h2>";
echo "This test will end execution if errors are properly displayed.<br>";
echo "If you see this message but no error below, then fatal errors are not being displayed.<br>";
nonexistent_function();

// This line will never be reached if fatal errors are displayed
echo "<p>If you see this text, fatal errors are NOT being displayed properly.</p>";
?>
