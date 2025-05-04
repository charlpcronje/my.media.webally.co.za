# Analytics Documentation

The Media Share application includes a comprehensive analytics system that tracks user interactions with media content and provides detailed insights for administrators. This documentation explains how the analytics system works and how to use the analytics dashboard.

## Table of Contents

- [Analytics Overview](#analytics-overview)
- [Tracked Events](#tracked-events)
- [Analytics Dashboard](#analytics-dashboard)
- [Reporting Features](#reporting-features)
- [Data Storage](#data-storage)
- [Privacy Considerations](#privacy-considerations)

## Analytics Overview

The Media Share analytics system uses a session-based tracking approach to record user interactions with media content. Each user session generates multiple events that are recorded in the database for later analysis.

### Key Features

- Real-time event tracking
- Session-based analytics
- Comprehensive media engagement metrics
- User behavior analysis
- Search analytics
- Visual reporting dashboard

## Tracked Events

The following user interactions are tracked by the analytics system:

### Media Viewing Events

| Event Type | Description | Data Collected |
|------------|-------------|----------------|
| view | User views a media item | Media ID, timestamp, user name |
| play | User plays a video or audio | Media ID, timestamp, user name, position |
| pause | User pauses a video or audio | Media ID, timestamp, user name, position, percentage |
| seek | User jumps to a position | Media ID, timestamp, user name, position, percentage |
| progress | Periodic progress update | Media ID, timestamp, user name, position, percentage, duration |
| ended | Media playback ended | Media ID, timestamp, user name, percentage, completed flag |
| download | User downloads a media file | Media ID, timestamp, user name |

### Chapter Events

| Event Type | Description | Data Collected |
|------------|-------------|----------------|
| chapter_navigation | User navigates to a chapter | Media ID, timestamp, user name, chapter ID |

### Image Interaction Events

| Event Type | Description | Data Collected |
|------------|-------------|----------------|
| image_enlarge | User enlarges an image | Media ID, timestamp, user name |
| image_click | User clicks on an image | Media ID, timestamp, user name |
| image_zoom_in | User zooms in on an image | Media ID, timestamp, user name |
| image_zoom_out | User zooms out on an image | Media ID, timestamp, user name |
| image_rotate | User rotates an image | Media ID, timestamp, user name |
| image_reset_view | User resets image view | Media ID, timestamp, user name |

### Search Events

| Event Type | Description | Data Collected |
|------------|-------------|----------------|
| search | User performs a search | Search term, filters, user name, results count |

## Analytics Dashboard

The analytics dashboard provides administrative users with visualizations and insights about media engagement and user behavior.

### Accessing the Dashboard

1. Log in to the admin panel at https://admin.media.webally.co.za
2. Navigate to "Analytics" in the sidebar menu
3. To view the enhanced dashboard, click on "Analytics Dashboard"

### Dashboard Sections

#### Key Metrics

The top of the dashboard displays key performance indicators:

- **Total Views**: Number of media views
- **Total Plays**: Number of video/audio playbacks
- **Active Users**: Number of unique users
- **Average Engagement**: Events per user

#### Charts and Visualizations

The dashboard includes several visual representations of analytics data:

- **Event Distribution**: Pie chart showing the distribution of different event types
- **Media Type Distribution**: Bar chart comparing views across media types
- **Top Media**: Table of most-viewed media items
- **Top Users**: Table of most active users
- **Search Analytics**: Tables showing popular and zero-result searches

#### Filtering Options

You can filter the dashboard data using the following options:

- **Date Range**: Select a specific time period
- **User**: Filter by a specific user
- **Media Type**: Filter by video, audio, or image content

## Reporting Features

### Media-Specific Analytics

To view analytics for a specific media item:

1. Go to the Media Library in the admin panel
2. Click on a media item to edit it
3. Scroll down to the "Analytics Summary" section

This view provides:
- View count
- Play count (for video/audio)
- Unique viewers
- Completion rate
- Recent activity log

### User Activity Reporting

To view analytics for specific users:

1. Go to the Analytics page
2. Filter by a specific username
3. View the user's activity across all media items

### Export Options

Data can be exported from the analytics dashboard:

1. Navigate to any analytics view
2. Click the "Export" button in the top-right corner
3. Choose the export format (CSV, Excel, PDF)

## Data Storage

Analytics data is stored in several database tables:

- `analytics`: Main table storing all event data
- `search_logs`: Table tracking search queries
- `user_preferences`: Table storing user settings

### Data Retention

By default, analytics data is kept indefinitely. Administrators can configure data retention policies in the application settings.

## Privacy Considerations

The analytics system is designed with privacy in mind:

- No personally identifiable information is collected beyond usernames
- IP addresses are hashed for privacy
- All data collection adheres to relevant privacy regulations
- Users are made aware of analytics tracking through the Terms of Service

### User Consent

Users implicitly consent to analytics tracking when they use the application. The privacy policy should clearly state what data is collected and how it is used.