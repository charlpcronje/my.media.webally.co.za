<?php
// backend/api/track.php
require_once('../config.php');
require_once('../models/AnalyticsRepository.php');
enableCors();

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user name from session
if (!isset($_SESSION['user_name'])) {
    sendJsonResponse(['error' => 'User not logged in or session expired'], 401);
    exit;
}
$userName = $_SESSION['user_name'];

// Get database connection
$db = getDbConnection();
if (!$db) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
    exit;
}

// Initialize analytics repository
$analyticsRepo = new AnalyticsRepository($db);

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Method not allowed'], 405);
    exit;
}

// Get request data
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if (!$data) {
    $data = $_POST;
}

// Validate required fields
if (!isset($data['media_id']) || !isset($data['event_type'])) {
    sendJsonResponse(['error' => 'Missing required fields (media_id, event_type)'], 400);
    exit;
}

// Add user_name from session to data for recordEvent
$data['user_name'] = $userName;

// Special handling for search events
if ($data['event_type'] === 'search') {
    if (!isset($data['search_term'])) {
        sendJsonResponse(['error' => 'Search term is required for search events'], 400);
        exit;
    }
    
    $filters = isset($data['filters']) ? $data['filters'] : [];
    $resultsCount = isset($data['results_count']) ? (int)$data['results_count'] : 0;
    
    $result = $analyticsRepo->trackSearch(
        $data['search_term'],
        $filters,
        $userName,
        $resultsCount
    );
    
    if ($result === false) {
        sendJsonResponse(['error' => 'Failed to track search'], 500);
    } else {
        sendJsonResponse(['success' => true, 'id' => $result]);
    }
    
    exit;
}

// Record analytics event
$result = $analyticsRepo->recordEvent($data);

if ($result === false) {
    sendJsonResponse(['error' => 'Failed to record event'], 500);
} else {
    sendJsonResponse(['success' => true, 'id' => $result]);
}