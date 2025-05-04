// frontend/src/pages/MediaDetails.jsx
import React, { useEffect, useRef, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { useMediaStore } from '@/stores/mediaStore';
import { useToast } from '@/components/ui/use-toast';
import { ArrowLeft, Play, Pause, Volume2, VolumeX, DownloadIcon } from 'lucide-react';
import { logger } from '@/lib/logger';

export function MediaDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const { 
    selectedItem, 
    loading, 
    error, 
    fetchMediaById, 
    clearSelectedItem,
    trackMediaEvent
  } = useMediaStore();
  
  const [isPlaying, setIsPlaying] = useState(false);
  const [isMuted, setIsMuted] = useState(false);
  const [duration, setDuration] = useState(0);
  const [currentTime, setCurrentTime] = useState(0);
  const [progress, setProgress] = useState(0);
  
  const mediaRef = useRef(null);
  const analyticsInterval = useRef(null);
  
  // Fetch media details
  useEffect(() => {
    fetchMediaById(id);
    
    return () => {
      clearSelectedItem();
      if (analyticsInterval.current) {
        clearInterval(analyticsInterval.current);
      }
    };
  }, [id, fetchMediaById, clearSelectedItem]);
  
  // Track view event when media loads
  useEffect(() => {
    if (selectedItem) {
      trackMediaEvent(selectedItem.id, 'view');
    }
  }, [selectedItem, trackMediaEvent]);
  
  // Media player event handlers
  const handlePlayPause = () => {
    if (mediaRef.current) {
      if (isPlaying) {
        mediaRef.current.pause();
        trackMediaEvent(selectedItem.id, 'pause', { 
          position: mediaRef.current.currentTime,
          percentage: (mediaRef.current.currentTime / mediaRef.current.duration) * 100
        });
      } else {
        mediaRef.current.play()
          .then(() => {
            trackMediaEvent(selectedItem.id, 'play', { 
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
      
      trackMediaEvent(selectedItem.id, 'seek', { 
        position: seekTime,
        percentage: (seekTime / duration) * 100
      });
    }
  };
  
  const handleDownload = () => {
    if (selectedItem) {
      const link = document.createElement('a');
      link.href = `/backend/uploads/${selectedItem.filename}`;
      link.download = selectedItem.filename;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      trackMediaEvent(selectedItem.id, 'download');
      
      toast({
        title: 'Download started',
        description: `Downloading ${selectedItem.caption}`
      });
    }
  };
  
  // Setup media tracking
  useEffect(() => {
    if (selectedItem && selectedItem.type !== 'image' && isPlaying) {
      // Track progress every 10 seconds
      analyticsInterval.current = setInterval(() => {
        if (mediaRef.current) {
          trackMediaEvent(selectedItem.id, 'progress', {
            position: mediaRef.current.currentTime,
            percentage: (mediaRef.current.currentTime / mediaRef.current.duration) * 100
          });
        }
      }, 10000);
    } 
    
    return () => {
      if (analyticsInterval.current) {
        clearInterval(analyticsInterval.current);
      }
    };
  }, [selectedItem, isPlaying, trackMediaEvent]);
  
  // Format time in MM:SS
  const formatTime = (seconds) => {
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
    const baseUrl = '/backend/uploads';
    
    if (selectedItem.type === 'video') {
      return (
        <div className="media-player rounded-lg overflow-hidden bg-black">
          <video
            ref={mediaRef}
            src={`${baseUrl}/${selectedItem.filename}`}
            className="w-full max-h-[70vh] object-contain"
            poster={selectedItem.thumbnail ? `${baseUrl}/${selectedItem.thumbnail}` : undefined}
            onClick={handlePlayPause}
            onPlay={() => setIsPlaying(true)}
            onPause={() => setIsPlaying(false)}
            onDurationChange={(e) => setDuration(e.target.duration)}
            onTimeUpdate={handleTimeUpdate}
            onEnded={() => {
              setIsPlaying(false);
              trackMediaEvent(selectedItem.id, 'ended', { 
                percentage: 100,
                position: duration
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
          {selectedItem.thumbnail && (
            <div className="mb-4 flex justify-center">
              <img 
                src={`${baseUrl}/${selectedItem.thumbnail}`} 
                alt={selectedItem.caption} 
                className="w-48 h-48 object-cover rounded-lg" 
              />
            </div>
          )}
          
          <audio
            ref={mediaRef}
            src={`${baseUrl}/${selectedItem.filename}`}
            className="w-full"
            onPlay={() => setIsPlaying(true)}
            onPause={() => setIsPlaying(false)}
            onDurationChange={(e) => setDuration(e.target.duration)}
            onTimeUpdate={handleTimeUpdate}
            onEnded={() => {
              setIsPlaying(false);
              trackMediaEvent(selectedItem.id, 'ended', { 
                percentage: 100,
                position: duration
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
        <div className="flex justify-center">
          <img 
            src={`${baseUrl}/${selectedItem.filename}`} 
            alt={selectedItem.caption} 
            className="max-h-[70vh] object-contain rounded-lg" 
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
                  {selectedItem.filename.split('.').pop()}
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