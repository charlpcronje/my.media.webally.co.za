<?php
// backend/api/chapters.php
require_once('../config.php');
require_once('../models/ChaptersRepository.php');
enableCors();

// Get database connection
$db = getDbConnection();
if (!$db) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
    exit;
}

// Initialize chapters repository
$chaptersRepo = new ChaptersRepository($db);

// Handle request based on method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetRequest($chaptersRepo);
        break;
    case 'POST':
        handlePostRequest($chaptersRepo);
        break;
    case 'PUT':
        handlePutRequest($chaptersRepo);
        break;
    case 'DELETE':
        handleDeleteRequest($chaptersRepo);
        break;
    default:
        sendJsonResponse(['error' => 'Method not allowed'], 405);
        break;
}

/**
 * Handle GET requests
 * @param ChaptersRepository $chaptersRepo
 */
function handleGetRequest($chaptersRepo) {
    // Get chapters for a specific media
    if (isset($_GET['media_id'])) {
        $mediaId = (int)$_GET['media_id'];
        $chapters = $chaptersRepo->getByMediaId($mediaId);
        sendJsonResponse($chapters);
        return;
    }
    
    // Get a specific chapter
    if (isset($_GET['id'])) {
        $chapterId = (int)$_GET['id'];
        $chapter = $chaptersRepo->getById($chapterId);
        
        if (!$chapter) {
            sendJsonResponse(['error' => 'Chapter not found'], 404);
            return;
        }
        
        sendJsonResponse($chapter);
        return;
    }
    
    // No parameters provided, return error
    sendJsonResponse(['error' => 'Missing required parameters'], 400);
}

/**
 * Handle POST requests
 * @param ChaptersRepository $chaptersRepo
 */
function handlePostRequest($chaptersRepo) {
    // Get request data
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    // Validate required fields
    if (!isset($data['media_id']) || !isset($data['title']) || !isset($data['start_time'])) {
        sendJsonResponse(['error' => 'Missing required fields'], 400);
        return;
    }
    
    // Convert string time to float if necessary
    if (is_string($data['start_time']) && strpos($data['start_time'], ':') !== false) {
        $data['start_time'] = convertTimeToSeconds($data['start_time']);
    }
    
    if (isset($data['end_time']) && is_string($data['end_time']) && strpos($data['end_time'], ':') !== false) {
        $data['end_time'] = convertTimeToSeconds($data['end_time']);
    }
    
    // Create new chapter
    $chapter = $chaptersRepo->create($data);
    
    if (!$chapter) {
        sendJsonResponse(['error' => 'Failed to create chapter'], 500);
        return;
    }
    
    sendJsonResponse($chapter, 201);
}

/**
 * Handle PUT requests
 * @param ChaptersRepository $chaptersRepo
 */
function handlePutRequest($chaptersRepo) {
    // Get chapter ID from URL
    $requestUri = $_SERVER['REQUEST_URI'];
    preg_match('/\/chapters\/(\d+)/', $requestUri, $matches);
    
    if (!isset($matches[1])) {
        sendJsonResponse(['error' => 'Chapter ID is required'], 400);
        return;
    }
    
    $chapterId = (int)$matches[1];
    
    // Get request data
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    if (!$data) {
        sendJsonResponse(['error' => 'Invalid JSON data'], 400);
        return;
    }
    
    // Convert string time to float if necessary
    if (isset($data['start_time']) && is_string($data['start_time']) && strpos($data['start_time'], ':') !== false) {
        $data['start_time'] = convertTimeToSeconds($data['start_time']);
    }
    
    if (isset($data['end_time']) && is_string($data['end_time']) && strpos($data['end_time'], ':') !== false) {
        $data['end_time'] = convertTimeToSeconds($data['end_time']);
    }
    
    // Update chapter
    $chapter = $chaptersRepo->update($chapterId, $data);
    
    if (!$chapter) {
        sendJsonResponse(['error' => 'Failed to update chapter'], 500);
        return;
    }
    
    sendJsonResponse($chapter);
}

/**
 * Handle DELETE requests
 * @param ChaptersRepository $chaptersRepo
 */
function handleDeleteRequest($chaptersRepo) {
    // Get chapter ID from URL
    $requestUri = $_SERVER['REQUEST_URI'];
    preg_match('/\/chapters\/(\d+)/', $requestUri, $matches);
    
    if (!isset($matches[1])) {
        sendJsonResponse(['error' => 'Chapter ID is required'], 400);
        return;
    }
    
    $chapterId = (int)$matches[1];
    
    // Delete chapter
    $success = $chaptersRepo->delete($chapterId);
    
    if (!$success) {
        sendJsonResponse(['error' => 'Failed to delete chapter'], 500);
        return;
    }
    
    sendJsonResponse(['success' => true, 'message' => 'Chapter deleted successfully']);
}

/**
 * Convert time string (HH:MM:SS or MM:SS) to seconds
 * @param string $timeString Time string
 * @return float Time in seconds
 */
function convertTimeToSeconds($timeString) {
    $parts = array_map('intval', explode(':', $timeString));
    
    if (count($parts) === 3) {
        // HH:MM:SS
        return $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
    } else if (count($parts) === 2) {
        // MM:SS
        return $parts[0] * 60 + $parts[1];
    }
    
    return (float)$timeString;
}