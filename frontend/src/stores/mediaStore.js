// frontend/src/stores/mediaStore.js
import { create } from 'zustand';
import axios from 'axios';
import { logger } from '../lib/logger';
import { apiConfig } from '../lib/apiConfig';
import { useUserStore } from './userStore';

export const useMediaStore = create((set, get) => ({
  mediaItems: [],
  selectedItem: null,
  loading: false,
  error: null,
  
  fetchMedia: async (filters = {}) => {
    try {
      set({ loading: true, error: null });
      
      // Get user from userStore
      const user = useUserStore.getState().user;
      
      // Pass user as second arg, filters as third
      const url = apiConfig.getUrl('media', user, filters);
      
      const response = await axios.get(url);
      
      // Ensure we always have an array of media items
      const mediaItems = Array.isArray(response.data) ? response.data : [];
      
      set({ mediaItems, loading: false });
      return mediaItems;
    } catch (error) {
      const errorData = apiConfig.handleApiError(error, 'fetchMedia');
      logger.error('Error fetching media:', errorData);
      set({ error: 'Failed to load media', loading: false, mediaItems: [] });
      return [];
    }
  },
  
  fetchMediaById: async (id) => {
    try {
      set({ loading: true, error: null });
      
      // Get user from userStore
      const user = useUserStore.getState().user;
      
      // Pass user as second arg, {id} as third
      const url = apiConfig.getUrl('mediaById', user, { id });
      
      const response = await axios.get(url);
      const data = response.data;
      
      if (Array.isArray(data) && data.length > 0) {
        set({ selectedItem: data[0], loading: false });
        return data[0];
      } else if (data && !Array.isArray(data)) {
        // Handle case where API returns a single object instead of an array
        set({ selectedItem: data, loading: false });
        return data;
      } else {
        set({ error: 'Media not found', loading: false });
        return null;
      }
    } catch (error) {
      const errorData = apiConfig.handleApiError(error, 'fetchMediaById');
      logger.error('Error fetching media by id:', errorData);
      set({ error: 'Failed to load media details', loading: false });
      return null;
    }
  },
  
  clearSelectedItem: () => set({ selectedItem: null }),
  
  trackMediaEvent: async (mediaId, event, details = {}) => {
    try {
      // Get user from userStore (assume this is passed via get())
      const { user } = get();
      if (!user || !mediaId) return;
      
      const payload = {
        media_id: mediaId,
        event_type: event,
        user_name: user,
        timestamp: new Date().toISOString(),
        ...details
      };
      
      // Use apiConfig to get the URL for tracking events
      const url = apiConfig.getUrl('track');
      
      const response = await axios.post(url, payload);
      return response.data;
    } catch (error) {
      const errorData = apiConfig.handleApiError(error, 'trackMediaEvent');
      logger.error('Error tracking media event:', errorData);
      return null;
    }
  },
  
  // New method for tracking view duration
  trackViewDuration: async (mediaId, duration, percentage = null) => {
    try {
      return await get().trackMediaEvent(mediaId, 'view_duration', {
        duration,
        percentage,
        view_end: new Date().toISOString()
      });
    } catch (error) {
      logger.error('Error tracking view duration:', error);
      return null;
    }
  },
  
  // New method for reporting that user viewed an entire media item
  trackCompletion: async (mediaId) => {
    try {
      return await get().trackMediaEvent(mediaId, 'completion', {
        completed_at: new Date().toISOString()
      });
    } catch (error) {
      logger.error('Error tracking completion:', error);
      return null;
    }
  }
}));