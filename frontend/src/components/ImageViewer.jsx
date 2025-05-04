// frontend/src/components/ImageViewer.jsx
import React, { useEffect } from 'react';
import { X, ZoomIn, ZoomOut, RotateCw, Download } from 'lucide-react';
import { mediaAnalyticsService } from '../services/MediaAnalyticsService';
import { useUserStore } from '../stores/userStore';

export function ImageViewer({ 
  image, 
  isOpen, 
  onClose, 
  mediaId 
}) {
  const [scale, setScale] = React.useState(1);
  const [rotation, setRotation] = React.useState(0);
  const { user } = useUserStore();
  
  // Track when the image is enlarged
  useEffect(() => {
    if (isOpen && mediaId) {
      mediaAnalyticsService.trackImageInteraction(mediaId, 'enlarge', user);
    }
    
    // Start a session timer for how long the enlarged view is open
    let sessionId = null;
    
    if (isOpen && mediaId) {
      sessionId = mediaAnalyticsService.startTracking(
        { id: mediaId, type: 'image' },
        user,
        { enlargedView: true }
      );
    }
    
    return () => {
      // End the tracking session when component unmounts or dialog closes
      if (sessionId) {
        mediaAnalyticsService.endTracking(sessionId);
      }
    };
  }, [isOpen, mediaId, user]);
  
  // Handle download
  const handleDownload = () => {
    if (!image) return;
    
    // Track the download action
    if (mediaId) {
      mediaAnalyticsService.trackInteraction(mediaId, 'download', user);
    }
    
    // Create a download link
    const link = document.createElement('a');
    link.href = image;
    link.download = image.split('/').pop();
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };
  
  // Handle zoom in
  const handleZoomIn = () => {
    setScale(prev => Math.min(prev + 0.25, 3));
  };
  
  // Handle zoom out
  const handleZoomOut = () => {
    setScale(prev => Math.max(prev - 0.25, 0.5));
  };
  
  // Handle rotation
  const handleRotate = () => {
    setRotation(prev => (prev + 90) % 360);
  };
  
  if (!isOpen) return null;
  
  return (
    <div className="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4">
      {/* Close button */}
      <button 
        onClick={onClose}
        className="absolute top-4 right-4 text-white bg-black/50 p-2 rounded-full hover:bg-black/70 transition-colors"
        aria-label="Close"
      >
        <X />
      </button>
      
      {/* Image container */}
      <div className="relative max-w-full max-h-full overflow-auto">
        {image && (
          <img 
            src={image} 
            alt="Enlarged view" 
            style={{ 
              transform: `scale(${scale}) rotate(${rotation}deg)`,
              transformOrigin: 'center',
              transition: 'transform 0.2s ease'
            }}
            className="max-w-none"
          />
        )}
      </div>
      
      {/* Controls */}
      <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex items-center gap-3 bg-black/50 p-2 rounded-full">
        <button 
          onClick={handleZoomIn}
          className="text-white p-2 rounded-full hover:bg-black/70 transition-colors"
          aria-label="Zoom in"
        >
          <ZoomIn size={20} />
        </button>
        
        <button 
          onClick={handleZoomOut}
          className="text-white p-2 rounded-full hover:bg-black/70 transition-colors"
          aria-label="Zoom out"
        >
          <ZoomOut size={20} />
        </button>
        
        <button 
          onClick={handleRotate}
          className="text-white p-2 rounded-full hover:bg-black/70 transition-colors"
          aria-label="Rotate"
        >
          <RotateCw size={20} />
        </button>
        
        <button 
          onClick={handleDownload}
          className="text-white p-2 rounded-full hover:bg-black/70 transition-colors"
          aria-label="Download"
        >
          <Download size={20} />
        </button>
      </div>
    </div>
  );
}