<?php
// backend/api/session.php
require_once('../config.php');
enableCors();

// Start or resume session
session_start();

// Set session lifetime
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);

// Handle GET request to set user in session
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['name'])) {
    $userName = strtolower(trim($_GET['name']));
    
    // Validate user name (only 'charl' or 'nade' are valid)
    if ($userName === 'charl' || $userName === 'nade') {
        $_SESSION['user_name'] = $userName;
        $_SESSION['session_start_time'] = time();
        
        sendJsonResponse([
            'success' => true,
            'user' => $userName,
            'message' => 'Session started successfully'
        ]);
    } else {
        http_response_code(400);
        sendJsonResponse([
            'error' => 'Invalid user name',
            'message' => 'User name must be either "charl" or "nade"'
        ]);
    }
}
// Handle GET request to get current session info
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['user_name'])) {
        sendJsonResponse([
            'success' => true,
            'user' => $_SESSION['user_name'],
            'session_duration' => time() - ($_SESSION['session_start_time'] ?? time())
        ]);
    } else {
        sendJsonResponse([
            'success' => false,
            'message' => 'No active session'
        ]);
    }
}
// Handle POST request to end session
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['end'])) {
    // Clear session data
    $_SESSION = [];
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
    
    sendJsonResponse([
        'success' => true,
        'message' => 'Session ended successfully'
    ]);
} else {
    http_response_code(405);
    sendJsonResponse([
        'error' => 'Method not allowed',
        'message' => 'Use GET to start or check session, POST with ?end to end session'
    ]);
}