<?php
// Simple test file to check if PHP is working
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'Test file is accessible',
    'time' => date('Y-m-d H:i:s')
]);
?>
