<?php
// backend/models/UserPreferencesRepository.php
require_once(__DIR__ . '/../config.php');

/**
 * Repository class for managing user preferences
 */
class UserPreferencesRepository {
    private $db;
    
    /**
     * Constructor
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get default preferences
     * @return array Default preferences
     */
    public function getDefaultPreferences() {
        return [
            'theme' => 'system',
            'volume' => 80,
            'autoplay' => true
        ];
    }
    
    /**
     * Get preferences for a user
     * @param string $userName Username
     * @return array|null User preferences or null if not found
     */
    public function getByUserName($userName) {
        try {
            $query = "SELECT * FROM user_preferences WHERE user_name = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userName]);
            
            $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$preferences) {
                return null;
            }
            
            // Convert boolean fields from database
            if (isset($preferences['autoplay'])) {
                $preferences['autoplay'] = (bool)$preferences['autoplay'];
            }
            
            return $preferences;
        } catch (PDOException $e) {
            logError('Error getting user preferences: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Save preferences for a user
     * @param string $userName Username
     * @param array $data Preference data
     * @return bool Success status
     */
    public function savePreferences($userName, $data) {
        try {
            // Check if user preferences already exist
            $existing = $this->getByUserName($userName);
            
            if ($existing) {
                // Update existing preferences
                return $this->updatePreferences($userName, $data);
            } else {
                // Create new preferences
                return $this->createPreferences($userName, $data);
            }
        } catch (PDOException $e) {
            logError('Error saving user preferences: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create new preferences for a user
     * @param string $userName Username
     * @param array $data Preference data
     * @return bool Success status
     */
    private function createPreferences($userName, $data) {
        try {
            // Merge with default preferences
            $preferences = array_merge($this->getDefaultPreferences(), $data);
            
            // Get valid fields
            $validFields = $this->getValidFields($preferences);
            
            if (empty($validFields)) {
                return false;
            }
            
            $fields = array_keys($validFields);
            $fields[] = 'user_name';
            
            $placeholders = array_fill(0, count($fields), '?');
            $values = array_values($validFields);
            $values[] = $userName;
            
            $query = "INSERT INTO user_preferences (" . implode(', ', $fields) . ") 
                      VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            logError('Error creating user preferences: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing preferences for a user
     * @param string $userName Username
     * @param array $data Preference data
     * @return bool Success status
     */
    private function updatePreferences($userName, $data) {
        try {
            // Get valid fields
            $validFields = $this->getValidFields($data);
            
            if (empty($validFields)) {
                return true; // Nothing to update
            }
            
            $setStatements = [];
            $values = [];
            
            foreach ($validFields as $field => $value) {
                $setStatements[] = "$field = ?";
                $values[] = $value;
            }
            
            $values[] = $userName;
            
            $query = "UPDATE user_preferences SET " . implode(', ', $setStatements) . " 
                      WHERE user_name = ?";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            logError('Error updating user preferences: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete preferences for a user
     * @param string $userName Username
     * @return bool Success status
     */
    public function deletePreferences($userName) {
        try {
            $query = "DELETE FROM user_preferences WHERE user_name = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$userName]);
        } catch (PDOException $e) {
            logError('Error deleting user preferences: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Filter and validate preference fields
     * @param array $data Preference data
     * @return array Valid fields
     */
    private function getValidFields($data) {
        $allowedFields = [
            'theme' => 'string',
            'volume' => 'int',
            'autoplay' => 'bool'
        ];
        
        $validFields = [];
        
        foreach ($allowedFields as $field => $type) {
            if (!isset($data[$field])) {
                continue;
            }
            
            $value = $data[$field];
            
            switch ($type) {
                case 'int':
                    $validFields[$field] = (int)$value;
                    break;
                    
                case 'bool':
                    $validFields[$field] = $value ? 1 : 0;
                    break;
                    
                case 'string':
                    if ($field === 'theme' && !in_array($value, ['light', 'dark', 'system'])) {
                        $validFields[$field] = 'system';
                    } else {
                        $validFields[$field] = (string)$value;
                    }
                    break;
                    
                default:
                    // Skip unknown types
                    break;
            }
        }
        
        return $validFields;
    }
}