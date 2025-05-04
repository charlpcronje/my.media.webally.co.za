<?php
// Enable error reporting for all PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to a file (optional)
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

// Set a generous execution time for debugging
ini_set('max_execution_time', 300); // 5 minutes

// Increase memory limit if needed
ini_set('memory_limit', '256M');
?>
