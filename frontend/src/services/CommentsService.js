// frontend/src/services/CommentsService.js
import { logger } from '../lib/logger';
import { apiConfig } from '../lib/apiConfig';
import axios from 'axios';

/**
 * Service for handling media comments
 */
class CommentsService {
  /**
   * Get comments for a specific media item
   * @param {number} mediaId - ID of the media item
   * @param {number} [chapterId] - Optional chapter ID to filter comments
   * @returns {Promise<Array>} Array of comment objects
   */
  async getCommentsByMediaId(mediaId, chapterId = null) {
    try {
      const url = apiConfig.getUrl('commentsByMedia', { id: mediaId });
      const params = {};
      
      if (chapterId) {
        params.chapter_id = chapterId;
      }
      
      const response = await axios.get(url, { params });
      
      return Array.isArray(response.data) ? response.data : [];
    } catch (error) {
      logger.error('Error fetching comments:', error);
      return [];
    }
  }
  
  /**
   * Add a new comment to a media item
   * @param {number} mediaId - ID of the media item
   * @param {string} userName - Username of the commenter
   * @param {string} commentText - Comment content
   * @param {number} [chapterId] - Optional chapter ID
   * @returns {Promise<Object>} Created comment data
   */
  async addComment(mediaId, userName, commentText, chapterId = null) {
    try {
      const url = apiConfig.getUrl('comments');
      const payload = {
        media_id: mediaId,
        user_name: userName,
        comment: commentText,
        chapter_id: chapterId
      };
      
      const response = await axios.post(url, payload);
      return response.data;
    } catch (error) {
      logger.error('Error adding comment:', error);
      throw error;
    }
  }
  
  /**
   * Update an existing comment
   * @param {number} commentId - ID of the comment to update
   * @param {string} commentText - Updated comment content
   * @returns {Promise<Object>} Updated comment data
   */
  async updateComment(commentId, commentText) {
    try {
      const url = apiConfig.getUrl('comments') + '/' + commentId;
      const payload = {
        comment: commentText
      };
      
      const response = await axios.put(url, payload);
      return response.data;
    } catch (error) {
      logger.error('Error updating comment:', error);
      throw error;
    }
  }
  
  /**
   * Delete a comment
   * @param {number} commentId - ID of the comment to delete
   * @returns {Promise<Object>} Response data
   */
  async deleteComment(commentId) {
    try {
      const url = apiConfig.getUrl('comments') + '/' + commentId;
      const response = await axios.delete(url);
      return response.data;
    } catch (error) {
      logger.error('Error deleting comment:', error);
      throw error;
    }
  }
  
  /**
   * Format a comment timestamp for display
   * @param {string} timestamp - ISO timestamp string
   * @returns {string} Formatted date and time
   */
  formatCommentTimestamp(timestamp) {
    try {
      const date = new Date(timestamp);
      return date.toLocaleString();
    } catch (error) {
      logger.error('Error formatting comment timestamp:', error);
      return timestamp;
    }
  }
  
  /**
   * Get comment count for a media item
   * @param {number} mediaId - ID of the media item
   * @returns {Promise<number>} Comment count
   */
  async getCommentCount(mediaId) {
    try {
      const comments = await this.getCommentsByMediaId(mediaId);
      return comments.length;
    } catch (error) {
      logger.error('Error getting comment count:', error);
      return 0;
    }
  }
}

// Export as singleton
export const commentsService = new CommentsService();