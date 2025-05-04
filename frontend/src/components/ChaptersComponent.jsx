// frontend/src/components/ChaptersComponent.jsx
import React, { useEffect, useState } from 'react';
import { chaptersService } from '../services/ChaptersService';
import { mediaAnalyticsService } from '../services/MediaAnalyticsService';
import { useUserStore } from '../stores/userStore';
import { logger } from '../lib/logger';

export function ChaptersComponent({ 
  mediaId, 
  currentTime = 0, 
  onChapterClick,
  showAddChapter = false
}) {
  const [chapters, setChapters] = useState([]);
  const [loading, setLoading] = useState(true);
  const [currentChapter, setCurrentChapter] = useState(null);
  const [showForm, setShowForm] = useState(false);
  const [newChapter, setNewChapter] = useState({
    title: '',
    start_time: '',
    end_time: '',
    description: ''
  });
  
  const { user } = useUserStore();
  
  // Load chapters when component mounts
  useEffect(() => {
    const loadChapters = async () => {
      setLoading(true);
      try {
        const chaptersData = await chaptersService.getChaptersByMediaId(mediaId);
        setChapters(chaptersData);
      } catch (error) {
        logger.error('Error loading chapters:', error);
      } finally {
        setLoading(false);
      }
    };
    
    loadChapters();
  }, [mediaId]);
  
  // Update current chapter based on media position
  useEffect(() => {
    const chapter = chaptersService.findCurrentChapter(chapters, currentTime);
    setCurrentChapter(chapter);
  }, [chapters, currentTime]);
  
  // Handle chapter click
  const handleChapterClick = (chapter) => {
    if (onChapterClick) {
      onChapterClick(chapter);
      
      // Track chapter navigation
      mediaAnalyticsService.trackChapterNavigation(mediaId, chapter.id, user);
    }
  };
  
  // Handle adding a new chapter
  const handleAddChapter = async (e) => {
    e.preventDefault();
    
    try {
      // Convert time strings to seconds
      const startTime = chaptersService.parseTimeString(newChapter.start_time);
      const endTime = newChapter.end_time ? 
        chaptersService.parseTimeString(newChapter.end_time) : null;
      
      const chapterData = {
        title: newChapter.title,
        start_time: startTime,
        end_time: endTime,
        description: newChapter.description
      };
      
      // Create the chapter
      const createdChapter = await chaptersService.createChapter(mediaId, chapterData);
      
      // Add to local state
      setChapters([...chapters, createdChapter]);
      
      // Reset form
      setNewChapter({
        title: '',
        start_time: '',
        end_time: '',
        description: ''
      });
      
      setShowForm(false);
    } catch (error) {
      logger.error('Error adding chapter:', error);
    }
  };
  
  // Handle form input changes
  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setNewChapter({
      ...newChapter,
      [name]: value
    });
  };
  
  if (loading) {
    return <div className="text-sm text-muted-foreground animate-pulse">Loading chapters...</div>;
  }
  
  if (chapters.length === 0 && !showAddChapter) {
    return null; // Don't show anything if no chapters and not showing add form
  }
  
  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between">
        <h3 className="text-lg font-semibold">Chapters</h3>
        
        {showAddChapter && (
          <button 
            className="text-sm underline hover:text-primary"
            onClick={() => setShowForm(!showForm)}
          >
            {showForm ? 'Cancel' : 'Add Chapter'}
          </button>
        )}
      </div>
      
      {/* Add chapter form */}
      {showForm && (
        <form onSubmit={handleAddChapter} className="space-y-3 p-3 border rounded-md bg-card">
          <div>
            <label className="block text-sm font-medium mb-1">Title</label>
            <input
              type="text"
              name="title"
              value={newChapter.title}
              onChange={handleInputChange}
              className="w-full px-3 py-2 border rounded-md border-input"
              required
            />
          </div>
          
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm font-medium mb-1">Start Time (MM:SS)</label>
              <input
                type="text"
                name="start_time"
                value={newChapter.start_time}
                onChange={handleInputChange}
                className="w-full px-3 py-2 border rounded-md border-input"
                placeholder="00:00"
                required
              />
            </div>
            
            <div>
              <label className="block text-sm font-medium mb-1">End Time (Optional)</label>
              <input
                type="text"
                name="end_time"
                value={newChapter.end_time}
                onChange={handleInputChange}
                className="w-full px-3 py-2 border rounded-md border-input"
                placeholder="MM:SS"
              />
            </div>
          </div>
          
          <div>
            <label className="block text-sm font-medium mb-1">Description (Optional)</label>
            <textarea
              name="description"
              value={newChapter.description}
              onChange={handleInputChange}
              className="w-full px-3 py-2 border rounded-md border-input resize-none"
              rows="2"
            />
          </div>
          
          <div className="flex justify-end">
            <button
              type="submit"
              className="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
            >
              Add Chapter
            </button>
          </div>
        </form>
      )}
      
      {/* Chapters list */}
      {chapters.length > 0 && (
        <div className="space-y-1">
          {chapters
            .sort((a, b) => a.start_time - b.start_time)
            .map((chapter) => (
              <div
                key={chapter.id}
                onClick={() => handleChapterClick(chapter)}
                className={`
                  p-2 rounded-md cursor-pointer hover:bg-muted transition-colors
                  ${currentChapter?.id === chapter.id ? 'bg-primary/10 border-l-4 border-primary' : ''}
                `}
              >
                <div className="flex items-center justify-between">
                  <span className="font-medium">{chapter.title}</span>
                  <span className="text-sm text-muted-foreground">
                    {chaptersService.formatChapterTime(chapter.start_time)}
                    {chapter.end_time && ` - ${chaptersService.formatChapterTime(chapter.end_time)}`}
                  </span>
                </div>
                
                {chapter.description && (
                  <p className="text-sm text-muted-foreground line-clamp-2 mt-1">
                    {chapter.description}
                  </p>
                )}
              </div>
            ))}
        </div>
      )}
    </div>
  );
}