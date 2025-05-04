<?php
// backend/api/tags.php
// Force error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the error display helper
require_once('./display_errors.php');
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
        getTags($conn);
        break;
    case 'POST':
        createTag($conn);
        break;
    case 'DELETE':
        deleteTag($conn);
        break;
    default:
        sendJsonResponse(['error' => 'Method not allowed'], 405);
}

/**
 * Get all tags or filtered tags
 */
function getTags($conn) {
    try {
        $params = [];
        $where = [];
        $query = "SELECT name FROM tags";
        
        // Filter by name
        if (isset($_GET['name'])) {
            $where[] = "name LIKE ?";
            $params[] = '%' . $_GET['name'] . '%';
        }
        
        // Add WHERE clause if needed
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }
        
        // Order by name
        $query .= " ORDER BY name ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        sendJsonResponse($tags);
    } catch (PDOException $e) {
        logError('Error getting tags: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to fetch tags'], 500);
    }
}

/**
 * Create a new tag
 */
function createTag($conn) {
    try {
        // Get request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || empty(trim($data['name']))) {
            sendJsonResponse(['error' => 'Tag name is required'], 400);
        }
        
        $tagName = trim($data['name']);
        
        // Check if tag already exists
        $sql = "SELECT id FROM tags WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tagName]);
        
        if ($stmt->rowCount() > 0) {
            sendJsonResponse(['error' => 'Tag already exists'], 409);
        }
        
        // Insert new tag
        $sql = "INSERT INTO tags (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tagName]);
        
        $tagId = $conn->lastInsertId();
        
        sendJsonResponse([
            'success' => true,
            'id' => $tagId,
            'name' => $tagName,
            'message' => 'Tag created successfully'
        ]);
    } catch (PDOException $e) {
        logError('Error creating tag: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to create tag'], 500);
    }
}

/**
 * Delete a tag
 */
function deleteTag($conn) {
    try {
        // Get tag name from query parameter
        if (!isset($_GET['name'])) {
            sendJsonResponse(['error' => 'Tag name is required'], 400);
        }
        
        $tagName = $_GET['name'];
        
        // Get tag ID
        $sql = "SELECT id FROM tags WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tagName]);
        $tag = $stmt->fetch();
        
        if (!$tag) {
            sendJsonResponse(['error' => 'Tag not found'], 404);
        }
        
        $tagId = $tag['id'];
        
        // Start transaction
        $conn->beginTransaction();
        
        // Delete from media_tags
        $sql = "DELETE FROM media_tags WHERE tag_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tagId]);
        
        // Delete from tags
        $sql = "DELETE FROM tags WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tagId]);
        
        // Commit transaction
        $conn->commit();
        
        sendJsonResponse([
            'success' => true,
            'message' => 'Tag deleted successfully'
        ]);
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Error deleting tag: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to delete tag'], 500);
    }
}