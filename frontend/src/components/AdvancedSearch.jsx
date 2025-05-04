// frontend/src/components/AdvancedSearch.jsx
import React, { useState, useEffect, useRef } from 'react';
import { searchService } from '../services/SearchService';
import { useUserStore } from '../stores/userStore';
import { Button } from './ui/button';
import { Input } from './ui/input';
import { Badge } from './ui/badge';
import { Search, X, Clock, Filter, Tag, ChevronDown, ChevronUp } from 'lucide-react';
import { logger } from '../lib/logger';

/**
 * Advanced search component with filters, suggestions, and recent searches
 */
export function AdvancedSearch({ onSearch, initialQuery = '', showFilters = false }) {
  const [query, setQuery] = useState(initialQuery);
  const [isExpanded, setIsExpanded] = useState(showFilters);
  const [isLoading, setIsLoading] = useState(false);
  const [suggestions, setSuggestions] = useState([]);
  const [recentSearches, setRecentSearches] = useState([]);
  const [showSuggestions, setShowSuggestions] = useState(false);
  const [filters, setFilters] = useState({
    type: [],
    tag: '',
    sortBy: 'date'
  });
  
  const searchContainerRef = useRef(null);
  const inputRef = useRef(null);
  const { user } = useUserStore();
  
  // Load recent searches on mount
  useEffect(() => {
    if (user) {
      loadRecentSearches();
    }
  }, [user]);
  
  // Handle outside click for suggestions
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (searchContainerRef.current && !searchContainerRef.current.contains(event.target)) {
        setShowSuggestions(false);
      }
    };
    
    document.addEventListener('mousedown', handleClickOutside);
    
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);
  
  // Get search suggestions when query changes
  useEffect(() => {
    const getSuggestions = async () => {
      if (!query || query.length < 2) {
        setSuggestions([]);
        return;
      }
      
      try {
        const results = await searchService.getSuggestions(query);
        setSuggestions(results);
      } catch (error) {
        logger.error('Error getting search suggestions:', error);
        setSuggestions([]);
      }
    };
    
    const timer = setTimeout(getSuggestions, 300);
    
    return () => clearTimeout(timer);
  }, [query]);
  
  // Load recent searches from service
  const loadRecentSearches = async () => {
    try {
      const searches = await searchService.getRecentSearches(user, 5);
      setRecentSearches(searches);
    } catch (error) {
      logger.error('Error loading recent searches:', error);
      setRecentSearches([]);
    }
  };
  
  // Handle search submission
  const handleSearch = async () => {
    if (!query.trim()) return;
    
    setIsLoading(true);
    setShowSuggestions(false);
    
    try {
      // Save search to recent searches
      searchService.addToRecentSearches(query, filters, user);
      
      // Load updated recent searches
      await loadRecentSearches();
      
      // Notify parent component
      if (onSearch) {
        onSearch(query, filters);
      }
    } catch (error) {
      logger.error('Error performing search:', error);
    } finally {
      setIsLoading(false);
    }
  };
  
  // Handle suggestion click
  const handleSuggestionClick = (suggestion) => {
    setQuery(suggestion);
    setShowSuggestions(false);
    setTimeout(handleSearch, 100);
  };
  
  // Handle recent search click
  const handleRecentSearchClick = (search) => {
    setQuery(search.term);
    if (search.filters) {
      setFilters(search.filters);
    }
    setShowSuggestions(false);
    setTimeout(handleSearch, 100);
  };
  
  // Handle input focus
  const handleInputFocus = () => {
    if (query.length >= 2 && suggestions.length > 0) {
      setShowSuggestions(true);
    } else if (recentSearches.length > 0) {
      setShowSuggestions(true);
    }
  };
  
  // Handle input key press
  const handleKeyPress = (event) => {
    if (event.key === 'Enter') {
      handleSearch();
    }
  };
  
  // Handle filter changes
  const handleFilterChange = (name, value) => {
    const newFilters = { ...filters };
    
    if (name === 'type') {
      const typeArray = [...newFilters.type];
      const index = typeArray.indexOf(value);
      
      if (index === -1) {
        typeArray.push(value);
      } else {
        typeArray.splice(index, 1);
      }
      
      newFilters.type = typeArray;
    } else {
      newFilters[name] = value;
    }
    
    setFilters(newFilters);
  };
  
  // Clear all filters
  const clearFilters = () => {
    setFilters({
      type: [],
      tag: '',
      sortBy: 'date'
    });
  };
  
  // Clear the search query
  const clearSearch = () => {
    setQuery('');
    inputRef.current?.focus();
  };
  
  // Clear recent searches
  const clearRecentSearches = () => {
    searchService.clearRecentSearches(user);
    setRecentSearches([]);
  };
  
  // Format date for recent searches
  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
    
    if (diffInDays === 0) {
      return 'Today';
    } else if (diffInDays === 1) {
      return 'Yesterday';
    } else if (diffInDays < 7) {
      return `${diffInDays} days ago`;
    } else {
      return date.toLocaleDateString();
    }
  };
  
  return (
    <div ref={searchContainerRef} className="relative space-y-2">
      {/* Search input */}
      <div className="relative flex items-center">
        <div className="relative flex-1">
          <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input
            ref={inputRef}
            type="search"
            placeholder="Search by title, description, or tags..."
            className="pl-8 pr-10"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            onFocus={handleInputFocus}
            onKeyPress={handleKeyPress}
            disabled={isLoading}
          />
          {query && (
            <button
              className="absolute right-2.5 top-2.5 h-5 w-5 text-muted-foreground hover:text-foreground"
              onClick={clearSearch}
            >
              <X size={18} />
            </button>
          )}
        </div>
        
        <Button
          type="button"
          variant="default"
          className="ml-2"
          onClick={handleSearch}
          disabled={!query.trim() || isLoading}
        >
          Search
        </Button>
        
        <Button
          type="button"
          variant="outline"
          className="ml-2"
          onClick={() => setIsExpanded(!isExpanded)}
        >
          <Filter size={18} className="mr-1" />
          {isExpanded ? <ChevronUp size={18} /> : <ChevronDown size={18} />}
          <span className="sr-only">
            {isExpanded ? 'Hide filters' : 'Show filters'}
          </span>
        </Button>
      </div>
      
      {/* Suggestions dropdown */}
      {showSuggestions && (
        <div className="absolute z-10 w-full bg-card border rounded-md shadow-md mt-1 overflow-hidden">
          {suggestions.length > 0 && (
            <div className="p-2">
              <h4 className="text-xs text-muted-foreground mb-2">Suggestions</h4>
              <ul className="space-y-1">
                {suggestions.map((suggestion, index) => (
                  <li key={`suggestion-${index}`}>
                    <button
                      className="w-full text-left px-3 py-2 rounded-md hover:bg-muted flex items-center"
                      onClick={() => handleSuggestionClick(suggestion)}
                    >
                      <Search size={14} className="mr-2 text-muted-foreground" />
                      {suggestion}
                    </button>
                  </li>
                ))}
              </ul>
            </div>
          )}
          
          {recentSearches.length > 0 && (
            <div className="p-2 border-t">
              <div className="flex justify-between items-center mb-2">
                <h4 className="text-xs text-muted-foreground">Recent Searches</h4>
                <button
                  className="text-xs text-muted-foreground hover:text-foreground"
                  onClick={clearRecentSearches}
                >
                  Clear
                </button>
              </div>
              <ul className="space-y-1">
                {recentSearches.map((search, index) => (
                  <li key={`recent-${index}`}>
                    <button
                      className="w-full text-left px-3 py-2 rounded-md hover:bg-muted flex items-center justify-between"
                      onClick={() => handleRecentSearchClick(search)}
                    >
                      <div className="flex items-center">
                        <Clock size={14} className="mr-2 text-muted-foreground" />
                        {search.term}
                      </div>
                      <span className="text-xs text-muted-foreground">
                        {search.timestamp && formatDate(search.timestamp)}
                      </span>
                    </button>
                  </li>
                ))}
              </ul>
            </div>
          )}
        </div>
      )}
      
      {/* Filters */}
      {isExpanded && (
        <div className="p-3 bg-card border rounded-md mt-2 animate-in fade-in-0 zoom-in-95 duration-100">
          <div className="flex flex-wrap gap-4">
            <div>
              <h4 className="text-sm font-medium mb-2">Type</h4>
              <div className="flex gap-2">
                {['video', 'audio', 'image'].map((type) => (
                  <Badge
                    key={type}
                    variant={filters.type.includes(type) ? 'default' : 'outline'}
                    className="cursor-pointer capitalize"
                    onClick={() => handleFilterChange('type', type)}
                  >
                    {type}
                  </Badge>
                ))}
              </div>
            </div>
            
            <div>
              <h4 className="text-sm font-medium mb-2">Tag</h4>
              <div className="flex items-center">
                <Tag size={14} className="mr-2 text-muted-foreground" />
                <Input
                  type="text"
                  value={filters.tag}
                  onChange={(e) => handleFilterChange('tag', e.target.value)}
                  placeholder="Filter by tag"
                  className="h-8 w-40"
                />
              </div>
            </div>
            
            <div>
              <h4 className="text-sm font-medium mb-2">Sort By</h4>
              <div className="flex gap-2">
                {[
                  { value: 'date', label: 'Date' },
                  { value: 'popularity', label: 'Popularity' },
                  { value: 'name', label: 'Name' }
                ].map((option) => (
                  <Badge
                    key={option.value}
                    variant={filters.sortBy === option.value ? 'default' : 'outline'}
                    className="cursor-pointer"
                    onClick={() => handleFilterChange('sortBy', option.value)}
                  >
                    {option.label}
                  </Badge>
                ))}
              </div>
            </div>
          </div>
          
          <div className="flex justify-between mt-4">
            <Button
              type="button"
              variant="outline"
              size="sm"
              onClick={clearFilters}
            >
              Clear Filters
            </Button>
            
            <Button
              type="button"
              variant="default"
              size="sm"
              onClick={handleSearch}
              disabled={!query.trim() || isLoading}
            >
              Apply
            </Button>
          </div>
        </div>
      )}
      
      {/* Active filters */}
      {(filters.type.length > 0 || filters.tag || filters.sortBy !== 'date') && !isExpanded && (
        <div className="flex flex-wrap gap-2 mt-2">
          {filters.type.map((type) => (
            <Badge key={type} variant="secondary" className="capitalize">
              {type}
              <X
                size={14}
                className="ml-1 cursor-pointer"
                onClick={() => handleFilterChange('type', type)}
              />
            </Badge>
          ))}
          
          {filters.tag && (
            <Badge variant="secondary">
              <Tag size={14} className="mr-1" />
              {filters.tag}
              <X
                size={14}
                className="ml-1 cursor-pointer"
                onClick={() => handleFilterChange('tag', '')}
              />
            </Badge>
          )}
          
          {filters.sortBy !== 'date' && (
            <Badge variant="secondary">
              Sort: {filters.sortBy}
              <X
                size={14}
                className="ml-1 cursor-pointer"
                onClick={() => handleFilterChange('sortBy', 'date')}
              />
            </Badge>
          )}
          
          <Button
            type="button"
            variant="ghost"
            size="sm"
            className="h-6 px-2 text-xs"
            onClick={clearFilters}
          >
            Clear All
          </Button>
        </div>
      )}
    </div>
  );
}