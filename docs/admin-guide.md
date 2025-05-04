# Admin Guide

This guide provides instructions for administrators on how to manage the Media Share platform.

## Admin Dashboard Access

The admin dashboard is accessible at:

```
https://admin.media.example.com
```

Replace `example.com` with your actual domain.

### Login

Use your administrator credentials to log in to the dashboard. The default login credentials are:

- Username: `admin`
- Password: `admin123`

**Important**: For security reasons, change the default password immediately after the first login.

## Dashboard Overview

The admin dashboard provides a comprehensive interface for managing all aspects of the Media Share platform. The main dashboard displays key metrics including:

- Total media count
- Media type distribution
- Recent user activities
- System status

## Media Management

### Viewing Media

1. Navigate to **Media Library** in the sidebar
2. Browse the media items using the filters:
   - Type (Video, Audio, Image)
   - Tags
   - Search term
3. Click on a media item to view its details

### Adding Media

1. Navigate to **Upload Media** in the sidebar
2. Fill in the required fields:
   - Select the media file
   - Add a caption (required)
   - Add a description (optional)
   - Select or create tags (optional)
   - Add a thumbnail (optional, for video and audio)
3. Click **Upload Media**

### Editing Media

1. Navigate to **Media Library** in the sidebar
2. Find the media item you want to edit
3. Click the **Edit** button
4. Update the media details:
   - Change the caption
   - Update the description
   - Add or remove tags
   - Replace the media file
   - Replace the thumbnail
5. Click **Save Changes**

### Deleting Media

1. Navigate to **Media Library** in the sidebar
2. Find the media item you want to delete
3. Click the **Delete** button
4. Confirm the deletion

## Chapter Management

Chapters provide navigation points for video and audio content.

### Viewing Chapters

1. Navigate to **Media Library** in the sidebar
2. Find the media item you want to manage chapters for
3. Click **Edit**
4. Click **Manage Chapters**

### Adding Chapters

1. Navigate to the Chapter Management interface
2. Play the media to find the desired start time
3. Click **Capture Current Time** to automatically fill the Start Time field
4. Enter the chapter title
5. Optionally, enter an end time
6. Add a description if needed
7. Click **Add Chapter**

### Editing Chapters

1. Navigate to the Chapter Management interface
2. Find the chapter you want to edit
3. Click the **Edit** button
4. Update the chapter details
5. Click **Save Changes**

### Managing Chapter Order

Chapters are automatically ordered by their start time. To reorder chapters:

1. Edit the chapter
2. Adjust the start time
3. Save changes

### Chapter Analytics

To view analytics for specific chapters:

1. Navigate to **Analytics** in the sidebar
2. Select **Chapter Analytics** from the dropdown
3. Choose the media item
4. View the performance of each chapter

## Tag Management

Tags help organize and categorize media content.

### Viewing Tags

1. Navigate to **Manage Tags** in the sidebar
2. View all available tags and their usage count

### Adding Tags

1. Navigate to **Manage Tags** in the sidebar
2. Enter the tag name in the form
3. Click **Add Tag**

### Deleting Tags

1. Navigate to **Manage Tags** in the sidebar
2. Find the tag you want to delete
3. Click the **Delete** button

Note: You can only delete tags that are not associated with any media items.

## Analytics

The Analytics section provides comprehensive insights into media usage and user behavior.

### Media Analytics

1. Navigate to **Analytics** in the sidebar
2. Choose filter options:
   - Date range
   - Media type
   - User (optional)
3. View analytics data for media items:
   - View counts
   - Play counts
   - Completion rates
   - User engagement metrics

### User Analytics

1. Navigate to **Analytics** in the sidebar
2. Select **User Analytics** from the dropdown
3. Choose a user to analyze
4. View user behavior data:
   - Media viewed
   - Total viewing time
   - Most viewed content
   - User preferences

### Search Analytics

1. Navigate to **Analytics** in the sidebar
2. Select **Search Analytics** from the dropdown
3. View search behavior data:
   - Most popular search terms
   - Zero-result searches
   - Search conversion rates
   - Search trends over time

### Exporting Analytics Data

1. Navigate to any analytics page
2. Apply desired filters
3. Click the **Export** button
4. Choose the export format (CSV, Excel, PDF)
5. Download the file

## Comment Management

### Viewing Comments

1. Navigate to **Comments** in the sidebar
2. Browse all comments or filter by:
   - Media item
   - User
   - Date range

### Moderating Comments

1. Navigate to **Comments** in the sidebar
2. Review comments that may need moderation
3. Use the action buttons to:
   - Edit the comment
   - Delete the comment
   - Flag the comment for review

## System Management

### Configuration Settings

1. Navigate to **Settings** in the sidebar
2. Adjust system configuration:
   - Storage paths
   - File upload limits
   - Allowed file types
   - CORS settings
   - Session settings

### Maintenance Tools

#### Storage Management

1. Navigate to **Settings** > **Storage** in the sidebar
2. Monitor disk usage
3. Clean up temporary files
4. Optimize media storage

#### Database Maintenance

1. Navigate to **Settings** > **Database** in the sidebar
2. Run maintenance operations:
   - Optimize tables
   - Repair tables
   - Clean up orphaned records

### Backup and Restore

#### Creating Backups

1. Navigate to **Settings** > **Backup** in the sidebar
2. Choose what to back up:
   - Database
   - Media files
   - Configuration files
3. Click **Create Backup**
4. Download the backup file

#### Restoring from Backup

1. Navigate to **Settings** > **Backup** in the sidebar
2. Click **Restore from Backup**
3. Upload the backup file
4. Choose what to restore
5. Click **Restore**

## Security Considerations

### Changing Admin Password

1. Navigate to **Settings** > **Account** in the sidebar
2. Enter your current password
3. Enter and confirm your new password
4. Click **Update Password**

### Monitoring Suspicious Activity

1. Navigate to **Analytics** > **Security Logs** in the sidebar
2. Review login attempts and API usage
3. Identify potential security issues

## Troubleshooting

### Common Issues

#### Media Upload Failures

- Check file size limits in settings
- Verify file type is allowed
- Ensure upload directory has proper permissions

#### Playback Issues

- Verify media files exist in the correct location
- Check file format compatibility
- Test with different browsers

#### Analytics Discrepancies

- Check for tracking script blockers
- Verify analytics tracking is enabled
- Wait for data processing to complete

### Error Logs

1. Navigate to **Settings** > **Logs** in the sidebar
2. Review error logs for troubleshooting
3. Filter logs by:
   - Date range
   - Error type
   - Application component

## Advanced Features

### API Access Management

1. Navigate to **Settings** > **API** in the sidebar
2. Generate API keys for external applications
3. Set permission levels for API access
4. Monitor API usage

### Custom Frontend Configuration

1. Navigate to **Settings** > **Frontend** in the sidebar
2. Customize the user interface:
   - Theme settings
   - Default playback settings
   - Featured content settings

## Best Practices

### Media Organization

- Use consistent naming conventions for media captions
- Apply relevant tags to all media items
- Group related content with common tags
- Create chapters for longer video and audio content

### Performance Optimization

- Compress large media files before uploading
- Create optimized thumbnails
- Schedule database maintenance regularly
- Monitor disk space usage

### Security

- Change default passwords immediately
- Regularly update admin passwords
- Monitor login attempts
- Keep the application updated with the latest security patches

### User Experience

- Organize content with meaningful tags
- Use descriptive captions and detailed descriptions
- Create chapters for longer content to improve navigation
- Regularly review analytics to understand user behavior and preferences