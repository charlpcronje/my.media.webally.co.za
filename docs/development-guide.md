# Development Guide

This guide provides essential information for developers working on the Media Share application.

## Table of Contents

- [Development Environment Setup](#development-environment-setup)
- [Project Structure](#project-structure)
- [Frontend Development](#frontend-development)
- [Backend Development](#backend-development)
- [API Documentation](#api-documentation)
- [Database Schema](#database-schema)
- [Testing](#testing)
- [Deployment](#deployment)
- [Best Practices](#best-practices)

## Development Environment Setup

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Node.js 14.x or higher
- npm 6.x or higher
- Apache with mod_rewrite enabled
- Git

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/media-share.git
   cd media-share
   ```

2. **Set up the database**
   ```bash
   mysql -u root -p
   CREATE DATABASE media_share;
   exit;
   mysql -u root -p media_share < backend/database_updates.sql
   ```

3. **Configure the application**
   ```bash
   cp backend/config.template.php backend/config.php
   # Edit the config.php file with your database credentials
   ```

4. **Set up local virtual hosts**

   Add the following to your hosts file (`/etc/hosts` on Linux/Mac, `C:\Windows\System32\drivers\etc\hosts` on Windows):
   ```
   127.0.0.1 my.media.local
   127.0.0.1 api.media.local
   127.0.0.1 admin.media.local
   ```

   Configure Apache virtual hosts to match these domains and point to the appropriate directories.

5. **Install frontend dependencies**
   ```bash
   cd frontend
   npm install
   ```

6. **Start the development server**
   ```bash
   npm run dev
   ```

## Project Structure

The Media Share application follows a modular structure with separate frontend and backend components:

```
media-share/
├── backend/                  # PHP backend code
│   ├── admin/                # Admin panel
│   ├── api/                  # API endpoints
│   ├── config/               # Configuration files
│   ├── edit/                 # File editor
│   ├── models/               # Data models and repositories
│   └── uploads/              # Media upload directory
├── frontend/                 # React frontend
│   ├── dist/                 # Production build
│   ├── public/               # Static assets
│   └── src/                  # Source code
│       ├── components/       # UI components
│       ├── hooks/            # Custom React hooks
│       ├── lib/              # Utility functions
│       ├── pages/            # Page components
│       ├── services/         # Service classes
│       └── stores/           # State management
└── docs/                     # Documentation
```

## Frontend Development

The frontend is built with React, using modern patterns and best practices.

### Technology Stack

- **React**: UI library
- **Zustand**: State management
- **React Router**: Navigation
- **Axios**: API client
- **Tailwind CSS**: Styling
- **Vite**: Build tool

### Key Components

- **MediaDetails.jsx**: Main component for displaying media items
- **ChaptersComponent.jsx**: Handles chapter navigation for video/audio
- **CommentsList.jsx & CommentForm.jsx**: Comment functionality
- **EnhancedImageViewer.jsx**: Advanced image viewing capabilities
- **AdvancedSearch.jsx**: Search interface with filtering

### State Management

The application uses Zustand for state management with the following main stores:

- **mediaStore.js**: Manages media data and interactions
- **userStore.js**: Handles user authentication and preferences

### Services

Business logic is encapsulated in service classes:

- **MediaAnalyticsService.js**: Tracks user interactions
- **ChaptersService.js**: Manages chapter data
- **CommentsService.js**: Handles comment operations
- **SearchService.js**: Provides search functionality
- **UserPreferencesService.js**: Manages user preferences

### Development Workflow

1. **Run the development server**
   ```bash
   cd frontend
   npm run dev
   ```

2. **Build for production**
   ```bash
   npm run build
   ```

### Adding New Components

When adding new components, follow these guidelines:

1. Create the component in the appropriate directory
2. Use functional components with hooks
3. Handle errors and loading states
4. Include JSDoc comments for documentation
5. Add proper PropTypes validation

Example:
```jsx
// frontend/src/components/NewComponent.jsx
import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import { logger } from '../lib/logger';

/**
 * NewComponent description
 * @param {Object} props - Component props
 * @param {string} props.title - Component title
 */
export function NewComponent({ title }) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchData = async () => {
      try {
        // Data fetching logic here
        setData(result);
      } catch (error) {
        logger.error('Error in NewComponent:', error);
        setError('Failed to load data');
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div>
      <h2>{title}</h2>
      {/* Component content */}
    </div>
  );
}

NewComponent.propTypes = {
  title: PropTypes.string.isRequired
};
```

## Backend Development

The backend is built with PHP, following an object-oriented approach with repository pattern.

### PHP Components

- **Repository Classes**: Handle database operations
- **API Endpoints**: Process client requests
- **Admin Pages**: Server-rendered admin interface
- **Service Classes**: Implement business logic

### Adding a New API Endpoint

1. Create a new PHP file in the `backend/api/` directory
2. Include common requirements
3. Implement the API logic
4. Use proper error handling and response formatting

Example:
```php
<?php
// backend/api/new_endpoint.php
require_once('../config.php');
require_once('../models/NewRepository.php');
enableCors();

// Get database connection
$db = getDbConnection();
if (!$db) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
    exit;
}

// Initialize repository
$repository = new NewRepository($db);

// Handle request based on method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetRequest($repository);
        break;
    case 'POST':
        handlePostRequest($repository);
        break;
    default:
        sendJsonResponse(['error' => 'Method not allowed'], 405);
        break;
}

/**
 * Handle GET requests
 * @param NewRepository $repository
 */
function handleGetRequest($repository) {
    try {
        // Implementation
        sendJsonResponse($data);
    } catch (Exception $e) {
        logError('Error handling GET request: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Server error'], 500);
    }
}

/**
 * Handle POST requests
 * @param NewRepository $repository
 */
function handlePostRequest($repository) {
    try {
        // Implementation
        sendJsonResponse($result, 201);
    } catch (Exception $e) {
        logError('Error handling POST request: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Server error'], 500);
    }
}
```

### Adding a New Repository

1. Create a new PHP file in the `backend/models/` directory
2. Define the repository class
3. Implement data access methods

Example:
```php
<?php
// backend/models/NewRepository.php
require_once(__DIR__ . '/../config.php');

/**
 * Repository class for handling new feature
 */
class NewRepository {
    private $db;
    
    /**
     * Constructor
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get items from database
     * @param array $filters Optional filters
     * @return array Items
     */
    public function getItems($filters = []) {
        try {
            // Implementation
            return $results;
        } catch (PDOException $e) {
            logError('Error getting items: ' . $e->getMessage());
            return [];
        }
    }
    
    // More methods...
}
```

## API Documentation

The Media Share API uses RESTful principles with JSON responses.

### Base URLs

- API Base URL: `https://api.media.webally.co.za`

### Authentication

The API currently uses a simple session-based authentication system:

1. Call the `/session` endpoint with a `user_name` parameter
2. Session ID is stored in cookies
3. All subsequent requests use this session

### Core Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/media` | GET | Get all media items |
| `/media/{id}` | GET | Get a specific media item |
| `/media/{id}/stream` | GET | Stream media content |
| `/media/{id}/thumbnail` | GET | Get media thumbnail |
| `/chapters/media/{id}` | GET | Get chapters for a media item |
| `/comments/media/{id}` | GET | Get comments for a media item |
| `/search` | GET | Search for media items |
| `/track` | POST | Record an analytics event |
| `/session` | GET | Start or get session information |
| `/session` | POST | End a session |
| `/preferences` | GET/POST | Manage user preferences |

For complete API documentation, see [API Documentation](api-docs.md).

## Database Schema

The database uses a relational schema with the following main tables:

- `media`: Stores media item metadata
- `tags`: Stores tag definitions
- `media_tags`: Many-to-many relationship between media and tags
- `chapters`: Stores chapter information for time-based media
- `comments`: Stores user comments
- `analytics`: Tracks user interactions
- `search_logs`: Records search activity
- `user_preferences`: Stores user settings

For the complete database schema, see [Database Schema](database-schema.md).

## Testing

### Frontend Testing

Run tests with:
```bash
cd frontend
npm test
```

Write tests using Jest and React Testing Library.

### Backend Testing

Run tests with:
```bash
cd backend/tests
php run_tests.php
```

## Deployment

### Production Build

1. **Frontend Build**
   ```bash
   cd frontend
   npm run build
   ```

2. **Deploy to Web Server**
   - Copy the `frontend/dist` directory to the web server
   - Deploy backend PHP files
   - Configure server to route requests properly

### Server Requirements

- Apache 2.4+ with mod_rewrite
- PHP 7.4+
- MySQL 5.7+
- 1GB+ RAM

## Best Practices

### Code Style

- **PHP**: Follow PSR-12 coding standards
- **JavaScript**: Use ESLint with Airbnb configuration
- **CSS**: Follow BEM naming convention

### Security

- Always validate user input
- Use prepared statements for database queries
- Sanitize output to prevent XSS attacks
- Use HTTPS in production
- Implement proper error handling

### Performance

- Optimize media files (compression, thumbnails)
- Use lazy loading for media items
- Implement pagination for large datasets
- Cache frequently accessed data
- Minimize HTTP requests

### Git Workflow

1. Create a feature branch from `develop`
2. Make changes and commit with descriptive messages
3. Push branch and create a pull request
4. Ensure code passes CI checks
5. Get code review and approval
6. Merge to `develop`

### Documentation

- Document all functions, classes, and methods
- Update API documentation when endpoints change
- Keep this development guide up to date