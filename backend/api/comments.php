<?php
// backend/api/comments.php
require_once('../config.php');
require_once('../models/CommentsRepository.php');
enableCors();

// Get database connection
$db = getDbConnection();
if (!$db) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
    exit;
}

// Initialize comments repository
$commentsRepo = new CommentsRepository($db);

// Handle request based on method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetRequest($commentsRepo);
        break;
    case 'POST':
        handlePostRequest($commentsRepo);
        break;
    case 'PUT':
        handlePutRequest($commentsRepo);
        break;
    case 'DELETE':
        handleDeleteRequest($commentsRepo);
        break;
    default:
        sendJsonResponse(['error' => 'Method not allowed'], 405);
        break;
}

/**
 * Handle GET requests
 * @param CommentsRepository $commentsRepo
 */
function handleGetRequest($commentsRepo) {
    // Get comments for a specific media
    if (isset($_GET['media_id'])) {
        $mediaId = (int)$_GET['media_id'];
        $chapterId = isset($_GET['chapter_id']) ? (int)$_GET['chapter_id'] : null;
        $comments = $commentsRepo->getByMediaId($mediaId, $chapterId);
        sendJsonResponse($comments);
        return;
    }
    
    // Get a specific comment
    if (isset($_GET['id'])) {
        $commentId = (int)$_GET['id'];
        $comment = $commentsRepo->getById($commentId);
        
        if (!$comment) {
            sendJsonResponse(['error' => 'Comment not found'], 404);
            return;
        }
        
        sendJsonResponse($comment);
        return;
    }
    
    // Get recent comments
    if (isset($_GET['recent'])) {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $comments = $commentsRepo->getRecentComments($limit);
        sendJsonResponse($comments);
        return;
    }
    
    // No parameters provided, return error
    sendJsonResponse(['error' => 'Missing required parameters'], 400);
}

/**
 * Handle POST requests
 * @param CommentsRepository $commentsRepo
 */
function handlePostRequest($commentsRepo) {
    // Get request data
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    // Validate required fields
    if (!isset($data['media_id']) || !isset($data['user_name']) || !isset($data['comment'])) {
        sendJsonResponse(['error' => 'Missing required fields'], 400);
        return;
    }
    
    // Create new comment
    $comment = $commentsRepo->create($data);
    
    if (!$comment) {
        sendJsonResponse(['error' => 'Failed to create comment'], 500);
        return;
    }
    
    sendJsonResponse($comment, 201);
}

/**
 * Handle PUT requests
 * @param CommentsRepository $commentsRepo
 */
function handlePutRequest($commentsRepo) {
    // Get comment ID from URL
    $requestUri = $_SERVER['REQUEST_URI'];
    preg_match('/\/comments\/(\d+)/', $requestUri, $matches);
    
    if (!isset($matches[1])) {
        sendJsonResponse(['error' => 'Comment ID is required'], 400);
        return;
    }
    
    $commentId = (int)$matches[1];
    
    // Get request data
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    if (!$data) {
        sendJsonResponse(['error' => 'Invalid JSON data'], 400);
        return;
    }
    
    // Get user name from request, required for permission check
    if (!isset($data['user_name'])) {
        sendJsonResponse(['error' => 'User name is required'], 400);
        return;
    }
    
    // Update comment
    $comment = $commentsRepo->update($commentId, $data, $data['user_name']);
    
    if (!$comment) {
        sendJsonResponse(['error' => 'Failed to update comment or insufficient permissions'], 403);
        return;
    }
    
    sendJsonResponse($comment);
}

/**
 * Handle DELETE requests
 * @param CommentsRepository $commentsRepo
 */
function handleDeleteRequest($commentsRepo) {
    // Get comment ID from URL
    $requestUri = $_SERVER['REQUEST_URI'];
    preg_match('/\/comments\/(\d+)/', $requestUri, $matches);
    
    if (!isset($matches[1])) {
        sendJsonResponse(['error' => 'Comment ID is required'], 400);
        return;
    }
    
    $commentId = (int)$matches[1];
    
    // Get user name from query string, required for permission check
    if (!isset($_GET['user_name'])) {
        sendJsonResponse(['error' => 'User name is required'], 400);
        return;
    }
    
    $userName = $_GET['user_name'];
    
    // Delete comment
    $success = $commentsRepo->delete($commentId, $userName);
    
    if (!$success) {
        sendJsonResponse(['error' => 'Failed to delete comment or insufficient permissions'], 403);
        return;
    }
    
    sendJsonResponse(['success' => true, 'message' => 'Comment deleted successfully']);
}