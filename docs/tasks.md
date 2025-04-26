# Tasks

## Section 1: Project Setup
1. [ ] Set up development environment
   1.1. [ ] Install and configure local web server
      1.1.1. [ ] Install Apache/Nginx or use built-in PHP server
      1.1.2. [ ] Configure PHP 7.4+ with required extensions (mysqli, json, fileinfo)
      1.1.3. [ ] Set appropriate PHP configuration in php.ini (upload_max_filesize, post_max_size)
   1.2. [ ] Set up MySQL database
      1.2.1. [ ] Create database named 'media_manager'
      1.2.2. [ ] Create database user with appropriate permissions
   1.3. [ ] Create application folder structure
      1.3.1. [xx] Create project root folder
      1.3.2. [xx] Create '/backend' folder for PHP files
      1.3.3. [xx] Create '/frontend' folder for React application
      1.3.4. [xx] Create '/backend/uploads' folder for media storage
      1.3.5. [xx] Create '/backend/uploads/thumbnails' folder
      1.3.6. [xx] Create '/backend/uploads/media' folder

2. [ ] Create configuration files
   2.1. [ ] Create database configuration file
      2.1.1. [xx] Create '/backend/config/database.php' with the following variables:
         - $db_host (database hostname)
         - $db_name (database name)
         - $db_user (database username)
         - $db_pass (database password)
   2.2. [ ] Create application configuration file
      2.2.1. [xx] Create '/backend/config/app.php' with the following variables:
         - $app_url (base URL of the application)
         - $jwt_secret (random string for JWT signing)
         - $upload_path (relative path to media uploads)
         - $thumbnail_path (relative path to thumbnail uploads)

## Section 2: Database Schema Creation
1. [ ] Create users table
   1.1. [xx] Create '/backend/database/users_table.sql' with the following SQL:
   ```sql
   CREATE TABLE users (
     id INT PRIMARY KEY AUTO_INCREMENT,
     username VARCHAR(50) NOT NULL UNIQUE,
     email VARCHAR(100) NOT NULL UNIQUE,
     password VARCHAR(255) NOT NULL,
     role ENUM('user', 'admin') DEFAULT 'user',
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
   );
   ```
   1.2. [xx] Create '/backend/database/admin_user.sql' with admin creation SQL:
   ```sql
   INSERT INTO users (username, email, password, role) 
   VALUES ('admin', 'admin@example.com', SHA2('admin_password', 256), 'admin');
   ```

2. [ ] Create media table
   2.1. [xx] Create '/backend/database/media_table.sql' with the following SQL:
   ```sql
   CREATE TABLE media (
     id INT PRIMARY KEY AUTO_INCREMENT,
     title VARCHAR(100) NOT NULL,
     description TEXT,
     type ENUM('video', 'audio') NOT NULL,
     file_path VARCHAR(255) NOT NULL,
     thumbnail_path VARCHAR(255),
     duration INT,
     tags VARCHAR(255),
     average_rating DECIMAL(3,2) DEFAULT 0,
     ratings_count INT DEFAULT 0,
     play_count INT DEFAULT 0,
     uploaded_by INT,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     FOREIGN KEY (uploaded_by) REFERENCES users(id)
   );
   ```

3. [ ] Create ratings table
   3.1. [xx] Create '/backend/database/ratings_table.sql' with the following SQL:
   ```sql
   CREATE TABLE ratings (
     id INT PRIMARY KEY AUTO_INCREMENT,
     media_id INT NOT NULL,
     user_id INT NOT NULL,
     rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE,
     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
     UNIQUE KEY (media_id, user_id)
   );
   ```

4. [ ] Create analytics table
   4.1. [xx] Create '/backend/database/analytics_table.sql' with the following SQL:
   ```sql
   CREATE TABLE analytics (
     id INT PRIMARY KEY AUTO_INCREMENT,
     media_id INT NOT NULL,
     user_id INT NOT NULL,
     session_id VARCHAR(100) NOT NULL,
     device_type VARCHAR(50),
     browser VARCHAR(50),
     os VARCHAR(50),
     start_time TIMESTAMP NOT NULL,
     end_time TIMESTAMP NULL,
     duration INT,
     completed BOOLEAN DEFAULT FALSE,
     skipped BOOLEAN DEFAULT FALSE,
     skip_position INT,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE,
     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
   );
   ```

5. [ ] Create database initialization script
   5.1. [xx] Create '/backend/database/init_db.php' that:
      5.1.1. [ ] Includes database configuration 
      5.1.2. [ ] Executes all SQL scripts in proper order
      5.1.3. [ ] Reports success/failure for each table creation

## Section 3: Backend PHP Core
1. [ ] Create database connection utility
   1.1. [xx] Create '/backend/utils/Database.php' with:
      1.1.1. [xx] Database class with connection handling
      1.1.2. [xx] Methods for query execution
      1.1.3. [xx] Methods for prepared statements
      1.1.4. [xx] Error handling and logging

2. [ ] Create authentication utilities  
   2.1. [xx] Create '/backend/utils/Auth.php' with:
      2.1.1. [xx] Methods for password hashing and verification
      2.1.2. [xx] JWT token generation and validation
      2.1.3. [xx] Session handling functions
   
3. [ ] Create file handling utility
   3.1. [xx] Create '/backend/utils/FileUpload.php' with:
      3.1.1. [xx] Methods for validating file types (video/audio)
      3.1.2. [xx] Function for generating unique filenames
      3.1.3. [xx] File upload handling with error checking
      3.1.4. [xx] Basic thumbnail generation for videos

4. [ ] Create logger utility
   4.1. [xx] Create '/backend/utils/Logger.php' with:
      4.1.1. [xx] Methods for logging errors, warnings, info
      4.1.2. [xx] Log rotation functionality
      4.1.3. [xx] Error formatting

## Section 4: Backend PHP Models
1. [xx] Create User model
   1.1. [xx] Create '/backend/models/User.php' with:
      1.1.1. [xx] Properties matching user table fields
      1.1.2. [xx] Methods for user creation
      1.1.3. [xx] Methods for user authentication
      1.1.4. [xx] Methods for user profile management
      1.1.5. [xx] Methods for retrieving users list (admin only)

2. [xx] Create Media model
   2.1. [xx] Create '/backend/models/Media.php' with:
      2.1.1. [xx] Properties matching media table fields
      2.1.2. [xx] Methods for media creation
      2.1.3. [xx] Methods for media retrieval (single and list)
      2.1.4. [xx] Methods for media update
      2.1.5. [xx] Methods for media deletion
      2.1.6. [xx] Methods for incrementing play count

3. [xx] Create Rating model
   3.1. [xx] Create '/backend/models/Rating.php' with:
      3.1.1. [xx] Properties matching rating table fields
      3.1.2. [xx] Methods for adding/updating ratings
      3.1.3. [xx] Methods for calculating average rating
      3.1.4. [xx] Methods for retrieving user's rating for media

4. [xx] Create Analytics model
   4.1. [xx] Create '/backend/models/Analytics.php' with:
      4.1.1. [xx] Properties matching analytics table fields
      4.1.2. [xx] Methods for recording playback start
      4.1.3. [xx] Methods for recording playback end
      4.1.4. [xx] Methods for recording skips
      4.1.5. [xx] Methods for retrieving analytics data

## Section 5: Backend PHP Controllers
 1. [xx] Create base controller
   1.1. [xx] Create '/backend/controllers/Controller.php' with:
      1.1.1. [xx] Methods for handling JSON responses
      1.1.2. [xx] Error response formatting
      1.1.3. [xx] Request validation utilities

 2. [xx] Create authentication controller
   2.1. [xx] Create '/backend/controllers/AuthController.php' with:
      2.1.1. [xx] Login endpoint handling (POST /api/auth/login)
         - Required fields: username/email, password
         - Response: JWT token, user info
      2.1.2. [xx] Logout endpoint (POST /api/auth/logout)
      2.1.3. [xx] Get current user endpoint (GET /api/auth/user)
      2.1.4. [xx] User role verification methods

 3. [xx] Create media controller
   3.1. [xx] Create '/backend/controllers/MediaController.php' with:
      3.1.1. [xx] List media endpoint (GET /api/media)
         - Optional query params: type, page, limit
         - Response: media items array with pagination
      3.1.2. [xx] Get single media endpoint (GET /api/media/{id})
         - Response: single media item with ratings
      3.1.3. [xx] Play media endpoint (GET /api/media/{id}/play)
         - Records analytics before serving file
      3.1.4. [xx] Add media endpoint (POST /api/media) [Admin only]
         - Required fields: title, type, media_file
         - Optional fields: description, tags
      3.1.5. [xx] Update media endpoint (PUT /api/media/{id}) [Admin only]
         - Updatable fields: title, description, tags
      3.1.6. [xx] Delete media endpoint (DELETE /api/media/{id}) [Admin only]

 4. [xx] Create rating controller
   4.1. [xx] Create '/backend/controllers/RatingController.php' with:
      4.1.1. [xx] Rate media endpoint (POST /api/media/{id}/rate)
         - Required fields: rating (1-5)
         - Response: updated average rating
      4.1.2. [xx] Get user rating endpoint (GET /api/media/{id}/rating)
         - Response: user's rating or null

 5. [xx] Create analytics controller
   5.1. [xx] Create '/backend/controllers/AnalyticsController.php' with:
      5.1.1. [xx] Track playback start endpoint (POST /api/analytics/start)
         - Required fields: media_id, session_id
         - Optional fields: device_type, browser, os
      5.1.2. [xx] Track playback end endpoint (POST /api/analytics/end)
         - Required fields: media_id, session_id, duration
         - Optional fields: completed
      5.1.3. [xx] Track skip endpoint (POST /api/analytics/skip)
         - Required fields: media_id, session_id, position
      5.1.4. [xx] Get analytics endpoint (GET /api/analytics) [Admin only]
         - Optional query params: media_id, user_id, date_range
         - Response: analytics data with aggregations

 6. [xx] Create admin controller
   6.1. [xx] Create '/backend/controllers/AdminController.php' with:
      6.1.1. [xx] User management endpoints
         - List users (GET /api/admin/users)
         - Create user (POST /api/admin/users)
         - Update user (PUT /api/admin/users/{id})
         - Delete user (DELETE /api/admin/users/{id})
      6.1.2. [xx] Dashboard data endpoint (GET /api/admin/dashboard)
         - Response: total users, total media, recent plays

## Section 6: Backend API Routes
1. [xx] Create API entry point
   1.1. [xx] Create '/backend/index.php' that:
      1.1.1. [xx] Handles routing to appropriate controllers
      1.1.2. [xx] Processes request headers and body
      1.1.3. [xx] Sets appropriate response headers
      1.1.4. [xx] Handles CORS if needed

2. [xx] Create .htaccess for API routing
   2.1. [xx] Create '/backend/.htaccess' with:
      2.1.1. [xx] Rewrite rules to direct all API requests to index.php
      2.1.2. [xx] CORS headers configuration
      2.1.3. [xx] Security headers configuration

## Section 7: Frontend Setup
1. [xx] Initialize React application
   1.1. [xx] Create React app in '/frontend' folder
      1.1.1. [xx] Run create-react-app with JavaScript template
      1.1.2. [xx] Clean up default files and components
   1.2. [xx] Install dependencies
      1.2.1. [xx] Install React Router: npm install react-router-dom
      1.2.2. [xx] Install Axios: npm install axios
      1.2.3. [xx] Install Tailwind CSS: npm install tailwindcss postcss autoprefixer
      1.2.4. [xx] Install ShadCN UI components
      1.2.5. [xx] Install React Player: npm install react-player

2. [xx] Configure Tailwind CSS with dark mode
   2.1. [xx] Initialize Tailwind CSS
      2.1.1. [xx] Generate tailwind.config.js and postcss.config.js
      2.1.2. [xx] Configure dark mode in tailwind.config.js
   2.2. [xx] Set up ShadCN UI
      2.2.1. [xx] Configure dark mode theme in ShadCN
      2.2.2. [xx] Import necessary ShadCN components

3. [xx] Create PWA configuration
   3.1. [xx] Configure manifest.json in '/frontend/public'
      3.1.1. [xx] Set app name, description, theme colors
      3.1.2. [xx] Define app icons in various sizes
   3.2. [xx] Create service worker in '/frontend/src/serviceWorker.js'
      3.2.1. [xx] Implement caching strategies for assets
      3.2.2. [xx] Add offline support functionality
      3.2.3. [xx] Configure media caching rules

## Section 8: Frontend Services
1. [xx] Create API service
   1.1. [xx] Create '/frontend/src/services/api.js' with:
      1.1.1. [xx] Axios instance configuration with base URL
      1.1.2. [xx] Request/response interceptors for JWT handling
      1.1.3. [xx] Error handling utilities

2. [xx] Create authentication service
   2.1. [xx] Create '/frontend/src/services/auth.js' with:
      2.1.1. [xx] Login function (username/email, password)
      2.1.2. [xx] Logout function
      2.1.3. [xx] Get current user function
      2.1.4. [xx] JWT storage and retrieval
      2.1.5. [xx] Check if user is admin function

3. [xx] Create media service
   3.1. [xx] Create '/frontend/src/services/media.js' with:
      3.1.1. [xx] Get all media function (with filter/pagination)
      3.1.2. [xx] Get single media function
      3.1.3. [xx] Get media play URL function
      3.1.4. [xx] Admin: add media function
      3.1.5. [xx] Admin: update media function
      3.1.6. [xx] Admin: delete media function

4. [xx] Create rating service
   4.1. [xx] Create '/frontend/src/services/rating.js' with:
      4.1.1. [xx] Rate media function
      4.1.2. [xx] Get user rating for media function

5. [xx] Create analytics service
   5.1. [xx] Create '/frontend/src/services/analytics.js' with:
      5.1.1. [xx] Track playback start function
      5.1.2. [xx] Track playback end function
      5.1.3. [xx] Track skip function
      5.1.4. [xx] Admin: get analytics data function

6. [xx] Create admin service
   6.1. [xx] Create '/frontend/src/services/admin.js' with:
      6.1.1. [xx] User management functions
      6.1.2. [xx] Media management functions
      6.1.3. [xx] Analytics data retrieval function

## Section 9: Frontend Context
1. [xx] Create authentication context
   1.1. [xx] Create '/frontend/src/contexts/AuthContext.js' with:
      1.1.1. [xx] User state management
      1.1.2. [xx] Login/logout functions
      1.1.3. [xx] Auth state persistence

2. [xx] Create media context
   2.1. [xx] Create '/frontend/src/contexts/MediaContext.js' with:
      2.1.1. [xx] Media list state management
      2.1.2. [xx] Current media state management
      2.1.3. [xx] Media loading state

3. [xx] Create rating context
   3.1. [xx] Create '/frontend/src/contexts/RatingContext.js' with:
      3.1.1. [xx] User rating state management
      3.1.2. [xx] Rate media function
      3.1.3. [xx] Get user rating for media function

4. [xx] Create analytics context
   4.1. [xx] Create '/frontend/src/contexts/AnalyticsContext.js' with:
      4.1.1. [xx] Analytics data state management
      4.1.2. [xx] Track playback start function
      4.1.3. [xx] Track playback end function
      4.1.4. [xx] Track skip function
      4.1.5. [xx] Admin: get analytics data function

5. [xx] Create admin context
   5.1. [xx] Create '/frontend/src/contexts/AdminContext.js' with:
      5.1.1. [xx] User management functions
      5.1.2. [xx] Media management functions
      5.1.3. [xx] Analytics data retrieval function

## Section 10: Frontend Components
 1. [xx] Create layout components
   1.1. [xx] Create '/frontend/src/components/layout/Layout.js'
      1.1.1. [xx] Basic page structure with navigation
      1.1.2. [xx] Dark mode styling
   1.2. [xx] Create '/frontend/src/components/layout/Header.js'
      1.2.1. [xx] Logo and navigation links
      1.2.2. [xx] Login/logout button
      1.2.3. [xx] Admin section link (if admin)
   1.3. [xx] Create '/frontend/src/components/layout/Footer.js'
      1.3.1. [xx] Simple footer with copyright info
   1.4. [xx] Create '/frontend/src/components/layout/Sidebar.js'
      1.4.1. [xx] Sidebar navigation
   1.5. [xx] Create '/frontend/src/components/common/Card.js'
   1.6. [xx] Create '/frontend/src/components/common/Button.js'
   1.7. [xx] Create '/frontend/src/components/common/Input.js'
   1.8. [xx] Create '/frontend/src/components/common/Loader.js'
   1.9. [xx] Create '/frontend/src/components/common/NotFound.js'
 2. [xx] Create media components
   2.1. [xx] Create '/frontend/src/components/media/MediaList.js'
      2.1.1. [xx] Grid display of media items
      2.1.2. [xx] Responsive design for mobile/desktop
   2.2. [xx] Create '/frontend/src/components/media/MediaCard.js'
      2.2.1. [xx] Thumbnail display
      2.2.2. [xx] Title and brief description
      2.2.3. [xx] Average rating display
      2.2.4. [xx] Click handler for media selection
   2.3. [xx] Create '/frontend/src/components/media/MediaPlayer.js'
      2.3.1. [xx] Full-screen video/audio player
      2.3.2. [xx] Custom controls overlay
      2.3.3. [xx] Title and description overlay
      2.3.4. [xx] Play/pause/progress tracking
      2.3.5. [xx] Integration with analytics service
   2.4. [xx] Create '/frontend/src/components/media/RatingStars.js'
      2.4.1. [xx] 5-star rating display
      2.4.2. [xx] Interactive star selection
      2.4.3. [xx] Submit rating functionality
      2.4.4. [xx] Current user rating display
 3. [xx] Create auth components
   3.1. [xx] Create '/frontend/src/components/auth/LoginForm.js'
      3.1.1. [xx] Username/email input field
      3.1.2. [xx] Password input field
      3.1.3. [xx] Submit button
      3.1.4. [xx] Form validation
      3.1.5. [xx] Error handling and display
   3.2. [xx] Create '/frontend/src/components/auth/RegisterForm.js'
      3.2.1. [xx] Username/email input field
      3.2.2. [xx] Password input field
      3.2.3. [xx] Submit button
      3.2.4. [xx] Form validation
      3.2.5. [xx] Error handling and display
 4. [xx] Create admin components
   4.1. [xx] Create '/frontend/src/components/admin/MediaForm.js'
      4.1.1. [xx] Media title input field
      4.1.2. [xx] Media description textarea
      4.1.3. [xx] Media type selection (video/audio)
      4.1.4. [xx] File upload input
      4.1.5. [xx] Tags input field
      4.1.6. [xx] Submit button with loading state
   4.2. [xx] Create '/frontend/src/components/admin/MediaTable.js'
      4.2.1. [xx] Table of all media items
      4.2.2. [xx] Edit and delete buttons
      4.2.3. [xx] Sorting and filtering options
   4.3. [xx] Create '/frontend/src/components/admin/UserTable.js'
      4.3.1. [xx] Table of all users
      4.3.2. [xx] Edit and delete buttons
   4.4. [xx] Create '/frontend/src/components/admin/AnalyticsDisplay.js'
      4.4.1. [xx] Basic analytics metrics display
      4.4.2. [xx] Media play count statistics
      4.4.3. [xx] Recent playback activity
   4.5. [xx] Create '/frontend/src/components/admin/Dashboard.js'
      4.5.1. [xx] Overview statistics
      4.5.2. [xx] Recent activity
      4.1.2. [xx] Interactive star selection
      4.1.3. [xx] Submit rating functionality
      4.1.4. [xx] Current user rating display

5. [xx] Create admin components
   5.1. [xx] Create '/frontend/src/components/admin/MediaForm.js'
      5.1.1. [xx] Media title input field
      5.1.2. [xx] Media description textarea
      5.1.3. [xx] Media type selection (video/audio)
      5.1.4. [xx] File upload input
      5.1.5. [xx] Tags input field
      5.1.6. [xx] Submit button with loading state
   5.2. [xx] Create '/frontend/src/components/admin/MediaTable.js'
      5.2.1. [xx] Table of all media items
      5.2.2. [xx] Edit and delete buttons
      5.2.3. [xx] Sorting and filtering options
   5.3. [xx] Create '/frontend/src/components/admin/UserTable.js'
      5.3.1. [xx] Table of all users
      5.3.2. [ ] Edit and delete buttons
   5.4. [ ] Create '/frontend/src/components/admin/AnalyticsDisplay.js'
      5.4.1. [ ] Basic analytics metrics display
      5.4.2. [ ] Media play count statistics
      5.4.3. [ ] Recent playback activity

## Section 11: Frontend Pages
1. [xx] Create home page
   1.1. [xx] Create '/frontend/src/pages/Home.js'
      1.1.1. [xx] Display MediaList component
      1.1.2. [xx] Mobile-optimized layout

2. [xx] Create media player page
   2.1. [xx] Create '/frontend/src/pages/MediaPlayer.js'
      2.1.1. [xx] Full-screen MediaPlayer component
      2.1.2. [xx] Rating component below player
      2.1.3. [xx] Back button to return to media list
      2.1.4. [xx] Media details display

3. [xx] Create login page
   3.1. [xx] Create '/frontend/src/pages/Login.js'
      3.1.1. [xx] LoginForm component
      3.1.2. [xx] Redirect to home after login

4. [xx] Create admin pages
   4.1. [xx] Create '/frontend/src/pages/admin/Dashboard.js'
      4.1.1. [xx] Overview statistics
      4.1.2. [xx] Recent activity
   4.2. [xx] Create '/frontend/src/pages/admin/MediaManagement.js'
      4.2.1. [xx] MediaTable component
      4.2.2. [xx] Add new media button
      4.2.3. [xx] MediaForm component for adding/editing
   4.3. [xx] Create '/frontend/src/pages/admin/UserManagement.js'
      4.3.1. [xx] UserTable component
      4.3.2. [xx] User form for adding/editing users
   4.4. [xx] Create '/frontend/src/pages/admin/Analytics.js'
      4.4.1. [xx] AnalyticsDisplay component
      4.4.2. [xx] Date range filters

## Section 12: Frontend Routing and Main App
 1. [xx] Set up React Router
   1.1. [xx] Create '/frontend/src/App.js' with routes:
      1.1.1. [xx] Route for home page ('/')
      1.1.2. [xx] Route for media player ('/media/:id')
      1.1.3. [xx] Route for login page ('/login')
      1.1.4. [xx] Routes for admin:
         - Dashboard ('/admin')
         - Media Management ('/admin/media')
         - User Management ('/admin/users')
         - Analytics ('/admin/analytics')
    1.2. [xx] Implement protected routes for admin section
       1.2.1. [xx] Create ProtectedRoute component
       1.2.2. [xx] Verify admin role for admin routes

2. [xx] Configure application entry point
   2.1. [xx] Update '/frontend/src/index.js'
      2.1.1. [xx] Wrap App with necessary contexts
      2.1.2. [xx] Include Tailwind CSS
      2.1.3. [xx] Register service worker for PWA

## Section 13: Deployment
1. [xx] Prepare frontend for production
   1.1. [xx] Build React application
      1.1.1. [xx] Run production build
      1.1.2. [xx] Verify build output
   
2. [xx] Prepare backend for production
   2.1. [xx] Create production configuration
      2.1.1. [xx] Update database credentials for production
      2.1.2. [xx] Configure appropriate file paths

3. [ ] Deploy to production server
   3.1. [ ] Upload frontend build files
   3.2. [ ] Upload backend PHP files
   3.3. [ ] Set appropriate file permissions
   3.4. [ ] Configure web server