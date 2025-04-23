<?php
// API Entry Point
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/utils/Database.php';

// Load controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/MediaController.php';
require_once __DIR__ . '/controllers/RatingController.php';
require_once __DIR__ . '/controllers/AnalyticsController.php';
require_once __DIR__ . '/controllers/AdminController.php';

// Basic routing logic (stub, to be expanded)
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$db = new Database($db_host, $db_user, $db_pass, $db_name);

// Example: Route stub
if (strpos($uri, '/api/auth/login') !== false && $method === 'POST') {
    $controller = new AuthController();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->login($db->getConnection(), $data, $jwt_secret);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
