// backend/api/serve.php
<?php
/**
 * File serving endpoint for media files with analytics tracking
 */
require_once __DIR__ . '/../config.php';
enableCors();

// Initialize request
$request_id = uniqid();
$start_time = microtime(true);
logError("[$request_id] Serve request started");

// Get file ID and parameters
$file_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$thumbnail = isset($_GET['thumbnail']) && $_GET['thumbnail'] === '1';

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    logError("[$request_id] Database connection failed");
    sendJsonResponse(['error' => 'Server error'], 500);
    exit;
}

// Validate file ID
if (!$file_id) {
    logError("[$request_id] Missing file ID in request");
    http_response_code(400);
    exit('Missing file ID');
}

// Get media info from database
try {
    $query = "SELECT id, type, filename, thumbnail FROM media WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        logError("[$request_id] Failed to prepare statement: " . $conn->error);
        http_response_code(500);
        exit('Server error');
    }
    
    $stmt->execute([$file_id]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$media) {
        logError("[$request_id] Media ID $file_id not found");
        http_response_code(404);
        exit('File not found');
    }
    
    logError("[$request_id] Found media: " . json_encode($media));
} catch (PDOException $e) {
    logError("[$request_id] Database error: " . $e->getMessage());
    http_response_code(500);
    exit('Server error');
}

// Determine file path
// First try the new location (in /media directory)
$new_base_path = dirname(BASE_PATH) . '/media/';
// Use the existing config for old path
$old_base_path = UPLOAD_PATH . '/';

$file_to_serve = $thumbnail && !empty($media['thumbnail']) 
    ? $media['thumbnail'] 
    : $media['filename'];

$new_full_path = $new_base_path . $file_to_serve;
$old_full_path = $old_base_path . $file_to_serve;

// Check if file exists in new location
$serve_path = '';
if (file_exists($new_full_path)) {
    $serve_path = $new_full_path;
    logError("[$request_id] File found in new location: $new_full_path");
} 
// Check if file exists in old location
else if (file_exists($old_full_path)) {
    $serve_path = $old_full_path;
    logError("[$request_id] File found in old location: $old_full_path");
    
    // Optionally copy to new location
    if (defined('AUTO_MIGRATE_FILES') && AUTO_MIGRATE_FILES) {
        // Create target directory if needed
        $target_dir = dirname($new_full_path);
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        // Copy file to new location
        if (copy($old_full_path, $new_full_path)) {
            logError("[$request_id] Copied file to new location: $new_full_path");
            $serve_path = $new_full_path;
        } else {
            logError("[$request_id] Failed to copy file to new location");
        }
    }
} else {
    logError("[$request_id] File not found in either location: $new_full_path or $old_full_path");
    http_response_code(404);
    exit('File not found on disk');
}

// Get session ID for tracking
session_start();
$session_id = session_id();

// Track media access in analytics (only for media, not thumbnails)
if (!$thumbnail) {
    $device_type = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $current_time = date('Y-m-d H:i:s');
    
    try {
        // Check if analytics table exists
        $stmt = $conn->query("SHOW TABLES LIKE 'analytics'");
        $analyticsTableExists = $stmt->rowCount() > 0;
        
        if ($analyticsTableExists) {
            // Record the view/access
            $sql = "INSERT INTO analytics 
                    (media_id, user_id, session_id, device_type, event_type, timestamp) 
                    VALUES (?, ?, ?, ?, 'view', ?)";
                    
            $stmt = $conn->prepare($sql);
            $stmt->execute([$file_id, 0, $session_id, $device_type, $current_time]);
        }
        
        // Update view count if column exists
        $stmt = $conn->query("SHOW COLUMNS FROM media LIKE 'view_count'");
        $viewCountExists = $stmt->rowCount() > 0;
        
        if ($viewCountExists) {
            $sql = "UPDATE media SET view_count = view_count + 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$file_id]);
        } else {
            // Update play_count as fallback
            $stmt = $conn->query("SHOW COLUMNS FROM media LIKE 'play_count'");
            $playCountExists = $stmt->rowCount() > 0;
            
            if ($playCountExists) {
                $sql = "UPDATE media SET play_count = play_count + 1 WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$file_id]);
            }
        }
        
        logError("[$request_id] Recorded view for media ID $file_id");
    } catch (PDOException $e) {
        // Just log error but continue to serve the file
        logError("[$request_id] Failed to record analytics: " . $e->getMessage());
    }
}

// Determine content type
$extension = strtolower(pathinfo($file_to_serve, PATHINFO_EXTENSION));
$content_types = [
    // Images
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    // Audio
    'mp3' => 'audio/mpeg',
    'ogg' => 'audio/ogg',
    'wav' => 'audio/wav',
    'm4a' => 'audio/mp4',
    // Video
    'mp4' => 'video/mp4',
    'webm' => 'video/webm',
    'mov' => 'video/quicktime',
    'm4v' => 'video/mp4'
];

$content_type = isset($content_types[$extension]) 
    ? $content_types[$extension] 
    : 'application/octet-stream';

// Get file size
$file_size = filesize($serve_path);

// Handle range requests for streaming
$range = isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : null;
if ($range) {
    list($unit, $range) = explode('=', $range, 2);
    
    if ($unit == 'bytes') {
        list($start, $end) = explode('-', $range, 2);
        
        $start = max(0, intval($start));
        $end = $end ? min($file_size - 1, intval($end)) : $file_size - 1;
        
        $length = $end - $start + 1;
        
        http_response_code(206);
        header("Content-Range: bytes $start-$end/$file_size");
        header("Content-Length: $length");
        
        // Output the file partial content
        $fp = fopen($serve_path, 'rb');
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
        
        $end_time = microtime(true);
        logError("[$request_id] Served file range $start-$end in " . 
                 number_format($end_time - $start_time, 3) . "s");
        exit;
    }
}

// Serve the entire file
header("Content-Type: $content_type");
header("Content-Length: $file_size");
header("Accept-Ranges: bytes");
header("Content-Disposition: inline; filename=\"" . basename($file_to_serve) . "\"");
header("Cache-Control: public, max-age=86400");

readfile($serve_path);

$end_time = microtime(true);
logError("[$request_id] Served complete file in " . 
         number_format($end_time - $start_time, 3) . "s");
exit;