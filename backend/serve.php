// backend/serve.php
<?php
// File serving endpoint for media files
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/utils/Database.php';
require_once __DIR__ . '/utils/Logger.php';
require_once __DIR__ . '/models/Analytics.php';

// Initialize logger
$logger = new Logger();
$logger->info("Media serving request initiated");

// Set up database connection
$db = new Database($db_host, $db_user, $db_pass, $db_name);
$conn = $db->getConnection();

// Get file ID from request
$file_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$thumbnail = isset($_GET['thumbnail']) && $_GET['thumbnail'] === '1';

// Validate file ID
if (!$file_id) {
    $logger->error("Missing file ID in request");
    header('HTTP/1.1 400 Bad Request');
    exit('Missing file ID');
}

// Get current user from JWT token
$user_id = 0; // Default to anonymous
$auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
if (preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
    $token = $matches[1];
    require_once __DIR__ . '/utils/Auth.php';
    require_once __DIR__ . '/config/app.php';
    
    $payload = Auth::validateJWT($token, $jwt_secret);
    if ($payload) {
        $user_id = $payload->id;
    }
}

// Get media info from database
$stmt = $conn->prepare("SELECT id, type, file_path, thumbnail_path FROM media WHERE id = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $logger->error("Media ID $file_id not found");
    header('HTTP/1.1 404 Not Found');
    exit('File not found');
}

$media = $result->fetch_assoc();

// Set file path based on thumbnail flag
$file_path = $thumbnail ? $media['thumbnail_path'] : $media['file_path'];

// Check if file exists
if (!file_exists($file_path)) {
    $logger->error("File not found on disk: $file_path");
    header('HTTP/1.1 404 Not Found');
    exit('File not found on disk');
}

// Create session ID if not exists
session_start();
$session_id = session_id();

// Track media access in analytics (only for media, not thumbnails)
if (!$thumbnail) {
    $analytics = new Analytics();
    $analytics->media_id = $file_id;
    $analytics->user_id = $user_id;
    $analytics->session_id = $session_id;
    $analytics->device_type = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $analytics->browser = 'Unknown'; // Could parse from user agent
    $analytics->os = 'Unknown';      // Could parse from user agent
    $analytics->start_time = date('Y-m-d H:i:s');
    
    // Record analytics
    $stmt = $conn->prepare("INSERT INTO analytics 
        (media_id, user_id, session_id, device_type, browser, os, start_time) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "iisssss", 
        $analytics->media_id, 
        $analytics->user_id, 
        $analytics->session_id, 
        $analytics->device_type, 
        $analytics->browser, 
        $analytics->os, 
        $analytics->start_time
    );
    $stmt->execute();
    
    // Update play count
    $stmt = $conn->prepare("UPDATE media SET play_count = play_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    
    $logger->info("Started playback of media ID $file_id by user $user_id");
}

// Determine content type
$content_types = [
    'video' => 'video/mp4',
    'audio' => 'audio/mpeg',
    'image' => 'image/jpeg'
];

$content_type = 'application/octet-stream'; // Default

if ($thumbnail) {
    $content_type = 'image/jpeg';
} else if (isset($content_types[$media['type']])) {
    $content_type = $content_types[$media['type']];
}

// Get file size
$file_size = filesize($file_path);

// Handle range requests for streaming
$range = isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : null;
if ($range) {
    list($unit, $range) = explode('=', $range, 2);
    
    if ($unit == 'bytes') {
        list($start, $end) = explode('-', $range, 2);
        
        $start = max(0, intval($start));
        $end = $end ? min($file_size - 1, intval($end)) : $file_size - 1;
        
        $length = $end - $start + 1;
        
        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes $start-$end/$file_size");
        header("Content-Length: $length");
        
        // Output the file partial content
        $fp = fopen($file_path, 'rb');
        fseek($fp, $start);
        $buffer = 1024 * 8;
        $bytes_to_read = $length;
        
        while (!feof($fp) && $bytes_to_read > 0) {
            $bytes_to_read_now = min($buffer, $bytes_to_read);
            echo fread($fp, $bytes_to_read_now);
            $bytes_to_read -= $bytes_to_read_now;
            flush();
        }
        
        fclose($fp);
        exit;
    }
}

// Serve the entire file
header("Content-Type: $content_type");
header("Content-Length: $file_size");
header("Accept-Ranges: bytes");
header("Content-Disposition: inline; filename=\"" . basename($file_path) . "\"");
header("Cache-Control: public, max-age=86400");

readfile($file_path);
exit;