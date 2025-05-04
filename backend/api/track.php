
<?php
// backend/api/track.php
require_once('../config.php');
enableCors();

$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Method not allowed'], 405);
}

// Get request body
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['media_id']) || !isset($data['event_type']) || !isset($data['user_name'])) {
    sendJsonResponse(['error' => 'Missing required fields'], 400);
}

$mediaId = $data['media_id'];
$eventType = $data['event_type'];
$userName = $data['user_name'];
$position = $data['position'] ?? null;
$percentage = $data['percentage'] ?? null;

// Validate event type
$validEventTypes = ['view', 'play', 'pause', 'seek', 'progress', 'ended', 'download'];
if (!in_array($eventType, $validEventTypes)) {
    sendJsonResponse(['error' => 'Invalid event type'], 400);
}

try {
    // Check if media exists
    $sql = "SELECT id FROM media WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$mediaId]);
    
    if ($stmt->rowCount() === 0) {
        sendJsonResponse(['error' => 'Media not found'], 404);
    }
    
    // Insert analytics record
    $sql = "INSERT INTO analytics (media_id, user_name, event_type, position, percentage) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$mediaId, $userName, $eventType, $position, $percentage]);
    
    sendJsonResponse([
        'success' => true,
        'message' => 'Event tracked successfully'
    ]);
} catch (PDOException $e) {
    logError('Error tracking media event: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Failed to track event'], 500);
}