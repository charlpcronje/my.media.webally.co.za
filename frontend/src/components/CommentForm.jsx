// frontend/src/components/CommentForm.jsx
import React, { useState } from 'react';
import { commentsService } from '../services/CommentsService';
import { useUserStore } from '../stores/userStore';
import { logger } from '../lib/logger';

export function CommentForm({ mediaId, chapterId = null, onCommentAdded }) {
  const [comment, setComment] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState(null);
  
  const { user } = useUserStore();
  
  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!comment.trim()) {
      setError('Please enter a comment');
      return;
    }
    
    if (!user) {
      setError('You must be logged in to comment');
      return;
    }
    
    setIsSubmitting(true);
    setError(null);
    
    try {
      const newComment = await commentsService.addComment(
        mediaId,
        user,
        comment.trim(),
        chapterId
      );
      
      // Reset the form
      setComment('');
      
      // Notify parent component
      if (onCommentAdded) {
        onCommentAdded(newComment);
      }
    } catch (error) {
      logger.error('Error submitting comment:', error);
      setError('Failed to submit comment. Please try again.');
    } finally {
      setIsSubmitting(false);
    }
  };
  
  return (
    <form onSubmit={handleSubmit} className="space-y-3">
      <div>
        <textarea
          value={comment}
          onChange={(e) => setComment(e.target.value)}
          placeholder="Add a comment..."
          className="w-full px-3 py-2 border rounded-md resize-none"
          rows="3"
          disabled={isSubmitting}
        />
        
        {error && (
          <p className="text-sm text-destructive mt-1">{error}</p>
        )}
      </div>
      
      <div className="flex justify-end">
        <button
          type="submit"
          className="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50"
          disabled={isSubmitting || !comment.trim() || !user}
        >
          {isSubmitting ? 'Submitting...' : 'Post Comment'}
        </button>
      </div>
    </form>
  );
}