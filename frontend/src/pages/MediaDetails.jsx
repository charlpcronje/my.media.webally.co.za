// frontend/src/pages/MediaDetails.jsx
import React, { useEffect, useRef, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { useMediaStore } from '@/stores/mediaStore';
import { useUserStore } from '@/stores/userStore';
import { useToast } from '@/components/ui/use-toast';
import { ArrowLeft, Play, Pause, Volume2, VolumeX, DownloadIcon, Maximize } from 'lucide-react';
import { logger } from '@/lib/logger';
import { mediaAnalyticsService } from '@/services/MediaAnalyticsService';
import { ChaptersComponent } from '@/components/ChaptersComponent';
import { CommentsList } from '@/components/CommentsList';
import { ImageViewer } from '@/components/ImageViewer';
import { apiConfig } from '@/lib/apiConfig';

export function MediaDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const { selectedItem, loading, error, fetchMediaById, clearSelectedItem } = useMediaStore();
  const { user } = useUserStore();
  
  const [isPlaying, setIsPlaying] = useState(false);
  const [isMuted, setIsMuted] = useState(false);
  const [duration, setDuration] = useState(0);
  const [currentTime, setCurrentTime] = useState(0);
  const [progress, setProgress] = useState(0);
  const [activeTab, setActiveTab] = useState('chapters');
  const [imageViewerOpen, setImageViewerOpen] = useState(false);
  
  const mediaRef = useRef(null);
  const trackingSessionId = useRef(null);
  
  // Fetch media details
  useEffect(() => {
    fetchMediaById(id);
    
    return () => {
      clearSelectedItem();
      
      // End tracking session if active
      if (trackingSessionId.current) {
        mediaAnalyticsService.endTracking(trackingSessionId.current);
        trackingSessionId.current = null;
      }
    };
  }, [id, fetchMediaById, clearSelectedItem]);
  
  // Start tracking when media loads
  useEffect(() => {
    if (selectedItem && user) {
      // Start a tracking session
      trackingSessionId.current = mediaAnalyticsService.startTracking(
        selectedItem,
        user,
        {
          getDurationPercentage: () => {
            if (mediaRef.current && duration > 0) {
              return (mediaRef.current.currentTime / duration) * 100;
            }
            return null;
          }
        }
      );
    }
  }, [selectedItem, user]);
  
  // Media player event handlers
  const handlePlayPause = () => {
    if (mediaRef.current) {
      if (isPlaying) {
        mediaRef.current.pause();
        mediaAnalyticsService.trackEvent(selectedItem.id, 'pause', { 
          user_name: user,
          position: mediaRef.current.currentTime,
          percentage: (mediaRef.current.currentTime / mediaRef.current.duration) * 100
        });
      } else {
        mediaRef.current.play()
          .then(() => {
            mediaAnalyticsService.trackEvent(selectedItem.id, 'play', { 
              user_name: user,
              position: mediaRef.current.currentTime,
              percentage: (mediaRef.current.currentTime / mediaRef.current.duration) * 100
            });
          })
          .catch(err => {
            logger.error('Error playing media:', err);
            toast({
              variant: 'destructive',
              title: 'Playback error',
              description: 'Could not play this media file'
            });
          });
      }
    }
  };
  
  const handleMuteToggle = () => {
    if (mediaRef.current) {
      mediaRef.current.muted = !isMuted;
      setIsMuted(!isMuted);
    }
  };
  
  const handleTimeUpdate = () => {
    if (mediaRef.current) {
      const current = mediaRef.current.currentTime;
      const duration = mediaRef.current.duration;
      
      setCurrentTime(current);
      setProgress((current / duration) * 100);
    }
  };
  
  const handleSeek = (e) => {
    if (mediaRef.current && duration) {
      const rect = e.currentTarget.getBoundingClientRect();
      const seekPos = (e.clientX - rect.left) / rect.width;
      const seekTime = duration * seekPos;
      
      mediaRef.current.currentTime = seekTime;
      setCurrentTime(seekTime);
      setProgress((seekTime / duration) * 100);
      
      mediaAnalyticsService.trackEvent(selectedItem.id, 'seek', { 
        user_name: user,
        position: seekTime,
        percentage: (seekTime / duration) * 100
      });
    }
  };
  
  const handleDownload = () => {
    if (selectedItem) {
      const url = apiConfig.getUrl('mediaById', { id: selectedItem.id }) + '/download';
      
      const link = document.createElement('a');
      link.href = url;
      link.download = selectedItem.filename || `media-${selectedItem.id}`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      mediaAnalyticsService.trackInteraction(selectedItem.id, 'download', user);
      
      toast({
        title: 'Download started',
        description: `Downloading ${selectedItem.caption}`
      });
    }
  };
  
  // Handle chapter navigation
  const handleChapterClick = (chapter) => {
    if (mediaRef.current && selectedItem.type !== 'image') {
      mediaRef.current.currentTime = chapter.start_time;
      
      if (!isPlaying) {
        handlePlayPause();
      }
    }
  };
  
  // Handle image click for images
  const handleImageClick = () => {
    if (selectedItem && selectedItem.type === 'image') {
      mediaAnalyticsService.trackImageInteraction(selectedItem.id, 'click', user);
      toast({
        title: selectedItem.caption,
        description: selectedItem.description || 'No description available'
      });
    }
  };
  
  // Open image viewer for image enlargement
  const handleImageEnlarge = () => {
    if (selectedItem && selectedItem.type === 'image') {
      setImageViewerOpen(true);
    }
  };
  
  // Format time in MM:SS
  const formatTime = (seconds) => {
    if (isNaN(seconds)) return '0:00';
    
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
  };
  
  if (loading) {
    return (
      <div className="flex flex-col items-center justify-center h-[60vh]">
        <div className="animate-spin h-8 w-8 border-4 border-primary border-t-transparent rounded-full"></div>
        <p className="mt-4 text-muted-foreground">Loading media...</p>
      </div>
    );
  }
  
  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-[60vh]">
        <p className="text-destructive text-lg font-medium">{error}</p>
        <Button variant="outline" onClick={() => navigate(-1)} className="mt-4">
          Go back
        </Button>
      </div>
    );
  }
  
  if (!selectedItem) return null;
  
  const renderMedia = () => {
    // Construct media URL using our API config
    const mediaUrl = apiConfig.getUrl('mediaById', { id: selectedItem.id }) + '/stream';
    const thumbnailUrl = selectedItem.thumbnail ? 
      apiConfig.getUrl('mediaById', { id: selectedItem.id }) + '/thumbnail' : 
      undefined;
    
    if (selectedItem.type === 'video') {
      return (
        <div className="media-player rounded-lg overflow-hidden bg-black">
          <video
            ref={mediaRef}
            src={mediaUrl}
            className="w-full max-h-[70vh] object-contain"
            poster={thumbnailUrl}
            onClick={handlePlayPause}
            onPlay={() => setIsPlaying(true)}
            onPause={() => setIsPlaying(false)}
            onDurationChange={(e) => setDuration(e.target.duration)}
            onTimeUpdate={handleTimeUpdate}
            onEnded={() => {
              setIsPlaying(false);
              mediaAnalyticsService.trackEvent(selectedItem.id, 'ended', { 
                user_name: user,
                percentage: 100,
                position: duration,
                completed: true
              });
            }}
          ></video>
          
          <div className="media-controls p-3 bg-black/60">
            <div className="flex items-center gap-2 mb-2">
              <Button 
                variant="ghost" 
                size="icon" 
                className="h-8 w-8 text-white" 
                onClick={handlePlayPause}
              >
                {isPlaying ? <Pause size={16} /> : <Play size={16} />}
              </Button>
              
              <Button 
                variant="ghost" 
                size="icon" 
                className="h-8 w-8 text-white" 
                onClick={handleMuteToggle}
              >
                {isMuted ? <VolumeX size={16} /> : <Volume2 size={16} />}
              </Button>
              
              <div className="text-xs text-white">
                {formatTime(currentTime)} / {formatTime(duration)}
              </div>
            </div>
            
            <div 
              className="h-2 bg-slate-700 rounded-full cursor-pointer" 
              onClick={handleSeek}
            >
              <div 
                className="h-full bg-primary rounded-full" 
                style={{ width: `${progress}%` }}
              ></div>
            </div>
          </div>
        </div>
      );
    }
    
    if (selectedItem.type === 'audio') {
      return (
        <div className="media-player rounded-lg overflow-hidden bg-card p-4 border">
          {thumbnailUrl && (
            <div className="mb-4 flex justify-center">
              <img 
                src={thumbnailUrl} 
                alt={selectedItem.caption} 
                className="w-48 h-48 object-cover rounded-lg" 
              />
            </div>
          )}
          
          <audio
            ref={mediaRef}
            src={mediaUrl}
            className="w-full"
            onPlay={() => setIsPlaying(true)}
            onPause={() => setIsPlaying(false)}
            onDurationChange={(e) => setDuration(e.target.duration)}
            onTimeUpdate={handleTimeUpdate}
            onEnded={() => {
              setIsPlaying(false);
              mediaAnalyticsService.trackEvent(selectedItem.id, 'ended', { 
                user_name: user,
                percentage: 100,
                position: duration,
                completed: true
              });
            }}
          ></audio>
          
          <div className="mt-4 space-y-3">
            <div className="flex items-center gap-2">
              <Button 
                variant="outline" 
                size="icon" 
                className="h-10 w-10" 
                onClick={handlePlayPause}
              >
                {isPlaying ? <Pause size={18} /> : <Play size={18} />}
              </Button>
              
              <Button 
                variant="outline" 
                size="icon" 
                className="h-10 w-10" 
                onClick={handleMuteToggle}
              >
                {isMuted ? <VolumeX size={18} /> : <Volume2 size={18} />}
              </Button>
              
              <div className="text-sm">
                {formatTime(currentTime)} / {formatTime(duration)}
              </div>
            </div>
            
            <Progress value={progress} className="h-2 cursor-pointer" onClick={handleSeek} />
          </div>
        </div>
      );
    }
    
    if (selectedItem.type === 'image') {
      return (
        <div className="flex justify-center relative group">
          <img 
            src={mediaUrl} 
            alt={selectedItem.caption} 
            className="max-h-[70vh] object-contain rounded-lg cursor-pointer" 
            onClick={handleImageClick}
          />
          
          <button
            className="absolute top-2 right-2 bg-black/50 p-2 rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity"
            onClick={handleImageEnlarge}
            aria-label="Enlarge image"
          >
            <Maximize size={18} />
          </button>
          
          {/* Image viewer modal */}
          <ImageViewer 
            image={mediaUrl}
            isOpen={imageViewerOpen}
            onClose={() => setImageViewerOpen(false)}
            mediaId={selectedItem.id}
          />
        </div>
      );
    }
    
    return null;
  };
  
  return (
    <div className="space-y-6">
      <div className="flex items-center">
        <Button 
          variant="ghost" 
          size="sm" 
          onClick={() => navigate(-1)} 
          className="mr-2"
        >
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
      </div>
      
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="md:col-span-2 space-y-4">
          {renderMedia()}
          
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold">{selectedItem.caption}</h1>
            
            <Button 
              variant="outline" 
              size="sm" 
              onClick={handleDownload}
              className="flex items-center gap-1"
            >
              <DownloadIcon className="h-4 w-4" />
              Download
            </Button>
          </div>
          
          <p className="text-muted-foreground whitespace-pre-line">
            {selectedItem.description}
          </p>
          
          {/* Tabs for chapters and comments */}
          <div className="border-b">
            <div className="flex space-x-2">
              <button
                className={`px-4 py-2 font-medium border-b-2 transition-colors ${activeTab === 'chapters' ? 'border-primary text-primary' : 'border-transparent hover:text-primary/80'}`}
                onClick={() => setActiveTab('chapters')}
              >
                Chapters
              </button>
              <button
                className={`px-4 py-2 font-medium border-b-2 transition-colors ${activeTab === 'comments' ? 'border-primary text-primary' : 'border-transparent hover:text-primary/80'}`}
                onClick={() => setActiveTab('comments')}
              >
                Comments
              </button>
            </div>
          </div>
          
          <div className="min-h-[200px]">
            {activeTab === 'chapters' && (
              <ChaptersComponent 
                mediaId={selectedItem.id} 
                currentTime={currentTime}
                onChapterClick={handleChapterClick}
              />
            )}
            
            {activeTab === 'comments' && (
              <CommentsList mediaId={selectedItem.id} />
            )}
          </div>
        </div>
        
        <div className="space-y-4">
          <div className="rounded-lg border bg-card p-4">
            <h2 className="font-semibold mb-3">Media Info</h2>
            
            <div className="space-y-2">
              <div className="flex items-center gap-2">
                <span className="text-sm font-medium">Type:</span>
                <Badge variant="outline" className="capitalize">
                  {selectedItem.type}
                </Badge>
              </div>
              
              <div className="flex items-center gap-2">
                <span className="text-sm font-medium">Format:</span>
                <span className="text-sm text-muted-foreground uppercase">
                  {selectedItem.filename?.split('.').pop()}
                </span>
              </div>
              
              {selectedItem.created_at && (
                <div className="flex items-center gap-2">
                  <span className="text-sm font-medium">Added:</span>
                  <span className="text-sm text-muted-foreground">
                    {new Date(selectedItem.created_at).toLocaleDateString()}
                  </span>
                </div>
              )}
            </div>
          </div>
          
          {selectedItem.tags && selectedItem.tags.length > 0 && (
            <div className="rounded-lg border bg-card p-4">
              <h2 className="font-semibold mb-3">Tags</h2>
              <div className="flex flex-wrap gap-2">
                {selectedItem.tags.map(tag => (
                  <Badge key={tag} variant="secondary">
                    #{tag}
                  </Badge>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}