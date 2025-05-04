// frontend/src/stores/mediaStore.js
import { create } from 'zustand';
import axios from 'axios';
import { logger } from '../lib/logger';

export const useMediaStore = create((set, get) => ({
  mediaItems: [],
  selectedItem: null,
  loading: false,
  error: null,
  
  fetchMedia: async (filters = {}) => {
    try {
      set({ loading: true, error: null });
      
      const queryParams = new URLSearchParams();
      if (filters.type) queryParams.append('type', filters.type);
      if (filters.tag) queryParams.append('tag', filters.tag);
      
      const response = await fetch(`/api/media.php?${queryParams.toString()}`);
      
      if (!response.ok) {
        throw new Error(`API error: ${response.status}`);
      }
      
      const data = await response.json();
      
      const mediaItems = Array.isArray(data) ? data : [];
      
      set({ mediaItems, loading: false });
      return mediaItems;
    } catch (error) {
      logger.error('Error fetching media:', error);
      set({ error: 'Failed to load media', loading: false, mediaItems: [] });
      return [];
    }
  },
  
  fetchMediaById: async (id) => {
    try {
      set({ loading: true, error: null });
      const response = await fetch(`/api/media.php?id=${id}`);
      
      if (!response.ok) {
        throw new Error(`API error: ${response.status}`);
      }
      
      const data = await response.json();
      
      if (Array.isArray(data) && data.length > 0) {
        set({ selectedItem: data[0], loading: false });
        return data[0];
      } else {
        set({ error: 'Media not found', loading: false });
        return null;
      }
    } catch (error) {
      logger.error('Error fetching media by id:', error);
      set({ error: 'Failed to load media details', loading: false });
      return null;
    }
  },
  
  clearSelectedItem: () => set({ selectedItem: null }),
  
  trackMediaEvent: async (mediaId, event, details = {}) => {
    try {
      const { user } = get();
      if (!user || !mediaId) return;
      
      const payload = {
        media_id: mediaId,
        event_type: event,
        user_name: user,
        ...details
      };
      
      const response = await fetch('/api/track.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      });
      
      if (!response.ok) {
        throw new Error(`API error: ${response.status}`);
      }
    } catch (error) {
      logger.error('Error tracking media event:', error);
    }
  }
}));