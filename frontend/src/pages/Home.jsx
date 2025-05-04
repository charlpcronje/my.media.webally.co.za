// frontend/src/pages/Home.jsx
import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { MediaCard } from '@/components/MediaCard';
import { useMediaStore } from '@/stores/mediaStore';
import { useUserStore } from '@/stores/userStore';
import { Search, X } from 'lucide-react';
import { logger } from '@/lib/logger';
import { apiConfig } from '@/lib/apiConfig';

export function Home() {
  const { mediaItems, loading, error, fetchMedia } = useMediaStore();
  const { user } = useUserStore();
  const [mediaType, setMediaType] = useState('all');
  const [searchTerm, setSearchTerm] = useState('');
  const [tags, setTags] = useState([]);
  const [selectedTag, setSelectedTag] = useState('');
  
  useEffect(() => {
    const loadMedia = async () => {
      try {
        const filters = {};
        if (mediaType !== 'all') {
          filters.type = mediaType;
        }
        if (selectedTag) {
          filters.tag = selectedTag;
        }
        await fetchMedia(filters);
      } catch (err) {
        logger.error('Error loading media in Home component:', err);
      }
    };
    
    loadMedia();
  }, [fetchMedia, mediaType, selectedTag]);
  
  // Load tags
  useEffect(() => {
    const loadTags = async () => {
      try {
        const url = apiConfig.getUrl('tags');
        const response = await fetch(url);
        if (!response.ok) throw new Error(`Failed to load tags: ${response.status}`);
        
        const data = await response.json();
        // Ensure tags is always an array
        setTags(Array.isArray(data) ? data : []);
      } catch (err) {
        logger.error('Error loading tags:', err);
        // Set empty array on error to prevent issues
        setTags([]);
      }
    };
    
    loadTags();
  }, []);
  
  const filteredMedia = mediaItems.filter(item => {
    const matchesSearch = searchTerm.trim() === '' || 
      item.caption.toLowerCase().includes(searchTerm.toLowerCase()) ||
      item.description.toLowerCase().includes(searchTerm.toLowerCase());
    
    return matchesSearch;
  });
  
  const handleChangeMediaType = (value) => {
    setMediaType(value);
  };
  
  const handleTagSelection = (tag) => {
    setSelectedTag(tag === selectedTag ? '' : tag);
  };
  
  const clearFilters = () => {
    setSearchTerm('');
    setSelectedTag('');
    setMediaType('all');
  };
  
  if (!user) {
    return <div>Please select a user to view media</div>;
  }
  
  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold tracking-tight">Media Library</h1>
      </div>
      
      <div className="flex flex-col md:flex-row gap-4">
        <div className="relative flex-1">
          <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input
            type="search"
            placeholder="Search by caption or description..."
            className="pl-8"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        
        {(searchTerm || selectedTag || mediaType !== 'all') && (
          <Button variant="ghost" onClick={clearFilters} className="shrink-0">
            <X className="mr-2 h-4 w-4" />
            Clear filters
          </Button>
        )}
      </div>
      
      <div className="flex flex-wrap gap-2 mb-4">
        {tags.map(tag => (
          <Button
            key={tag}
            variant={selectedTag === tag ? "default" : "outline"}
            size="sm"
            onClick={() => handleTagSelection(tag)}
            className="rounded-full"
          >
            #{tag}
          </Button>
        ))}
      </div>
      
      <Tabs defaultValue="all" value={mediaType} onValueChange={handleChangeMediaType}>
        <TabsList>
          <TabsTrigger value="all">All</TabsTrigger>
          <TabsTrigger value="video">Videos</TabsTrigger>
          <TabsTrigger value="audio">Audio</TabsTrigger>
          <TabsTrigger value="image">Images</TabsTrigger>
        </TabsList>
        
        <TabsContent value="all" className="mt-4">
          <MediaGrid media={filteredMedia} loading={loading} error={error} />
        </TabsContent>
        
        <TabsContent value="video" className="mt-4">
          <MediaGrid 
            media={filteredMedia.filter(m => m.type === 'video')} 
            loading={loading} 
            error={error} 
          />
        </TabsContent>
        
        <TabsContent value="audio" className="mt-4">
          <MediaGrid 
            media={filteredMedia.filter(m => m.type === 'audio')} 
            loading={loading} 
            error={error} 
          />
        </TabsContent>
        
        <TabsContent value="image" className="mt-4">
          <MediaGrid 
            media={filteredMedia.filter(m => m.type === 'image')} 
            loading={loading} 
            error={error} 
          />
        </TabsContent>
      </Tabs>
    </div>
  );
}

function MediaGrid({ media, loading, error }) {
  if (loading) {
    return <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {[1, 2, 3, 4, 5, 6].map((i) => (
        <div key={i} className="rounded-lg border bg-card text-card-foreground shadow-sm animate-pulse">
          <div className="aspect-video w-full bg-muted"></div>
          <div className="p-4 space-y-2">
            <div className="h-4 bg-muted rounded w-3/4"></div>
            <div className="h-3 bg-muted rounded w-1/2"></div>
          </div>
        </div>
      ))}
    </div>;
  }
  
  if (error) {
    return <div className="p-4 border rounded-md bg-destructive/10 text-destructive">
      {error}
    </div>;
  }
  
  if (media.length === 0) {
    return <div className="text-center py-10">
      <p className="text-lg text-muted-foreground">No media found</p>
    </div>;
  }
  
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {media.map((item) => (
        <Link to={`/media/${item.id}`} key={item.id}>
          <MediaCard item={item} />
        </Link>
      ))}
    </div>
  );
}