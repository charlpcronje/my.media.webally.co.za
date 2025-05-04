// frontend/src/services/MediaAnalyticsService.js
import { logger } from '../lib/logger';
import { apiConfig } from '../lib/apiConfig';
import axios from 'axios';

/**
 * Service for handling media analytics tracking
 */
class MediaAnalyticsService {
  constructor() {
    // Tracking intervals for different media types
    this.TRACKING_INTERVALS = {
      image: 5000,  // Every 5 seconds for images
      video: 10000, // Every 10 seconds for videos
      audio: 10000  // Every 10 seconds for audio
    };
    
    // Store active tracking sessions
    this.activeTrackingSessions = new Map();
  }

  /**
   * Start tracking a media item view
   * @param {Object} mediaItem - The media item being viewed
   * @param {string} userName - Username of the viewer
   * @param {Object} [options] - Additional tracking options
   * @returns {string} Session ID for the tracking session
   */
  startTracking(mediaItem, userName, options = {}) {
    if (!mediaItem || !mediaItem.id || !userName) {
      logger.error('MediaAnalyticsService: Invalid parameters for startTracking');
      return null;
    }
    
    // Generate a unique session ID
    const sessionId = `${mediaItem.id}-${userName}-${Date.now()}`;
    
    // Record start time
    const startData = {
      sessionId,
      mediaId: mediaItem.id,
      mediaType: mediaItem.type,
      userName,
      startTime: new Date(),
      lastUpdateTime: new Date(),
      totalViewTime: 0,
      intervalId: null,
      completed: false,
      options
    };
    
    // Track initial view event
    this.trackEvent(mediaItem.id, 'view', {
      session_id: sessionId
    });
    
    // Set up interval for periodic tracking
    const intervalTime = this.TRACKING_INTERVALS[mediaItem.type] || 10000;
    
    const intervalId = setInterval(() => {
      this.updateTracking(sessionId);
    }, intervalTime);
    
    startData.intervalId = intervalId;
    
    // Store tracking session
    this.activeTrackingSessions.set(sessionId, startData);
    
    return sessionId;
  }
  
  /**
   * Update an active tracking session
   * @param {string} sessionId - ID of the tracking session to update
   */
  updateTracking(sessionId) {
    const session = this.activeTrackingSessions.get(sessionId);
    
    if (!session) {
      logger.warn(`MediaAnalyticsService: Session not found for updateTracking: ${sessionId}`);
      return;
    }
    
    const now = new Date();
    const timeElapsed = now - session.lastUpdateTime;
    
    // Update session data
    session.lastUpdateTime = now;
    session.totalViewTime += timeElapsed;
    
    // Track progress event
    this.trackEvent(session.mediaId, 'progress', {
      session_id: sessionId,
      duration: Math.round(session.totalViewTime / 1000), // Convert to seconds
      percentage: session.options.getDurationPercentage ? 
        session.options.getDurationPercentage() : null
    });
  }
  
  /**
   * End a tracking session
   * @param {string} sessionId - ID of the tracking session to end
   * @param {Object} [finalData] - Final tracking data
   */
  endTracking(sessionId, finalData = {}) {
    const session = this.activeTrackingSessions.get(sessionId);
    
    if (!session) {
      logger.warn(`MediaAnalyticsService: Session not found for endTracking: ${sessionId}`);
      return;
    }
    
    // Clear the interval
    if (session.intervalId) {
      clearInterval(session.intervalId);
    }
    
    // Calculate final stats
    const now = new Date();
    const finalTimeElapsed = now - session.lastUpdateTime;
    const totalDuration = session.totalViewTime + finalTimeElapsed;
    
    // Determine if the viewing was completed
    const completed = finalData.completed || session.completed;
    
    // Track final event
    this.trackEvent(session.mediaId, 'ended', {
      session_id: sessionId,
      duration: Math.round(totalDuration / 1000), // Convert to seconds
      percentage: finalData.percentage || 
        (session.options.getDurationPercentage ? session.options.getDurationPercentage() : null),
      completed
    });
    
    // Remove from active sessions
    this.activeTrackingSessions.delete(sessionId);
    
    return {
      mediaId: session.mediaId,
      totalDuration: Math.round(totalDuration / 1000), // In seconds
      completed
    };
  }
  
  /**
   * Track a specific media event
   * @param {number} mediaId - ID of the media item
   * @param {string} eventType - Type of event (view, play, pause, etc.)
   * @param {Object} details - Additional event details
   * @returns {Promise<Object>} Response data
   */
  async trackEvent(mediaId, eventType, details = {}) {
    try {
      const url = apiConfig.getUrl('track');
      
      const payload = {
        media_id: mediaId,
        event_type: eventType,
        timestamp: new Date().toISOString(),
        ...details
      };
      
      const response = await axios.post(url, payload);
      return response.data;
    } catch (error) {
      logger.error(`MediaAnalyticsService: Error tracking ${eventType} event:`, error);
      return null;
    }
  }
  
  /**
   * Track navigation between chapters
   * @param {number} mediaId Media ID
   * @param {string} chapterId Chapter ID
   */
  async trackChapterNavigation(mediaId, chapterId) {
    return this.trackEvent(mediaId, 'chapter_navigation', {
      chapter_id: chapterId
    });
  }
  
  /**
   * Track a user interaction (like, share, download)
   * @param {number} mediaId Media ID
   * @param {string} interactionType Type of interaction
   */
  async trackInteraction(mediaId, interactionType) {
    return this.trackEvent(mediaId, interactionType, {});
  }
  
  /**
   * Track image interactions (zoom, pan)
   * @param {number} mediaId Media ID
   * @param {string} action Type of action (zoom_in, zoom_out, pan)
   */
  async trackImageInteraction(mediaId, action) {
    return this.trackEvent(mediaId, `image_${action}`, {});
  }
  
  /**
   * Track a search event
   * @param {string} searchTerm Search term
   * @param {Object} filters Applied filters
   */
  async trackSearch(searchTerm, filters) {
    return this.trackEvent(0, 'search', {
      search_term: searchTerm,
      filters: JSON.stringify(filters)
    });
  }
}

// Export as singleton
export const mediaAnalyticsService = new MediaAnalyticsService();