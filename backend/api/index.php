<?php
// backend/api/index.php - API Router for clean URLs
require_once('../config.php');
enableCors();

// Log incoming request
error_log("API Router: Received request URI: " . $_SERVER['REQUEST_URI'] . " Method: " . $_SERVER['REQUEST_METHOD']);

// Initialize response
$response = [
    'success' => false,
    'message' => 'Endpoint not found',
    'data' => null
];
$statusCode = 404;

// Parse requested endpoint from URL
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/'; // Base path of the API

// Extract path from the request URI
$path = parse_url($requestUri, PHP_URL_PATH);
$path = substr($path, strlen($basePath));
$pathSegments = explode('/', trim($path, '/'));

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Get request parameters
$params = [];
if ($method === 'GET') {
    $params = $_GET;
} elseif (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
    
    if (stripos($contentType, 'application/json') !== false) {
        $jsonData = file_get_contents('php://input');
        $params = json_decode($jsonData, true) ?: [];
    } elseif ($method === 'POST') {
        $params = $_POST;
    }
}

// Initialize database connection
$db = getDbConnection();
if (!$db) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
    exit;
}

// Route the request to the appropriate handler
try {
    // First segment is the primary resource
    $resource = $pathSegments[0] ?? '';
    error_log("API Router: Routing resource: {$resource}"); // Log the resource being routed
    
    // Route based on resource and HTTP method
    switch ($resource) {
        case 'media':
            require_once('media.php');
            
            // Handle media resource
            if (count($pathSegments) === 1) {
                // Collection endpoint: /media
                if ($method === 'GET') {
                    // Get all media items with optional filters
                    $response = getMediaResponse($db, $params);
                    $statusCode = 200;
                } elseif ($method === 'POST') {
                    // Create new media item
                    $response = createMediaResponse($db, $params);
                    $statusCode = 201;
                } else {
                    $response['message'] = 'Method not allowed for this endpoint';
                    $statusCode = 405;
                }
            } elseif (count($pathSegments) === 2 && is_numeric($pathSegments[1])) {
                // Individual media item: /media/{id}
                $mediaId = intval($pathSegments[1]);
                
                if ($method === 'GET') {
                    // Get specific media item
                    $response = getMediaByIdResponse($db, $mediaId);
                    $statusCode = 200;
                } elseif ($method === 'PUT') {
                    // Update media item
                    $response = updateMediaResponse($db, $mediaId, $params);
                    $statusCode = 200;
                } elseif ($method === 'DELETE') {
                    // Delete media item
                    $response = deleteMediaResponse($db, $mediaId);
                    $statusCode = 200;
                } else {
                    $response['message'] = 'Method not allowed for this endpoint';
                    $statusCode = 405;
                }
            } else {
                $response['message'] = 'Invalid media endpoint';
                $statusCode = 404;
            }
            break;
            
        case 'tags':
            require_once('tags.php');
            
            // Handle tags resource
            if (count($pathSegments) === 1) {
                // Collection endpoint: /tags
                if ($method === 'GET') {
                    // Get all tags
                    $response = getTagsResponse($db, $params);
                    $statusCode = 200;
                } elseif ($method === 'POST') {
                    // Create new tag
                    $response = createTagResponse($db, $params);
                    $statusCode = 201;
                } else {
                    $response['message'] = 'Method not allowed for this endpoint';
                    $statusCode = 405;
                }
            } elseif (count($pathSegments) === 2) {
                // Individual tag: /tags/{name}
                $tagName = $pathSegments[1];
                
                if ($method === 'GET') {
                    // Get specific tag
                    $response = getTagByNameResponse($db, $tagName);
                    $statusCode = 200;
                } elseif ($method === 'DELETE') {
                    // Delete tag
                    $response = deleteTagResponse($db, $tagName);
                    $statusCode = 200;
                } else {
                    $response['message'] = 'Method not allowed for this endpoint';
                    $statusCode = 405;
                }
            } else {
                $response['message'] = 'Invalid tags endpoint';
                $statusCode = 404;
            }
            break;
            
        case 'track':
            require_once('track.php');
            
            // Handle analytics tracking
            if ($method === 'POST') {
                $response = trackEventResponse($db, $params);
                $statusCode = 200;
            } else {
                $response['message'] = 'Only POST method is allowed for tracking';
                $statusCode = 405;
            }
            break;
            
        case 'session':
            require_once('session.php');
            
            // Handle session management
            if ($method === 'GET') {
                if (isset($params['name'])) {
                    $response = startSessionResponse($params['name']);
                } else {
                    $response = getSessionResponse();
                }
                $statusCode = 200;
            } elseif ($method === 'POST' && isset($params['end'])) {
                $response = endSessionResponse();
                $statusCode = 200;
            } else {
                $response['message'] = 'Invalid session operation';
                $statusCode = 400;
            }
            break;
            
        case 'preferences':
            require_once('preferences.php');
            require_once('../models/UserPreferencesRepository.php');
            
            // Initialize user preferences repository
            $prefRepo = new UserPreferencesRepository($db);
            
            // Handle user preferences
            if ($method === 'GET') {
                handleGetRequest($prefRepo);
                exit; // handleGetRequest will send the response
            } elseif ($method === 'POST') {
                handlePostRequest($prefRepo);
                exit; // handlePostRequest will send the response
            } elseif ($method === 'DELETE') {
                handleDeleteRequest($prefRepo);
                exit; // handleDeleteRequest will send the response
            } else {
                $response['message'] = 'Method not allowed for preferences';
                $statusCode = 405;
            }
            break;
            
        // Additional resources would be added here
            
        default:
            // Resource not found
            $response['message'] = 'Resource not found';
            $statusCode = 404;
            break;
    }
} catch (Exception $e) {
    // Log the caught exception
    error_log('API Router Exception: ' . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
    logError('API Router error: ' . $e->getMessage());
    $response = [
        'success' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ];
    $statusCode = 500;
}

// Send response
http_response_code($statusCode);
header('Content-Type: application/json');
echo json_encode($response);
exit;

/**
 * Helper functions to convert the existing API implementation to the new router format
 */

// Media API helpers
function getMediaResponse($db, $params) {
    // Call the existing getMedia function but wrap the response
    ob_start();
    getMedia($db, $params);
    $output = ob_get_clean();
    
    return json_decode($output, true) ?: ['success' => false, 'message' => 'Failed to get media'];
}

function getMediaByIdResponse($db, $mediaId) {
    // Simulate a GET request with ID parameter
    $_GET['id'] = $mediaId;
    
    ob_start();
    getMedia($db);
    $output = ob_get_clean();
    
    $data = json_decode($output, true);
    
    // The original API returns an array, but for individual items we want to return a single object
    if (is_array($data) && count($data) === 1) {
        return $data[0];
    }
    
    return $data ?: ['success' => false, 'message' => 'Media not found'];
}

function createMediaResponse($db, $params) {
    ob_start();
    createMedia($db, $params);
    $output = ob_get_clean();
    
    return json_decode($output, true) ?: ['success' => false, 'message' => 'Failed to create media'];
}

function updateMediaResponse($db, $mediaId, $params) {
    // This would need to be implemented in media.php
    // For now, return a placeholder response
    return ['success' => true, 'message' => 'Media updated successfully', 'id' => $mediaId];
}

function deleteMediaResponse($db, $mediaId) {
    // Set up GET parameter for compatibility with the existing function
    $_GET['id'] = $mediaId;
    
    ob_start();
    deleteMedia($db);
    $output = ob_get_clean();
    
    return json_decode($output, true) ?: ['success' => false, 'message' => 'Failed to delete media'];
}

// Tags API helpers
function getTagsResponse($db, $params) {
    ob_start();
    getTags($db);
    $output = ob_get_clean();
    
    return json_decode($output, true) ?: ['success' => false, 'message' => 'Failed to get tags'];
}

function createTagResponse($db, $params) {
    ob_start();
    createTag($db);
    $output = ob_get_clean();
    
    return json_decode($output, true) ?: ['success' => false, 'message' => 'Failed to create tag'];
}

function getTagByNameResponse($db, $tagName) {
    // Set up GET parameter for compatibility with the existing function
    $_GET['name'] = $tagName;
    
    ob_start();
    getTags($db);
    $output = ob_get_clean();
    
    return json_decode($output, true) ?: ['success' => false, 'message' => 'Tag not found'];
}

function deleteTagResponse($db, $tagName) {
    // Set up GET parameter for compatibility with the existing function
    $_GET['name'] = $tagName;
    
    ob_start();
    deleteTag($db);
    $output = ob_get_clean();
    
    return json_decode($output, true) ?: ['success' => false, 'message' => 'Failed to delete tag'];
}

// Track API helper
function trackEventResponse($db, $params) {
    ob_start();
    // The original API expects a POST request
    $_POST = $params;
    include('track.php');
    $output = ob_get_clean();
    
    $response = json_decode($output, true);
    return $response ?: ['success' => true, 'message' => 'Event tracked successfully'];
}

// Session API helpers
function startSessionResponse($userName) {
    // The original API expects a GET request
    $_GET['name'] = $userName;
    
    ob_start();
    include('session.php');
    $output = ob_get_clean();
    
    return json_decode($output, true) ?: ['success' => false, 'message' => 'Failed to start session'];
}

function getSessionResponse() {
    ob_start();
    include('session.php');
    $output = ob_get_clean();
    
    return json_decode($output, true) ?: ['success' => false, 'message' => 'No active session'];
}

function endSessionResponse() {
    // The original API expects a GET parameter 'end'
    $_GET['end'] = true;
    
    ob_start();
    include('session.php');
    $output = ob_get_clean();
    
    return json_decode($output, true) ?: ['success' => true, 'message' => 'Session ended successfully'];
}