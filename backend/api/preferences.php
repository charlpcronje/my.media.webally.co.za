<?php
// backend/api/preferences.php
// Force error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log("Preferences API: Script started.");

// Include config for DB connection etc.
require_once('../config.php'); 

// Include the error display helper if it exists
if (file_exists('./display_errors.php')) {
    require_once('./display_errors.php');
}

require_once('../models/UserPreferencesRepository.php');
enableCors();

// Debug information
error_log("Preferences API: Script started.");

// Get database connection
$db = getDbConnection();
if (!$db) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
    exit;
}

// Initialize user preferences repository
$prefRepo = new UserPreferencesRepository($db);

// Handle request based on method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetRequest($prefRepo);
        break;
    case 'POST':
        handlePostRequest($prefRepo);
        break;
    case 'DELETE':
        handleDeleteRequest($prefRepo);
        break;
    default:
        sendJsonResponse(['error' => 'Method not allowed'], 405);
        break;
}

/**
 * Handle GET requests
 * @param UserPreferencesRepository $prefRepo
 */
function handleGetRequest($prefRepo) {
    error_log("Preferences API: handleGetRequest called.");

    // Get user name from query parameter
    if (!isset($_GET['user_name']) || empty(trim($_GET['user_name']))) {
        error_log("Preferences API: user_name query parameter is missing or empty.");
        sendJsonResponse(['error' => 'user_name query parameter is required'], 400); // 400 Bad Request
        return;
    }
    $userName = trim($_GET['user_name']);
    error_log("Preferences API: Received user_name '{$userName}' from query parameter.");

    // Get user preferences
    $preferences = $prefRepo->getByUserName($userName);
    
    if (!$preferences) {
        // Return default preferences if none found
        sendJsonResponse($prefRepo->getDefaultPreferences());
        return;
    }
    
    sendJsonResponse($preferences);
}

/**
 * Handle POST requests
 * @param UserPreferencesRepository $prefRepo
 */
function handlePostRequest($prefRepo) {
    error_log("Preferences API: handlePostRequest called.");

    // Get user name from query parameter (assuming it's always passed)
    if (!isset($_GET['user_name']) || empty(trim($_GET['user_name']))) {
        error_log("Preferences API: user_name query parameter is missing or empty for POST.");
        sendJsonResponse(['error' => 'user_name query parameter is required'], 400); // 400 Bad Request
        return;
    }
    $userName = trim($_GET['user_name']);
    error_log("Preferences API: Received user_name '{$userName}' from query parameter for POST.");

    // Get request data
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    // Validate required fields
    // Remove user_name from data to avoid duplication
    unset($data['user_name']);
    
    // Update or create user preferences
    $success = $prefRepo->savePreferences($userName, $data);
    
    if (!$success) {
        sendJsonResponse(['error' => 'Failed to save preferences'], 500);
        return;
    }
    
    // Get updated preferences
    $preferences = $prefRepo->getByUserName($userName);
    sendJsonResponse($preferences);
}

/**
 * Handle DELETE requests
 * @param UserPreferencesRepository $prefRepo
 */
function handleDeleteRequest($prefRepo) {
    // Get user name from query parameter
    if (!isset($_GET['user_name']) || empty(trim($_GET['user_name']))) {
        error_log("Preferences API: user_name query parameter is missing or empty for DELETE.");
        sendJsonResponse(['error' => 'user_name query parameter is required'], 400); // 400 Bad Request
        return;
    }
    $userName = trim($_GET['user_name']);
    error_log("Preferences API: Received user_name '{$userName}' from query parameter for DELETE.");

    // Delete user preferences
    $success = $prefRepo->deletePreferences($userName);
    
    if (!$success) {
        sendJsonResponse(['error' => 'Failed to delete preferences'], 500);
        return;
    }
    
    sendJsonResponse(['success' => true, 'message' => 'Preferences deleted successfully']);
}