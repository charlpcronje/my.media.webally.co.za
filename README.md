# Media Share PWA

A simple Progressive Web App for sharing media files (videos, audio, and images) with analytics tracking.

## Features

- Upload and share media files (MP4, MP3, JPG, PNG, GIF)
- Tag-based organization
- User identification via URL parameter
- Media playback with tracking (views, play, pause, seek, etc.)
- PWA support for offline access
- Dark/Light mode toggle
- Admin dashboard for managing content

## Project Structure

```
media-share/
├── frontend/         # React frontend (Vite, TailwindCSS, ShadCN)
└── backend/          # PHP API and admin dashboard
    ├── api/          # PHP API endpoints
    ├── admin/        # PHP admin dashboard
    └── uploads/      # Media storage directory
```

## Requirements

### Frontend
- Node.js v22
- NPM

### Backend
- PHP 7.4+
- MySQL
- Apache

## Setup Instructions

### Backend Setup

1. Make sure your Apache server is running and MySQL server is configured
2. Place the `backend` folder in your document root
3. Navigate to `http://your-server/backend/init_db.php` to initialize the database
4. The default admin credentials will be created:
   - Username: `admin`
   - Password: `admin123`
5. Access the admin dashboard at `http://your-server/backend/admin/`

### Frontend Setup

1. Navigate to the frontend directory
2. Install dependencies:
   ```
   npm install
   ```
3. Configure the API URL in `vite.config.js` to point to your backend
4. Start the development server:
   ```
   npm run dev
   ```
5. Build for production:
   ```
   npm run build
   ```
6. Deploy the built files from `dist` folder to your web server

## Usage

### User Interface

- Access the application at `http://your-server/`
- Add a user parameter to the URL: `http://your-server/?name=charl` or `http://your-server/?name=nade`
- Browse, play, and interact with media

### Admin Interface

- Access the admin dashboard at `http://your-server/backend/admin/`
- Upload and manage media files
- View analytics data
- Create and manage tags

## API Endpoints

- `GET /api/media.php` - Get all media or filter by type/tag
- `POST /api/media.php` - Upload new media
- `DELETE /api/media.php?id=X` - Delete media
- `GET /api/tags.php` - Get all tags
- `POST /api/tags.php` - Create new tag
- `DELETE /api/tags.php?name=X` - Delete tag
- `POST /api/track.php` - Track media events
- `GET /api/session.php?name=X` - Start session
- `GET /api/session.php` - Get session info
- `POST /api/session.php?end` - End session
