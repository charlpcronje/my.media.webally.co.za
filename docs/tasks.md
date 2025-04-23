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
      1.3.1. [x] Create project root folder
      1.3.2. [x] Create '/backend' folder for PHP files
      1.3.3. [x] Create '/frontend' folder for React application
      1.3.4. [x] Create '/backend/uploads' folder for media storage
      1.3.5. [x] Create '/backend/uploads/thumbnails' folder
      1.3.6. [x] Create '/backend/uploads/media' folder

2. [ ] Create configuration files
   2.1. [ ] Create database configuration file
      2.1.1. [x] Create '/backend/config/database.php' with the following variables:
         - $db_host (database hostname)
         - $db_name (database name)
         - $db_user (database username)
         - $db_pass (database password)
   2.2. [ ] Create application configuration file
      2.2.1. [x] Create '/backend/config/app.php' with the following variables:
         - $app_url (base URL of the application)
         - $jwt_secret (random string for JWT signing)
         - $upload_path (relative path to media uploads)
         - $thumbnail_path (relative path to thumbnail uploads)

## Section 2: Database Schema Creation
1. [ ] Create users table
   1.1. [x] Create '/backend/database/users_table.sql' with the following SQL:
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
   1.2. [x] Create '/backend/database/admin_user.sql' with admin creation SQL:
   ```sql
   INSERT INTO users (username, email, password, role) 
   VALUES ('admin', 'admin@example.com', SHA2('admin_password', 256), 'admin');
   ```

2. [ ] Create media table
   2.1. [x] Create '/backend/database/media_table.sql' with the following SQL:
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
   3.1. [x] Create '/backend/database/ratings_table.sql' with the following SQL:
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
   4.1. [x] Create '/backend/database/analytics_table.sql' with the following SQL:
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
   5.1. [x] Create '/backend/database/init_db.php' that:
      5.1.1. [ ] Includes database configuration 
      5.1.2. [ ] Executes all SQL scripts in proper order
      5.1.3. [ ] Reports success/failure for each table creation

## Section 3: Backend PHP Core
1. [ ] Create database connection utility
   1.1. [x] Create '/backend/utils/Database.php' with:
      1.1.1. [x] Database class with connection handling
      1.1.2. [x] Methods for query execution
      1.1.3. [x] Methods for prepared statements
      1.1.4. [x] Error handling and logging

2. [ ] Create authentication utilities  
   2.1. [x] Create '/backend/utils/Auth.php' with:
      2.1.1. [x] Methods for password hashing and verification
      2.1.2. [x] JWT token generation and validation
      2.1.3. [x] Session handling functions
   
3. [ ] Create file handling utility
   3.1. [x] Create '/backend/utils/FileUpload.php' with:
      3.1.1. [x] Methods for validating file types (video/audio)
      3.1.2. [x] Function for generating unique filenames
      3.1.3. [x] File upload handling with error checking
      3.1.4. [x] Basic thumbnail generation for videos

4. [ ] Create logger utility
   4.1. [x] Create '/backend/utils/Logger.php' with:
      4.1.1. [x] Methods for logging errors, warnings, info
      4.1.2. [x] Log rotation functionality
      4.1.3. [x] Error formatting

## Section 4: Backend PHP Models
1. [x] Create User model
   1.1. [x] Create '/backend/models/User.php' with:
      1.1.1. [x] Properties matching user table fields
      1.1.2. [x] Methods for user creation
      1.1.3. [x] Methods for user authentication
      1.1.4. [x] Methods for user profile management
      1.1.5. [x] Methods for retrieving users list (admin only)

2. [x] Create Media model
   2.1. [x] Create '/backend/models/Media.php' with:
      2.1.1. [x] Properties matching media table fields
      2.1.2. [x] Methods for media creation
      2.1.3. [x] Methods for media retrieval (single and list)
      2.1.4. [x] Methods for media update
      2.1.5. [x] Methods for media deletion
      2.1.6. [x] Methods for incrementing play count

3. [x] Create Rating model
   3.1. [x] Create '/backend/models/Rating.php' with:
      3.1.1. [x] Properties matching rating table fields
      3.1.2. [x] Methods for adding/updating ratings
      3.1.3. [x] Methods for calculating average rating
      3.1.4. [x] Methods for retrieving user's rating for media

4. [x] Create Analytics model
   4.1. [x] Create '/backend/models/Analytics.php' with:
      4.1.1. [x] Properties matching analytics table fields
      4.1.2. [x] Methods for recording playback start
      4.1.3. [x] Methods for recording playback end
      4.1.4. [x] Methods for recording skips
      4.1.5. [x] Methods for retrieving analytics data

## Section 5: Backend PHP Controllers
 1. [x] Create base controller
   1.1. [x] Create '/backend/controllers/Controller.php' with:
      1.1.1. [x] Methods for handling JSON responses
      1.1.2. [x] Error response formatting
      1.1.3. [x] Request validation utilities

 2. [x] Create authentication controller
   2.1. [x] Create '/backend/controllers/AuthController.php' with:
      2.1.1. [x] Login endpoint handling (POST /api/auth/login)
         - Required fields: username/email, password
         - Response: JWT token, user info
      2.1.2. [x] Logout endpoint (POST /api/auth/logout)
      2.1.3. [x] Get current user endpoint (GET /api/auth/user)
      2.1.4. [x] User role verification methods

 3. [x] Create media controller
   3.1. [x] Create '/backend/controllers/MediaController.php' with:
      3.1.1. [x] List media endpoint (GET /api/media)
         - Optional query params: type, page, limit
         - Response: media items array with pagination
      3.1.2. [x] Get single media endpoint (GET /api/media/{id})
         - Response: single media item with ratings
      3.1.3. [x] Play media endpoint (GET /api/media/{id}/play)
         - Records analytics before serving file
      3.1.4. [x] Add media endpoint (POST /api/media) [Admin only]
         - Required fields: title, type, media_file
         - Optional fields: description, tags
      3.1.5. [x] Update media endpoint (PUT /api/media/{id}) [Admin only]
         - Updatable fields: title, description, tags
      3.1.6. [x] Delete media endpoint (DELETE /api/media/{id}) [Admin only]

 4. [x] Create rating controller
   4.1. [x] Create '/backend/controllers/RatingController.php' with:
      4.1.1. [x] Rate media endpoint (POST /api/media/{id}/rate)
         - Required fields: rating (1-5)
         - Response: updated average rating
      4.1.2. [x] Get user rating endpoint (GET /api/media/{id}/rating)
         - Response: user's rating or null

 5. [x] Create analytics controller
   5.1. [x] Create '/backend/controllers/AnalyticsController.php' with:
      5.1.1. [x] Track playback start endpoint (POST /api/analytics/start)
         - Required fields: media_id, session_id
         - Optional fields: device_type, browser, os
      5.1.2. [x] Track playback end endpoint (POST /api/analytics/end)
         - Required fields: media_id, session_id, duration
         - Optional fields: completed
      5.1.3. [x] Track skip endpoint (POST /api/analytics/skip)
         - Required fields: media_id, session_id, position
      5.1.4. [x] Get analytics endpoint (GET /api/analytics) [Admin only]
         - Optional query params: media_id, user_id, date_range
         - Response: analytics data with aggregations

 6. [x] Create admin controller
   6.1. [x] Create '/backend/controllers/AdminController.php' with:
      6.1.1. [x] User management endpoints
         - List users (GET /api/admin/users)
         - Create user (POST /api/admin/users)
         - Update user (PUT /api/admin/users/{id})
         - Delete user (DELETE /api/admin/users/{id})
      6.1.2. [x] Dashboard data endpoint (GET /api/admin/dashboard)
         - Response: total users, total media, recent plays

## Section 6: Backend API Routes
1. [x] Create API entry point
   1.1. [x] Create '/backend/index.php' that:
      1.1.1. [x] Handles routing to appropriate controllers
      1.1.2. [x] Processes request headers and body
      1.1.3. [x] Sets appropriate response headers
      1.1.4. [x] Handles CORS if needed

2. [x] Create .htaccess for API routing
   2.1. [x] Create '/backend/.htaccess' with:
      2.1.1. [x] Rewrite rules to direct all API requests to index.php
      2.1.2. [x] CORS headers configuration
      2.1.3. [x] Security headers configuration

## Section 7: Frontend Setup
1. [x] Initialize React application
   1.1. [x] Create React app in '/frontend' folder
      1.1.1. [x] Run create-react-app with JavaScript template
      1.1.2. [x] Clean up default files and components
   1.2. [x] Install dependencies
      1.2.1. [x] Install React Router: npm install react-router-dom
      1.2.2. [x] Install Axios: npm install axios
      1.2.3. [x] Install Tailwind CSS: npm install tailwindcss postcss autoprefixer
      1.2.4. [x] Install ShadCN UI components
      1.2.5. [x] Install React Player: npm install react-player

2. [x] Configure Tailwind CSS with dark mode
   2.1. [x] Initialize Tailwind CSS
      2.1.1. [x] Generate tailwind.config.js and postcss.config.js
      2.1.2. [x] Configure dark mode in tailwind.config.js
   2.2. [x] Set up ShadCN UI
      2.2.1. [x] Configure dark mode theme in ShadCN
      2.2.2. [x] Import necessary ShadCN components

3. [x] Create PWA configuration
   3.1. [x] Configure manifest.json in '/frontend/public'
      3.1.1. [x] Set app name, description, theme colors
      3.1.2. [x] Define app icons in various sizes
   3.2. [x] Create service worker in '/frontend/src/serviceWorker.js'
      3.2.1. [x] Implement caching strategies for assets
      3.2.2. [x] Add offline support functionality
      3.2.3. [x] Configure media caching rules

## Section 8: Frontend Services
1. [x] Create API service
   1.1. [x] Create '/frontend/src/services/api.js' with:
      1.1.1. [x] Axios instance configuration with base URL
      1.1.2. [x] Request/response interceptors for JWT handling
      1.1.3. [x] Error handling utilities

2. [x] Create authentication service
   2.1. [x] Create '/frontend/src/services/auth.js' with:
      2.1.1. [x] Login function (username/email, password)
      2.1.2. [x] Logout function
      2.1.3. [x] Get current user function
      2.1.4. [x] JWT storage and retrieval
      2.1.5. [x] Check if user is admin function

3. [x] Create media service
   3.1. [x] Create '/frontend/src/services/media.js' with:
      3.1.1. [x] Get all media function (with filter/pagination)
      3.1.2. [x] Get single media function
      3.1.3. [x] Get media play URL function
      3.1.4. [x] Admin: add media function
      3.1.5. [x] Admin: update media function
      3.1.6. [x] Admin: delete media function

4. [x] Create rating service
   4.1. [x] Create '/frontend/src/services/rating.js' with:
      4.1.1. [x] Rate media function
      4.1.2. [x] Get user rating for media function

5. [x] Create analytics service
   5.1. [x] Create '/frontend/src/services/analytics.js' with:
      5.1.1. [x] Track playback start function
      5.1.2. [x] Track playback end function
      5.1.3. [x] Track skip function
      5.1.4. [x] Admin: get analytics data function

6. [x] Create admin service
   6.1. [x] Create '/frontend/src/services/admin.js' with:
      6.1.1. [x] User management functions
      6.1.2. [x] Media management functions
      6.1.3. [x] Analytics data retrieval function

## Section 9: Frontend Context
1. [x] Create authentication context
   1.1. [x] Create '/frontend/src/contexts/AuthContext.js' with:
      1.1.1. [x] User state management
      1.1.2. [x] Login/logout functions
      1.1.3. [x] Auth state persistence

2. [x] Create media context
   2.1. [x] Create '/frontend/src/contexts/MediaContext.js' with:
      2.1.1. [x] Media list state management
      2.1.2. [x] Current media state management
      2.1.3. [x] Media loading state

3. [x] Create rating context
   3.1. [x] Create '/frontend/src/contexts/RatingContext.js' with:
      3.1.1. [x] User rating state management
      3.1.2. [x] Rate media function
      3.1.3. [x] Get user rating for media function

4. [x] Create analytics context
   4.1. [x] Create '/frontend/src/contexts/AnalyticsContext.js' with:
      4.1.1. [x] Analytics data state management
      4.1.2. [x] Track playback start function
      4.1.3. [x] Track playback end function
      4.1.4. [x] Track skip function
      4.1.5. [x] Admin: get analytics data function

5. [x] Create admin context
   5.1. [x] Create '/frontend/src/contexts/AdminContext.js' with:
      5.1.1. [x] User management functions
      5.1.2. [x] Media management functions
      5.1.3. [x] Analytics data retrieval function

## Section 10: Frontend Components
 1. [x] Create layout components
   1.1. [x] Create '/frontend/src/components/layout/Layout.js'
      1.1.1. [x] Basic page structure with navigation
      1.1.2. [x] Dark mode styling
   1.2. [x] Create '/frontend/src/components/layout/Header.js'
      1.2.1. [x] Logo and navigation links
      1.2.2. [x] Login/logout button
      1.2.3. [x] Admin section link (if admin)
   1.3. [x] Create '/frontend/src/components/layout/Footer.js'
      1.3.1. [x] Simple footer with copyright info
   1.4. [x] Create '/frontend/src/components/layout/Sidebar.js'
      1.4.1. [x] Sidebar navigation
   1.5. [x] Create '/frontend/src/components/common/Card.js'
   1.6. [x] Create '/frontend/src/components/common/Button.js'
   1.7. [x] Create '/frontend/src/components/common/Input.js'
   1.8. [x] Create '/frontend/src/components/common/Loader.js'
   1.9. [x] Create '/frontend/src/components/common/NotFound.js'
 2. [x] Create media components
   2.1. [x] Create '/frontend/src/components/media/MediaList.js'
      2.1.1. [x] Grid display of media items
      2.1.2. [x] Responsive design for mobile/desktop
   2.2. [x] Create '/frontend/src/components/media/MediaCard.js'
      2.2.1. [x] Thumbnail display
      2.2.2. [x] Title and brief description
      2.2.3. [x] Average rating display
      2.2.4. [x] Click handler for media selection
   2.3. [x] Create '/frontend/src/components/media/MediaPlayer.js'
      2.3.1. [x] Full-screen video/audio player
      2.3.2. [x] Custom controls overlay
      2.3.3. [x] Title and description overlay
      2.3.4. [x] Play/pause/progress tracking
      2.3.5. [x] Integration with analytics service
   2.4. [x] Create '/frontend/src/components/media/RatingStars.js'
      2.4.1. [x] 5-star rating display
      2.4.2. [x] Interactive star selection
      2.4.3. [x] Submit rating functionality
      2.4.4. [x] Current user rating display
 3. [x] Create auth components
   3.1. [x] Create '/frontend/src/components/auth/LoginForm.js'
      3.1.1. [x] Username/email input field
      3.1.2. [x] Password input field
      3.1.3. [x] Submit button
      3.1.4. [x] Form validation
      3.1.5. [x] Error handling and display
   3.2. [x] Create '/frontend/src/components/auth/RegisterForm.js'
      3.2.1. [x] Username/email input field
      3.2.2. [x] Password input field
      3.2.3. [x] Submit button
      3.2.4. [x] Form validation
      3.2.5. [x] Error handling and display
 4. [x] Create admin components
   4.1. [x] Create '/frontend/src/components/admin/MediaForm.js'
      4.1.1. [x] Media title input field
      4.1.2. [x] Media description textarea
      4.1.3. [x] Media type selection (video/audio)
      4.1.4. [x] File upload input
      4.1.5. [x] Tags input field
      4.1.6. [x] Submit button with loading state
   4.2. [x] Create '/frontend/src/components/admin/MediaTable.js'
      4.2.1. [x] Table of all media items
      4.2.2. [x] Edit and delete buttons
      4.2.3. [x] Sorting and filtering options
   4.3. [x] Create '/frontend/src/components/admin/UserTable.js'
      4.3.1. [x] Table of all users
      4.3.2. [x] Edit and delete buttons
   4.4. [x] Create '/frontend/src/components/admin/AnalyticsDisplay.js'
      4.4.1. [x] Basic analytics metrics display
      4.4.2. [x] Media play count statistics
      4.4.3. [x] Recent playback activity
   4.5. [x] Create '/frontend/src/components/admin/Dashboard.js'
      4.5.1. [x] Overview statistics
      4.5.2. [x] Recent activity
      4.1.2. [x] Interactive star selection
      4.1.3. [x] Submit rating functionality
      4.1.4. [x] Current user rating display

5. [x] Create admin components
   5.1. [x] Create '/frontend/src/components/admin/MediaForm.js'
      5.1.1. [x] Media title input field
      5.1.2. [x] Media description textarea
      5.1.3. [x] Media type selection (video/audio)
      5.1.4. [x] File upload input
      5.1.5. [x] Tags input field
      5.1.6. [x] Submit button with loading state
   5.2. [x] Create '/frontend/src/components/admin/MediaTable.js'
      5.2.1. [x] Table of all media items
      5.2.2. [x] Edit and delete buttons
      5.2.3. [x] Sorting and filtering options
   5.3. [x] Create '/frontend/src/components/admin/UserTable.js'
      5.3.1. [x] Table of all users
      5.3.2. [ ] Edit and delete buttons
   5.4. [ ] Create '/frontend/src/components/admin/AnalyticsDisplay.js'
      5.4.1. [ ] Basic analytics metrics display
      5.4.2. [ ] Media play count statistics
      5.4.3. [ ] Recent playback activity

## Section 11: Frontend Pages
1. [x] Create home page
   1.1. [x] Create '/frontend/src/pages/Home.js'
      1.1.1. [x] Display MediaList component
      1.1.2. [x] Mobile-optimized layout

2. [x] Create media player page
   2.1. [x] Create '/frontend/src/pages/MediaPlayer.js'
      2.1.1. [x] Full-screen MediaPlayer component
      2.1.2. [x] Rating component below player
      2.1.3. [x] Back button to return to media list
      2.1.4. [x] Media details display

3. [x] Create login page
   3.1. [x] Create '/frontend/src/pages/Login.js'
      3.1.1. [x] LoginForm component
      3.1.2. [x] Redirect to home after login

4. [x] Create admin pages
   4.1. [x] Create '/frontend/src/pages/admin/Dashboard.js'
      4.1.1. [x] Overview statistics
      4.1.2. [x] Recent activity
   4.2. [x] Create '/frontend/src/pages/admin/MediaManagement.js'
      4.2.1. [x] MediaTable component
      4.2.2. [x] Add new media button
      4.2.3. [x] MediaForm component for adding/editing
   4.3. [x] Create '/frontend/src/pages/admin/UserManagement.js'
      4.3.1. [x] UserTable component
      4.3.2. [x] User form for adding/editing users
   4.4. [x] Create '/frontend/src/pages/admin/Analytics.js'
      4.4.1. [x] AnalyticsDisplay component
      4.4.2. [x] Date range filters

## Section 12: Frontend Routing and Main App
 1. [x] Set up React Router
   1.1. [x] Create '/frontend/src/App.js' with routes:
      1.1.1. [x] Route for home page ('/')
      1.1.2. [x] Route for media player ('/media/:id')
      1.1.3. [x] Route for login page ('/login')
      1.1.4. [x] Routes for admin:
         - Dashboard ('/admin')
         - Media Management ('/admin/media')
         - User Management ('/admin/users')
         - Analytics ('/admin/analytics')
    1.2. [x] Implement protected routes for admin section
       1.2.1. [x] Create ProtectedRoute component
       1.2.2. [x] Verify admin role for admin routes

2. [x] Configure application entry point
   2.1. [x] Update '/frontend/src/index.js'
      2.1.1. [x] Wrap App with necessary contexts
      2.1.2. [x] Include Tailwind CSS
      2.1.3. [x] Register service worker for PWA

## Section 13: Deployment
1. [x] Prepare frontend for production
   1.1. [x] Build React application
      1.1.1. [x] Run production build
      1.1.2. [x] Verify build output
   
2. [x] Prepare backend for production
   2.1. [x] Create production configuration
      2.1.1. [x] Update database credentials for production
      2.1.2. [x] Configure appropriate file paths

3. [ ] Deploy to production server
   3.1. [ ] Upload frontend build files
   3.2. [ ] Upload backend PHP files
   3.3. [ ] Set appropriate file permissions
   3.4. [ ] Configure web server