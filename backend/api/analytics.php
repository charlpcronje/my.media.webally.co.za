<?php
// backend/api/analytics.php
require_once('../config.php');
require_once('../models/AnalyticsRepository.php');
enableCors();

// Get database connection
$db = getDbConnection();
if (!$db) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
    exit;
}

// Initialize analytics repository
$analyticsRepo = new AnalyticsRepository($db);

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(['error' => 'Method not allowed'], 405);
    exit;
}

// Get filters from query parameters
$filters = [];

if (isset($_GET['date_from'])) {
    $filters['date_from'] = $_GET['date_from'];
}

if (isset($_GET['date_to'])) {
    $filters['date_to'] = $_GET['date_to'];
}

// Get analytics data based on the requested type
$type = $_GET['type'] ?? 'overall';

switch ($type) {
    case 'media':
        // Get analytics for a specific media item
        if (!isset($_GET['id'])) {
            sendJsonResponse(['error' => 'Media ID is required'], 400);
            exit;
        }
        
        $mediaId = (int)$_GET['id'];
        $data = $analyticsRepo->getMediaAnalytics($mediaId, $filters);
        sendJsonResponse($data);
        break;
        
    case 'user':
        // Get analytics for a specific user
        if (!isset($_GET['user_name'])) {
            sendJsonResponse(['error' => 'User name is required'], 400);
            exit;
        }
        
        $userName = $_GET['user_name'];
        
        if (isset($_GET['summary']) && $_GET['summary'] === 'true') {
            // Get user summary stats
            $data = $analyticsRepo->getUserSummaryStats($userName);
        } else {
            // Get detailed user analytics
            $data = $analyticsRepo->getUserAnalytics($userName, $filters);
        }
        
        sendJsonResponse($data);
        break;
        
    case 'search':
        // Get search analytics
        $data = $analyticsRepo->getSearchAnalytics($filters);
        sendJsonResponse($data);
        break;
        
    case 'overall':
    default:
        // Get overall analytics
        $data = $analyticsRepo->getOverallAnalytics($filters);
        sendJsonResponse($data);
        break;
}