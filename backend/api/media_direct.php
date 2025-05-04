<?php
// Direct test file for media endpoint
header('Content-Type: application/json');

// Force error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo json_encode([
    'status' => 'success',
    'message' => 'Direct media access works',
    'time' => date('Y-m-d H:i:s'),
    'server_path' => __FILE__,
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
]);
?>
