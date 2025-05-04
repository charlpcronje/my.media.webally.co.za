# API Reference

This document provides detailed information about the Media Share API endpoints, request/response formats, and authentication methods.

## Base URL

The base URL for all API endpoints is:

```
https://api.media.example.com
```

Replace `example.com` with your actual domain.

## Authentication

Most API endpoints do not require authentication for read operations. Write operations may require a user identifier.

### User Identification

For endpoints that track user activity, pass the user's name in the request:

```json
{
  "user_name": "username"
}
```

## Error Handling

All API endpoints use standard HTTP status codes. Common error codes:

- `400 Bad Request`: Invalid input parameters
- `404 Not Found`: Resource not found
- `405 Method Not Allowed`: HTTP method not supported
- `500 Internal Server Error`: Server-side error

Error responses include a JSON object with error details:

```json
{
  "error": "Error message",
  "message": "Detailed error description"
}
```

## API Endpoints

### Media Endpoints

#### Get Media List

Retrieves a list of media items with optional filtering.

```
GET /media
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| type      | string | Filter by media type (video,audio,image)| No       |
| tag       | string | Filter by tag                           | No       |
| search    | string | Search term for title/description       | No       |
| sort_by   | string | Sort order (date,name,popularity)       | No       |

**Response:**

```json
[
  {
    "id": 1,
    "caption": "Sample Media",
    "description": "Description of the media",
    "type": "video",
    "filename": "sample.mp4",
    "thumbnail": "thumbnails/sample.jpg",
    "tags": ["tag1", "tag2"],
    "created_at": "2023-01-01 12:00:00",
    "updated_at": "2023-01-02 12:00:00",
    "view_count": 100,
    "play_count": 50
  },
  // ... more media items
]
```

#### Get Single Media Item

Retrieves a specific media item by ID.

```
GET /media/{id}
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| id        | integer| Media item ID                           | Yes      |

**Response:**

```json
{
  "id": 1,
  "caption": "Sample Media",
  "description": "Description of the media",
  "type": "video",
  "filename": "sample.mp4",
  "thumbnail": "thumbnails/sample.jpg",
  "tags": ["tag1", "tag2"],
  "created_at": "2023-01-01 12:00:00",
  "updated_at": "2023-01-02 12:00:00",
  "view_count": 100,
  "play_count": 50
}
```

#### Create Media Item

Creates a new media item.

```
POST /media
```

**Request Body:**

Form data with the following fields:

| Field       | Type   | Description                           | Required |
|-------------|--------|---------------------------------------|----------|
| file        | file   | Media file to upload                  | Yes      |
| caption     | string | Media title/caption                   | Yes      |
| description | string | Media description                     | No       |
| type        | string | Media type (video,audio,image)        | Yes      |
| tags        | string | Comma-separated list of tags          | No       |
| thumbnail   | file   | Thumbnail image (for video/audio)     | No       |

**Response:**

```json
{
  "success": true,
  "id": 123,
  "message": "Media uploaded successfully"
}
```

#### Update Media Item

Updates an existing media item.

```
PUT /media/{id}
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| id        | integer| Media item ID                           | Yes      |

**Request Body:**

Form data with the following fields:

| Field       | Type   | Description                           | Required |
|-------------|--------|---------------------------------------|----------|
| caption     | string | Media title/caption                   | No       |
| description | string | Media description                     | No       |
| tags        | string | Comma-separated list of tags          | No       |
| file        | file   | New media file (to replace existing)  | No       |
| thumbnail   | file   | New thumbnail image                   | No       |

**Response:**

```json
{
  "success": true,
  "message": "Media updated successfully"
}
```

#### Delete Media Item

Deletes a media item.

```
DELETE /media/{id}
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| id        | integer| Media item ID                           | Yes      |

**Response:**

```json
{
  "success": true,
  "message": "Media deleted successfully"
}
```

#### Stream Media

Streams media content.

```
GET /media/{id}/stream
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| id        | integer| Media item ID                           | Yes      |

**Response:**

Media file stream with appropriate content type headers.

#### Get Media Thumbnail

Retrieves a media thumbnail.

```
GET /media/{id}/thumbnail
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| id        | integer| Media item ID                           | Yes      |

**Response:**

Image file stream with image/jpeg content type.

### Tags Endpoints

#### Get Tags List

Retrieves a list of all tags.

```
GET /tags
```

**Response:**

```json
[
  "tag1",
  "tag2",
  "tag3"
]
```

#### Create Tag

Creates a new tag.

```
POST /tags
```

**Request Body:**

```json
{
  "name": "newtag"
}
```

**Response:**

```json
{
  "success": true,
  "id": 42,
  "name": "newtag",
  "message": "Tag created successfully"
}
```

#### Delete Tag

Deletes a tag.

```
DELETE /tags/{name}
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| name      | string | Tag name                                | Yes      |

**Response:**

```json
{
  "success": true,
  "message": "Tag deleted successfully"
}
```

### Chapters Endpoints

#### Get Chapters for Media

Retrieves all chapters for a media item.

```
GET /chapters/media/{media_id}
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| media_id  | integer| Media item ID                           | Yes      |

**Response:**

```json
[
  {
    "id": 1,
    "media_id": 123,
    "title": "Chapter 1",
    "start_time": 0,
    "end_time": 120.5,
    "description": "Introduction chapter",
    "created_at": "2023-01-01 12:00:00",
    "updated_at": "2023-01-02 12:00:00"
  },
  // ... more chapters
]
```

#### Create Chapter

Creates a new chapter for a media item.

```
POST /chapters
```

**Request Body:**

```json
{
  "media_id": 123,
  "title": "New Chapter",
  "start_time": 300.5,
  "end_time": 450.75,
  "description": "Chapter description"
}
```

**Response:**

```json
{
  "id": 42,
  "media_id": 123,
  "title": "New Chapter",
  "start_time": 300.5,
  "end_time": 450.75,
  "description": "Chapter description",
  "created_at": "2023-01-01 12:00:00",
  "updated_at": "2023-01-01 12:00:00"
}
```

#### Update Chapter

Updates an existing chapter.

```
PUT /chapters/{id}
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| id        | integer| Chapter ID                              | Yes      |

**Request Body:**

```json
{
  "title": "Updated Chapter Title",
  "start_time": 310.5,
  "end_time": 460.75,
  "description": "Updated chapter description"
}
```

**Response:**

```json
{
  "id": 42,
  "media_id": 123,
  "title": "Updated Chapter Title",
  "start_time": 310.5,
  "end_time": 460.75,
  "description": "Updated chapter description",
  "created_at": "2023-01-01 12:00:00",
  "updated_at": "2023-01-02 12:00:00"
}
```

#### Delete Chapter

Deletes a chapter.

```
DELETE /chapters/{id}
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| id        | integer| Chapter ID                              | Yes      |

**Response:**

```json
{
  "success": true,
  "message": "Chapter deleted successfully"
}
```

### Comments Endpoints

#### Get Comments for Media

Retrieves comments for a media item.

```
GET /comments/media/{media_id}
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| media_id  | integer| Media item ID                           | Yes      |

**Query Parameters:**

| Parameter  | Type   | Description                            | Required |
|------------|--------|----------------------------------------|----------|
| chapter_id | integer| Filter by chapter ID                   | No       |

**Response:**

```json
[
  {
    "id": 1,
    "media_id": 123,
    "chapter_id": null,
    "user_name": "username",
    "comment": "This is a comment",
    "timestamp": "2023-01-01 12:00:00",
    "updated_at": "2023-01-01 12:00:00"
  },
  // ... more comments
]
```

#### Add Comment

Adds a new comment to a media item.

```
POST /comments
```

**Request Body:**

```json
{
  "media_id": 123,
  "user_name": "username",
  "comment": "This is a new comment",
  "chapter_id": null
}
```

**Response:**

```json
{
  "id": 42,
  "media_id": 123,
  "chapter_id": null,
  "user_name": "username",
  "comment": "This is a new comment",
  "timestamp": "2023-01-01 12:00:00",
  "updated_at": "2023-01-01 12:00:00"
}
```

#### Update Comment

Updates an existing comment.

```
PUT /comments/{id}
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| id        | integer| Comment ID                              | Yes      |

**Request Body:**

```json
{
  "user_name": "username",
  "comment": "Updated comment text"
}
```

**Response:**

```json
{
  "id": 42,
  "media_id": 123,
  "chapter_id": null,
  "user_name": "username",
  "comment": "Updated comment text",
  "timestamp": "2023-01-01 12:00:00",
  "updated_at": "2023-01-02 12:00:00"
}
```

#### Delete Comment

Deletes a comment.

```
DELETE /comments/{id}?user_name=username
```

**Path Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| id        | integer| Comment ID                              | Yes      |

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| user_name | string | Username of the commenter               | Yes      |

**Response:**

```json
{
  "success": true,
  "message": "Comment deleted successfully"
}
```

### Analytics Tracking Endpoints

#### Track Media Event

Tracks a media interaction event.

```
POST /track
```

**Request Body:**

```json
{
  "media_id": 123,
  "event_type": "view",
  "user_name": "username",
  "position": 120.5,
  "percentage": 50,
  "view_duration": 180,
  "session_id": "session_123",
  "chapter_id": null
}
```

**Event Types:**

- `view`: Media was viewed
- `play`: Media started playing
- `pause`: Media was paused
- `seek`: User seeked to a specific position
- `progress`: Periodic progress update
- `ended`: Media playback ended
- `download`: Media was downloaded
- `image_click`: Image was clicked
- `image_enlarge`: Image was enlarged
- `chapter_navigation`: Chapter navigation occurred

**Response:**

```json
{
  "success": true,
  "id": 42
}
```

#### Track Search Event

Tracks a search query.

```
POST /track
```

**Request Body:**

```json
{
  "event_type": "search",
  "user_name": "username",
  "search_term": "query",
  "filters": {
    "type": ["video", "audio"],
    "tag": "sampletag",
    "sortBy": "date"
  },
  "results_count": 5
}
```

**Response:**

```json
{
  "success": true,
  "id": 42
}
```

### Search Endpoints

#### Search Media

Searches for media items.

```
GET /search
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| q         | string | Search query                            | Yes      |
| type      | string | Media type filter (comma-separated)     | No       |
| tag       | string | Tag filter                              | No       |
| sortBy    | string | Sort order (date,name,popularity)       | No       |
| user_name | string | Username for tracking                   | No       |

**Response:**

```json
[
  {
    "id": 1,
    "caption": "Sample Media",
    "description": "Description of the media",
    "type": "video",
    "filename": "sample.mp4",
    "thumbnail": "thumbnails/sample.jpg",
    "tags": ["tag1", "tag2"],
    "created_at": "2023-01-01 12:00:00",
    "updated_at": "2023-01-02 12:00:00",
    "view_count": 100,
    "play_count": 50
  },
  // ... more media items
]
```

#### Get Search Suggestions

Retrieves search suggestions based on a partial query.

```
GET /search?suggest=true&q=partial_query
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| suggest   | boolean| Set to true for suggestions             | Yes      |
| q         | string | Partial search query                    | Yes      |

**Response:**

```json
[
  "suggestion1",
  "suggestion2",
  "suggestion3"
]
```

#### Get Recent Searches

Retrieves recent searches for a user.

```
GET /search?recent=true&user_name=username&limit=5
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| recent    | boolean| Set to true for recent searches         | Yes      |
| user_name | string | Username                                | Yes      |
| limit     | integer| Maximum number of results               | No       |

**Response:**

```json
[
  {
    "term": "search term",
    "filters": {
      "type": ["video"],
      "tag": "sampletag",
      "sortBy": "date"
    },
    "timestamp": "2023-01-01 12:00:00"
  },
  // ... more search records
]
```

### User Preferences Endpoints

#### Get User Preferences

Retrieves preferences for a user.

```
GET /preferences?user_name=username
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| user_name | string | Username                                | Yes      |

**Response:**

```json
{
  "theme": "dark",
  "volume": 80,
  "autoplay": true,
  "last_login": "2023-01-01 12:00:00",
  "updated_at": "2023-01-02 12:00:00"
}
```

#### Save User Preferences

Saves preferences for a user.

```
POST /preferences
```

**Request Body:**

```json
{
  "user_name": "username",
  "theme": "dark",
  "volume": 80,
  "autoplay": true
}
```

**Response:**

```json
{
  "user_name": "username",
  "theme": "dark",
  "volume": 80,
  "autoplay": true,
  "last_login": "2023-01-01 12:00:00",
  "updated_at": "2023-01-02 12:00:00"
}
```

#### Delete User Preferences

Deletes preferences for a user.

```
DELETE /preferences?user_name=username
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| user_name | string | Username                                | Yes      |

**Response:**

```json
{
  "success": true,
  "message": "Preferences deleted successfully"
}
```

### Analytics Data Endpoints

#### Get Overall Analytics

Retrieves overall analytics data.

```
GET /analytics?type=overall
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| type      | string | Analytics type (overall)                | Yes      |
| date_from | string | Start date (YYYY-MM-DD)                 | No       |
| date_to   | string | End date (YYYY-MM-DD)                   | No       |

**Response:**

```json
{
  "top_media": [
    {
      "id": 1,
      "caption": "Popular Video",
      "type": "video",
      "view_count": 500,
      "unique_viewers": 300
    }
  ],
  "top_users": [
    {
      "user_name": "active_user",
      "action_count": 1200,
      "media_viewed": 50,
      "total_view_time": 7200
    }
  ],
  "event_types": [
    {
      "event_type": "view",
      "count": 5000
    }
  ],
  "media_types": [
    {
      "type": "video",
      "view_count": 3000,
      "unique_viewers": 1500
    }
  ]
}
```

#### Get Media Analytics

Retrieves analytics data for a specific media item.

```
GET /analytics?type=media&id=123
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| type      | string | Analytics type (media)                  | Yes      |
| id        | integer| Media ID                                | Yes      |
| date_from | string | Start date (YYYY-MM-DD)                 | No       |
| date_to   | string | End date (YYYY-MM-DD)                   | No       |
| user_name | string | Filter by username                      | No       |

**Response:**

```json
{
  "events": [
    {
      "event_type": "view",
      "count": 500,
      "unique_users": 300,
      "avg_duration": 120,
      "max_progress": 100
    }
  ],
  "summary": {
    "view_count": 500,
    "play_count": 400,
    "unique_viewers": 300,
    "completion_rate": 75.5
  }
}
```

#### Get User Analytics

Retrieves analytics data for a specific user.

```
GET /analytics?type=user&user_name=username
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| type      | string | Analytics type (user)                   | Yes      |
| user_name | string | Username                                | Yes      |
| date_from | string | Start date (YYYY-MM-DD)                 | No       |
| date_to   | string | End date (YYYY-MM-DD)                   | No       |

**Response:**

```json
[
  {
    "media_id": 123,
    "media_title": "Sample Media",
    "media_type": "video",
    "view_count": 10,
    "max_progress": 100,
    "total_duration": 1200,
    "last_viewed": "2023-01-01 12:00:00"
  }
]
```

#### Get Search Analytics

Retrieves search analytics data.

```
GET /analytics?type=search
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| type      | string | Analytics type (search)                 | Yes      |
| date_from | string | Start date (YYYY-MM-DD)                 | No       |
| date_to   | string | End date (YYYY-MM-DD)                   | No       |
| user_name | string | Filter by username                      | No       |

**Response:**

```json
{
  "top_searches": [
    {
      "search_term": "popular term",
      "count": 50,
      "avg_results": 12.5
    }
  ],
  "zero_results": [
    {
      "search_term": "no results term",
      "count": 5
    }
  ]
}
```

## Session Endpoints

#### Start Session

Starts a user session.

```
GET /session?name=username
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| name      | string | Username                                | Yes      |

**Response:**

```json
{
  "success": true,
  "user": "username",
  "message": "Session started successfully"
}
```

#### Get Session Info

Retrieves current session information.

```
GET /session
```

**Response:**

```json
{
  "success": true,
  "user": "username",
  "session_duration": 300
}
```

#### End Session

Ends the current session.

```
POST /session?end=true
```

**Query Parameters:**

| Parameter | Type   | Description                             | Required |
|-----------|--------|-----------------------------------------|----------|
| end       | boolean| Set to true to end session              | Yes      |

**Response:**

```json
{
  "success": true,
  "message": "Session ended successfully"
}
```

## API Usage Examples

### Example: Upload a Media File

```bash
curl -X POST https://api.media.example.com/media \
  -F "file=@/path/to/video.mp4" \
  -F "caption=Sample Video" \
  -F "description=A sample video upload" \
  -F "type=video" \
  -F "tags=sample,test,demo"
```

### Example: Search for Media

```bash
curl -X GET "https://api.media.example.com/search?q=sample&type=video,audio&sortBy=date"
```

### Example: Track a Media View Event

```bash
curl -X POST https://api.media.example.com/track \
  -H "Content-Type: application/json" \
  -d '{
    "media_id": 123,
    "event_type": "view",
    "user_name": "username",
    "position": 0,
    "percentage": 0,
    "view_duration": 0,
    "session_id": "session_123"
  }'
```

## Rate Limiting

API endpoints are subject to rate limiting to prevent abuse. Current limits:

- 100 requests per minute per IP address
- 1000 requests per hour per IP address

When rate limits are exceeded, the API returns a `429 Too Many Requests` status code.

## Cross-Origin Resource Sharing (CORS)

The API supports Cross-Origin Resource Sharing (CORS) for browser-based applications. The following headers are included in API responses:

```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-Key
Access-Control-Allow-Credentials: true
```

For production environments, it's recommended to restrict the `Access-Control-Allow-Origin` header to specific domains.