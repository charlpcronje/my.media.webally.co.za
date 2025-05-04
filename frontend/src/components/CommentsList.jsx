// frontend/src/components/CommentsList.jsx
import React, { useEffect, useState } from 'react';
import { commentsService } from '../services/CommentsService';
import { useUserStore } from '../stores/userStore';
import { logger } from '../lib/logger';
import { CommentForm } from './CommentForm';

export function CommentsList({ mediaId, chapterId = null }) {
  const [comments, setComments] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [editingCommentId, setEditingCommentId] = useState(null);
  
  const { user } = useUserStore();
  
  useEffect(() => {
    const loadComments = async () => {
      setLoading(true);
      try {
        const commentsData = await commentsService.getCommentsByMediaId(mediaId, chapterId);
        setComments(commentsData);
        setError(null);
      } catch (error) {
        logger.error('Error loading comments:', error);
        setError('Failed to load comments. Please try again later.');
      } finally {
        setLoading(false);
      }
    };
    
    loadComments();
  }, [mediaId, chapterId]);
  
  const handleAddComment = (newComment) => {
    setComments([...comments, newComment]);
  };
  
  const handleUpdateComment = async (commentId, commentText) => {
    try {
      const updatedComment = await commentsService.updateComment(commentId, commentText);
      
      // Update in the local state
      const updatedComments = comments.map(comment => 
        comment.id === commentId ? updatedComment : comment
      );
      
      setComments(updatedComments);
      setEditingCommentId(null);
    } catch (error) {
      logger.error('Error updating comment:', error);
    }
  };
  
  const handleDeleteComment = async (commentId) => {
    try {
      await commentsService.deleteComment(commentId);
      
      // Remove from local state
      const updatedComments = comments.filter(comment => comment.id !== commentId);
      setComments(updatedComments);
    } catch (error) {
      logger.error('Error deleting comment:', error);
    }
  };
  
  if (loading) {
    return <div className="text-sm text-muted-foreground animate-pulse">Loading comments...</div>;
  }
  
  return (
    <div className="space-y-4">
      <h3 className="text-lg font-semibold">
        Comments {comments.length > 0 && `(${comments.length})`}
      </h3>
      
      {error && (
        <div className="p-3 rounded-md bg-destructive/10 text-destructive text-sm">
          {error}
        </div>
      )}
      
      <CommentForm 
        mediaId={mediaId} 
        chapterId={chapterId} 
        onCommentAdded={handleAddComment} 
      />
      
      {comments.length === 0 ? (
        <div className="text-center text-muted-foreground py-4">
          No comments yet. Be the first to comment!
        </div>
      ) : (
        <div className="space-y-3">
          {comments.map(comment => (
            <div key={comment.id} className="p-3 border rounded-md bg-card">
              <div className="flex justify-between items-start">
                <div className="flex items-center gap-2">
                  <span className="font-medium">{comment.user_name}</span>
                  <span className="text-xs text-muted-foreground">
                    {commentsService.formatCommentTimestamp(comment.timestamp)}
                  </span>
                </div>
                
                {comment.user_name === user && (
                  <div className="flex gap-2">
                    <button
                      onClick={() => setEditingCommentId(comment.id)}
                      className="text-xs text-primary hover:underline"
                    >
                      Edit
                    </button>
                    <button
                      onClick={() => {
                        if (window.confirm('Are you sure you want to delete this comment?')) {
                          handleDeleteComment(comment.id);
                        }
                      }}
                      className="text-xs text-destructive hover:underline"
                    >
                      Delete
                    </button>
                  </div>
                )}
              </div>
              
              {editingCommentId === comment.id ? (
                <div className="mt-2">
                  <textarea
                    defaultValue={comment.comment}
                    className="w-full px-3 py-2 border rounded-md resize-none"
                    rows="3"
                    id={`edit-comment-${comment.id}`}
                  />
                  <div className="flex justify-end gap-2 mt-2">
                    <button
                      onClick={() => setEditingCommentId(null)}
                      className="px-3 py-1 text-sm rounded-md border hover:bg-muted"
                    >
                      Cancel
                    </button>
                    <button
                      onClick={() => {
                        const textarea = document.getElementById(`edit-comment-${comment.id}`);
                        handleUpdateComment(comment.id, textarea.value);
                      }}
                      className="px-3 py-1 text-sm bg-primary text-primary-foreground rounded-md hover:bg-primary/90"
                    >
                      Save
                    </button>
                  </div>
                </div>
              ) : (
                <p className="mt-2 whitespace-pre-line">{comment.comment}</p>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}