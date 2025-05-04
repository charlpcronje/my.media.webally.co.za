<?php
// backend/api/preferences.php
// Force error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log("Preferences API: Script started.");

// Include config FIRST to set session cookie parameters
require_once('../config.php');

// Start the session to access session variables
if (session_status() === PHP_SESSION_NONE) {
    error_log("Preferences API: Starting session.");
    session_start();
} else {
    error_log("Preferences API: Session already active (Status: " . session_status() . ").");
}

// Include the error display helper if it exists
if (file_exists('./display_errors.php')) {
    require_once('./display_errors.php');
}

require_once('../models/UserPreferencesRepository.php');
enableCors();

// Debug information
echo "<!-- Debug: Preferences endpoint accessed -->";

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

    // Get user name from PHP session
    if (!isset($_SESSION['user_name'])) {
        error_log("Preferences API: User not logged in or session expired. Session data: " . print_r($_SESSION, true));
        echo "DEBUG: handleGetRequest - Session user_name NOT SET. Session data: " . print_r($_SESSION, true);
        exit();
        sendJsonResponse(['error' => 'User not logged in or session expired'], 401); // 401 Unauthorized
        return;
    }
    $userName = $_SESSION['user_name'];
    error_log("Preferences API: Retrieved user '{$userName}' from session.");
    echo "DEBUG: handleGetRequest - Session user_name IS SET. User: {$userName}. Session data: " . print_r($_SESSION, true);
    exit();

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
    // Get user name from PHP session
    if (!isset($_SESSION['user_name'])) {
        error_log("Preferences API: User not logged in or session expired for POST. Session data: " . print_r($_SESSION, true));
        sendJsonResponse(['error' => 'User not logged in or session expired'], 401);
        return;
    }
    $userName = $_SESSION['user_name'];
    error_log("Preferences API: Retrieved user '{$userName}' from session for POST.");

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
    // Get user name from PHP session
    if (!isset($_SESSION['user_name'])) {
        sendJsonResponse(['error' => 'User not logged in or session expired'], 401);
        return;
    }
    $userName = $_SESSION['user_name'];
    
    // Delete user preferences
    $success = $prefRepo->deletePreferences($userName);
    
    if (!$success) {
        sendJsonResponse(['error' => 'Failed to delete preferences'], 500);
        return;
    }
    
    sendJsonResponse(['success' => true, 'message' => 'Preferences deleted successfully']);
}