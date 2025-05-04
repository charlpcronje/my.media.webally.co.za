// frontend/src/components/EnhancedImageViewer.jsx
import React, { useEffect, useRef, useState } from 'react';
import { X, ZoomIn, ZoomOut, RotateCw, Download, Move, RefreshCw } from 'lucide-react';
import { mediaAnalyticsService } from '../services/MediaAnalyticsService';
import { useUserStore } from '../stores/userStore';
import { logger } from '../lib/logger';

/**
 * Enhanced image viewer with zoom, pan, and rotation capabilities
 */
export function EnhancedImageViewer({ 
  image, 
  isOpen, 
  onClose, 
  mediaId,
  caption,
  description
}) {
  const [scale, setScale] = useState(1);
  const [rotation, setRotation] = useState(0);
  const [position, setPosition] = useState({ x: 0, y: 0 });
  const [isPanning, setIsPanning] = useState(false);
  const [startPanPosition, setStartPanPosition] = useState({ x: 0, y: 0 });
  const [showInfo, setShowInfo] = useState(false);
  
  const imageRef = useRef(null);
  const containerRef = useRef(null);
  
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
  
  // Reset view when image changes or viewer is opened
  useEffect(() => {
    if (isOpen) {
      resetView();
    }
  }, [isOpen, image]);
  
  // Handle mouse wheel for zooming
  useEffect(() => {
    const handleWheel = (e) => {
      if (!isOpen || !containerRef.current) return;
      
      e.preventDefault();
      
      // Determine zoom direction
      const delta = e.deltaY < 0 ? 0.1 : -0.1;
      const newScale = Math.max(0.5, Math.min(5, scale + delta));
      
      setScale(newScale);
    };
    
    const container = containerRef.current;
    if (container) {
      container.addEventListener('wheel', handleWheel, { passive: false });
    }
    
    return () => {
      if (container) {
        container.removeEventListener('wheel', handleWheel);
      }
    };
  }, [isOpen, scale]);
  
  // Handle keyboard shortcuts
  useEffect(() => {
    if (!isOpen) return;
    
    const handleKeyDown = (e) => {
      switch (e.key) {
        case 'Escape':
          onClose();
          break;
        case '+':
        case '=':
          handleZoomIn();
          break;
        case '-':
          handleZoomOut();
          break;
        case 'r':
          handleRotate();
          break;
        case 'i':
          setShowInfo(!showInfo);
          break;
        case '0':
          resetView();
          break;
        default:
          break;
      }
    };
    
    window.addEventListener('keydown', handleKeyDown);
    
    return () => {
      window.removeEventListener('keydown', handleKeyDown);
    };
  }, [isOpen, showInfo]);
  
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
    setScale(prev => Math.min(prev + 0.25, 5));
    
    // Track zoom action
    if (mediaId) {
      mediaAnalyticsService.trackImageInteraction(mediaId, 'zoom_in', user);
    }
  };
  
  // Handle zoom out
  const handleZoomOut = () => {
    setScale(prev => Math.max(prev - 0.25, 0.5));
    
    // Track zoom action
    if (mediaId) {
      mediaAnalyticsService.trackImageInteraction(mediaId, 'zoom_out', user);
    }
  };
  
  // Handle rotation
  const handleRotate = () => {
    setRotation(prev => (prev + 90) % 360);
    
    // Track rotation action
    if (mediaId) {
      mediaAnalyticsService.trackImageInteraction(mediaId, 'rotate', user);
    }
  };
  
  // Reset view
  const resetView = () => {
    setScale(1);
    setRotation(0);
    setPosition({ x: 0, y: 0 });
    
    // Track reset action
    if (mediaId && isOpen) {
      mediaAnalyticsService.trackImageInteraction(mediaId, 'reset_view', user);
    }
  };
  
  // Handle mouse down for panning
  const handleMouseDown = (e) => {
    if (e.button !== 0) return; // Only left click
    
    setIsPanning(true);
    setStartPanPosition({
      x: e.clientX - position.x,
      y: e.clientY - position.y
    });
    
    // Track pan start action
    if (mediaId) {
      mediaAnalyticsService.trackImageInteraction(mediaId, 'pan_start', user);
    }
  };
  
  // Handle mouse move for panning
  const handleMouseMove = (e) => {
    if (!isPanning) return;
    
    setPosition({
      x: e.clientX - startPanPosition.x,
      y: e.clientY - startPanPosition.y
    });
  };
  
  // Handle mouse up for ending panning
  const handleMouseUp = () => {
    if (!isPanning) return;
    
    setIsPanning(false);
    
    // Track pan end action
    if (mediaId) {
      mediaAnalyticsService.trackImageInteraction(mediaId, 'pan_end', user);
    }
  };
  
  // Toggle image information display
  const toggleInfo = () => {
    setShowInfo(!showInfo);
    
    // Track info action
    if (mediaId) {
      mediaAnalyticsService.trackImageInteraction(mediaId, showInfo ? 'hide_info' : 'show_info', user);
    }
  };
  
  // Prevent the context menu
  const handleContextMenu = (e) => {
    e.preventDefault();
    return false;
  };
  
  if (!isOpen) return null;
  
  return (
    <div className="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4">
      {/* Close button */}
      <button 
        onClick={onClose}
        className="absolute top-4 right-4 text-white bg-black/50 p-2 rounded-full hover:bg-black/70 transition-colors"
        aria-label="Close"
      >
        <X />
      </button>
      
      {/* Image container */}
      <div 
        ref={containerRef}
        className="relative flex-1 h-full flex items-center justify-center overflow-hidden"
        style={{ cursor: isPanning ? 'grabbing' : 'grab' }}
        onMouseDown={handleMouseDown}
        onMouseMove={handleMouseMove}
        onMouseUp={handleMouseUp}
        onMouseLeave={handleMouseUp}
        onContextMenu={handleContextMenu}
      >
        {image && (
          <img 
            ref={imageRef}
            src={image} 
            alt={caption || "Enlarged view"} 
            style={{ 
              transform: `translate(${position.x}px, ${position.y}px) scale(${scale}) rotate(${rotation}deg)`,
              transformOrigin: 'center',
              transition: isPanning ? 'none' : 'transform 0.2s ease'
            }}
            className="max-w-none"
            draggable="false"
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
          onClick={resetView}
          className="text-white p-2 rounded-full hover:bg-black/70 transition-colors"
          aria-label="Reset view"
        >
          <RefreshCw size={20} />
        </button>
        
        <button 
          onClick={toggleInfo}
          className={`text-white p-2 rounded-full hover:bg-black/70 transition-colors ${showInfo ? 'bg-white/20' : ''}`}
          aria-label="Show information"
        >
          <span className="text-sm font-bold">i</span>
        </button>
        
        <button 
          onClick={handleDownload}
          className="text-white p-2 rounded-full hover:bg-black/70 transition-colors"
          aria-label="Download"
        >
          <Download size={20} />
        </button>
      </div>
      
      {/* Image information */}
      {showInfo && caption && (
        <div className="absolute top-4 left-4 max-w-md bg-black/70 p-4 rounded-lg text-white">
          <h3 className="text-lg font-semibold mb-2">{caption}</h3>
          {description && <p className="text-sm opacity-80">{description}</p>}
        </div>
      )}
      
      {/* Keyboard shortcuts info */}
      <div className="absolute top-4 left-1/2 transform -translate-x-1/2 bg-black/50 p-2 rounded-lg text-white text-xs">
        Zoom: Mouse wheel, + / - | Rotate: R | Info: I | Reset: 0 | Close: ESC
      </div>
    </div>
  );
}