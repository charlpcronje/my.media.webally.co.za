<?php
// backend/models/CommentsRepository.php
require_once(__DIR__ . '/../config.php');

/**
 * Repository class for managing comments
 */
class CommentsRepository {
    private $db;
    
    /**
     * Constructor
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all comments for a media item
     * @param int $mediaId Media ID
     * @param int|null $chapterId Optional chapter ID to filter by
     * @return array Comments data
     */
    public function getByMediaId($mediaId, $chapterId = null) {
        try {
            $params = [$mediaId];
            $query = "SELECT * FROM comments WHERE media_id = ?";
            
            if ($chapterId !== null) {
                $query .= " AND chapter_id = ?";
                $params[] = $chapterId;
            }
            
            $query .= " ORDER BY timestamp DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            logError('Error getting comments: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get a comment by ID
     * @param int $commentId Comment ID
     * @return array|null Comment data
     */
    public function getById($commentId) {
        try {
            $query = "SELECT * FROM comments WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$commentId]);
            
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
            return $comment ?: null;
        } catch (PDOException $e) {
            logError('Error getting comment: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new comment
     * @param array $commentData Comment data
     * @return array|false Created comment or false on failure
     */
    public function create($commentData) {
        try {
            // Validate required fields
            if (!isset($commentData['media_id']) || !isset($commentData['user_name']) 
                || !isset($commentData['comment'])) {
                return false;
            }
            
            $query = "INSERT INTO comments (media_id, chapter_id, user_name, comment) 
                      VALUES (?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $commentData['media_id'],
                $commentData['chapter_id'] ?? null,
                $commentData['user_name'],
                $commentData['comment']
            ]);
            
            $commentId = $this->db->lastInsertId();
            
            return $this->getById($commentId);
        } catch (PDOException $e) {
            logError('Error creating comment: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing comment
     * @param int $commentId Comment ID
     * @param array $commentData Updated comment data
     * @param string $userName Username of the commenter (for permission check)
     * @return array|false Updated comment or false on failure
     */
    public function update($commentId, $commentData, $userName) {
        try {
            $comment = $this->getById($commentId);
            if (!$comment) {
                return false;
            }
            
            // Check if user has permission to update this comment
            if ($comment['user_name'] !== $userName) {
                return false;
            }
            
            // Only update the comment text
            if (!isset($commentData['comment'])) {
                return $comment;
            }
            
            $query = "UPDATE comments SET comment = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$commentData['comment'], $commentId]);
            
            return $this->getById($commentId);
        } catch (PDOException $e) {
            logError('Error updating comment: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a comment
     * @param int $commentId Comment ID
     * @param string $userName Username of the commenter (for permission check)
     * @return bool Success status
     */
    public function delete($commentId, $userName) {
        try {
            // Check if comment exists and belongs to user
            $comment = $this->getById($commentId);
            if (!$comment || $comment['user_name'] !== $userName) {
                return false;
            }
            
            $query = "DELETE FROM comments WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$commentId]);
        } catch (PDOException $e) {
            logError('Error deleting comment: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get comment count for a media item
     * @param int $mediaId Media ID
     * @return int Comment count
     */
    public function getCommentCount($mediaId) {
        try {
            $query = "SELECT COUNT(*) FROM comments WHERE media_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$mediaId]);
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            logError('Error getting comment count: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get recent comments across all media
     * @param int $limit Maximum number of comments to return
     * @return array Recent comments
     */
    public function getRecentComments($limit = 10) {
        try {
            $query = "
                SELECT c.*, m.caption as media_caption, m.type as media_type
                FROM comments c
                JOIN media m ON c.media_id = m.id
                ORDER BY c.timestamp DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            logError('Error getting recent comments: ' . $e->getMessage());
            return [];
        }
    }
}