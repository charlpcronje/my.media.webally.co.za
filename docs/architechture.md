# Architecture

This document outlines the architecture of the Media Share platform, explaining the key components and how they interact.

## System Architecture

The Media Share platform follows a multi-tier architecture with the following components:

1. **Frontend Application**: A React-based single-page application (SPA) for the user interface
2. **Backend API**: PHP-based RESTful API endpoints
3. **Admin Panel**: PHP-based web interface for administrative tasks
4. **Database**: MySQL database for data storage

### High-Level Architecture Diagram

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│                 │     │                 │     │                 │
│  Frontend SPA   │────▶│    API Layer    │────▶│    Database     │
│                 │     │                 │     │                 │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         ▲                      ▲                       ▲
         │                      │                       │
         │                      │                       │
         │                      │                       │
         │               ┌─────────────────┐           │
         │               │                 │           │
         └───────────────│  Admin Panel    │───────────┘
                         │                 │
                         └─────────────────┘
```

## Domain Structure

The application uses a subdomain-based architecture:

- `my.media.example.com`: Frontend SPA
- `api.media.example.com`: API endpoints
- `admin.media.example.com`: Admin Panel

## Directory Structure

```
/
├── backend/
│   ├── admin/           # Admin panel files
│   ├── api/             # API endpoints
│   ├── config/          # Configuration files
│   ├── edit/            # Code editor module
│   ├── models/          # Data models and repositories
│   ├── uploads/         # Media file storage (legacy)
│   ├── config.php       # Main configuration file
│   └── setup.php        # Setup script
│
├── frontend/
│   ├── dist/            # Compiled frontend files
│   ├── public/          # Static assets
│   ├── src/             # Source code
│   │   ├── components/  # React components
│   │   ├── hooks/       # Custom React hooks
│   │   ├── lib/         # Utility libraries
│   │   ├── pages/       # Page components
│   │   ├── services/    # Service classes
│   │   └── stores/      # State management stores
│   └── package.json     # Frontend dependencies
│
├── media/               # New media file storage
│   └── thumbnails/      # Thumbnail images
│
└── docs/                # Documentation
```

## Component Architecture

### Frontend Architecture

The frontend follows a component-based architecture using React. The key architectural components include:

1. **UI Components**: Reusable interface elements built with shadcn/ui
2. **Page Components**: Container components for specific routes
3. **Service Classes**: Encapsulate API communication and business logic
4. **State Management**: Using Zustand for global state management
5. **Hooks**: Custom React hooks for shared functionality

#### State Management

The application uses Zustand for global state management:

- `mediaStore`: Manages media items state and operations
- `userStore`: Manages user authentication and preferences

### Backend Architecture

The backend follows a repository pattern architecture:

1. **API Endpoints**: RESTful endpoints for data access
2. **Repository Classes**: Encapsulate database operations
3. **Models**: Represent data entities
4. **Services**: Implement business logic

#### Repository Pattern

The application uses the repository pattern to abstract database operations:

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│                 │     │                 │     │                 │
│  API Endpoints  │────▶│  Repositories   │────▶│    Database     │
│                 │     │                 │     │                 │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

Key repositories:

- `MediaRepository`: Handles media items
- `ChaptersRepository`: Manages chapters for time-based media
- `CommentsRepository`: Manages user comments
- `AnalyticsRepository`: Handles analytics data
- `UserPreferencesRepository`: Manages user preferences

## Database Schema

The database schema consists of the following key tables:

- `media`: Stores media items metadata
- `tags`: Stores tags for categorization
- `media_tags`: Junction table for many-to-many relationship
- `chapters`: Stores chapter information for time-based media
- `comments`: Stores user comments
- `analytics`: Stores user interaction data
- `user_preferences`: Stores user preferences
- `search_logs`: Tracks search queries for analytics

## Authentication

The application uses session-based authentication for the admin panel and a simple username-based identification for the frontend (non-secure).

## API Architecture

The API follows RESTful principles with these key endpoints:

- `/media`: Media items CRUD operations
- `/tags`: Media tags management
- `/chapters`: Chapters management
- `/comments`: Comments management
- `/track`: Analytics tracking
- `/search`: Search functionality
- `/preferences`: User preferences

## Technologies Used

### Frontend

- React
- Zustand (State Management)
- shadcn/ui (UI Components)
- TailwindCSS (Styling)
- Vite (Build Tool)

### Backend

- PHP 7.4+
- MySQL
- Apache

## Performance Considerations

- Image and media files are served directly by the web server
- Database indexes are used for performance optimization
- Frontend assets are optimized and minified
- Analytics data is processed asynchronously

## Security Considerations

- API endpoints validate all input data
- SQL prepared statements prevent SQL injection
- Session-based authentication for admin panel
- Proper file type validation for uploads
- CORS configuration to restrict API access

## Scalability

The application can be scaled horizontally by:

1. Using a load balancer for the API servers
2. Implementing a CDN for media delivery
3. Sharding the database for larger installations
4. Moving media storage to cloud object storage (S3, etc.)