// frontend/src/services/SearchService.js
import { logger } from '../lib/logger';
import { apiConfig } from '../lib/apiConfig';
import { mediaAnalyticsService } from './MediaAnalyticsService';
import axios from 'axios';

/**
 * Service for handling search functionality
 */
class SearchService {
  /**
   * Perform a search query
   * @param {string} searchTerm - Search term
   * @param {Object} filters - Search filters
   * @param {string} user - Username
   * @returns {Promise<Object>} Search results
   */
  async search(searchTerm, filters = {}, user) {
    try {
      // Prepare the query parameters
      const queryParams = {
        q: searchTerm,
        ...filters
      };
      
      const url = apiConfig.getUrl('search', { query: queryParams });
      const response = await axios.get(url);
      
      // Track the search query
      await this.trackSearch(searchTerm, filters, user, response.data.length || 0);
      
      return response.data;
    } catch (error) {
      logger.error('Error performing search:', error);
      return [];
    }
  }
  
  /**
   * Track a search query for analytics
   * @param {string} searchTerm - Search term
   * @param {Object} filters - Search filters
   * @param {string} user - Username
   * @param {number} resultsCount - Number of results
   * @returns {Promise<void>}
   */
  async trackSearch(searchTerm, filters, user, resultsCount) {
    try {
      await mediaAnalyticsService.trackSearch(searchTerm, filters, user, resultsCount);
    } catch (error) {
      logger.error('Error tracking search:', error);
    }
  }
  
  /**
   * Get search suggestions based on a partial query
   * @param {string} partialQuery - Partial search query
   * @returns {Promise<Array>} Search suggestions
   */
  async getSuggestions(partialQuery) {
    try {
      if (!partialQuery || partialQuery.length < 2) {
        return [];
      }
      
      const url = apiConfig.getUrl('searchSuggestions', { query: { q: partialQuery } });
      const response = await axios.get(url);
      
      return response.data;
    } catch (error) {
      logger.error('Error getting search suggestions:', error);
      return [];
    }
  }
  
  /**
   * Get recent searches for a user
   * @param {string} user - Username
   * @param {number} limit - Maximum number of recent searches to return
   * @returns {Promise<Array>} Recent searches
   */
  async getRecentSearches(user, limit = 5) {
    try {
      // Try to get recent searches from local storage first
      const searches = this.getLocalRecentSearches(user, limit);
      
      // If there are no local searches, try to get from the server
      if (searches.length === 0) {
        const url = apiConfig.getUrl('recentSearches', { 
          query: { user_name: user, limit }
        });
        
        const response = await axios.get(url);
        return response.data;
      }
      
      return searches;
    } catch (error) {
      logger.error('Error getting recent searches:', error);
      return this.getLocalRecentSearches(user, limit);
    }
  }
  
  /**
   * Get recent searches from local storage
   * @param {string} user - Username
   * @param {number} limit - Maximum number of recent searches to return
   * @returns {Array} Recent searches
   */
  getLocalRecentSearches(user, limit = 5) {
    try {
      const storageKey = this.getRecentSearchesKey(user);
      const storedData = localStorage.getItem(storageKey);
      
      if (storedData) {
        const searches = JSON.parse(storedData);
        return searches.slice(0, limit);
      }
      
      return [];
    } catch (error) {
      logger.error('Error reading recent searches from local storage:', error);
      return [];
    }
  }
  
  /**
   * Add a search to recent searches
   * @param {string} searchTerm - Search term
   * @param {Object} filters - Search filters
   * @param {string} user - Username
   * @returns {void}
   */
  addToRecentSearches(searchTerm, filters, user) {
    try {
      if (!searchTerm || !user) return;
      
      // Get existing recent searches
      const storageKey = this.getRecentSearchesKey(user);
      const storedData = localStorage.getItem(storageKey);
      
      const searches = storedData ? JSON.parse(storedData) : [];
      
      // Create search object with timestamp
      const searchObj = {
        term: searchTerm,
        filters,
        timestamp: new Date().toISOString()
      };
      
      // Remove duplicates
      const filteredSearches = searches.filter(s => s.term !== searchTerm);
      
      // Add new search to beginning
      filteredSearches.unshift(searchObj);
      
      // Keep only the most recent 10 searches
      const limitedSearches = filteredSearches.slice(0, 10);
      
      // Save to local storage
      localStorage.setItem(storageKey, JSON.stringify(limitedSearches));
    } catch (error) {
      logger.error('Error saving recent search:', error);
    }
  }
  
  /**
   * Clear recent searches for a user
   * @param {string} user - Username
   * @returns {void}
   */
  clearRecentSearches(user) {
    try {
      const storageKey = this.getRecentSearchesKey(user);
      localStorage.removeItem(storageKey);
    } catch (error) {
      logger.error('Error clearing recent searches:', error);
    }
  }
  
  /**
   * Get storage key for recent searches
   * @param {string} user - Username
   * @returns {string} Storage key
   */
  getRecentSearchesKey(user) {
    return `media-share-recent-searches-${user}`;
  }
}

// Export as singleton
export const searchService = new SearchService();