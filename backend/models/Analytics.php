// backend/models/Analytics.php
<?php
// Analytics model
class Analytics {
    public $id;
    public $media_id;
    public $user_id;
    public $session_id;
    public $device_type;
    public $browser;
    public $os;
    public $start_time;
    public $end_time;
    public $duration;
    public $completed;
    public $skipped;
    public $skip_position;
    public $created_at;
    
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    
    public static function recordStart($db, $data) {
        try {
            $stmt = $db->prepare("INSERT INTO analytics 
                (media_id, user_id, session_id, device_type, start_time) 
                VALUES (?, ?, ?, ?, ?)");
                
            $stmt->bind_param(
                "iisss", 
                $data['media_id'], 
                $data['user_id'], 
                $data['session_id'], 
                $data['device_type'], 
                $data['start_time']
            );
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error recording analytics start: " . $e->getMessage());
            return false;
        }
    }
    
    public static function recordEnd($db, $data) {
        try {
            $stmt = $db->prepare("UPDATE analytics 
                SET end_time = ?, duration = ?, completed = ? 
                WHERE media_id = ? AND user_id = ? AND session_id = ? 
                ORDER BY start_time DESC LIMIT 1");
                
            $stmt->bind_param(
                "siisis", 
                $data['end_time'], 
                $data['duration'], 
                $data['completed'], 
                $data['media_id'], 
                $data['user_id'], 
                $data['session_id']
            );
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error recording analytics end: " . $e->getMessage());
            return false;
        }
    }
    
    public static function recordSkip($db, $data) {
        try {
            $stmt = $db->prepare("UPDATE analytics 
                SET skipped = 1, skip_position = ?, end_time = ? 
                WHERE media_id = ? AND user_id = ? AND session_id = ? 
                ORDER BY start_time DESC LIMIT 1");
                
            $stmt->bind_param(
                "isiis", 
                $data['skip_position'], 
                $data['end_time'], 
                $data['media_id'], 
                $data['user_id'], 
                $data['session_id']
            );
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error recording analytics skip: " . $e->getMessage());
            return false;
        }
    }
    
    public static function getAnalytics($db, $filters = []) {
        try {
            $sql = "SELECT 
                    m.id AS media_id, 
                    m.title AS media_title, 
                    COUNT(a.id) AS plays, 
                    COUNT(DISTINCT a.user_id) AS unique_users,
                    AVG(a.duration) AS avg_duration,
                    SUM(a.completed) AS completions,
                    SUM(a.skipped) AS skips
                FROM analytics a
                JOIN media m ON a.media_id = m.id
                WHERE 1=1";
            
            $params = [];
            $types = "";
            
            if (!empty($filters['media_id'])) {
                $sql .= " AND a.media_id = ?";
                $params[] = $filters['media_id'];
                $types .= "i";
            }
            
            if (!empty($filters['user_id'])) {
                $sql .= " AND a.user_id = ?";
                $params[] = $filters['user_id'];
                $types .= "i";
            }
            
            if (!empty($filters['date_from'])) {
                $sql .= " AND a.start_time >= ?";
                $params[] = $filters['date_from'];
                $types .= "s";
            }
            
            if (!empty($filters['date_to'])) {
                $sql .= " AND a.start_time <= ?";
                $params[] = $filters['date_to'];
                $types .= "s";
            }
            
            $sql .= " GROUP BY m.id, m.title ORDER BY plays DESC";
            
            $stmt = $db->prepare($sql);
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $analytics = [];
            while ($row = $result->fetch_assoc()) {
                $analytics[] = $row;
            }
            
            return $analytics;
        } catch (Exception $e) {
            error_log("Error getting analytics: " . $e->getMessage());
            return [];
        }
    }
}