// frontend/src/services/ChaptersService.js
import { logger } from '../lib/logger';
import { apiConfig } from '../lib/apiConfig';
import axios from 'axios';

/**
 * Service for handling media chapters
 */
class ChaptersService {
  /**
   * Get all chapters for a specific media item
   * @param {number} mediaId - ID of the media item
   * @returns {Promise<Array>} Array of chapter objects
   */
  async getChaptersByMediaId(mediaId) {
    try {
      const url = apiConfig.getUrl('chaptersByMedia', { id: mediaId });
      const response = await axios.get(url);
      
      return Array.isArray(response.data) ? response.data : [];
    } catch (error) {
      logger.error('Error fetching chapters:', error);
      return [];
    }
  }
  
  /**
   * Create a new chapter for a media item
   * @param {number} mediaId - ID of the media item
   * @param {Object} chapterData - Chapter data
   * @returns {Promise<Object>} Created chapter data
   */
  async createChapter(mediaId, chapterData) {
    try {
      const url = apiConfig.getUrl('chapters');
      const payload = {
        media_id: mediaId,
        ...chapterData
      };
      
      const response = await axios.post(url, payload);
      return response.data;
    } catch (error) {
      logger.error('Error creating chapter:', error);
      throw error;
    }
  }
  
  /**
   * Update an existing chapter
   * @param {number} chapterId - ID of the chapter to update
   * @param {Object} chapterData - Updated chapter data
   * @returns {Promise<Object>} Updated chapter data
   */
  async updateChapter(chapterId, chapterData) {
    try {
      const url = apiConfig.getUrl('chapters') + '/' + chapterId;
      const response = await axios.put(url, chapterData);
      return response.data;
    } catch (error) {
      logger.error('Error updating chapter:', error);
      throw error;
    }
  }
  
  /**
   * Delete a chapter
   * @param {number} chapterId - ID of the chapter to delete
   * @returns {Promise<Object>} Response data
   */
  async deleteChapter(chapterId) {
    try {
      const url = apiConfig.getUrl('chapters') + '/' + chapterId;
      const response = await axios.delete(url);
      return response.data;
    } catch (error) {
      logger.error('Error deleting chapter:', error);
      throw error;
    }
  }
  
  /**
   * Format chapter time for display
   * @param {number} timeInSeconds - Time in seconds
   * @returns {string} Formatted time (MM:SS or HH:MM:SS)
   */
  formatChapterTime(timeInSeconds) {
    if (isNaN(timeInSeconds)) return '00:00';
    
    const hours = Math.floor(timeInSeconds / 3600);
    const minutes = Math.floor((timeInSeconds % 3600) / 60);
    const seconds = Math.floor(timeInSeconds % 60);
    
    if (hours > 0) {
      return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  }
  
  /**
   * Parse a time string into seconds
   * @param {string} timeString - Time string (MM:SS or HH:MM:SS)
   * @returns {number} Time in seconds
   */
  parseTimeString(timeString) {
    if (!timeString) return 0;
    
    const parts = timeString.split(':').map(part => parseInt(part, 10));
    
    if (parts.length === 3) {
      // HH:MM:SS
      return (parts[0] * 3600) + (parts[1] * 60) + parts[2];
    } else if (parts.length === 2) {
      // MM:SS
      return (parts[0] * 60) + parts[1];
    }
    
    return 0;
  }
  
  /**
   * Find the current chapter based on media position
   * @param {Array} chapters - Array of chapter objects
   * @param {number} currentTime - Current media position in seconds
   * @returns {Object|null} Current chapter or null if not found
   */
  findCurrentChapter(chapters, currentTime) {
    if (!chapters || !chapters.length) return null;
    
    // Sort chapters by start time
    const sortedChapters = [...chapters].sort((a, b) => a.start_time - b.start_time);
    
    let currentChapter = null;
    
    for (const chapter of sortedChapters) {
      if (currentTime >= chapter.start_time) {
        if (chapter.end_time === null || currentTime <= chapter.end_time) {
          currentChapter = chapter;
        }
      } else {
        // We've gone past the current time, no need to check further
        break;
      }
    }
    
    return currentChapter;
  }
}

// Export as singleton
export const chaptersService = new ChaptersService();