<?php
// backend/models/ChaptersRepository.php
require_once(__DIR__ . '/../config.php');

/**
 * Repository class for managing chapters
 */
class ChaptersRepository {
    private $db;
    
    /**
     * Constructor
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all chapters for a media item
     * @param int $mediaId Media ID
     * @return array Chapters data
     */
    public function getByMediaId($mediaId) {
        try {
            $query = "SELECT * FROM chapters WHERE media_id = ? ORDER BY start_time ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$mediaId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            logError('Error getting chapters: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get a chapter by ID
     * @param int $chapterId Chapter ID
     * @return array|null Chapter data
     */
    public function getById($chapterId) {
        try {
            $query = "SELECT * FROM chapters WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$chapterId]);
            
            $chapter = $stmt->fetch(PDO::FETCH_ASSOC);
            return $chapter ?: null;
        } catch (PDOException $e) {
            logError('Error getting chapter: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new chapter
     * @param array $chapterData Chapter data
     * @return array|false Created chapter or false on failure
     */
    public function create($chapterData) {
        try {
            // Validate required fields
            if (!isset($chapterData['media_id']) || !isset($chapterData['title']) 
                || !isset($chapterData['start_time'])) {
                return false;
            }
            
            $query = "INSERT INTO chapters (media_id, title, start_time, end_time, description) 
                      VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $chapterData['media_id'],
                $chapterData['title'],
                $chapterData['start_time'],
                $chapterData['end_time'] ?? null,
                $chapterData['description'] ?? null
            ]);
            
            $chapterId = $this->db->lastInsertId();
            
            return $this->getById($chapterId);
        } catch (PDOException $e) {
            logError('Error creating chapter: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing chapter
     * @param int $chapterId Chapter ID
     * @param array $chapterData Updated chapter data
     * @return array|false Updated chapter or false on failure
     */
    public function update($chapterId, $chapterData) {
        try {
            $chapter = $this->getById($chapterId);
            if (!$chapter) {
                return false;
            }
            
            $fields = [];
            $values = [];
            
            // Only update provided fields
            if (isset($chapterData['title'])) {
                $fields[] = 'title = ?';
                $values[] = $chapterData['title'];
            }
            
            if (isset($chapterData['start_time'])) {
                $fields[] = 'start_time = ?';
                $values[] = $chapterData['start_time'];
            }
            
            if (array_key_exists('end_time', $chapterData)) {
                $fields[] = 'end_time = ?';
                $values[] = $chapterData['end_time'];
            }
            
            if (array_key_exists('description', $chapterData)) {
                $fields[] = 'description = ?';
                $values[] = $chapterData['description'];
            }
            
            if (empty($fields)) {
                return $chapter;
            }
            
            $values[] = $chapterId;
            
            $query = "UPDATE chapters SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute($values);
            
            return $this->getById($chapterId);
        } catch (PDOException $e) {
            logError('Error updating chapter: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a chapter
     * @param int $chapterId Chapter ID
     * @return bool Success status
     */
    public function delete($chapterId) {
        try {
            // Check if chapter exists
            $chapter = $this->getById($chapterId);
            if (!$chapter) {
                return false;
            }
            
            $query = "DELETE FROM chapters WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$chapterId]);
        } catch (PDOException $e) {
            logError('Error deleting chapter: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get chapter analytics
     * @param int $chapterId Chapter ID
     * @return array Analytics data
     */
    public function getAnalytics($chapterId) {
        try {
            $query = "SELECT 
                COUNT(*) as view_count,
                COUNT(DISTINCT user_name) as unique_viewers,
                AVG(view_duration) as avg_view_duration
             FROM analytics 
             WHERE chapter_id = ?";
             
            $stmt = $this->db->prepare($query);
            $stmt->execute([$chapterId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            logError('Error getting chapter analytics: ' . $e->getMessage());
            return [
                'view_count' => 0,
                'unique_viewers' => 0,
                'avg_view_duration' => 0
            ];
        }
    }
}