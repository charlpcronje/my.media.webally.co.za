// frontend/src/services/UserPreferencesService.js
import { logger } from '../lib/logger';
import { apiConfig } from '../lib/apiConfig';
import axios from 'axios';

/**
 * Service for managing user preferences
 */
class UserPreferencesService {
  constructor() {
    this.storageKeyPrefix = 'media-share-';
    this.preferenceFields = [
      'theme',
      'volume',
      'autoplay'
    ];
  }
  
  /**
   * Get all preferences for a user
   * @param {string} userName Username
   * @returns {Promise<Object>} User preferences
   */
  async getPreferences(userName) {
    try {
      // First try to get from API
      const url = apiConfig.getUrl('userPreferences', userName);
      
      const response = await axios.get(url);
      
      if (response.data) {
        // Save to local storage as cache
        this.saveLocalPreferences(userName, response.data);
        return response.data;
      }
      
      // If no data from API, try local storage
      return this.getLocalPreferences(userName);
    } catch (error) {
      logger.warn('Failed to get user preferences from API:', error);
      
      // Fallback to local storage
      return this.getLocalPreferences(userName);
    }
  }
  
  /**
   * Save preferences for a user
   * @param {string} userName Username
   * @param {Object} preferences Preferences to save
   * @returns {Promise<boolean>} Success status
   */
  async savePreferences(userName, preferences) {
    try {
      // First save to local storage
      this.saveLocalPreferences(userName, preferences);
      
      // Then try to save to API
      const url = apiConfig.getUrl('userPreferences', userName);
      
      // Send only preferences data in the body
      const data = { ...preferences }; 
      
      await axios.post(url, data);
      return true;
    } catch (error) {
      logger.warn('Failed to save user preferences to API:', error);
      return false;
    }
  }
  
  /**
   * Get a specific preference
   * @param {string} userName Username
   * @param {string} key Preference key
   * @param {*} defaultValue Default value if preference not found
   * @returns {Promise<*>} Preference value
   */
  async getPreference(userName, key, defaultValue = null) {
    const preferences = await this.getPreferences(userName);
    return preferences[key] !== undefined ? preferences[key] : defaultValue;
  }
  
  /**
   * Set a specific preference
   * @param {string} userName Username
   * @param {string} key Preference key
   * @param {*} value Preference value
   * @returns {Promise<boolean>} Success status
   */
  async setPreference(userName, key, value) {
    const preferences = await this.getPreferences(userName);
    preferences[key] = value;
    return this.savePreferences(userName, preferences);
  }
  
  /**
   * Get preferences from local storage
   * @param {string} userName Username
   * @returns {Object} User preferences
   */
  getLocalPreferences(userName) {
    try {
      const storageKey = this.getStorageKey(userName);
      const storedData = localStorage.getItem(storageKey);
      
      if (storedData) {
        return JSON.parse(storedData);
      }
      
      return this.getDefaultPreferences();
    } catch (error) {
      logger.error('Error reading user preferences from local storage:', error);
      return this.getDefaultPreferences();
    }
  }
  
  /**
   * Save preferences to local storage
   * @param {string} userName Username
   * @param {Object} preferences Preferences to save
   */
  saveLocalPreferences(userName, preferences) {
    try {
      const storageKey = this.getStorageKey(userName);
      
      // Filter out only known preference fields
      const filteredPreferences = {};
      this.preferenceFields.forEach(field => {
        if (preferences[field] !== undefined) {
          filteredPreferences[field] = preferences[field];
        }
      });
      
      localStorage.setItem(storageKey, JSON.stringify(filteredPreferences));
    } catch (error) {
      logger.error('Error saving user preferences to local storage:', error);
    }
  }
  
  /**
   * Get the storage key for a user
   * @param {string} userName Username
   * @returns {string} Storage key
   */
  getStorageKey(userName) {
    return `${this.storageKeyPrefix}preferences-${userName}`;
  }
  
  /**
   * Get default preferences
   * @returns {Object} Default preferences
   */
  getDefaultPreferences() {
    return {
      theme: 'system',
      volume: 80,
      autoplay: true
    };
  }
  
  /**
   * Clear all user preferences
   * @param {string} userName Username
   * @returns {Promise<boolean>} Success status
   */
  async clearPreferences(userName) {
    try {
      // Remove from local storage
      localStorage.removeItem(this.getStorageKey(userName));
      
      // Clear from API
      const url = apiConfig.getUrl('userPreferences', userName); 
      // Assuming DELETE uses the username in the URL query param like GET/POST
      await axios.delete(url);
      
      return true;
    } catch (error) {
      logger.error('Error clearing user preferences:', error);
      return false;
    }
  }
}

// Export as singleton
export const userPreferencesService = new UserPreferencesService();