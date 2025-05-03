<?php
// backend/api/media.php
require_once('../config.php');
enableCors();

$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getMedia($conn);
        break;
    case 'POST':
        createMedia($conn);
        break;
    case 'DELETE':
        deleteMedia($conn);
        break;
    default:
        sendJsonResponse(['error' => 'Method not allowed'], 405);
}

/**
 * Get media items with optional filtering
 */
function getMedia($conn) {
    try {
        $params = [];
        $where = [];
        
        // Build query based on parameters
        $query = "
            SELECT m.*, GROUP_CONCAT(t.name) as tags
            FROM media m
            LEFT JOIN media_tags mt ON m.id = mt.media_id
            LEFT JOIN tags t ON mt.tag_id = t.id
        ";
        
        // Filter by ID
        if (isset($_GET['id'])) {
            $where[] = "m.id = ?";
            $params[] = $_GET['id'];
        }
        
        // Filter by type
        if (isset($_GET['type']) && in_array($_GET['type'], ['video', 'audio', 'image'])) {
            $where[] = "m.type = ?";
            $params[] = $_GET['type'];
        }
        
        // Filter by tag
        if (isset($_GET['tag'])) {
            $where[] = "t.name = ?";
            $params[] = $_GET['tag'];
        }
        
        // Add WHERE clause if needed
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }
        
        // Group by media id to handle tags properly
        $query .= " GROUP BY m.id";
        
        // Order by creation date (newest first)
        $query .= " ORDER BY m.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        $media = $stmt->fetchAll();
        
        // Process the results
        $result = [];
        foreach ($media as $item) {
            // Convert tags from string to array
            $item['tags'] = $item['tags'] ? explode(',', $item['tags']) : [];
            $result[] = $item;
        }
        
        sendJsonResponse($result);
    } catch (PDOException $e) {
        logError('Error getting media: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to fetch media'], 500);
    }
}

/**
 * Create new media item with upload handling
 */
function createMedia($conn) {
    try {
        // Verify that we have file uploads and required fields
        if (!isset($_FILES['file']) || !isset($_POST['caption']) || !isset($_POST['type'])) {
            sendJsonResponse(['error' => 'Missing required fields'], 400);
        }
        
        $file = $_FILES['file'];
        $caption = $_POST['caption'];
        $type = $_POST['type'];
        $description = $_POST['description'] ?? '';
        $tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];
        
        // Validate file
        if ($file['error'] !== UPLOAD_NO_ERROR) {
            sendJsonResponse(['error' => 'File upload error: ' . $file['error']], 400);
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            sendJsonResponse(['error' => 'File exceeds maximum size'], 400);
        }
        
        // Get file extension and validate by type
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        switch ($type) {
            case 'image':
                if (!in_array($fileExt, ALLOWED_IMAGE_TYPES)) {
                    sendJsonResponse(['error' => 'Invalid image file type'], 400);
                }
                break;
            case 'video':
                if (!in_array($fileExt, ALLOWED_VIDEO_TYPES)) {
                    sendJsonResponse(['error' => 'Invalid video file type'], 400);
                }
                break;
            case 'audio':
                if (!in_array($fileExt, ALLOWED_AUDIO_TYPES)) {
                    sendJsonResponse(['error' => 'Invalid audio file type'], 400);
                }
                break;
            default:
                sendJsonResponse(['error' => 'Invalid media type'], 400);
        }
        
        // Generate unique filename
        $filename = uniqid() . '.' . $fileExt;
        $filepath = UPLOAD_PATH . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            logError('Failed to move uploaded file: ' . $file['name']);
            sendJsonResponse(['error' => 'Failed to save file'], 500);
        }
        
        // Process thumbnail if provided
        $thumbnail = null;
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_NO_ERROR) {
            $thumbFile = $_FILES['thumbnail'];
            $thumbExt = strtolower(pathinfo($thumbFile['name'], PATHINFO_EXTENSION));
            
            if (!in_array($thumbExt, ALLOWED_IMAGE_TYPES)) {
                sendJsonResponse(['error' => 'Invalid thumbnail file type'], 400);
            }
            
            $thumbnailFilename = uniqid() . '_thumb.' . $thumbExt;
            $thumbnailPath = THUMBNAIL_PATH . '/' . $thumbnailFilename;
            
            if (!move_uploaded_file($thumbFile['tmp_name'], $thumbnailPath)) {
                logError('Failed to move thumbnail file: ' . $thumbFile['name']);
                sendJsonResponse(['error' => 'Failed to save thumbnail'], 500);
            }
            
            $thumbnail = 'thumbnails/' . $thumbnailFilename;
        }
        
        // Start transaction to ensure data consistency
        $conn->beginTransaction();
        
        // Insert media record
        $sql = "INSERT INTO media (filename, thumbnail, type, caption, description) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$filename, $thumbnail, $type, $caption, $description]);
        
        $mediaId = $conn->lastInsertId();
        
        // Process tags
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (empty($tag)) continue;
                
                // Insert tag if it doesn't exist
                $sql = "INSERT IGNORE INTO tags (name) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$tag]);
                
                // Get tag ID
                $sql = "SELECT id FROM tags WHERE name = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$tag]);
                $tagId = $stmt->fetch()['id'];
                
                // Link tag to media
                $sql = "INSERT IGNORE INTO media_tags (media_id, tag_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$mediaId, $tagId]);
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        sendJsonResponse([
            'success' => true,
            'id' => $mediaId,
            'message' => 'Media uploaded successfully'
        ]);
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Error creating media: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to save media data'], 500);
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Error: ' . $e->getMessage());
        sendJsonResponse(['error' => 'An error occurred'], 500);
    }
}

/**
 * Delete media item
 */
function deleteMedia($conn) {
    try {
        // Get ID from query parameter
        if (!isset($_GET['id'])) {
            sendJsonResponse(['error' => 'Missing media ID'], 400);
        }
        
        $mediaId = $_GET['id'];
        
        // Get media info before deletion
        $sql = "SELECT filename, thumbnail FROM media WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$mediaId]);
        $media = $stmt->fetch();
        
        if (!$media) {
            sendJsonResponse(['error' => 'Media not found'], 404);
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        // Delete from media_tags
        $sql = "DELETE FROM media_tags WHERE media_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$mediaId]);
        
        // Delete from analytics
        $sql = "DELETE FROM analytics WHERE media_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$mediaId]);
        
        // Delete from media
        $sql = "DELETE FROM media WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$mediaId]);
        
        // Commit transaction
        $conn->commit();
        
        // Delete files
        if (file_exists(UPLOAD_PATH . '/' . $media['filename'])) {
            unlink(UPLOAD_PATH . '/' . $media['filename']);
        }
        
        if ($media['thumbnail'] && file_exists(UPLOAD_PATH . '/' . $media['thumbnail'])) {
            unlink(UPLOAD_PATH . '/' . $media['thumbnail']);
        }
        
        sendJsonResponse([
            'success' => true,
            'message' => 'Media deleted successfully'
        ]);
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Error deleting media: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to delete media'], 500);
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Error: ' . $e->getMessage());
        sendJsonResponse(['error' => 'An error occurred'], 500);
    }
}