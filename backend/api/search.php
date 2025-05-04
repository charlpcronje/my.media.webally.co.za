<?php
// backend/api/search.php
require_once('../config.php');
require_once('../models/AnalyticsRepository.php');
enableCors();

// Get database connection
$db = getDbConnection();
if (!$db) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
    exit;
}

// Initialize analytics repository for tracking searches
$analyticsRepo = new AnalyticsRepository($db);

// Handle search request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
    $mediaType = isset($_GET['type']) ? $_GET['type'] : '';
    $tag = isset($_GET['tag']) ? $_GET['tag'] : '';
    $sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'date';
    $userName = isset($_GET['user_name']) ? $_GET['user_name'] : '';
    
    // Validate search term
    if (empty($searchTerm) && empty($mediaType) && empty($tag)) {
        sendJsonResponse(['error' => 'Search term, media type, or tag is required'], 400);
        exit;
    }
    
    try {
        // Build the search query
        $query = "
            SELECT m.*, GROUP_CONCAT(t.name) as tags
            FROM media m
            LEFT JOIN media_tags mt ON m.id = mt.media_id
            LEFT JOIN tags t ON mt.tag_id = t.id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Add search term condition if provided
        if (!empty($searchTerm)) {
            $query .= " AND (
                m.caption LIKE ? OR
                m.description LIKE ? OR
                EXISTS (
                    SELECT 1 FROM media_tags mt2
                    JOIN tags t2 ON mt2.tag_id = t2.id
                    WHERE mt2.media_id = m.id AND t2.name LIKE ?
                )
            )";
            
            $likeParam = "%{$searchTerm}%";
            $params[] = $likeParam;
            $params[] = $likeParam;
            $params[] = $likeParam;
        }
        
        // Add media type filter if provided
        if (!empty($mediaType)) {
            // Handle multiple media types as array or comma-separated string
            if (is_array($mediaType)) {
                $typeCount = count($mediaType);
                $placeholders = str_repeat('?,', $typeCount - 1) . '?';
                $query .= " AND m.type IN ($placeholders)";
                $params = array_merge($params, $mediaType);
            } else if (strpos($mediaType, ',') !== false) {
                $types = explode(',', $mediaType);
                $typeCount = count($types);
                $placeholders = str_repeat('?,', $typeCount - 1) . '?';
                $query .= " AND m.type IN ($placeholders)";
                $params = array_merge($params, $types);
            } else {
                $query .= " AND m.type = ?";
                $params[] = $mediaType;
            }
        }
        
        // Add tag filter if provided
        if (!empty($tag)) {
            $query .= " AND EXISTS (
                SELECT 1 FROM media_tags mt2
                JOIN tags t2 ON mt2.tag_id = t2.id
                WHERE mt2.media_id = m.id AND t2.name = ?
            )";
            $params[] = $tag;
        }
        
        // Group by media ID to handle tags properly
        $query .= " GROUP BY m.id";
        
        // Add sorting
        switch ($sortBy) {
            case 'name':
                $query .= " ORDER BY m.caption ASC";
                break;
            case 'popularity':
                $query .= " ORDER BY (m.view_count + m.play_count) DESC";
                break;
            case 'date':
            default:
                $query .= " ORDER BY m.created_at DESC";
                break;
        }
        
        // Execute the query
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        // Get the results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process the results
        $processedResults = [];
        foreach ($results as $item) {
            // Convert the tags from comma-separated string to array
            $item['tags'] = $item['tags'] ? explode(',', $item['tags']) : [];
            $processedResults[] = $item;
        }
        
        // Track the search query
        $analyticsRepo->trackSearch(
            $searchTerm,
            [
                'type' => $mediaType,
                'tag' => $tag,
                'sortBy' => $sortBy
            ],
            $userName,
            count($processedResults)
        );
        
        // Return the results
        sendJsonResponse($processedResults);
    } catch (PDOException $e) {
        logError('Error performing search: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Database error occurred while searching'], 500);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['suggest'])) {
    // Handle search suggestions
    $partialQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    if (empty($partialQuery) || strlen($partialQuery) < 2) {
        sendJsonResponse([]);
        exit;
    }
    
    try {
        // Get suggestions from media captions and tags
        $query = "
            (SELECT DISTINCT caption as suggestion
            FROM media
            WHERE caption LIKE ?
            LIMIT 5)
            
            UNION
            
            (SELECT DISTINCT name as suggestion
            FROM tags
            WHERE name LIKE ?
            LIMIT 5)
        ";
        
        $stmt = $db->prepare($query);
        $likeParam = "%{$partialQuery}%";
        $stmt->execute([$likeParam, $likeParam]);
        
        $suggestions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        sendJsonResponse($suggestions);
    } catch (PDOException $e) {
        logError('Error getting search suggestions: ' . $e->getMessage());
        sendJsonResponse([]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['recent'])) {
    // Handle recent searches retrieval
    $userName = isset($_GET['user_name']) ? $_GET['user_name'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    
    if (empty($userName)) {
        sendJsonResponse(['error' => 'User name is required'], 400);
        exit;
    }
    
    try {
        $query = "
            SELECT search_term, filters, timestamp
            FROM search_logs
            WHERE user_name = ?
            ORDER BY timestamp DESC
            LIMIT ?
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$userName, $limit]);
        
        $searches = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $searches[] = [
                'term' => $row['search_term'],
                'filters' => $row['filters'] ? json_decode($row['filters'], true) : null,
                'timestamp' => $row['timestamp']
            ];
        }
        
        sendJsonResponse($searches);
    } catch (PDOException $e) {
        logError('Error getting recent searches: ' . $e->getMessage());
        sendJsonResponse([]);
    }
} else {
    sendJsonResponse(['error' => 'Method not allowed'], 405);
}