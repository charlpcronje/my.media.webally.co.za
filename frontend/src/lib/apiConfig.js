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
   * @param {string} [userName] - Optional username to append as query parameter
   * @param {Object} [queryParams] - Optional additional query parameters
   * @returns {string} Full URL
   */
  getUrl(endpoint, userName = null, queryParams = {}) {
    let path;
    let idParam = queryParams.id; // Extract id if present

    // Check if endpoint is a function (for parameterized endpoints like /media/{id})
    if (typeof this.endpoints[endpoint] === 'function') {
      // Ensure idParam is passed correctly to endpoint functions
      path = this.endpoints[endpoint](idParam);
      // Remove id from queryParams since it's part of the path now
      delete queryParams.id;
    } else if (this.endpoints[endpoint]) {
      path = this.endpoints[endpoint];
    } else {
      // Handle custom endpoints (treat endpoint as the path)
      path = endpoint;
      logger.warn(`Using custom endpoint path: ${path}`);
    }

    let url = `${this.baseUrl}${path}`;

    // Start building query string
    const queryStringParams = { ...queryParams }; // Copy params

    // Append user_name query parameter if provided
    if (userName) {
        queryStringParams['user_name'] = userName;
    }

    // Build query string from remaining params
    const queryString = new URLSearchParams(queryStringParams).toString();

    // Append query string if it's not empty
    if (queryString) {
        url = `${url}?${queryString}`;
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