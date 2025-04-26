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

// Routing logic for all API endpoints
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$db = new Database($db_host, $db_user, $db_pass, $db_name);

// Auth endpoints
if ($method === 'POST' && preg_match('#/api/auth/login$#', $uri)) {
    $controller = new AuthController();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->login($db->getConnection(), $data, $jwt_secret);
} elseif ($method === 'POST' && preg_match('#/api/auth/logout$#', $uri)) {
    $controller = new AuthController();
    $controller->logout();
} elseif ($method === 'GET' && preg_match('#/api/auth/user$#', $uri)) {
    $controller = new AuthController();
    $controller->getCurrentUser();

// Media endpoints
} elseif ($method === 'GET' && preg_match('#/api/media/?$#', $uri)) {
    $controller = new MediaController();
    $controller->listMedia($db->getConnection());
} elseif ($method === 'GET' && preg_match('#/api/media/(\d+)$#', $uri, $matches)) {
    $controller = new MediaController();
    $controller->getMedia($db->getConnection(), $matches[1]);
} elseif ($method === 'GET' && preg_match('#/api/media/(\d+)/play$#', $uri, $matches)) {
    $controller = new MediaController();
    $controller->playMedia($db->getConnection(), $matches[1]);
} elseif ($method === 'POST' && preg_match('#/api/media/?$#', $uri)) {
    $controller = new MediaController();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->addMedia($db->getConnection(), $data);
} elseif ($method === 'PUT' && preg_match('#/api/media/(\d+)$#', $uri, $matches)) {
    $controller = new MediaController();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->updateMedia($db->getConnection(), $matches[1], $data);
} elseif ($method === 'DELETE' && preg_match('#/api/media/(\d+)$#', $uri, $matches)) {
    $controller = new MediaController();
    $controller->deleteMedia($db->getConnection(), $matches[1]);

// Rating endpoints
} elseif ($method === 'POST' && preg_match('#/api/media/(\d+)/rate$#', $uri, $matches)) {
    $controller = new RatingController();
    $data = json_decode(file_get_contents('php://input'), true);
    // Assume user_id is from session/JWT in real implementation
    $user_id = isset($data['user_id']) ? $data['user_id'] : null;
    $controller->rateMedia($db->getConnection(), $matches[1], $user_id, $data['rating'] ?? null);
} elseif ($method === 'GET' && preg_match('#/api/media/(\d+)/rating$#', $uri, $matches)) {
    $controller = new RatingController();
    // Assume user_id is from session/JWT in real implementation
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $controller->getUserRating($db->getConnection(), $matches[1], $user_id);

// Analytics endpoints
} elseif ($method === 'POST' && preg_match('#/api/analytics/start$#', $uri)) {
    $controller = new AnalyticsController();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->trackStart($db->getConnection(), $data);
} elseif ($method === 'POST' && preg_match('#/api/analytics/end$#', $uri)) {
    $controller = new AnalyticsController();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->trackEnd($db->getConnection(), $data);
} elseif ($method === 'POST' && preg_match('#/api/analytics/skip$#', $uri)) {
    $controller = new AnalyticsController();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->trackSkip($db->getConnection(), $data);
} elseif ($method === 'GET' && preg_match('#/api/analytics/?$#', $uri)) {
    $controller = new AnalyticsController();
    $controller->getAnalytics($db->getConnection(), $_GET);

// Admin endpoints
} elseif ($method === 'GET' && preg_match('#/api/admin/users/?$#', $uri)) {
    $controller = new AdminController();
    $controller->listUsers($db->getConnection());
} elseif ($method === 'POST' && preg_match('#/api/admin/users/?$#', $uri)) {
    $controller = new AdminController();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->createUser($db->getConnection(), $data);
} elseif ($method === 'PUT' && preg_match('#/api/admin/users/(\d+)$#', $uri, $matches)) {
    $controller = new AdminController();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->updateUser($db->getConnection(), $matches[1], $data);
} elseif ($method === 'DELETE' && preg_match('#/api/admin/users/(\d+)$#', $uri, $matches)) {
    $controller = new AdminController();
    $controller->deleteUser($db->getConnection(), $matches[1]);
} elseif ($method === 'GET' && preg_match('#/api/admin/dashboard$#', $uri)) {
    $controller = new AdminController();
    $controller->dashboard($db->getConnection());

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}

