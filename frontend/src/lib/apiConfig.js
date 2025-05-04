// frontend/src/lib/apiConfig.js
import { logger } from './logger';

/**
 * API Configuration for Media Share application
 * Centralizes API URLs and provides methods for API interactions
 */
class ApiConfig {
  constructor() {
    // Base URL for API endpoints (using subdomain)
    this.baseUrl = 'https://api.media.webally.co.za';

    // Get username from URL parameter (?name=...)
    const urlParams = new URLSearchParams(window.location.search);
    this.userName = urlParams.get('name');
    if (!this.userName) {
      logger.warn('ApiConfig: No "name" parameter found in URL. API requests may lack user context.');
    }

    // API endpoints
    this.endpoints = {
      media: '/media',
      mediaById: (id) => `/media/${id}`,
      tags: '/tags',
      session: '/session',
      track: '/track',
      chapters: '/chapters',
      chaptersByMedia: (mediaId) => `/chapters/media/${mediaId}`,
      comments: '/comments',
      commentsByMedia: (mediaId) => `/comments/media/${mediaId}`,
      search: '/search',
      analytics: '/analytics',
      userPreferences: '/preferences',
      userAnalytics: (userName) => `/analytics?type=user&user_name=${userName}`,
      mediaAnalytics: (mediaId) => `/analytics?type=media&id=${mediaId}`,
    };
  }

  /**
   * Get full URL for an endpoint
   * @param {string} endpoint - Endpoint key or path
   * @param {Object} params - URL parameters
   * @returns {string} Full URL
   */
  getUrl(endpoint, params = {}) {
    let url;
    
    // Check if endpoint is a function (for parameterized endpoints)
    if (typeof this.endpoints[endpoint] === 'function') {
      url = `${this.baseUrl}${this.endpoints[endpoint](params.id)}`;
    } else if (this.endpoints[endpoint]) {
      url = `${this.baseUrl}${this.endpoints[endpoint]}`;
    } else {
      // Handle custom endpoints
      url = `${this.baseUrl}${endpoint}`;
      logger.warn(`Using custom endpoint: ${endpoint}`);
    }
    
    // Add query parameters if provided
    if (params.query) {
      const queryParams = new URLSearchParams();
      Object.entries(params.query).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          queryParams.append(key, value);
        }
      });
      
      const queryString = queryParams.toString();
      if (queryString) {
        url = `${url}?${queryString}`;
      }
    }

    // Append user_name query parameter if available
    if (this.userName) {
      const separator = url.includes('?') ? '&' : '?';
      url = `${url}${separator}user_name=${encodeURIComponent(this.userName)}`;
    }

    logger.debug(`Generated API URL: ${url}`);
    return url;
  }
  
  /**
   * Helper method to handle API errors
   * @param {Error} error - Error object 
   * @param {string} context - Context where the error occurred
   * @returns {Object} Standardized error object
   */
  handleApiError(error, context) {
    logger.error(`API Error in ${context}:`, error);
    
    return {
      success: false,
      message: error.response?.data?.message || error.message || 'Unknown API error',
      status: error.response?.status || 500,
      context
    };
  }
}

// Export singleton instance
export const apiConfig = new ApiConfig();