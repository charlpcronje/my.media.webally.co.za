<?php
// backend/models/AnalyticsRepository.php
require_once(__DIR__ . '/../config.php');

/**
 * Repository class for managing analytics data
 */
class AnalyticsRepository {
    private $db;
    
    /**
     * Constructor
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Record an analytics event
     * @param array $data Event data
     * @return int|false ID of the inserted record or false on failure
     */
    public function recordEvent($data) {
        try {
            // Validate required fields
            if (!isset($data['media_id']) || !isset($data['event_type']) || !isset($data['user_name'])) {
                return false;
            }
            
            // List of all possible fields
            $availableFields = [
                'media_id', 'event_type', 'user_name', 'position', 'percentage',
                'view_duration', 'repeat_count', 'session_id', 'enlargement',
                'chapter_id', 'media_progress', 'timestamp_end'
            ];
            
            // Build dynamic query based on provided fields
            $fields = [];
            $placeholders = [];
            $values = [];
            
            foreach ($availableFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = $field;
                    $placeholders[] = '?';
                    $values[] = $data[$field];
                }
            }
            
            // Add IP address if available
            if (!in_array('ip_address', $fields) && isset($_SERVER['REMOTE_ADDR'])) {
                $fields[] = 'ip_address';
                $placeholders[] = '?';
                $values[] = $_SERVER['REMOTE_ADDR'];
            }
            
            $query = "INSERT INTO analytics (" . implode(', ', $fields) . ") 
                      VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($values);
            
            // Update media view/play counts
            if ($data['event_type'] === 'view') {
                $this->incrementMediaCounter($data['media_id'], 'view_count');
            } else if ($data['event_type'] === 'play') {
                $this->incrementMediaCounter($data['media_id'], 'play_count');
            }
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            logError('Error recording analytics event: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Increment a counter field in the media table
     * @param int $mediaId Media ID
     * @param string $field Field to increment
     * @return bool Success status
     */
    private function incrementMediaCounter($mediaId, $field) {
        try {
            $query = "UPDATE media SET $field = $field + 1 WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$mediaId]);
        } catch (PDOException $e) {
            logError("Error incrementing $field: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get analytics data for a specific media item
     * @param int $mediaId Media ID
     * @param array $filters Optional filters
     * @return array Analytics data
     */
    public function getMediaAnalytics($mediaId, $filters = []) {
        try {
            $params = [$mediaId];
            $query = "
                SELECT 
                    event_type, 
                    COUNT(*) as count,
                    COUNT(DISTINCT user_name) as unique_users,
                    AVG(view_duration) as avg_duration,
                    MAX(percentage) as max_progress
                FROM analytics 
                WHERE media_id = ?
            ";
            
            // Apply date filters if provided
            if (isset($filters['date_from'])) {
                $query .= " AND timestamp >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (isset($filters['date_to'])) {
                $query .= " AND timestamp <= ?";
                $params[] = $filters['date_to'];
            }
            
            // Filter by user if provided
            if (isset($filters['user_name'])) {
                $query .= " AND user_name = ?";
                $params[] = $filters['user_name'];
            }
            
            $query .= " GROUP BY event_type";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add summary info
            $summary = $this->getMediaSummaryStats($mediaId);
            
            return [
                'events' => $results,
                'summary' => $summary
            ];
        } catch (PDOException $e) {
            logError('Error getting media analytics: ' . $e->getMessage());
            return [
                'events' => [],
                'summary' => [
                    'view_count' => 0,
                    'play_count' => 0,
                    'unique_viewers' => 0,
                    'completion_rate' => 0
                ]
            ];
        }
    }
    
    /**
     * Get summary statistics for a media item
     * @param int $mediaId Media ID
     * @return array Summary statistics
     */
    public function getMediaSummaryStats($mediaId) {
        try {
            // First get basic counters from media table
            $query = "SELECT view_count, play_count FROM media WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$mediaId]);
            $media = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$media) {
                return [
                    'view_count' => 0,
                    'play_count' => 0,
                    'unique_viewers' => 0,
                    'completion_rate' => 0
                ];
            }
            
            // Get unique viewers
            $query = "SELECT COUNT(DISTINCT user_name) FROM analytics WHERE media_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$mediaId]);
            $uniqueViewers = $stmt->fetchColumn();
            
            // Calculate completion rate
            $query = "
                SELECT 
                    COUNT(*) as total_ended,
                    SUM(CASE WHEN percentage >= 90 THEN 1 ELSE 0 END) as completed
                FROM analytics 
                WHERE media_id = ? AND event_type = 'ended'
            ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$mediaId]);
            $completion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $completionRate = 0;
            if ($completion['total_ended'] > 0) {
                $completionRate = ($completion['completed'] / $completion['total_ended']) * 100;
            }
            
            return [
                'view_count' => (int)$media['view_count'],
                'play_count' => (int)$media['play_count'],
                'unique_viewers' => (int)$uniqueViewers,
                'completion_rate' => round($completionRate, 2)
            ];
        } catch (PDOException $e) {
            logError('Error getting media summary stats: ' . $e->getMessage());
            return [
                'view_count' => 0,
                'play_count' => 0,
                'unique_viewers' => 0,
                'completion_rate' => 0
            ];
        }
    }
    
    /**
     * Get user analytics data
     * @param string $userName Username
     * @param array $filters Optional filters
     * @return array User analytics data
     */
    public function getUserAnalytics($userName, $filters = []) {
        try {
            $params = [$userName];
            $query = "
                SELECT 
                    m.id as media_id,
                    m.caption as media_title,
                    m.type as media_type,
                    COUNT(a.id) as view_count,
                    MAX(a.percentage) as max_progress,
                    SUM(a.view_duration) as total_duration,
                    MAX(a.timestamp) as last_viewed
                FROM analytics a
                JOIN media m ON a.media_id = m.id
                WHERE a.user_name = ?
            ";
            
            // Apply date filters if provided
            if (isset($filters['date_from'])) {
                $query .= " AND a.timestamp >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (isset($filters['date_to'])) {
                $query .= " AND a.timestamp <= ?";
                $params[] = $filters['date_to'];
            }
            
            // Filter by media type if provided
            if (isset($filters['media_type'])) {
                $query .= " AND m.type = ?";
                $params[] = $filters['media_type'];
            }
            
            $query .= " GROUP BY m.id ORDER BY last_viewed DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            logError('Error getting user analytics: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user summary statistics
     * @param string $userName Username
     * @return array User summary statistics
     */
    public function getUserSummaryStats($userName) {
        try {
            $query = "
                SELECT 
                    COUNT(DISTINCT media_id) as unique_media_viewed,
                    SUM(view_duration) as total_view_time,
                    MAX(timestamp) as last_activity
                FROM analytics
                WHERE user_name = ?
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userName]);
            
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Also get media type distribution
            $query = "
                SELECT 
                    m.type,
                    COUNT(DISTINCT a.media_id) as count
                FROM analytics a
                JOIN media m ON a.media_id = m.id
                WHERE a.user_name = ?
                GROUP BY m.type
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userName]);
            
            $typeDistribution = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $typeDistribution[$row['type']] = (int)$row['count'];
            }
            
            return [
                'summary' => $stats,
                'type_distribution' => $typeDistribution
            ];
        } catch (PDOException $e) {
            logError('Error getting user summary stats: ' . $e->getMessage());
            return [
                'summary' => [
                    'unique_media_viewed' => 0,
                    'total_view_time' => 0,
                    'last_activity' => null
                ],
                'type_distribution' => []
            ];
        }
    }
    
    /**
     * Get overall analytics data
     * @param array $filters Optional filters
     * @return array Overall analytics data
     */
    public function getOverallAnalytics($filters = []) {
        try {
            $where = [];
            $params = [];
            
            // Apply date filters if provided
            if (isset($filters['date_from'])) {
                $where[] = "a.timestamp >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (isset($filters['date_to'])) {
                $where[] = "a.timestamp <= ?";
                $params[] = $filters['date_to'];
            }
            
            $whereClause = '';
            if (!empty($where)) {
                $whereClause = "WHERE " . implode(" AND ", $where);
            }
            
            // Get top media items
            $query = "
                SELECT 
                    m.id,
                    m.caption,
                    m.type,
                    COUNT(a.id) as view_count,
                    COUNT(DISTINCT a.user_name) as unique_viewers
                FROM media m
                LEFT JOIN analytics a ON m.id = a.media_id
                $whereClause
                GROUP BY m.id
                ORDER BY view_count DESC
                LIMIT 10
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $topMedia = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get top users
            $query = "
                SELECT 
                    a.user_name,
                    COUNT(a.id) as action_count,
                    COUNT(DISTINCT a.media_id) as media_viewed,
                    SUM(a.view_duration) as total_view_time
                FROM analytics a
                $whereClause
                GROUP BY a.user_name
                ORDER BY action_count DESC
                LIMIT 10
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get event type distribution
            $query = "
                SELECT 
                    a.event_type,
                    COUNT(a.id) as count
                FROM analytics a
                $whereClause
                GROUP BY a.event_type
                ORDER BY count DESC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $eventTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get media type distribution
            $query = "
                SELECT 
                    m.type,
                    COUNT(a.id) as view_count,
                    COUNT(DISTINCT a.user_name) as unique_viewers
                FROM analytics a
                JOIN media m ON a.media_id = m.id
                $whereClause
                GROUP BY m.type
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $mediaTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'top_media' => $topMedia,
                'top_users' => $topUsers,
                'event_types' => $eventTypes,
                'media_types' => $mediaTypes
            ];
        } catch (PDOException $e) {
            logError('Error getting overall analytics: ' . $e->getMessage());
            return [
                'top_media' => [],
                'top_users' => [],
                'event_types' => [],
                'media_types' => []
            ];
        }
    }
    
    /**
     * Track a search query
     * @param string $searchTerm Search term
     * @param array $filters Search filters
     * @param string $userName Username
     * @param int $resultsCount Number of results
     * @return int|false ID of the inserted record or false on failure
     */
    public function trackSearch($searchTerm, $filters = [], $userName = null, $resultsCount = 0) {
        try {
            $query = "INSERT INTO search_logs (search_term, filters, user_name, results_count) 
                      VALUES (?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $searchTerm,
                $filters ? json_encode($filters) : null,
                $userName,
                $resultsCount
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            logError('Error tracking search: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get search analytics
     * @param array $filters Optional filters
     * @return array Search analytics data
     */
    public function getSearchAnalytics($filters = []) {
        try {
            $where = [];
            $params = [];
            
            // Apply date filters if provided
            if (isset($filters['date_from'])) {
                $where[] = "timestamp >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (isset($filters['date_to'])) {
                $where[] = "timestamp <= ?";
                $params[] = $filters['date_to'];
            }
            
            // Filter by user if provided
            if (isset($filters['user_name'])) {
                $where[] = "user_name = ?";
                $params[] = $filters['user_name'];
            }
            
            $whereClause = '';
            if (!empty($where)) {
                $whereClause = "WHERE " . implode(" AND ", $where);
            }
            
            // Get top search terms
            $query = "
                SELECT 
                    search_term,
                    COUNT(*) as count,
                    AVG(results_count) as avg_results
                FROM search_logs
                $whereClause
                GROUP BY search_term
                ORDER BY count DESC
                LIMIT 10
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $topSearches = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get zero-result searches
            $query = "
                SELECT 
                    search_term,
                    COUNT(*) as count
                FROM search_logs
                WHERE results_count = 0
                $whereClause
                GROUP BY search_term
                ORDER BY count DESC
                LIMIT 10
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $zeroResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'top_searches' => $topSearches,
                'zero_results' => $zeroResults
            ];
        } catch (PDOException $e) {
            logError('Error getting search analytics: ' . $e->getMessage());
            return [
                'top_searches' => [],
                'zero_results' => []
            ];
        }
    }
}