// frontend/src/components/MediaCard.jsx
import React from 'react';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Play, Image, Music } from 'lucide-react';
import { useMediaStore } from '@/stores/mediaStore';

const TYPE_ICONS = {
  video: <Play className="h-5 w-5" />,
  audio: <Music className="h-5 w-5" />,
  image: <Image className="h-5 w-5" />
};

const TYPE_CLASSES = {
  video: 'video-thumb',
  audio: 'audio-thumb',
  image: 'image-thumb'
};

export function MediaCard({ item }) {
  const { trackMediaEvent } = useMediaStore();
  
  const handleCardClick = () => {
    trackMediaEvent(item.id, 'view');
  };
  
  const renderThumbnail = () => {
    const baseUrl = '/backend/uploads';
    
    // For video/audio, show a thumbnail if available, otherwise a placeholder
    if (item.type === 'video' || item.type === 'audio') {
      return item.thumbnail ? (
        <img 
          src={`${baseUrl}/${item.thumbnail}`} 
          alt={item.caption} 
          className={TYPE_CLASSES[item.type]} 
        />
      ) : (
        <div className={`${TYPE_CLASSES[item.type]} bg-muted flex items-center justify-center`}>
          {TYPE_ICONS[item.type]}
        </div>
      );
    }
    
    // For images, show the actual image
    if (item.type === 'image') {
      return (
        <img 
          src={`${baseUrl}/${item.filename}`} 
          alt={item.caption} 
          className={TYPE_CLASSES[item.type]} 
        />
      );
    }
    
    // Default placeholder
    return (
      <div className="aspect-video bg-muted flex items-center justify-center">
        {TYPE_ICONS[item.type] || <Image className="h-5 w-5" />}
      </div>
    );
  };
  
  return (
    <Card 
      className="overflow-hidden transition-all hover:shadow-md"
      onClick={handleCardClick}
    >
      <CardContent className="p-0 relative">
        {renderThumbnail()}
        <div className="absolute top-2 right-2">
          <Badge variant="secondary" className="flex items-center gap-1">
            {TYPE_ICONS[item.type]}
            <span className="capitalize">{item.type}</span>
          </Badge>
        </div>
      </CardContent>
      <CardFooter className="flex flex-col items-start p-4">
        <h3 className="font-semibold truncate w-full">{item.caption}</h3>
        <p className="text-sm text-muted-foreground line-clamp-2 mt-1">
          {item.description}
        </p>
        {item.tags && item.tags.length > 0 && (
          <div className="flex flex-wrap gap-1 mt-2">
            {item.tags.map(tag => (
              <Badge key={tag} variant="outline" className="text-xs">
                #{tag}
              </Badge>
            ))}
          </div>
        )}
      </CardFooter>
    </Card>
  );
}