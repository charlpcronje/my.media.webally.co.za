<?php
// backend/config.php
// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_NAME', 'my_media');
define('DB_USER', 'cp');
define('DB_PASS', '4334.4334');

// Path configurations
define('BASE_PATH', dirname(__FILE__));
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('THUMBNAIL_PATH', UPLOAD_PATH . '/thumbnails');
define('TEMP_PATH', UPLOAD_PATH . '/temp');

// File upload configurations
define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'webm', 'mov']);
define('ALLOWED_AUDIO_TYPES', ['mp3', 'wav', 'ogg']);

// Session configurations
define('SESSION_LIFETIME', 60 * 60 * 24 * 30); // 30 days

// Initialize database connection
function getDbConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch (PDOException $e) {
        logError('Database connection error: ' . $e->getMessage());
        return null;
    }
}

// Error logging function
function logError($message) {
    $logFile = BASE_PATH . '/logs/error.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    
    error_log($logMessage, 3, $logFile);
}

// Response helper function
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Check if the uploads directory exists, if not create it
function ensureUploadDirectories() {
    $directories = [UPLOAD_PATH, THUMBNAIL_PATH, TEMP_PATH];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, true)) {
                logError("Failed to create directory: $dir");
                return false;
            }
        }
    }
    
    return true;
}

// Enable CORS for API endpoints
function enableCors() {
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        }
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
        
        exit(0);
    }
}

// Initialize error handling
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/php_errors.log');
error_reporting(E_ALL);

// Ensure upload directories exist
ensureUploadDirectories();