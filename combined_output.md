# Combined Markdown Export

Generated: 2025-05-02T23:43:47.909852


## Index

- `.gitignore` — ~2 tokens
- `README.md` — ~505 tokens
- `backend\admin\includes\navbar.php` — ~66 tokens
- `backend\admin\includes\sidebar.php` — ~171 tokens
- `backend\admin\index.php` — ~1246 tokens
- `backend\admin\login.php` — ~426 tokens
- `backend\admin\logout.php` — ~59 tokens
- `backend\admin\upload.php` — ~1121 tokens
- `backend\api\media.php` — ~1239 tokens
- `backend\api\session.php` — ~307 tokens
- `backend\api\tags.php` — ~558 tokens
- `backend\api\track.php` — ~259 tokens
- `backend\config.php` — ~381 tokens
- `backend\config.template.php` — ~389 tokens
- `backend\edit\execute.php` — ~1191 tokens
- `backend\edit\file-actions.php` — ~1014 tokens
- `backend\edit\index.php` — ~3017 tokens
- `backend\edit\router.php` — ~1169 tokens
- `backend\init_db.php` — ~575 tokens
- `backend\schema.sql` — ~356 tokens
- `backend\setup.php` — ~1290 tokens
- `frontend\components.json` — ~50 tokens
- `frontend\index.html` — ~38 tokens
- `frontend\manifest.webmanifest` — ~51 tokens
- `frontend\package.json` — ~129 tokens
- `frontend\postcss.config.cjs` — ~14 tokens
- `frontend\postcss.config.js` — ~14 tokens
- `frontend\registerSW.js` — ~37 tokens
- `frontend\src\App.jsx` — ~453 tokens
- `frontend\src\components\Layout.jsx` — ~170 tokens
- `frontend\src\components\MediaCard.jsx` — ~332 tokens
- `frontend\src\components\mode-toggle.jsx` — ~127 tokens
- `frontend\src\components\theme-provider.jsx` — ~178 tokens
- `frontend\src\components\ui\badge.jsx` — ~118 tokens
- `frontend\src\components\ui\button.jsx` — ~196 tokens
- `frontend\src\components\ui\card.jsx` — ~206 tokens
- `frontend\src\components\ui\dropdown-menu.jsx` — ~45 tokens
- `frontend\src\components\ui\input.jsx` — ~74 tokens
- `frontend\src\components\ui\progress.jsx` — ~86 tokens
- `frontend\src\components\ui\tabs.jsx` — ~45 tokens
- `frontend\src\components\ui\toast.jsx` — ~389 tokens
- `frontend\src\components\ui\toaster.jsx` — ~91 tokens
- `frontend\src\components\ui\use-toast.js` — ~457 tokens
- `frontend\src\index.css` — ~363 tokens
- `frontend\src\lib\logger.js` — ~203 tokens
- `frontend\src\lib\utils.js` — ~25 tokens
- `frontend\src\main.jsx` — ~53 tokens
- `frontend\src\pages\Home.jsx` — ~711 tokens
- `frontend\src\pages\MediaDetails.jsx` — ~1262 tokens
- `frontend\src\stores\mediaStore.js` — ~296 tokens
- `frontend\src\stores\userStore.js` — ~59 tokens
- `frontend\tailwind.config.js` — ~222 tokens
- `frontend\vite.config.js` — ~151 tokens
- `index.php` — ~323 tokens

**Total tokens: ~22309**

---

### `.gitignore`

```
node_modules
.history
```

### `README.md`

```md
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

## License

MIT License

## Author

Created by Claude AI Assistant
```

### `backend\admin\includes\navbar.php`

```php
<!-- backend/admin/includes/navbar.php -->
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="index.php">Media Share Admin</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="w-100"></div>
    <div class="navbar-nav">
        <div class="nav-item text-nowrap">
            <a class="nav-link px-3" href="logout.php">Sign out</a>
        </div>
    </div>
</header>
```

### `backend\admin\includes\sidebar.php`

```php
<!-- backend/admin/includes/sidebar.php -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3 sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'media.php' ? 'active' : ''; ?>" href="media.php">
                    <i class="bi bi-file-earmark-play"></i>
                    Media Library
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'upload.php' ? 'active' : ''; ?>" href="upload.php">
                    <i class="bi bi-cloud-upload"></i>
                    Upload Media
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'tags.php' ? 'active' : ''; ?>" href="tags.php">
                    <i class="bi bi-tags"></i>
                    Manage Tags
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>" href="analytics.php">
                    <i class="bi bi-graph-up"></i>
                    Analytics
                </a>
            </li>
        </ul>
    </div>
</nav>
```

### `backend\admin\index.php`

```php
<?php
require_once('../config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Page title
$pageTitle = 'Admin Dashboard';

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    die('Database connection failed');
}

// Get media count by type
try {
    $stmt = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN type = 'video' THEN 1 ELSE 0 END) as videos,
            SUM(CASE WHEN type = 'audio' THEN 1 ELSE 0 END) as audio,
            SUM(CASE WHEN type = 'image' THEN 1 ELSE 0 END) as images
        FROM media
    ");
    $mediaCounts = $stmt->fetch();
} catch (PDOException $e) {
    logError('Error getting media counts: ' . $e->getMessage());
    $mediaCounts = [
        'total' => 0,
        'videos' => 0,
        'audio' => 0,
        'images' => 0
    ];
}

// Get recent events
try {
    $stmt = $conn->query("
        SELECT a.*, m.caption, m.type
        FROM analytics a
        JOIN media m ON a.media_id = m.id
        ORDER BY a.timestamp DESC
        LIMIT 10
    ");
    $recentEvents = $stmt->fetchAll();
} catch (PDOException $e) {
    logError('Error getting recent events: ' . $e->getMessage());
    $recentEvents = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .nav-link {
            font-weight: 500;
            color: #333;
        }
        .nav-link.active {
            color: #2470dc;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar">
                        <a href="#" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#testAppModal">
                            <i class="bi bi-bug"></i> Test All
                        </a>
                        <a href="upload.php" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Upload New Media
                        </a>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Media</h5>
                                <p class="card-text display-6"><?php echo $mediaCounts['total']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Videos</h5>
                                <p class="card-text display-6"><?php echo $mediaCounts['videos']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Audio</h5>
                                <p class="card-text display-6"><?php echo $mediaCounts['audio']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Images</h5>
                                <p class="card-text display-6"><?php echo $mediaCounts['images']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activities</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Media</th>
                                        <th>Event</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentEvents)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No recent activities</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentEvents as $event): ?>
                                        <tr>
                                            <td><?php echo date('M d, H:i', strtotime($event['timestamp'])); ?></td>
                                            <td><?php echo htmlspecialchars($event['user_name']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($event['caption']); ?>
                                                <span class="badge bg-secondary"><?php echo $event['type']; ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                    $eventClass = '';
                                                    switch ($event['event_type']) {
                                                        case 'view': $eventClass = 'bg-primary'; break;
                                                        case 'play': $eventClass = 'bg-success'; break;
                                                        case 'pause': $eventClass = 'bg-warning'; break;
                                                        case 'seek': $eventClass = 'bg-info'; break;
                                                        case 'ended': $eventClass = 'bg-danger'; break;
                                                        case 'download': $eventClass = 'bg-secondary'; break;
                                                        default: $eventClass = 'bg-light text-dark';
                                                    }
                                                ?>
                                                <span class="badge <?php echo $eventClass; ?>">
                                                    <?php echo ucfirst($event['event_type']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($event['percentage'] !== null): ?>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                        style="width: <?php echo $event['percentage']; ?>%"
                                                        aria-valuenow="<?php echo $event['percentage']; ?>" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo round($event['percentage'], 1); ?>%
                                                </small>
                                                <?php else: ?>
                                                <small class="text-muted">N/A</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="analytics.php" class="btn btn-sm btn-outline-secondary">View All Activities</a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Manage Media</h5>
                                <p class="card-text">View, edit, or delete media files.</p>
                                <a href="media.php" class="btn btn-primary">Go to Media</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Manage Tags</h5>
                                <p class="card-text">Create or delete tags for media organization.</p>
                                <a href="tags.php" class="btn btn-primary">Go to Tags</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">View Analytics</h5>
                                <p class="card-text">See detailed user interaction statistics.</p>
                                <a href="analytics.php" class="btn btn-primary">Go to Analytics</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Test App Modal -->
    <div class="modal fade" id="testAppModal" tabindex="-1" aria-labelledby="testAppModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testAppModalLabel">Test Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="d-flex bg-light border-bottom p-2">
                        <div class="btn-group me-3">
                            <button class="btn btn-sm btn-outline-secondary" id="deviceDesktop">
                                <i class="bi bi-laptop"></i> Desktop
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="deviceTablet">
                                <i class="bi bi-tablet"></i> Tablet
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="deviceMobile">
                                <i class="bi bi-phone"></i> Mobile
                            </button>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" id="reloadIframe">
                            <i class="bi bi-arrow-clockwise"></i> Reload
                        </button>
                    </div>
                    <div class="iframe-container d-flex justify-content-center align-items-center bg-light p-3">
                        <iframe id="previewFrame" src="../../frontend/dist/index.html" style="width: 100%; height: 600px; border: 1px solid #dee2e6; transition: all 0.3s ease;"></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="../edit/index.php" class="btn btn-primary">
                        <i class="bi bi-code-slash"></i> Open Editor
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle responsive testing buttons
            const iframe = document.getElementById('previewFrame');
            
            // Device sizes
            const devices = {
                desktop: { width: '100%', height: '600px' },
                tablet: { width: '768px', height: '1024px' },
                mobile: { width: '375px', height: '812px' }
            };
            
            // Set desktop active by default
            document.getElementById('deviceDesktop').classList.add('active');
            
            // Desktop button
            document.getElementById('deviceDesktop').addEventListener('click', function() {
                resetButtons();
                this.classList.add('active');
                iframe.style.width = devices.desktop.width;
                iframe.style.height = devices.desktop.height;
            });
            
            // Tablet button
            document.getElementById('deviceTablet').addEventListener('click', function() {
                resetButtons();
                this.classList.add('active');
                iframe.style.width = devices.tablet.width;
                iframe.style.height = devices.tablet.height;
            });
            
            // Mobile button
            document.getElementById('deviceMobile').addEventListener('click', function() {
                resetButtons();
                this.classList.add('active');
                iframe.style.width = devices.mobile.width;
                iframe.style.height = devices.mobile.height;
            });
            
            // Reload button
            document.getElementById('reloadIframe').addEventListener('click', function() {
                iframe.src = iframe.src;
            });
            
            // Helper function to reset button states
            function resetButtons() {
                document.getElementById('deviceDesktop').classList.remove('active');
                document.getElementById('deviceTablet').classList.remove('active');
                document.getElementById('deviceMobile').classList.remove('active');
            }
        });
    </script>
</body>
</html>
```

### `backend\admin\login.php`

```php
<?php
// backend/admin/login.php
require_once('../config.php');
session_start();

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// Initialize variables
$error = '';
$username = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get username and password
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Get database connection
        $conn = getDbConnection();
        if (!$conn) {
            $error = 'Database connection failed';
        } else {
            try {
                // Get user from database
                $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    // Login successful
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    
                    // Redirect to dashboard
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid username or password';
                }
            } catch (PDOException $e) {
                logError('Login error: ' . $e->getMessage());
                $error = 'An error occurred during login';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
            height: 100vh;
        }
        
        .form-login {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }
        
        .form-login .form-floating:focus-within {
            z-index: 2;
        }
        
        .form-login input[type="text"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }
        
        .form-login input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
</head>
<body class="text-center">
    <main class="form-login">
        <form method="POST" action="login.php">
            <h1 class="h3 mb-3 fw-normal">Media Share Admin</h1>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <div class="form-floating">
                <input type="text" class="form-control" id="username" name="username" 
                       placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
                <label for="username">Username</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Password" required>
                <label for="password">Password</label>
            </div>
            
            <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
            <p class="mt-5 mb-3 text-muted">&copy; <?php echo date('Y'); ?> Media Share</p>
        </form>
    </main>
</body>
</html>
```

### `backend\admin\logout.php`

```php
<?php
// backend/admin/logout.php
session_start();

// Clear session data
$_SESSION = array();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
```

### `backend\admin\upload.php`

```php
<?php
// backend/admin/upload.php
require_once('../config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Page title
$pageTitle = 'Upload Media';

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    die('Database connection failed');
}

// Initialize variables
$success = false;
$error = '';
$message = '';

// Get all tags for dropdown
try {
    $stmt = $conn->query("SELECT name FROM tags ORDER BY name ASC");
    $allTags = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    logError('Error getting tags: ' . $e->getMessage());
    $allTags = [];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if file was uploaded
        if (!isset($_FILES['media_file']) || $_FILES['media_file']['error'] === UPLOAD_ERR_NO_FILE) {
            $error = 'Please select a file to upload';
        } else {
            $mediaFile = $_FILES['media_file'];
            $caption = trim($_POST['caption'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            // Validate caption
            if (empty($caption)) {
                $error = 'Please enter a caption for the media';
            } 
            // Validate file upload
            else if ($mediaFile['error'] !== UPLOAD_ERR_OK) {
                $error = 'File upload error: ' . $mediaFile['error'];
            }
            // Validate file size
            else if ($mediaFile['size'] > MAX_FILE_SIZE) {
                $error = 'File is too large (max ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB)';
            } else {
                // Get file extension and validate media type
                $fileExt = strtolower(pathinfo($mediaFile['name'], PATHINFO_EXTENSION));
                
                if (in_array($fileExt, ALLOWED_VIDEO_TYPES)) {
                    $mediaType = 'video';
                } else if (in_array($fileExt, ALLOWED_AUDIO_TYPES)) {
                    $mediaType = 'audio';
                } else if (in_array($fileExt, ALLOWED_IMAGE_TYPES)) {
                    $mediaType = 'image';
                } else {
                    $error = 'Invalid file type. Allowed types: ' . 
                             implode(', ', array_merge(ALLOWED_VIDEO_TYPES, ALLOWED_AUDIO_TYPES, ALLOWED_IMAGE_TYPES));
                }
                
                if (empty($error)) {
                    // Generate unique filename
                    $filename = uniqid() . '.' . $fileExt;
                    $uploadPath = UPLOAD_PATH . '/' . $filename;
                    
                    // Move uploaded file
                    if (move_uploaded_file($mediaFile['tmp_name'], $uploadPath)) {
                        // Process thumbnail if provided
                        $thumbnail = null;
                        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                            $thumbnailFile = $_FILES['thumbnail'];
                            $thumbExt = strtolower(pathinfo($thumbnailFile['name'], PATHINFO_EXTENSION));
                            
                            if (!in_array($thumbExt, ALLOWED_IMAGE_TYPES)) {
                                throw new Exception('Invalid thumbnail file type');
                            }
                            
                            $thumbnailFilename = uniqid() . '_thumb.' . $thumbExt;
                            $thumbnailPath = THUMBNAIL_PATH . '/' . $thumbnailFilename;
                            
                            if (move_uploaded_file($thumbnailFile['tmp_name'], $thumbnailPath)) {
                                $thumbnail = 'thumbnails/' . $thumbnailFilename;
                            }
                        }
                        
                        // Start transaction
                        $conn->beginTransaction();
                        
                        // Insert media record
                        $sql = "INSERT INTO media (filename, thumbnail, type, caption, description) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$filename, $thumbnail, $mediaType, $caption, $description]);
                        
                        $mediaId = $conn->lastInsertId();
                        
                        // Process tags
                        $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
                        if (!empty($tags)) {
                            foreach ($tags as $tagName) {
                                // Get or create tag
                                $sql = "INSERT IGNORE INTO tags (name) VALUES (?)";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$tagName]);
                                
                                // Get tag ID
                                $sql = "SELECT id FROM tags WHERE name = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$tagName]);
                                $tagId = $stmt->fetch()['id'];
                                
                                // Link tag to media
                                $sql = "INSERT INTO media_tags (media_id, tag_id) VALUES (?, ?)";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$mediaId, $tagId]);
                            }
                        }
                        
                        // Commit transaction
                        $conn->commit();
                        
                        $success = true;
                        $message = 'Media uploaded successfully!';
                    } else {
                        $error = 'Failed to save uploaded file';
                    }
                }
            }
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Upload error: ' . $e->getMessage());
        $error = 'An error occurred during upload: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Media Share Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .nav-link {
            font-weight: 500;
            color: #333;
        }
        .nav-link.active {
            color: #2470dc;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $pageTitle; ?></h1>
                </div>
                
                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="media_file" class="form-label">Media File</label>
                                <input type="file" class="form-control" id="media_file" name="media_file" required>
                                <div class="form-text">
                                    Allowed file types: 
                                    <?php echo implode(', ', array_merge(ALLOWED_VIDEO_TYPES, ALLOWED_AUDIO_TYPES, ALLOWED_IMAGE_TYPES)); ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail (Optional)</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                                <div class="form-text">
                                    For videos and audio files, you can upload a custom thumbnail image.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="caption" class="form-label">Caption</label>
                                <input type="text" class="form-control" id="caption" name="caption" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <select id="tags" name="tags[]" multiple class="form-select" placeholder="Select or create tags...">
                                    <?php foreach ($allTags as $tag): ?>
                                    <option value="<?php echo htmlspecialchars($tag); ?>"><?php echo htmlspecialchars($tag); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Upload Media</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tag selector with ability to create new tags
            new TomSelect('#tags', {
                plugins: ['remove_button'],
                create: true,
                createOnBlur: true
            });
        });
    </script>
</body>
</html>
```

### `backend\api\media.php`

```php
<?php
// backend/api/media.php
require_once('../config.php');
enableCors();

$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getMedia($conn);
        break;
    case 'POST':
        createMedia($conn);
        break;
    case 'DELETE':
        deleteMedia($conn);
        break;
    default:
        sendJsonResponse(['error' => 'Method not allowed'], 405);
}

/**
 * Get media items with optional filtering
 */
function getMedia($conn) {
    try {
        $params = [];
        $where = [];
        
        // Build query based on parameters
        $query = "
            SELECT m.*, GROUP_CONCAT(t.name) as tags
            FROM media m
            LEFT JOIN media_tags mt ON m.id = mt.media_id
            LEFT JOIN tags t ON mt.tag_id = t.id
        ";
        
        // Filter by ID
        if (isset($_GET['id'])) {
            $where[] = "m.id = ?";
            $params[] = $_GET['id'];
        }
        
        // Filter by type
        if (isset($_GET['type']) && in_array($_GET['type'], ['video', 'audio', 'image'])) {
            $where[] = "m.type = ?";
            $params[] = $_GET['type'];
        }
        
        // Filter by tag
        if (isset($_GET['tag'])) {
            $where[] = "t.name = ?";
            $params[] = $_GET['tag'];
        }
        
        // Add WHERE clause if needed
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }
        
        // Group by media id to handle tags properly
        $query .= " GROUP BY m.id";
        
        // Order by creation date (newest first)
        $query .= " ORDER BY m.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        $media = $stmt->fetchAll();
        
        // Process the results
        $result = [];
        foreach ($media as $item) {
            // Convert tags from string to array
            $item['tags'] = $item['tags'] ? explode(',', $item['tags']) : [];
            $result[] = $item;
        }
        
        sendJsonResponse($result);
    } catch (PDOException $e) {
        logError('Error getting media: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to fetch media'], 500);
    }
}

/**
 * Create new media item with upload handling
 */
function createMedia($conn) {
    try {
        // Verify that we have file uploads and required fields
        if (!isset($_FILES['file']) || !isset($_POST['caption']) || !isset($_POST['type'])) {
            sendJsonResponse(['error' => 'Missing required fields'], 400);
        }
        
        $file = $_FILES['file'];
        $caption = $_POST['caption'];
        $type = $_POST['type'];
        $description = $_POST['description'] ?? '';
        $tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];
        
        // Validate file
        if ($file['error'] !== UPLOAD_NO_ERROR) {
            sendJsonResponse(['error' => 'File upload error: ' . $file['error']], 400);
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            sendJsonResponse(['error' => 'File exceeds maximum size'], 400);
        }
        
        // Get file extension and validate by type
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        switch ($type) {
            case 'image':
                if (!in_array($fileExt, ALLOWED_IMAGE_TYPES)) {
                    sendJsonResponse(['error' => 'Invalid image file type'], 400);
                }
                break;
            case 'video':
                if (!in_array($fileExt, ALLOWED_VIDEO_TYPES)) {
                    sendJsonResponse(['error' => 'Invalid video file type'], 400);
                }
                break;
            case 'audio':
                if (!in_array($fileExt, ALLOWED_AUDIO_TYPES)) {
                    sendJsonResponse(['error' => 'Invalid audio file type'], 400);
                }
                break;
            default:
                sendJsonResponse(['error' => 'Invalid media type'], 400);
        }
        
        // Generate unique filename
        $filename = uniqid() . '.' . $fileExt;
        $filepath = UPLOAD_PATH . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            logError('Failed to move uploaded file: ' . $file['name']);
            sendJsonResponse(['error' => 'Failed to save file'], 500);
        }
        
        // Process thumbnail if provided
        $thumbnail = null;
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_NO_ERROR) {
            $thumbFile = $_FILES['thumbnail'];
            $thumbExt = strtolower(pathinfo($thumbFile['name'], PATHINFO_EXTENSION));
            
            if (!in_array($thumbExt, ALLOWED_IMAGE_TYPES)) {
                sendJsonResponse(['error' => 'Invalid thumbnail file type'], 400);
            }
            
            $thumbnailFilename = uniqid() . '_thumb.' . $thumbExt;
            $thumbnailPath = THUMBNAIL_PATH . '/' . $thumbnailFilename;
            
            if (!move_uploaded_file($thumbFile['tmp_name'], $thumbnailPath)) {
                logError('Failed to move thumbnail file: ' . $thumbFile['name']);
                sendJsonResponse(['error' => 'Failed to save thumbnail'], 500);
            }
            
            $thumbnail = 'thumbnails/' . $thumbnailFilename;
        }
        
        // Start transaction to ensure data consistency
        $conn->beginTransaction();
        
        // Insert media record
        $sql = "INSERT INTO media (filename, thumbnail, type, caption, description) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$filename, $thumbnail, $type, $caption, $description]);
        
        $mediaId = $conn->lastInsertId();
        
        // Process tags
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (empty($tag)) continue;
                
                // Insert tag if it doesn't exist
                $sql = "INSERT IGNORE INTO tags (name) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$tag]);
                
                // Get tag ID
                $sql = "SELECT id FROM tags WHERE name = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$tag]);
                $tagId = $stmt->fetch()['id'];
                
                // Link tag to media
                $sql = "INSERT IGNORE INTO media_tags (media_id, tag_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$mediaId, $tagId]);
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        sendJsonResponse([
            'success' => true,
            'id' => $mediaId,
            'message' => 'Media uploaded successfully'
        ]);
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Error creating media: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to save media data'], 500);
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Error: ' . $e->getMessage());
        sendJsonResponse(['error' => 'An error occurred'], 500);
    }
}

/**
 * Delete media item
 */
function deleteMedia($conn) {
    try {
        // Get ID from query parameter
        if (!isset($_GET['id'])) {
            sendJsonResponse(['error' => 'Missing media ID'], 400);
        }
        
        $mediaId = $_GET['id'];
        
        // Get media info before deletion
        $sql = "SELECT filename, thumbnail FROM media WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$mediaId]);
        $media = $stmt->fetch();
        
        if (!$media) {
            sendJsonResponse(['error' => 'Media not found'], 404);
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        // Delete from media_tags
        $sql = "DELETE FROM media_tags WHERE media_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$mediaId]);
        
        // Delete from analytics
        $sql = "DELETE FROM analytics WHERE media_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$mediaId]);
        
        // Delete from media
        $sql = "DELETE FROM media WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$mediaId]);
        
        // Commit transaction
        $conn->commit();
        
        // Delete files
        if (file_exists(UPLOAD_PATH . '/' . $media['filename'])) {
            unlink(UPLOAD_PATH . '/' . $media['filename']);
        }
        
        if ($media['thumbnail'] && file_exists(UPLOAD_PATH . '/' . $media['thumbnail'])) {
            unlink(UPLOAD_PATH . '/' . $media['thumbnail']);
        }
        
        sendJsonResponse([
            'success' => true,
            'message' => 'Media deleted successfully'
        ]);
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Error deleting media: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to delete media'], 500);
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Error: ' . $e->getMessage());
        sendJsonResponse(['error' => 'An error occurred'], 500);
    }
}
```

### `backend\api\session.php`

```php
<?php
// backend/api/session.php
require_once('../config.php');
enableCors();

// Start or resume session
session_start();

// Set session lifetime
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);

// Handle GET request to set user in session
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['name'])) {
    $userName = strtolower(trim($_GET['name']));
    
    // Validate user name (only 'charl' or 'nade' are valid)
    if ($userName === 'charl' || $userName === 'nade') {
        $_SESSION['user_name'] = $userName;
        $_SESSION['session_start_time'] = time();
        
        sendJsonResponse([
            'success' => true,
            'user' => $userName,
            'message' => 'Session started successfully'
        ]);
    } else {
        http_response_code(400);
        sendJsonResponse([
            'error' => 'Invalid user name',
            'message' => 'User name must be either "charl" or "nade"'
        ]);
    }
}
// Handle GET request to get current session info
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['user_name'])) {
        sendJsonResponse([
            'success' => true,
            'user' => $_SESSION['user_name'],
            'session_duration' => time() - ($_SESSION['session_start_time'] ?? time())
        ]);
    } else {
        sendJsonResponse([
            'success' => false,
            'message' => 'No active session'
        ]);
    }
}
// Handle POST request to end session
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['end'])) {
    // Clear session data
    $_SESSION = [];
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
    
    sendJsonResponse([
        'success' => true,
        'message' => 'Session ended successfully'
    ]);
} else {
    http_response_code(405);
    sendJsonResponse([
        'error' => 'Method not allowed',
        'message' => 'Use GET to start or check session, POST with ?end to end session'
    ]);
}
```

### `backend\api\tags.php`

```php
<?php
// backend/api/tags.php
require_once('../config.php');
enableCors();

$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getTags($conn);
        break;
    case 'POST':
        createTag($conn);
        break;
    case 'DELETE':
        deleteTag($conn);
        break;
    default:
        sendJsonResponse(['error' => 'Method not allowed'], 405);
}

/**
 * Get all tags or filtered tags
 */
function getTags($conn) {
    try {
        $params = [];
        $where = [];
        $query = "SELECT name FROM tags";
        
        // Filter by name
        if (isset($_GET['name'])) {
            $where[] = "name LIKE ?";
            $params[] = '%' . $_GET['name'] . '%';
        }
        
        // Add WHERE clause if needed
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }
        
        // Order by name
        $query .= " ORDER BY name ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        sendJsonResponse($tags);
    } catch (PDOException $e) {
        logError('Error getting tags: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to fetch tags'], 500);
    }
}

/**
 * Create a new tag
 */
function createTag($conn) {
    try {
        // Get request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || empty(trim($data['name']))) {
            sendJsonResponse(['error' => 'Tag name is required'], 400);
        }
        
        $tagName = trim($data['name']);
        
        // Check if tag already exists
        $sql = "SELECT id FROM tags WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tagName]);
        
        if ($stmt->rowCount() > 0) {
            sendJsonResponse(['error' => 'Tag already exists'], 409);
        }
        
        // Insert new tag
        $sql = "INSERT INTO tags (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tagName]);
        
        $tagId = $conn->lastInsertId();
        
        sendJsonResponse([
            'success' => true,
            'id' => $tagId,
            'name' => $tagName,
            'message' => 'Tag created successfully'
        ]);
    } catch (PDOException $e) {
        logError('Error creating tag: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to create tag'], 500);
    }
}

/**
 * Delete a tag
 */
function deleteTag($conn) {
    try {
        // Get tag name from query parameter
        if (!isset($_GET['name'])) {
            sendJsonResponse(['error' => 'Tag name is required'], 400);
        }
        
        $tagName = $_GET['name'];
        
        // Get tag ID
        $sql = "SELECT id FROM tags WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tagName]);
        $tag = $stmt->fetch();
        
        if (!$tag) {
            sendJsonResponse(['error' => 'Tag not found'], 404);
        }
        
        $tagId = $tag['id'];
        
        // Start transaction
        $conn->beginTransaction();
        
        // Delete from media_tags
        $sql = "DELETE FROM media_tags WHERE tag_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tagId]);
        
        // Delete from tags
        $sql = "DELETE FROM tags WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tagId]);
        
        // Commit transaction
        $conn->commit();
        
        sendJsonResponse([
            'success' => true,
            'message' => 'Tag deleted successfully'
        ]);
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Error deleting tag: ' . $e->getMessage());
        sendJsonResponse(['error' => 'Failed to delete tag'], 500);
    }
}
```

### `backend\api\track.php`

```php
<?php
// backend/api/track.php
require_once('../config.php');
enableCors();

$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse(['error' => 'Database connection failed'], 500);
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['error' => 'Method not allowed'], 405);
}

// Get request body
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['media_id']) || !isset($data['event_type']) || !isset($data['user_name'])) {
    sendJsonResponse(['error' => 'Missing required fields'], 400);
}

$mediaId = $data['media_id'];
$eventType = $data['event_type'];
$userName = $data['user_name'];
$position = $data['position'] ?? null;
$percentage = $data['percentage'] ?? null;

// Validate event type
$validEventTypes = ['view', 'play', 'pause', 'seek', 'progress', 'ended', 'download'];
if (!in_array($eventType, $validEventTypes)) {
    sendJsonResponse(['error' => 'Invalid event type'], 400);
}

try {
    // Check if media exists
    $sql = "SELECT id FROM media WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$mediaId]);
    
    if ($stmt->rowCount() === 0) {
        sendJsonResponse(['error' => 'Media not found'], 404);
    }
    
    // Insert analytics record
    $sql = "INSERT INTO analytics (media_id, user_name, event_type, position, percentage) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$mediaId, $userName, $eventType, $position, $percentage]);
    
    sendJsonResponse([
        'success' => true,
        'message' => 'Event tracked successfully'
    ]);
} catch (PDOException $e) {
    logError('Error tracking media event: ' . $e->getMessage());
    sendJsonResponse(['error' => 'Failed to track event'], 500);
}
```

### `backend\config.php`

```php
<?php
// backend/config.php
// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_NAME', 'my_media');
define('DB_USER', 'cp');
define('DB_PASS', '4334.4334');

// Path configurations
define('BASE_PATH', dirname(__FILE__));
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('THUMBNAIL_PATH', UPLOAD_PATH . '/thumbnails');
define('TEMP_PATH', UPLOAD_PATH . '/temp');

// File upload configurations
define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'webm', 'mov']);
define('ALLOWED_AUDIO_TYPES', ['mp3', 'wav', 'ogg']);

// Session configurations
define('SESSION_LIFETIME', 60 * 60 * 24 * 30); // 30 days

// Initialize database connection
function getDbConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch (PDOException $e) {
        logError('Database connection error: ' . $e->getMessage());
        return null;
    }
}

// Error logging function
function logError($message) {
    $logFile = BASE_PATH . '/logs/error.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    
    error_log($logMessage, 3, $logFile);
}

// Response helper function
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Check if the uploads directory exists, if not create it
function ensureUploadDirectories() {
    $directories = [UPLOAD_PATH, THUMBNAIL_PATH, TEMP_PATH];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, true)) {
                logError("Failed to create directory: $dir");
                return false;
            }
        }
    }
    
    return true;
}

// Enable CORS for API endpoints
function enableCors() {
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        }
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
        
        exit(0);
    }
}

// Initialize error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/php_errors.log');

// Ensure upload directories exist
ensureUploadDirectories();
```

### `backend\config.template.php`

```php
// backend/config.template.php
<?php
// Database connection parameters
define('DB_HOST', '{{DB_HOST}}');
define('DB_PORT', '{{DB_PORT}}');
define('DB_NAME', '{{DB_NAME}}');
define('DB_USER', '{{DB_USER}}');
define('DB_PASS', '{{DB_PASS}}');

// Path configurations
define('BASE_PATH', dirname(__FILE__));
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('THUMBNAIL_PATH', UPLOAD_PATH . '/thumbnails');
define('TEMP_PATH', UPLOAD_PATH . '/temp');

// File upload configurations
define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'webm', 'mov']);
define('ALLOWED_AUDIO_TYPES', ['mp3', 'wav', 'ogg']);

// Session configurations
define('SESSION_LIFETIME', 60 * 60 * 24 * 30); // 30 days

// Initialize database connection
function getDbConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch (PDOException $e) {
        logError('Database connection error: ' . $e->getMessage());
        return null;
    }
}

// Error logging function
function logError($message) {
    $logFile = BASE_PATH . '/logs/error.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    
    error_log($logMessage, 3, $logFile);
}

// Response helper function
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Check if the uploads directory exists, if not create it
function ensureUploadDirectories() {
    $directories = [UPLOAD_PATH, THUMBNAIL_PATH, TEMP_PATH];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, true)) {
                logError("Failed to create directory: $dir");
                return false;
            }
        }
    }
    
    return true;
}

// Enable CORS for API endpoints
function enableCors() {
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
    }
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        }
        
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
        
        exit(0);
    }
}

// Initialize error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/php_errors.log');

// Ensure upload directories exist
ensureUploadDirectories();
```

### `backend\edit\execute.php`

```php
<?php
// backend/edit/execute.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/login.php');
    exit;
}

// Page title
$pageTitle = 'Execute Commands';

// Initialize variables
$command = '';
$output = '';
$error = '';
$success = false;

// Define allowed commands for security
$allowed_commands = [
    // PHP commands
    'php -v' => 'Get PHP version',
    'php -m' => 'List loaded PHP modules',
    'php -i' => 'PHP information',
    
    // Node.js commands
    'node -v' => 'Get Node.js version',
    'npm -v' => 'Get npm version',
    
    // Frontend commands
    'npm run dev' => 'Start frontend development server',
    'npm run build' => 'Build frontend for production',
    'npm install' => 'Install frontend dependencies',
    
    // Backend commands
    'composer install' => 'Install backend dependencies',
    'composer update' => 'Update backend dependencies',
    
    // System commands
    'ls -la' => 'List files with details',
    'df -h' => 'Show disk usage',
    'free -m' => 'Show memory usage',
    'uname -a' => 'System information'
];

// Custom commands with directory context
$custom_commands = [
    'frontend:dev' => [
        'name' => 'Start Frontend Dev Server',
        'cmd' => 'cd ../../frontend && npm run dev',
        'description' => 'Start the Vite development server for the frontend'
    ],
    'frontend:build' => [
        'name' => 'Build Frontend',
        'cmd' => 'cd ../../frontend && npm run build',
        'description' => 'Build the frontend for production'
    ],
    'frontend:install' => [
        'name' => 'Install Frontend Dependencies',
        'cmd' => 'cd ../../frontend && npm install',
        'description' => 'Install all frontend dependencies'
    ],
    'backend:test' => [
        'name' => 'Test API Endpoints',
        'cmd' => 'curl -s http://localhost/media-share/backend/api/media.php',
        'description' => 'Test the media API endpoint'
    ]
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['command'])) {
        $command = trim($_POST['command']);
        
        // Check if it's a custom command
        if (isset($_POST['custom_command']) && isset($custom_commands[$_POST['custom_command']])) {
            $customCmd = $custom_commands[$_POST['custom_command']];
            $command = $customCmd['cmd'];
            $isCustom = true;
        } else {
            $isCustom = false;
        }
        
        // Validate command (for non-custom commands)
        $command_allowed = $isCustom;
        if (!$isCustom) {
            foreach ($allowed_commands as $allowed => $description) {
                if (strpos($command, $allowed) === 0) {
                    $command_allowed = true;
                    break;
                }
            }
        }
        
        if (!$command_allowed) {
            $error = 'This command is not allowed for security reasons.';
        } else {
            // Execute command
            $output_lines = [];
            $return_val = 0;
            
            // Execute with output capturing
            exec($command . ' 2>&1', $output_lines, $return_val);
            
            if ($return_val !== 0) {
                $error = "Command execution failed with code $return_val";
                $output = implode(PHP_EOL, $output_lines);
            } else {
                $success = true;
                $output = implode(PHP_EOL, $output_lines);
            }
        }
    } else {
        $error = 'No command specified';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Media Share Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            padding: 20px;
        }
        .terminal {
            background-color: #1e1e1e;
            color: #f8f8f8;
            padding: 1rem;
            font-family: monospace;
            border-radius: 5px;
            overflow-x: auto;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
        .cmd-success {
            color: #4caf50;
            font-weight: bold;
        }
        .cmd-error {
            color: #f44336;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><?php echo $pageTitle; ?></h1>
            <div>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Editor
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Execute Command</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="command" class="form-label">Command</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="command" name="command" 
                                        value="<?php echo htmlspecialchars($command); ?>" required>
                                    <button type="submit" class="btn btn-primary">Execute</button>
                                </div>
                                <div class="form-text">
                                    Only allowed commands can be executed for security reasons.
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Commands</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" id="quickCommandForm">
                            <input type="hidden" name="command" id="quickCommandInput" value="">
                            <input type="hidden" name="custom_command" id="customCommandInput" value="">
                            
                            <div class="row g-3">
                                <?php foreach ($allowed_commands as $cmd => $desc): ?>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center justify-content-between w-100 cmd-btn" data-command="<?php echo htmlspecialchars($cmd); ?>">
                                        <span class="text-start"><?php echo htmlspecialchars($cmd); ?></span>
                                        <i class="bi bi-arrow-right-circle ms-2"></i>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Custom Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php foreach ($custom_commands as $key => $cmd): ?>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($cmd['name']); ?></h6>
                                        <p class="card-text small text-muted"><?php echo htmlspecialchars($cmd['description']); ?></p>
                                        <button type="button" class="btn btn-primary btn-sm w-100 custom-cmd-btn" data-key="<?php echo htmlspecialchars($key); ?>">
                                            Execute
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Output</h5>
                        <?php if (!empty($output)): ?>
                        <button id="copyOutputBtn" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-0">
                        <div class="terminal">
                            <?php if (!empty($command)): ?>
                            $ <span class="text-info"><?php echo htmlspecialchars($command); ?></span>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                            <div class="cmd-success">Command executed successfully</div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                            <div class="cmd-error"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <?php if (!empty($output)): ?>
                            <pre><?php echo htmlspecialchars($output); ?></pre>
                            <?php elseif (empty($command)): ?>
                            <div class="text-muted">Select or enter a command to execute</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Quick command buttons
            document.querySelectorAll('.cmd-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const command = this.getAttribute('data-command');
                    document.getElementById('quickCommandInput').value = command;
                    document.getElementById('quickCommandForm').submit();
                });
            });
            
            // Custom command buttons
            document.querySelectorAll('.custom-cmd-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const key = this.getAttribute('data-key');
                    document.getElementById('customCommandInput').value = key;
                    document.getElementById('quickCommandForm').submit();
                });
            });
            
            // Copy output button
            const copyBtn = document.getElementById('copyOutputBtn');
            if (copyBtn) {
                copyBtn.addEventListener('click', function() {
                    const output = <?php echo json_encode($output); ?>;
                    navigator.clipboard.writeText(output).then(() => {
                        // Change button text temporarily
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-check"></i> Copied!';
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 2000);
                    });
                });
            }
        });
    </script>
</body>
</html>
```

### `backend\edit\file-actions.php`

```php
<?php
// backend/edit/file-actions.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/login.php');
    exit;
}

// Base paths
$basePath = dirname(dirname(__FILE__));
$relativeBasePath = '..';

// Page title
$pageTitle = 'File Operations';

// Initialize variables
$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create-file':
            if (empty($_POST['file_path'])) {
                $error_message = 'File path is required';
            } else {
                $filePath = $_POST['file_path'];
                $content = $_POST['file_content'] ?? '';
                
                // Validate path is within our project
                $fullPath = $basePath . '/' . $filePath;
                if (strpos(realpath(dirname($fullPath)), realpath($basePath)) !== 0) {
                    $error_message = 'Invalid file path. Cannot create files outside project directory.';
                } else {
                    // Create parent directories if they don't exist
                    $dir = dirname($fullPath);
                    if (!file_exists($dir)) {
                        if (!mkdir($dir, 0755, true)) {
                            $error_message = 'Failed to create directory structure';
                            break;
                        }
                    }
                    
                    // Create file
                    if (file_put_contents($fullPath, $content) === false) {
                        $error_message = 'Failed to create file. Check file permissions.';
                    } else {
                        $success_message = 'File created successfully';
                    }
                }
            }
            break;
            
        case 'create-directory':
            if (empty($_POST['directory_path'])) {
                $error_message = 'Directory path is required';
            } else {
                $dirPath = $_POST['directory_path'];
                
                // Validate path is within our project
                $fullPath = $basePath . '/' . $dirPath;
                if (strpos(realpath(dirname($fullPath)), realpath($basePath)) !== 0) {
                    $error_message = 'Invalid directory path. Cannot create directories outside project.';
                } else {
                    // Create directory
                    if (!mkdir($fullPath, 0755, true)) {
                        $error_message = 'Failed to create directory. Check permissions.';
                    } else {
                        $success_message = 'Directory created successfully';
                    }
                }
            }
            break;
            
        case 'upload-file':
            if (!isset($_FILES['upload_file']) || $_FILES['upload_file']['error'] !== UPLOAD_ERR_OK) {
                $error_message = 'Please select a file to upload';
            } else if (empty($_POST['upload_path'])) {
                $error_message = 'Upload path is required';
            } else {
                $uploadPath = $_POST['upload_path'];
                $file = $_FILES['upload_file'];
                
                // Validate path is within our project
                $fullPath = $basePath . '/' . $uploadPath;
                if (strpos(realpath(dirname($fullPath)), realpath($basePath)) !== 0) {
                    $error_message = 'Invalid upload path. Cannot upload files outside project directory.';
                } else {
                    // Create parent directories if they don't exist
                    $dir = dirname($fullPath);
                    if (!file_exists($dir)) {
                        if (!mkdir($dir, 0755, true)) {
                            $error_message = 'Failed to create directory structure';
                            break;
                        }
                    }
                    
                    // Move uploaded file
                    if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
                        $error_message = 'Failed to upload file. Check file permissions.';
                    } else {
                        $success_message = 'File uploaded successfully';
                    }
                }
            }
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Media Share Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><?php echo $pageTitle; ?></h1>
            <div>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Editor
                </a>
            </div>
        </div>
        
        <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Create File</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="action" value="create-file">
                            
                            <div class="mb-3">
                                <label for="file_path" class="form-label">File Path</label>
                                <input type="text" class="form-control" id="file_path" name="file_path" 
                                    placeholder="e.g., path/to/file.txt" required>
                                <div class="form-text">
                                    Relative to the backend directory
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="file_content" class="form-label">File Content</label>
                                <textarea class="form-control" id="file_content" name="file_content" 
                                    rows="5"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Create File</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Create Directory</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="action" value="create-directory">
                            
                            <div class="mb-3">
                                <label for="directory_path" class="form-label">Directory Path</label>
                                <input type="text" class="form-control" id="directory_path" name="directory_path" 
                                    placeholder="e.g., path/to/directory" required>
                                <div class="form-text">
                                    Relative to the backend directory
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Create Directory</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Upload File</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="upload-file">
                            
                            <div class="mb-3">
                                <label for="upload_file" class="form-label">Select File</label>
                                <input type="file" class="form-control" id="upload_file" name="upload_file" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="upload_path" class="form-label">Upload Path</label>
                                <input type="text" class="form-control" id="upload_path" name="upload_path" 
                                    placeholder="e.g., path/to/file.txt" required>
                                <div class="form-text">
                                    Relative to the backend directory
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Upload File</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">File Operations Help</h5>
                </div>
                <div class="card-body">
                    <h6>Notes:</h6>
                    <ul>
                        <li>All paths are relative to the <code>backend</code> directory.</li>
                        <li>To create files in the frontend directory, use <code>../frontend/path/to/file</code>.</li>
                        <li>For security reasons, you cannot create or modify files outside the project directory.</li>
                        <li>Some system directories like <code>logs</code>, <code>node_modules</code>, and <code>vendor</code> are restricted.</li>
                    </ul>
                    
                    <h6>Examples:</h6>
                    <ul>
                        <li>Create a PHP file: <code>api/new-endpoint.php</code></li>
                        <li>Create a directory: <code>uploads/custom</code></li>
                        <li>Create a frontend component: <code>../frontend/src/components/NewComponent.jsx</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### `backend\edit\index.php`

```php
// backend/edit/index.php
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/login.php');
    exit;
}

// Base paths
$basePath = dirname(dirname(__FILE__));
$relativeBasePath = '..';

// Initialize variables
$current_file = '';
$file_content = '';
$success_message = '';
$error_message = '';
$file_list = [];

// Handle file operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save file
    if (isset($_POST['action']) && $_POST['action'] === 'save') {
        $filepath = $basePath . '/' . $_POST['filepath'];
        $content = $_POST['content'];
        
        // Validate path is within our project
        if (strpos(realpath($filepath), realpath($basePath)) !== 0) {
            $error_message = 'Invalid file path. Cannot save files outside project directory.';
        } else {
            if (file_put_contents($filepath, $content) !== false) {
                $success_message = 'File saved successfully.';
                $current_file = $_POST['filepath'];
                $file_content = $content;
            } else {
                $error_message = 'Failed to save file. Check file permissions.';
            }
        }
    }
    
    // Execute command
    if (isset($_POST['action']) && $_POST['action'] === 'execute') {
        $command = $_POST['command'];
        
        // Validate command for basic security
        $allowed_commands = [
            'npm run dev',
            'npm run build',
            'npm install',
            'composer install',
            'composer update',
            'php -v',
            'node -v',
            'npm -v'
        ];
        
        $command_safe = false;
        foreach ($allowed_commands as $allowed) {
            if (strpos($command, $allowed) === 0) {
                $command_safe = true;
                break;
            }
        }
        
        if (!$command_safe) {
            $error_message = 'Command not allowed for security reasons.';
        } else {
            $output = [];
            $return_var = 0;
            
            // Execute command in the appropriate directory
            if (strpos($command, 'npm') === 0 || strpos($command, 'node') === 0) {
                $cwd = dirname(dirname(dirname(__FILE__))) . '/frontend';
                chdir($cwd);
            }
            
            exec($command . ' 2>&1', $output, $return_var);
            
            $result = implode(PHP_EOL, $output);
            
            if ($return_var === 0) {
                $success_message = 'Command executed successfully: ' . $command;
            } else {
                $error_message = 'Command execution failed with error code ' . $return_var;
            }
            
            // Store command output in session for display
            $_SESSION['command_output'] = $result;
        }
    }
}

// Load file content
if (isset($_GET['file'])) {
    $filepath = $_GET['file'];
    $fullpath = $basePath . '/' . $filepath;
    
    // Validate path is within our project
    if (file_exists($fullpath) && strpos(realpath($fullpath), realpath($basePath)) === 0) {
        $current_file = $filepath;
        $file_content = file_get_contents($fullpath);
    } else {
        $error_message = 'Invalid or non-existent file.';
    }
}

// Function to scan directory recursively
function scanDirectoryRecursive($dir, $baseDir = '') {
    $result = [];
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        $relativePath = $baseDir ? $baseDir . '/' . $file : $file;
        
        if (is_dir($path)) {
            // Skip node_modules and vendor directories
            if ($file === 'node_modules' || $file === 'vendor' || $file === 'logs') {
                continue;
            }
            
            $result[] = [
                'type' => 'directory',
                'name' => $file,
                'path' => $relativePath,
                'children' => scanDirectoryRecursive($path, $relativePath)
            ];
        } else {
            // Skip certain file types
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($extension, ['log', 'zip', 'gz', 'tar'])) {
                continue;
            }
            
            $result[] = [
                'type' => 'file',
                'name' => $file,
                'path' => $relativePath
            ];
        }
    }
    
    return $result;
}

// Get file list for navigator
$file_list = scanDirectoryRecursive($basePath, '');

// Get appropriate editor mode based on file extension
function getEditorMode($filepath) {
    $extension = pathinfo($filepath, PATHINFO_EXTENSION);
    
    switch ($extension) {
        case 'php':
            return 'application/x-httpd-php';
        case 'js':
            return 'text/javascript';
        case 'jsx':
            return 'text/jsx';
        case 'css':
            return 'text/css';
        case 'html':
            return 'text/html';
        case 'json':
            return 'application/json';
        case 'sql':
            return 'text/x-sql';
        case 'md':
            return 'text/markdown';
        default:
            return 'text/plain';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Share - Code Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/dracula.min.css">
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }
        .editor-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .editor-header {
            flex: 0 0 auto;
        }
        .editor-body {
            flex: 1 1 auto;
            display: flex;
            overflow: hidden;
        }
        .file-explorer {
            flex: 0 0 250px;
            overflow-y: auto;
            border-right: 1px solid #dee2e6;
            padding: 1rem;
        }
        .editor-content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .CodeMirror {
            height: 100%;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
        }
        .treeview ul {
            list-style: none;
            padding-left: 1.2rem;
        }
        .treeview > ul {
            padding-left: 0;
        }
        .treeview li {
            padding: 2px 0;
        }
        .treeview .folder {
            cursor: pointer;
        }
        .treeview .file {
            cursor: pointer;
        }
        .treeview .file:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .terminal {
            background-color: #1e1e1e;
            color: #f8f8f8;
            padding: 0.5rem;
            font-family: monospace;
            overflow-y: auto;
            height: 200px;
            display: none;
        }
        .terminal.show {
            display: block;
        }
        .preview-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .tabs {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .tab {
            display: inline-block;
            padding: 8px 15px;
            cursor: pointer;
            border-right: 1px solid #dee2e6;
        }
        .tab.active {
            background-color: #fff;
            border-bottom: 2px solid #0d6efd;
        }
        .tab-content {
            display: none;
            height: 100%;
        }
        .tab-content.active {
            display: block;
        }
        .resizable-panel {
            resize: vertical;
            overflow: auto;
            min-height: 200px;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <div class="editor-header">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">Media Share Editor</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="../admin/index.php">
                                    <i class="bi bi-speedometer2"></i> Admin Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="btnSaveFile">
                                    <i class="bi bi-save"></i> Save File
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="btnToggleTerminal">
                                    <i class="bi bi-terminal"></i> Terminal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="btnPreview">
                                    <i class="bi bi-eye"></i> Preview
                                </a>
                            </li>
                        </ul>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="../admin/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="editor-body">
            <div class="file-explorer">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">File Explorer</h6>
                    <button class="btn btn-sm btn-outline-secondary" id="btnRefreshFiles">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <div class="treeview">
                    <ul>
                        <?php function renderFileTree($items) { ?>
                            <?php foreach ($items as $item): ?>
                                <?php if ($item['type'] === 'directory'): ?>
                                    <li>
                                        <div class="folder" data-path="<?php echo htmlspecialchars($item['path']); ?>">
                                            <i class="bi bi-folder"></i> <?php echo htmlspecialchars($item['name']); ?>
                                        </div>
                                        <?php if (!empty($item['children'])): ?>
                                            <ul style="display: none;">
                                                <?php renderFileTree($item['children']); ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <div class="file" data-path="<?php echo htmlspecialchars($item['path']); ?>">
                                            <i class="bi bi-file-earmark-code"></i> <?php echo htmlspecialchars($item['name']); ?>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php } ?>
                        <?php renderFileTree($file_list); ?>
                    </ul>
                </div>
            </div>
            
            <div class="editor-content">
                <div class="tabs">
                    <div class="tab active" data-tab="editor">Editor</div>
                    <div class="tab" data-tab="preview">Preview</div>
                    <div class="tab" data-tab="test">Test App</div>
                </div>
                
                <div class="tab-content active" id="editor-tab">
                    <?php if ($current_file): ?>
                        <div class="p-1 bg-light border-bottom">
                            <small class="text-muted">Editing: <?php echo htmlspecialchars($current_file); ?></small>
                        </div>
                        <div id="editor-container" style="height: calc(100% - 25px);"></div>
                    <?php else: ?>
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <div class="text-center">
                                <i class="bi bi-file-earmark-code display-1 text-muted"></i>
                                <p class="mt-3">Select a file from the explorer to edit</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="tab-content" id="preview-tab">
                    <div class="p-1 bg-light border-bottom d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Preview</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" id="btnReloadPreview">
                                <i class="bi bi-arrow-clockwise"></i> Reload
                            </button>
                        </div>
                    </div>
                    <iframe class="preview-iframe" id="preview-iframe"></iframe>
                </div>
                
                <div class="tab-content" id="test-tab">
                    <div class="p-1 bg-light border-bottom">
                        <small class="text-muted">Test Application</small>
                    </div>
                    <div class="container-fluid p-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">Frontend Commands</div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" data-command="npm run dev">
                                                <i class="bi bi-play"></i> Start Development Server
                                            </button>
                                            <button class="btn btn-secondary" data-command="npm run build">
                                                <i class="bi bi-hammer"></i> Build for Production
                                            </button>
                                            <button class="btn btn-info" data-command="npm install">
                                                <i class="bi bi-box"></i> Install Dependencies
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">System Info</div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-secondary" data-command="php -v">
                                                <i class="bi bi-info-circle"></i> PHP Version
                                            </button>
                                            <button class="btn btn-outline-secondary" data-command="node -v">
                                                <i class="bi bi-info-circle"></i> Node.js Version
                                            </button>
                                            <button class="btn btn-outline-secondary" data-command="npm -v">
                                                <i class="bi bi-info-circle"></i> NPM Version
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Test Application</span>
                                            <div>
                                                <button class="btn btn-sm btn-outline-secondary" id="btnToggleSize">
                                                    <i class="bi bi-arrows-angle-expand"></i> Toggle Size
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <iframe src="../../" id="test-iframe" style="width: 100%; height: 500px; border: none;"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="terminal resizable-panel" id="terminal">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted">Terminal Output</span>
                <button class="btn btn-sm btn-close btn-close-white" id="btnCloseTerminal"></button>
            </div>
            <pre id="terminal-output"><?php echo isset($_SESSION['command_output']) ? htmlspecialchars($_SESSION['command_output']) : ''; ?></pre>
        </div>
    </div>
    
    <!-- Hidden forms for POST actions -->
    <form id="saveForm" method="post" style="display: none;">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="filepath" id="save_filepath" value="<?php echo htmlspecialchars($current_file); ?>">
        <textarea name="content" id="save_content"></textarea>
    </form>
    
    <form id="executeForm" method="post" style="display: none;">
        <input type="hidden" name="action" value="execute">
        <input type="hidden" name="command" id="execute_command">
    </form>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
    
    <!-- CodeMirror modes -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/jsx/jsx.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/php/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/markdown/markdown.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/sql/sql.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables
            let editor = null;
            let currentFilePath = '<?php echo addslashes($current_file); ?>';
            
            // Initialize CodeMirror if a file is selected
            if (document.getElementById('editor-container')) {
                editor = CodeMirror(document.getElementById('editor-container'), {
                    value: <?php echo json_encode($file_content); ?>,
                    mode: '<?php echo $current_file ? getEditorMode($current_file) : 'text/plain'; ?>',
                    theme: 'dracula',
                    lineNumbers: true,
                    indentUnit: 4,
                    autoCloseBrackets: true,
                    matchBrackets: true,
                    lineWrapping: true,
                    tabSize: 4,
                    indentWithTabs: false,
                    extraKeys: {"Ctrl-Space": "autocomplete"}
                });
                
                // Auto-resize editor when window is resized
                window.addEventListener('resize', function() {
                    if (editor) {
                        editor.refresh();
                    }
                });
            }
            
            // File tree navigation
            document.querySelectorAll('.folder').forEach(folder => {
                folder.addEventListener('click', function() {
                    const parentLi = this.parentNode;
                    const subList = parentLi.querySelector('ul');
                    
                    if (subList) {
                        const isHidden = subList.style.display === 'none';
                        subList.style.display = isHidden ? 'block' : 'none';
                        
                        // Update folder icon
                        const icon = this.querySelector('i');
                        icon.className = isHidden ? 'bi bi-folder-open' : 'bi bi-folder';
                    }
                });
            });
            
            document.querySelectorAll('.file').forEach(file => {
                file.addEventListener('click', function() {
                    const filePath = this.getAttribute('data-path');
                    window.location.href = `index.php?file=${encodeURIComponent(filePath)}`;
                });
            });
            
            // Save file
            document.getElementById('btnSaveFile').addEventListener('click', function(e) {
                e.preventDefault();
                
                if (!currentFilePath || !editor) {
                    alert('No file is currently open for editing.');
                    return;
                }
                
                document.getElementById('save_filepath').value = currentFilePath;
                document.getElementById('save_content').value = editor.getValue();
                document.getElementById('saveForm').submit();
            });
            
            // Toggle terminal
            document.getElementById('btnToggleTerminal').addEventListener('click', function(e) {
                e.preventDefault();
                const terminal = document.getElementById('terminal');
                terminal.classList.toggle('show');
            });
            
            // Close terminal
            document.getElementById('btnCloseTerminal').addEventListener('click', function() {
                document.getElementById('terminal').classList.remove('show');
            });
            
            // Execute commands
            document.querySelectorAll('[data-command]').forEach(button => {
                button.addEventListener('click', function() {
                    const command = this.getAttribute('data-command');
                    if (confirm(`Execute command: ${command}?`)) {
                        document.getElementById('execute_command').value = command;
                        document.getElementById('executeForm').submit();
                    }
                });
            });
            
            // Refresh file list
            document.getElementById('btnRefreshFiles').addEventListener('click', function() {
                window.location.reload();
            });
            
            // Tab navigation
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    // Update active tab
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = this.getAttribute('data-tab');
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                    
                    // Special handling for preview tab
                    if (tabId === 'preview' && currentFilePath) {
                        updatePreview();
                    }
                });
            });
            
            // Preview button
            document.getElementById('btnPreview').addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector('[data-tab="preview"]').click();
            });
            
            // Update preview
            function updatePreview() {
                const previewFrame = document.getElementById('preview-iframe');
                
                if (!currentFilePath) {
                    previewFrame.src = 'about:blank';
                    return;
                }
                
                // Only preview HTML, PHP and image files directly
                const extension = currentFilePath.split('.').pop().toLowerCase();
                if (['html', 'php', 'jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                    previewFrame.src = `../${currentFilePath}`;
                } else {
                    previewFrame.src = 'about:blank';
                    previewFrame.onload = function() {
                        const doc = previewFrame.contentDocument || previewFrame.contentWindow.document;
                        doc.body.innerHTML = '<div style="padding: 20px; font-family: monospace;">' +
                            '<h3>Preview not available</h3>' +
                            '<p>Direct preview is only available for HTML, PHP and image files.</p>' +
                            '</div>';
                    };
                }
            }
            
            // Reload preview
            document.getElementById('btnReloadPreview').addEventListener('click', function() {
                updatePreview();
            });
            
            // Toggle iframe size
            document.getElementById('btnToggleSize').addEventListener('click', function() {
                const iframe = document.getElementById('test-iframe');
                if (iframe.style.height === '500px') {
                    iframe.style.height = '800px';
                } else {
                    iframe.style.height = '500px';
                }
            });
            
            // Auto-show terminal if there's output
            if (document.getElementById('terminal-output').textContent.trim()) {
                document.getElementById('terminal').classList.add('show');
            }
        });
    </script>
</body>
</html>success_message = 'Command executed successfully: ' . $command;
            } else {
                $error_message = 'Command execution failed with error code ' . $return_var;
            }
            
            // Store command output in session for display
            $_SESSION['command_output'] = $result;
        }
    }
}

// Load file content
if (isset($_GET['file'])) {
    $filepath = $_GET['file'];
    $fullpath = $basePath . '/' . $filepath;
    
    // Validate path is within our project
    if (file_exists($fullpath) && strpos(realpath($fullpath), realpath($basePath)) === 0) {
        $current_file = $filepath;
        $file_content = file_get_contents($fullpath);
    } else {
        $error_message = 'Invalid or non-existent file.';
    }
}

// Function to scan directory recursively
function scanDirectoryRecursive($dir, $baseDir = '') {
    $result = [];
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        $relativePath = $baseDir ? $baseDir . '/' . $file : $file;
        
        if (is_dir($path)) {
            // Skip node_modules and vendor directories
            if ($file === 'node_modules' || $file === 'vendor' || $file === 'logs') {
                continue;
            }
            
            $result[] = [
                'type' => 'directory',
                'name' => $file,
                'path' => $relativePath,
                'children' => scanDirectoryRecursive($path, $relativePath)
            ];
        } else {
            // Skip certain file types
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($extension, ['log', 'zip', 'gz', 'tar'])) {
                continue;
            }
            
            $result[] = [
                'type' => 'file',
                'name' => $file,
                'path' => $relativePath
            ];
        }
    }
    
    return $result;
}

// Get file list for navigator
$file_list = scanDirectoryRecursive($basePath, '');

// Get appropriate editor mode based on file extension
function getEditorMode($filepath) {
    $extension = pathinfo($filepath, PATHINFO_EXTENSION);
    
    switch ($extension) {
        case 'php':
            return 'application/x-httpd-php';
        case 'js':
            return 'text/javascript';
        case 'jsx':
            return 'text/jsx';
        case 'css':
            return 'text/css';
        case 'html':
            return 'text/html';
        case 'json':
            return 'application/json';
        case 'sql':
            return 'text/x-sql';
        case 'md':
            return 'text/markdown';
        default:
            return 'text/plain';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Share - Code Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/dracula.min.css">
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }
        .editor-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .editor-header {
            flex: 0 0 auto;
        }
        .editor-body {
            flex: 1 1 auto;
            display: flex;
            overflow: hidden;
        }
        .file-explorer {
            flex: 0 0 250px;
            overflow-y: auto;
            border-right: 1px solid #dee2e6;
            padding: 1rem;
        }
        .editor-content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .CodeMirror {
            height: 100%;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
        }
        .treeview ul {
            list-style: none;
            padding-left: 1.2rem;
        }
        .treeview > ul {
            padding-left: 0;
        }
        .treeview li {
            padding: 2px 0;
        }
        .treeview .folder {
            cursor: pointer;
        }
        .treeview .file {
            cursor: pointer;
        }
        .treeview .file:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .terminal {
            background-color: #1e1e1e;
            color: #f8f8f8;
            padding: 0.5rem;
            font-family: monospace;
            overflow-y: auto;
            height: 200px;
            display:
```

### `backend\edit\router.php`

```php
// backend/edit/router.php
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/login.php');
    exit;
}

// Get requested action
$action = isset($_GET['action']) ? $_GET['action'] : '';
$response = ['success' => false];

// Handle file system operations
switch ($action) {
    case 'create-file':
        $response = createFile();
        break;
        
    case 'create-directory':
        $response = createDirectory();
        break;
        
    case 'rename':
        $response = renameItem();
        break;
        
    case 'delete':
        $response = deleteItem();
        break;
        
    case 'get-file-list':
        $response = getFileList();
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;

/**
 * Create a new file
 */
function createFile() {
    if (!isset($_POST['path']) || !isset($_POST['content'])) {
        return ['success' => false, 'message' => 'Missing required parameters'];
    }
    
    $basePath = dirname(dirname(__FILE__));
    $path = $_POST['path'];
    $content = $_POST['content'];
    
    // Validate file path
    $fullPath = $basePath . '/' . $path;
    $realBasePath = realpath($basePath);
    
    if (strpos(realpath(dirname($fullPath)), $realBasePath) !== 0) {
        return ['success' => false, 'message' => 'Invalid file path. Cannot create files outside project directory.'];
    }
    
    // Create parent directories if they don't exist
    $dir = dirname($fullPath);
    if (!file_exists($dir)) {
        if (!mkdir($dir, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create directory structure'];
        }
    }
    
    // Create file
    if (file_put_contents($fullPath, $content) === false) {
        return ['success' => false, 'message' => 'Failed to create file. Check file permissions.'];
    }
    
    return ['success' => true, 'message' => 'File created successfully'];
}

/**
 * Create a new directory
 */
function createDirectory() {
    if (!isset($_POST['path'])) {
        return ['success' => false, 'message' => 'Missing required parameter: path'];
    }
    
    $basePath = dirname(dirname(__FILE__));
    $path = $_POST['path'];
    
    // Validate directory path
    $fullPath = $basePath . '/' . $path;
    $realBasePath = realpath($basePath);
    
    if (strpos(realpath(dirname($fullPath)), $realBasePath) !== 0) {
        return ['success' => false, 'message' => 'Invalid directory path. Cannot create directories outside project.'];
    }
    
    // Create directory
    if (!mkdir($fullPath, 0755, true)) {
        return ['success' => false, 'message' => 'Failed to create directory. Check permissions.'];
    }
    
    return ['success' => true, 'message' => 'Directory created successfully'];
}

/**
 * Rename a file or directory
 */
function renameItem() {
    if (!isset($_POST['old_path']) || !isset($_POST['new_path'])) {
        return ['success' => false, 'message' => 'Missing required parameters'];
    }
    
    $basePath = dirname(dirname(__FILE__));
    $oldPath = $_POST['old_path'];
    $newPath = $_POST['new_path'];
    
    // Validate paths
    $fullOldPath = $basePath . '/' . $oldPath;
    $fullNewPath = $basePath . '/' . $newPath;
    $realBasePath = realpath($basePath);
    
    if (strpos(realpath(dirname($fullOldPath)), $realBasePath) !== 0 ||
        strpos(realpath(dirname($fullNewPath)), $realBasePath) !== 0) {
        return ['success' => false, 'message' => 'Invalid paths. Cannot rename items outside project directory.'];
    }
    
    // Perform rename
    if (!rename($fullOldPath, $fullNewPath)) {
        return ['success' => false, 'message' => 'Failed to rename item. Check permissions.'];
    }
    
    return ['success' => true, 'message' => 'Item renamed successfully'];
}

/**
 * Delete a file or directory
 */
function deleteItem() {
    if (!isset($_POST['path'])) {
        return ['success' => false, 'message' => 'Missing required parameter: path'];
    }
    
    $basePath = dirname(dirname(__FILE__));
    $path = $_POST['path'];
    
    // Validate path
    $fullPath = $basePath . '/' . $path;
    $realBasePath = realpath($basePath);
    
    // Prevent deletion of sensitive files/directories
    $protectedItems = [
        'config.php',
        'admin',
        'api',
        'edit',
        'uploads'
    ];
    
    foreach ($protectedItems as $item) {
        if (stripos($path, $item) === 0 || $path === $item) {
            return ['success' => false, 'message' => 'Cannot delete protected files or directories'];
        }
    }
    
    if (strpos(realpath(dirname($fullPath)), $realBasePath) !== 0) {
        return ['success' => false, 'message' => 'Invalid path. Cannot delete items outside project directory.'];
    }
    
    // Delete file or directory
    if (is_file($fullPath)) {
        if (!unlink($fullPath)) {
            return ['success' => false, 'message' => 'Failed to delete file. Check permissions.'];
        }
    } else if (is_dir($fullPath)) {
        // Recursively delete directory
        if (!deleteDirectory($fullPath)) {
            return ['success' => false, 'message' => 'Failed to delete directory. Check permissions.'];
        }
    } else {
        return ['success' => false, 'message' => 'Item not found'];
    }
    
    return ['success' => true, 'message' => 'Item deleted successfully'];
}

/**
 * Recursively delete a directory
 */
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        
        if (!deleteDirectory($dir . '/' . $item)) {
            return false;
        }
    }
    
    return rmdir($dir);
}

/**
 * Get file list for a specific directory
 */
function getFileList() {
    $basePath = dirname(dirname(__FILE__));
    $path = isset($_GET['path']) ? $_GET['path'] : '';
    
    // Validate path
    $fullPath = $basePath . '/' . $path;
    $realBasePath = realpath($basePath);
    
    if (!file_exists($fullPath) || !is_dir($fullPath)) {
        return ['success' => false, 'message' => 'Directory not found'];
    }
    
    if (strpos(realpath($fullPath), $realBasePath) !== 0) {
        return ['success' => false, 'message' => 'Invalid path. Cannot list files outside project directory.'];
    }
    
    // Get file list
    $files = scandir($fullPath);
    $result = [];
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $filePath = $fullPath . '/' . $file;
        $relativePath = $path ? $path . '/' . $file : $file;
        
        if (is_dir($filePath)) {
            // Skip node_modules and vendor directories
            if ($file === 'node_modules' || $file === 'vendor' || $file === 'logs') {
                continue;
            }
            
            $result[] = [
                'type' => 'directory',
                'name' => $file,
                'path' => $relativePath
            ];
        } else {
            // Skip certain file types
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($extension, ['log', 'zip', 'gz', 'tar'])) {
                continue;
            }
            
            $result[] = [
                'type' => 'file',
                'name' => $file,
                'path' => $relativePath,
                'size' => filesize($filePath),
                'modified' => filemtime($filePath)
            ];
        }
    }
    
    return [
        'success' => true,
        'path' => $path,
        'items' => $result
    ];
}
```

### `backend\init_db.php`

```php
<?php
// backend/init_db.php
// Database connection parameters
$db_host = 'localhost';
$db_name = 'my_media';
$db_user = 'cp';
$db_pass = '4334.4334';

$conn = null;

try {
    // Create connection
    $conn = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sql);
    
    echo "Database created successfully<br>";
    
    // Select the database
    $conn->exec("USE `$db_name`");
    
    // Create tables
    
    // Media table
    $sql = "CREATE TABLE IF NOT EXISTS `media` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `filename` VARCHAR(255) NOT NULL,
        `thumbnail` VARCHAR(255),
        `type` ENUM('video', 'audio', 'image') NOT NULL,
        `caption` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    
    echo "Media table created successfully<br>";
    
    // Tags table
    $sql = "CREATE TABLE IF NOT EXISTS `tags` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `name` VARCHAR(50) NOT NULL UNIQUE
    )";
    $conn->exec($sql);
    
    echo "Tags table created successfully<br>";
    
    // Media_Tags relation table
    $sql = "CREATE TABLE IF NOT EXISTS `media_tags` (
        `media_id` INT NOT NULL,
        `tag_id` INT NOT NULL,
        PRIMARY KEY (`media_id`, `tag_id`),
        FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
    )";
    $conn->exec($sql);
    
    echo "Media_Tags table created successfully<br>";
    
    // Analytics table
    $sql = "CREATE TABLE IF NOT EXISTS `analytics` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `media_id` INT NOT NULL,
        `user_name` VARCHAR(50) NOT NULL,
        `event_type` ENUM('view', 'play', 'pause', 'seek', 'progress', 'ended', 'download') NOT NULL,
        `position` FLOAT,
        `percentage` FLOAT,
        `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE
    )";
    $conn->exec($sql);
    
    echo "Analytics table created successfully<br>";
    
    // Admin users table
    $sql = "CREATE TABLE IF NOT EXISTS `admin_users` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    
    echo "Admin users table created successfully<br>";
    
    // Create default admin account
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Check if admin already exists
    $stmt = $conn->prepare("SELECT id FROM `admin_users` WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() == 0) {
        $sql = "INSERT INTO `admin_users` (username, password) VALUES (?, ?)";
        $conn->prepare($sql)->execute([$username, $password]);
        echo "Default admin user created (username: admin, password: admin123)<br>";
    } else {
        echo "Admin user already exists<br>";
    }
    
    // Create uploads directory structure
    $upload_dirs = [
        '../uploads',
        '../uploads/thumbnails',
        '../uploads/temp'
    ];
    
    foreach ($upload_dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
            echo "Created directory: $dir<br>";
        } else {
            echo "Directory already exists: $dir<br>";
        }
    }
    
    echo "<p>Database initialization completed successfully!</p>";
    echo "<p><a href='admin/index.php'>Go to Admin Dashboard</a></p>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$conn = null;
?>
```

### `backend\schema.sql`

```sql
-- backend/schema.sql
-- Media Share database schema

-- Media table
CREATE TABLE IF NOT EXISTS `media` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `filename` VARCHAR(255) NOT NULL,
    `thumbnail` VARCHAR(255),
    `type` ENUM('video', 'audio', 'image') NOT NULL,
    `caption` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tags table
CREATE TABLE IF NOT EXISTS `tags` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Media_Tags relation table
CREATE TABLE IF NOT EXISTS `media_tags` (
    `media_id` INT NOT NULL,
    `tag_id` INT NOT NULL,
    PRIMARY KEY (`media_id`, `tag_id`),
    FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Analytics table
CREATE TABLE IF NOT EXISTS `analytics` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `media_id` INT NOT NULL,
    `user_name` VARCHAR(50) NOT NULL,
    `event_type` ENUM('view', 'play', 'pause', 'seek', 'progress', 'ended', 'download') NOT NULL,
    `position` FLOAT,
    `percentage` FLOAT,
    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin users table
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table for app configuration
CREATE TABLE IF NOT EXISTS `settings` (
    `key` VARCHAR(50) PRIMARY KEY,
    `value` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial settings
INSERT INTO `settings` (`key`, `value`) VALUES
('app_title', 'Media Share'),
('app_description', 'Share media with tracking capabilities'),
('frontend_url', '../'),
('max_upload_size', '104857600'),
('allowed_users', 'charl,nade');
```

### `backend\setup.php`

```php
// backend/setup.php
<?php
// Check if application is already installed
$configFile = __DIR__ . '/config.php';
$installed = false;

if (file_exists($configFile)) {
    // Check if config file contains database settings
    $configContent = file_get_contents($configFile);
    if (strpos($configContent, 'DB_HOST') !== false) {
        $installed = true;
    }
}

// Setup stage tracking
$stage = isset($_GET['stage']) ? $_GET['stage'] : 'database';
$error = '';
$success = '';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($stage) {
        case 'database':
            // Validate database connection and create database
            $db_host = $_POST['db_host'] ?? 'localhost';
            $db_port = $_POST['db_port'] ?? '3306';
            $db_user = $_POST['db_user'] ?? 'root';
            $db_pass = $_POST['db_pass'] ?? '';
            $db_name = $_POST['db_name'] ?? 'my_media';
            
            try {
                // Connect to database server (without specific database)
                $dsn = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
                $conn = new PDO($dsn, $db_user, $db_pass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Create database if it doesn't exist
                $conn->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                // Select the new database
                $conn->exec("USE `{$db_name}`");
                
                // Create tables
                $sqlScript = file_get_contents(__DIR__ . '/schema.sql');
                $conn->exec($sqlScript);
                
                // Generate config file
                $configTemplate = file_get_contents(__DIR__ . '/config.template.php');
                $configContent = str_replace(
                    ['{{DB_HOST}}', '{{DB_PORT}}', '{{DB_NAME}}', '{{DB_USER}}', '{{DB_PASS}}'],
                    [$db_host, $db_port, $db_name, $db_user, $db_pass],
                    $configTemplate
                );
                
                // Write config file
                if (file_put_contents($configFile, $configContent) === false) {
                    throw new Exception('Failed to write config file. Please check file permissions.');
                }
                
                $success = 'Database setup completed successfully!';
                header('Location: setup.php?stage=admin&success=' . urlencode($success));
                exit;
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            break;
            
        case 'admin':
            // Create admin user
            $admin_user = $_POST['admin_user'] ?? '';
            $admin_pass = $_POST['admin_pass'] ?? '';
            $admin_pass_confirm = $_POST['admin_pass_confirm'] ?? '';
            
            if (empty($admin_user) || empty($admin_pass)) {
                $error = 'All fields are required';
            } else if ($admin_pass !== $admin_pass_confirm) {
                $error = 'Passwords do not match';
            } else {
                try {
                    // Load config
                    require_once $configFile;
                    
                    // Connect to database
                    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                    $conn = new PDO($dsn, DB_USER, DB_PASS);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Check if admin user table exists
                    $stmt = $conn->query("SHOW TABLES LIKE 'admin_users'");
                    if ($stmt->rowCount() == 0) {
                        throw new Exception('Admin users table not found. Please restart setup.');
                    }
                    
                    // Hash password
                    $passwordHash = password_hash($admin_pass, PASSWORD_DEFAULT);
                    
                    // Insert admin user
                    $stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
                    $stmt->execute([$admin_user, $passwordHash]);
                    
                    // Create necessary directories if they don't exist
                    $directories = [
                        __DIR__ . '/uploads',
                        __DIR__ . '/uploads/thumbnails',
                        __DIR__ . '/uploads/temp',
                        __DIR__ . '/logs'
                    ];
                    
                    foreach ($directories as $dir) {
                        if (!file_exists($dir)) {
                            if (!mkdir($dir, 0755, true)) {
                                throw new Exception("Failed to create directory: $dir");
                            }
                        }
                    }
                    
                    $success = 'Setup completed successfully!';
                    header('Location: setup.php?stage=complete&success=' . urlencode($success));
                    exit;
                } catch (PDOException $e) {
                    $error = 'Database error: ' . $e->getMessage();
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
            break;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Share - Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .setup-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        .setup-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .setup-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .setup-steps::before {
            content: '';
            position: absolute;
            top: 14px;
            left: 30px;
            right: 30px;
            height: 2px;
            background: #dee2e6;
            z-index: 0;
        }
        .step {
            width: 30px;
            height: 30px;
            background-color: #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 1;
        }
        .step.active {
            background-color: #0d6efd;
            color: #fff;
        }
        .step.completed {
            background-color: #198754;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container setup-container">
        <div class="setup-logo">
            <h1>Media Share Setup</h1>
            <p class="text-muted">Let's get your application up and running</p>
        </div>
        
        <div class="setup-steps">
            <div class="step <?php echo $stage == 'database' ? 'active' : ($stage == 'admin' || $stage == 'complete' ? 'completed' : ''); ?>">1</div>
            <div class="step <?php echo $stage == 'admin' ? 'active' : ($stage == 'complete' ? 'completed' : ''); ?>">2</div>
            <div class="step <?php echo $stage == 'complete' ? 'active' : ''; ?>">3</div>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($installed && $stage != 'complete'): ?>
        <div class="alert alert-warning">
            Application appears to be already installed. Continuing with setup will overwrite existing configuration.
        </div>
        <?php endif; ?>
        
        <?php if ($stage == 'database'): ?>
        <!-- Database Configuration -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Database Configuration</h5>
            </div>
            <div class="card-body">
                <form method="post" action="setup.php?stage=database">
                    <div class="mb-3">
                        <label for="db_host" class="form-label">Database Host</label>
                        <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_port" class="form-label">Database Port</label>
                        <input type="text" class="form-control" id="db_port" name="db_port" value="3306" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_name" class="form-label">Database Name</label>
                        <input type="text" class="form-control" id="db_name" name="db_name" value="my_media" required>
                        <div class="form-text">Database will be created if it doesn't exist.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_user" class="form-label">Database Username</label>
                        <input type="text" class="form-control" id="db_user" name="db_user" value="root" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_pass" class="form-label">Database Password</label>
                        <input type="password" class="form-control" id="db_pass" name="db_pass" value="">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Next: Admin Setup</button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php elseif ($stage == 'admin'): ?>
        <!-- Admin User Setup -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Admin User Setup</h5>
            </div>
            <div class="card-body">
                <form method="post" action="setup.php?stage=admin">
                    <div class="mb-3">
                        <label for="admin_user" class="form-label">Admin Username</label>
                        <input type="text" class="form-control" id="admin_user" name="admin_user" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_pass" class="form-label">Admin Password</label>
                        <input type="password" class="form-control" id="admin_pass" name="admin_pass" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_pass_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="admin_pass_confirm" name="admin_pass_confirm" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Complete Setup</button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php elseif ($stage == 'complete'): ?>
        <!-- Setup Complete -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Setup Complete</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h4 class="alert-heading">Congratulations!</h4>
                    <p>Your Media Share application has been successfully set up.</p>
                </div>
                
                <div class="d-flex flex-column gap-2">
                    <a href="admin/index.php" class="btn btn-primary">Go to Admin Dashboard</a>
                    <a href="edit/index.php" class="btn btn-outline-primary">Go to Editor</a>
                    <a href="../index.html" class="btn btn-outline-secondary">View Frontend</a>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### `frontend\components.json`

```json
{
  "$schema": "https://ui.shadcn.com/schema.json",
  "style": "new-york",
  "rsc": false,
  "tsx": false,
  "tailwind": {
    "config": "tailwind.config.js",
    "css": "src/index.css",
    "baseColor": "slate",
    "cssVariables": true,
    "prefix": ""
  },
  "aliases": {
    "components": "@/components",
    "utils": "@/lib/utils",
    "ui": "@/components/ui",
    "lib": "@/lib",
    "hooks": "@/hooks"
  },
  "iconLibrary": "lucide"
}
```

### `frontend\index.html`

```html
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Media Share App</title>
  </head>
  <body>
    <div id="root"></div>
    <script type="module" src="/src/main.jsx"></script>
  </body>
</html>
```

### `frontend\manifest.webmanifest`

```webmanifest
{
  "name": "Media Manager",
  "short_name": "MediaMgr",
  "start_url": ".",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#000000",
  "description": "A media management app.",
  "icons": [
    {
      "src": "/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
```

### `frontend\package.json`

```json
{
    "name": "media-share-frontend",
    "private": true,
    "version": "0.1.0",
    "type": "module",
    "scripts": {
        "dev": "vite",
        "build": "vite build",
        "lint": "eslint . --ext js,jsx --report-unused-disable-directives --max-warnings 0",
        "preview": "vite preview"
    },
    "dependencies": {
        "@radix-ui/react-dialog": "^1.0.5",
        "@radix-ui/react-dropdown-menu": "^2.1.12",
        "@radix-ui/react-label": "^2.0.2",
        "@radix-ui/react-progress": "^1.1.4",
        "@radix-ui/react-slot": "^1.2.0",
        "@radix-ui/react-tabs": "^1.0.4",
        "@radix-ui/react-toast": "^1.2.11",
        "axios": "^1.6.2",
        "class-variance-authority": "^0.7.1",
        "clsx": "^2.1.1",
        "date-fns": "^2.30.0",
        "lucide-react": "^0.290.0",
        "react": "^18.2.0",
        "react-dom": "^18.2.0",
        "react-router-dom": "^6.18.0",
        "tailwind-merge": "^1.14.0",
        "tailwindcss-animate": "^1.0.7",
        "zustand": "^4.4.6"
    },
    "devDependencies": {
        "@types/node": "^20.9.0",
        "@types/react": "^18.2.15",
        "@types/react-dom": "^18.2.7",
        "@vitejs/plugin-react": "^4.0.3",
        "autoprefixer": "^10.4.16",
        "eslint": "^8.45.0",
        "eslint-plugin-react": "^7.32.2",
        "eslint-plugin-react-hooks": "^4.6.0",
        "eslint-plugin-react-refresh": "^0.4.3",
        "postcss": "^8.4.31",
        "tailwindcss": "^3.3.5",
        "vite": "^4.5.0",
        "vite-plugin-pwa": "^0.16.7"
    }
}
```

### `frontend\postcss.config.cjs`

```cjs
module.exports = {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
};
```

### `frontend\postcss.config.js`

```js
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
};
```

### `frontend\registerSW.js`

```js
// Simple service worker registration for Vite PWA
if ("serviceWorker" in navigator) {
  window.addEventListener("load", () => {
    navigator.serviceWorker.register("/sw.js").catch(err => {
      console.error("Service worker registration failed:", err);
    });
  });
}
```

### `frontend\src\App.jsx`

```jsx
// frontend/src/App.jsx
import React, { useEffect, useState } from 'react';
import { Routes, Route, useSearchParams, Navigate } from 'react-router-dom';
import { Home } from './pages/Home';
import { MediaDetails } from './pages/MediaDetails';
import { Layout } from './components/Layout';
import { ModeToggle } from './components/mode-toggle';
import { Button } from './components/ui/button';
import { useUserStore } from './stores/userStore';
import { useToast } from './components/ui/use-toast';
import { Toaster } from './components/ui/toaster';
import { logger } from './lib/logger';

export default function App() {
  const { toast } = useToast();
  const [params] = useSearchParams();
  const [loading, setLoading] = useState(true);
  const { user, setUser } = useUserStore();

  // Get user from URL param
  useEffect(() => {
    const handleUserParam = async () => {
      try {
        const nameParam = params.get('name')?.toLowerCase();
        
        if (nameParam && (nameParam === 'charl' || nameParam === 'nade')) {
          const response = await fetch(`/api/session.php?name=${nameParam}`);
          if (!response.ok) throw new Error('Session creation failed');
          
          const data = await response.json();
          if (data.success) {
            setUser(nameParam);
            toast({
              title: 'Welcome back!',
              description: `Logged in as ${nameParam}`,
            });
          }
        }
        setLoading(false);
      } catch (error) {
        logger.error('Session initialization error:', error);
        toast({
          variant: 'destructive',
          title: 'Session Error',
          description: 'Failed to initialize user session',
        });
        setLoading(false);
      }
    };

    handleUserParam();
  }, [params, setUser, toast]);

  // If still loading, show loading indicator
  if (loading) {
    return (
      <div className="flex h-screen items-center justify-center">
        <div className="text-center">
          <div className="animate-spin h-12 w-12 border-4 border-primary border-t-transparent rounded-full mx-auto mb-4"></div>
          <p className="text-lg font-medium">Loading...</p>
        </div>
      </div>
    );
  }

  // If no valid user in URL, prompt for user
  if (!user) {
    return (
      <div className="flex h-screen items-center justify-center bg-background">
        <div className="w-full max-w-md p-8 space-y-6 bg-card rounded-lg shadow-lg">
          <div className="flex justify-end">
            <ModeToggle />
          </div>
          <h1 className="text-2xl font-bold text-center">Media Share</h1>
          <p className="text-center text-muted-foreground">Please select a user:</p>
          <div className="grid grid-cols-2 gap-4">
            <Button
              className="text-lg py-8"
              onClick={() => window.location.href = "?name=charl"}
            >
              Charl
            </Button>
            <Button
              className="text-lg py-8"
              onClick={() => window.location.href = "?name=nade"}
            >
              Nade
            </Button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <>
      <Routes>
        <Route path="/" element={<Layout />}>
          <Route index element={<Home />} />
          <Route path="/media/:id" element={<MediaDetails />} />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Route>
      </Routes>
      <Toaster />
    </>
  );
}
```

### `frontend\src\components\Layout.jsx`

```jsx
// frontend/src/components/Layout.jsx
import React from 'react';
import { Outlet, Link } from 'react-router-dom';
import { ModeToggle } from './mode-toggle';
import { useUserStore } from '../stores/userStore';

export function Layout() {
  const { user } = useUserStore();

  return (
    <div className="flex min-h-screen flex-col">
      <header className="sticky top-0 z-40 border-b bg-background">
        <div className="container flex h-16 items-center justify-between py-4">
          <div className="flex items-center gap-2">
            <Link to="/" className="flex items-center space-x-2">
              <span className="font-bold text-xl">Media Share</span>
            </Link>
          </div>
          <div className="flex items-center gap-4">
            {user && (
              <div className="flex items-center gap-2">
                <span className="text-sm text-muted-foreground">Logged in as:</span>
                <span className="font-medium capitalize">{user}</span>
              </div>
            )}
            <ModeToggle />
          </div>
        </div>
      </header>
      <main className="flex-1 container py-6 md:py-10">
        <Outlet />
      </main>
      <footer className="border-t py-6 md:py-0">
        <div className="container flex h-16 items-center justify-between">
          <p className="text-sm text-muted-foreground">
            Media Share App &copy; {new Date().getFullYear()}
          </p>
        </div>
      </footer>
    </div>
  );
}
```

### `frontend\src\components\MediaCard.jsx`

```jsx
// frontend/src/components/MediaCard.jsx
import React from 'react';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Play, Image, Music } from 'lucide-react';
import { useMediaStore } from '@/stores/mediaStore';

const TYPE_ICONS = {
  video: <Play className="h-5 w-5" />,
  audio: <Music className="h-5 w-5" />,
  image: <Image className="h-5 w-5" />
};

const TYPE_CLASSES = {
  video: 'video-thumb',
  audio: 'audio-thumb',
  image: 'image-thumb'
};

export function MediaCard({ item }) {
  const { trackMediaEvent } = useMediaStore();
  
  const handleCardClick = () => {
    trackMediaEvent(item.id, 'view');
  };
  
  const renderThumbnail = () => {
    const baseUrl = '/backend/uploads';
    
    // For video/audio, show a thumbnail if available, otherwise a placeholder
    if (item.type === 'video' || item.type === 'audio') {
      return item.thumbnail ? (
        <img 
          src={`${baseUrl}/${item.thumbnail}`} 
          alt={item.caption} 
          className={TYPE_CLASSES[item.type]} 
        />
      ) : (
        <div className={`${TYPE_CLASSES[item.type]} bg-muted flex items-center justify-center`}>
          {TYPE_ICONS[item.type]}
        </div>
      );
    }
    
    // For images, show the actual image
    if (item.type === 'image') {
      return (
        <img 
          src={`${baseUrl}/${item.filename}`} 
          alt={item.caption} 
          className={TYPE_CLASSES[item.type]} 
        />
      );
    }
    
    // Default placeholder
    return (
      <div className="aspect-video bg-muted flex items-center justify-center">
        {TYPE_ICONS[item.type] || <Image className="h-5 w-5" />}
      </div>
    );
  };
  
  return (
    <Card 
      className="overflow-hidden transition-all hover:shadow-md"
      onClick={handleCardClick}
    >
      <CardContent className="p-0 relative">
        {renderThumbnail()}
        <div className="absolute top-2 right-2">
          <Badge variant="secondary" className="flex items-center gap-1">
            {TYPE_ICONS[item.type]}
            <span className="capitalize">{item.type}</span>
          </Badge>
        </div>
      </CardContent>
      <CardFooter className="flex flex-col items-start p-4">
        <h3 className="font-semibold truncate w-full">{item.caption}</h3>
        <p className="text-sm text-muted-foreground line-clamp-2 mt-1">
          {item.description}
        </p>
        {item.tags && item.tags.length > 0 && (
          <div className="flex flex-wrap gap-1 mt-2">
            {item.tags.map(tag => (
              <Badge key={tag} variant="outline" className="text-xs">
                #{tag}
              </Badge>
            ))}
          </div>
        )}
      </CardFooter>
    </Card>
  );
}
```

### `frontend\src\components\mode-toggle.jsx`

```jsx
// frontend/src/components/mode-toggle.jsx
import { Moon, Sun } from "lucide-react";
import { Button } from "./ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "./ui/dropdown-menu";
import { useTheme } from "./theme-provider";

export function ModeToggle() {
  const { setTheme } = useTheme();

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant="outline" size="icon">
          <Sun className="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
          <Moon className="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
          <span className="sr-only">Toggle theme</span>
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        <DropdownMenuItem onClick={() => setTheme("light")}>
          Light
        </DropdownMenuItem>
        <DropdownMenuItem onClick={() => setTheme("dark")}>
          Dark
        </DropdownMenuItem>
        <DropdownMenuItem onClick={() => setTheme("system")}>
          System
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
```

### `frontend\src\components\theme-provider.jsx`

```jsx
// frontend/src/components/theme-provider.jsx
import { createContext, useContext, useEffect, useState } from "react";

const ThemeProviderContext = createContext({
  theme: "system",
  setTheme: () => null,
  themes: ["light", "dark", "system"],
});

export function ThemeProvider({
  children,
  defaultTheme = "system",
  storageKey = "ui-theme",
  ...props
}) {
  const [theme, setTheme] = useState(
    () => localStorage.getItem(storageKey) || defaultTheme
  );

  useEffect(() => {
    const root = window.document.documentElement;
    root.classList.remove("light", "dark");

    if (theme === "system") {
      const systemTheme = window.matchMedia("(prefers-color-scheme: dark)")
        .matches
        ? "dark"
        : "light";
      root.classList.add(systemTheme);
      return;
    }

    root.classList.add(theme);
  }, [theme]);

  const value = {
    theme,
    setTheme: (newTheme) => {
      localStorage.setItem(storageKey, newTheme);
      setTheme(newTheme);
    },
    themes: ["light", "dark", "system"],
  };

  return (
    <ThemeProviderContext.Provider {...props} value={value}>
      {children}
    </ThemeProviderContext.Provider>
  );
}

export const useTheme = () => {
  const context = useContext(ThemeProviderContext);
  if (context === undefined)
    throw new Error("useTheme must be used within a ThemeProvider");
  return context;
};
```

### `frontend\src\components\ui\badge.jsx`

```jsx
import * as React from "react"
import { cva } from "class-variance-authority";

import { cn } from "@/lib/utils"

const badgeVariants = cva(
  "inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2",
  {
    variants: {
      variant: {
        default:
          "border-transparent bg-primary text-primary-foreground shadow hover:bg-primary/80",
        secondary:
          "border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80",
        destructive:
          "border-transparent bg-destructive text-destructive-foreground shadow hover:bg-destructive/80",
        outline: "text-foreground",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  }
)

function Badge({
  className,
  variant,
  ...props
}) {
  return (<div className={cn(badgeVariants({ variant }), className)} {...props} />);
}

export { Badge, badgeVariants }
```

### `frontend\src\components\ui\button.jsx`

```jsx
// frontend/src/components/ui/button.jsx
import * as React from "react"
import { Slot } from "@radix-ui/react-slot"
import { cva } from "class-variance-authority"
import { cn } from "@/lib/utils"

const buttonVariants = cva(
  "inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50",
  {
    variants: {
      variant: {
        default: "bg-primary text-primary-foreground hover:bg-primary/90",
        destructive:
          "bg-destructive text-destructive-foreground hover:bg-destructive/90",
        outline:
          "border border-input bg-background hover:bg-accent hover:text-accent-foreground",
        secondary:
          "bg-secondary text-secondary-foreground hover:bg-secondary/80",
        ghost: "hover:bg-accent hover:text-accent-foreground",
        link: "text-primary underline-offset-4 hover:underline",
      },
      size: {
        default: "h-10 px-4 py-2",
        sm: "h-9 rounded-md px-3",
        lg: "h-11 rounded-md px-8",
        icon: "h-10 w-10",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  }
)

const Button = React.forwardRef(({ className, variant, size, asChild = false, ...props }, ref) => {
  const Comp = asChild ? Slot : "button"
  return (
    <Comp
      className={cn(buttonVariants({ variant, size, className }))}
      ref={ref}
      {...props}
    />
  )
})
Button.displayName = "Button"

export { Button, buttonVariants }
```

### `frontend\src\components\ui\card.jsx`

```jsx
import * as React from "react"

import { cn } from "@/lib/utils"

const Card = React.forwardRef(({ className, ...props }, ref) => (
  <div
    ref={ref}
    className={cn("rounded-xl border bg-card text-card-foreground shadow", className)}
    {...props} />
))
Card.displayName = "Card"

const CardHeader = React.forwardRef(({ className, ...props }, ref) => (
  <div
    ref={ref}
    className={cn("flex flex-col space-y-1.5 p-6", className)}
    {...props} />
))
CardHeader.displayName = "CardHeader"

const CardTitle = React.forwardRef(({ className, ...props }, ref) => (
  <div
    ref={ref}
    className={cn("font-semibold leading-none tracking-tight", className)}
    {...props} />
))
CardTitle.displayName = "CardTitle"

const CardDescription = React.forwardRef(({ className, ...props }, ref) => (
  <div
    ref={ref}
    className={cn("text-sm text-muted-foreground", className)}
    {...props} />
))
CardDescription.displayName = "CardDescription"

const CardContent = React.forwardRef(({ className, ...props }, ref) => (
  <div ref={ref} className={cn("p-6 pt-0", className)} {...props} />
))
CardContent.displayName = "CardContent"

const CardFooter = React.forwardRef(({ className, ...props }, ref) => (
  <div
    ref={ref}
    className={cn("flex items-center p-6 pt-0", className)}
    {...props} />
))
CardFooter.displayName = "CardFooter"

export { Card, CardHeader, CardFooter, CardTitle, CardDescription, CardContent }
```

### `frontend\src\components\ui\dropdown-menu.jsx`

```jsx
// frontend/src/components/ui/dropdown-menu.jsx
import * as React from "react";
import * as DropdownMenuPrimitive from "@radix-ui/react-dropdown-menu";

export const DropdownMenu = DropdownMenuPrimitive.Root;
export const DropdownMenuTrigger = DropdownMenuPrimitive.Trigger;
export const DropdownMenuContent = DropdownMenuPrimitive.Content;
export const DropdownMenuItem = DropdownMenuPrimitive.Item;
```

### `frontend\src\components\ui\input.jsx`

```jsx
// frontend/src/components/ui/input.jsx
import * as React from "react";

export const Input = React.forwardRef(({ className = "", ...props }, ref) => (
  <input
    ref={ref}
    className={
      "flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 " +
      className
    }
    {...props}
  />
));
Input.displayName = "Input";
```

### `frontend\src\components\ui\progress.jsx`

```jsx
import * as React from "react"
import * as ProgressPrimitive from "@radix-ui/react-progress"

import { cn } from "@/lib/utils"

const Progress = React.forwardRef(({ className, value, ...props }, ref) => (
  <ProgressPrimitive.Root
    ref={ref}
    className={cn(
      "relative h-2 w-full overflow-hidden rounded-full bg-primary/20",
      className
    )}
    {...props}>
    <ProgressPrimitive.Indicator
      className="h-full w-full flex-1 bg-primary transition-all"
      style={{ transform: `translateX(-${100 - (value || 0)}%)` }} />
  </ProgressPrimitive.Root>
))
Progress.displayName = ProgressPrimitive.Root.displayName

export { Progress }
```

### `frontend\src\components\ui\tabs.jsx`

```jsx
// frontend/src/components/ui/tabs.jsx
import * as React from "react";
import * as TabsPrimitive from "@radix-ui/react-tabs";

export const Tabs = TabsPrimitive.Root;
export const TabsList = TabsPrimitive.List;
export const TabsTrigger = TabsPrimitive.Trigger;
export const TabsContent = TabsPrimitive.Content;
```

### `frontend\src\components\ui\toast.jsx`

```jsx
// frontend/src/components/ui/toast.jsx
import * as React from "react"
import * as ToastPrimitives from "@radix-ui/react-toast"
import { cva } from "class-variance-authority"
import { X } from "lucide-react"

import { cn } from "@/lib/utils"

const ToastProvider = ToastPrimitives.Provider

const ToastViewport = React.forwardRef(({ className, ...props }, ref) => (
  <ToastPrimitives.Viewport
    ref={ref}
    className={cn(
      "fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]",
      className
    )}
    {...props}
  />
))
ToastViewport.displayName = ToastPrimitives.Viewport.displayName

const toastVariants = cva(
  "group pointer-events-auto relative flex w-full items-center justify-between space-x-4 overflow-hidden rounded-md border p-6 pr-8 shadow-lg transition-all data-[swipe=cancel]:translate-x-0 data-[swipe=end]:translate-x-[var(--radix-toast-swipe-end-x)] data-[swipe=move]:translate-x-[var(--radix-toast-swipe-move-x)] data-[swipe=move]:transition-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[swipe=end]:animate-out data-[state=closed]:fade-out-80 data-[state=closed]:slide-out-to-right-full data-[state=open]:slide-in-from-top-full data-[state=open]:sm:slide-in-from-bottom-full",
  {
    variants: {
      variant: {
        default: "border bg-background text-foreground",
        destructive:
          "destructive group border-destructive bg-destructive text-destructive-foreground",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  }
)

const Toast = React.forwardRef(({ className, variant, ...props }, ref) => {
  return (
    <ToastPrimitives.Root
      ref={ref}
      className={cn(toastVariants({ variant }), className)}
      {...props}
    />
  )
})
Toast.displayName = ToastPrimitives.Root.displayName

const ToastAction = React.forwardRef(({ className, ...props }, ref) => (
  <ToastPrimitives.Action
    ref={ref}
    className={cn(
      "inline-flex h-8 shrink-0 items-center justify-center rounded-md border bg-transparent px-3 text-sm font-medium ring-offset-background transition-colors hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 group-[.destructive]:border-muted/40 group-[.destructive]:hover:border-destructive/30 group-[.destructive]:hover:bg-destructive group-[.destructive]:hover:text-destructive-foreground group-[.destructive]:focus:ring-destructive",
      className
    )}
    {...props}
  />
))
ToastAction.displayName = ToastPrimitives.Action.displayName

const ToastClose = React.forwardRef(({ className, ...props }, ref) => (
  <ToastPrimitives.Close
    ref={ref}
    className={cn(
      "absolute right-2 top-2 rounded-md p-1 text-foreground/50 opacity-0 transition-opacity hover:text-foreground focus:opacity-100 focus:outline-none focus:ring-2 group-hover:opacity-100 group-[.destructive]:text-red-300 group-[.destructive]:hover:text-red-50 group-[.destructive]:focus:ring-red-400 group-[.destructive]:focus:ring-offset-red-600",
      className
    )}
    toast-close=""
    {...props}
  >
    <X className="h-4 w-4" />
  </ToastPrimitives.Close>
))
ToastClose.displayName = ToastPrimitives.Close.displayName

const ToastTitle = React.forwardRef(({ className, ...props }, ref) => (
  <ToastPrimitives.Title
    ref={ref}
    className={cn("text-sm font-semibold", className)}
    {...props}
  />
))
ToastTitle.displayName = ToastPrimitives.Title.displayName

const ToastDescription = React.forwardRef(({ className, ...props }, ref) => (
  <ToastPrimitives.Description
    ref={ref}
    className={cn("text-sm opacity-90", className)}
    {...props}
  />
))
ToastDescription.displayName = ToastPrimitives.Description.displayName

export {
  ToastProvider,
  ToastViewport,
  Toast,
  ToastTitle,
  ToastDescription,
  ToastClose,
  ToastAction,
  toastVariants,
}
```

### `frontend\src\components\ui\toaster.jsx`

```jsx
// frontend/src/components/ui/toaster.jsx
import { useToast } from "@/components/ui/use-toast";
import {
  Toast,
  ToastClose,
  ToastDescription,
  ToastProvider,
  ToastTitle,
  ToastViewport,
} from "@/components/ui/toast";

export function Toaster() {
  const { toasts } = useToast();

  return (
    <ToastProvider>
      {toasts.map(function ({ id, title, description, action, ...props }) {
        return (
          <Toast key={id} {...props}>
            <div className="grid gap-1">
              {title && <ToastTitle>{title}</ToastTitle>}
              {description && (
                <ToastDescription>{description}</ToastDescription>
              )}
            </div>
            {action}
            <ToastClose />
          </Toast>
        );
      })}
      <ToastViewport />
    </ToastProvider>
  );
}
```

### `frontend\src\components\ui\use-toast.js`

```js
// frontend/src/components/ui/use-toast.js
import { useState, useEffect, createContext, useContext } from "react"

const TOAST_LIMIT = 5
const TOAST_REMOVE_DELAY = 5000

const ToastActionType = {
  ADD_TOAST: "ADD_TOAST",
  UPDATE_TOAST: "UPDATE_TOAST",
  DISMISS_TOAST: "DISMISS_TOAST",
  REMOVE_TOAST: "REMOVE_TOAST"
}

let count = 0

function genId() {
  count = (count + 1) % Number.MAX_SAFE_INTEGER
  return count.toString()
}

const toastTimeouts = new Map()

const addToRemoveQueue = (toastId) => {
  if (toastTimeouts.has(toastId)) {
    return
  }

  const timeout = setTimeout(() => {
    toastTimeouts.delete(toastId)
    dispatch({
      type: ToastActionType.REMOVE_TOAST,
      toastId,
    })
  }, TOAST_REMOVE_DELAY)

  toastTimeouts.set(toastId, timeout)
}

const reducer = (state, action) => {
  switch (action.type) {
    case ToastActionType.ADD_TOAST:
      return {
        ...state,
        toasts: [action.toast, ...state.toasts].slice(0, TOAST_LIMIT),
      }

    case ToastActionType.UPDATE_TOAST:
      return {
        ...state,
        toasts: state.toasts.map((t) =>
          t.id === action.toast.id ? { ...t, ...action.toast } : t
        ),
      }

    case ToastActionType.DISMISS_TOAST: {
      const { toastId } = action

      if (toastId) {
        addToRemoveQueue(toastId)
      } else {
        state.toasts.forEach((toast) => {
          addToRemoveQueue(toast.id)
        })
      }

      return {
        ...state,
        toasts: state.toasts.map((t) =>
          t.id === toastId || toastId === undefined
            ? {
                ...t,
                open: false,
              }
            : t
        ),
      }
    }
    case ToastActionType.REMOVE_TOAST:
      if (action.toastId === undefined) {
        return {
          ...state,
          toasts: [],
        }
      }
      return {
        ...state,
        toasts: state.toasts.filter((t) => t.id !== action.toastId),
      }
    default:
      return state
  }
}

const listeners = []

let memoryState = { toasts: [] }

function dispatch(action) {
  memoryState = reducer(memoryState, action)
  listeners.forEach((listener) => {
    listener(memoryState)
  })
}

const ToastContext = createContext({
  toasts: [],
  toast: () => {},
  dismiss: () => {},
})

function useToast() {
  const [state, setState] = useState(memoryState)

  useEffect(() => {
    listeners.push(setState)
    return () => {
      const index = listeners.indexOf(setState)
      if (index > -1) {
        listeners.splice(index, 1)
      }
    }
  }, [state])

  return {
    ...state,
    toast: (props) => {
      const id = genId()

      const update = (props) =>
        dispatch({
          type: ToastActionType.UPDATE_TOAST,
          toast: { ...props, id },
        })
      const dismiss = () => dispatch({ type: ToastActionType.DISMISS_TOAST, toastId: id })

      dispatch({
        type: ToastActionType.ADD_TOAST,
        toast: {
          ...props,
          id,
          open: true,
          onOpenChange: (open) => {
            if (!open) dismiss()
          },
        },
      })

      return {
        id,
        dismiss,
        update,
      }
    },
    dismiss: (toastId) => dispatch({ type: ToastActionType.DISMISS_TOAST, toastId }),
  }
}

export { useToast, ToastContext }
```

### `frontend\src\index.css`

```css
/* frontend/src/index.css */
@tailwind base;
@tailwind components;
@tailwind utilities;
 
@layer base {
  :root {
    --background: 0 0% 100%;
    --foreground: 222.2 84% 4.9%;

    --card: 0 0% 100%;
    --card-foreground: 222.2 84% 4.9%;
 
    --popover: 0 0% 100%;
    --popover-foreground: 222.2 84% 4.9%;
 
    --primary: 222.2 47.4% 11.2%;
    --primary-foreground: 210 40% 98%;
 
    --secondary: 210 40% 96.1%;
    --secondary-foreground: 222.2 47.4% 11.2%;
 
    --muted: 210 40% 96.1%;
    --muted-foreground: 215.4 16.3% 46.9%;
 
    --accent: 210 40% 96.1%;
    --accent-foreground: 222.2 47.4% 11.2%;
 
    --destructive: 0 84.2% 60.2%;
    --destructive-foreground: 210 40% 98%;

    --border: 214.3 31.8% 91.4%;
    --input: 214.3 31.8% 91.4%;
    --ring: 222.2 84% 4.9%;
 
    --radius: 0.5rem;
 
    --chart-1: 12 76% 61%;
 
    --chart-2: 173 58% 39%;
 
    --chart-3: 197 37% 24%;
 
    --chart-4: 43 74% 66%;
 
    --chart-5: 27 87% 67%;
  }
 
  .dark {
    --background: 222.2 84% 4.9%;
    --foreground: 210 40% 98%;
 
    --card: 222.2 84% 4.9%;
    --card-foreground: 210 40% 98%;
 
    --popover: 222.2 84% 4.9%;
    --popover-foreground: 210 40% 98%;
 
    --primary: 210 40% 98%;
    --primary-foreground: 222.2 47.4% 11.2%;
 
    --secondary: 217.2 32.6% 17.5%;
    --secondary-foreground: 210 40% 98%;
 
    --muted: 217.2 32.6% 17.5%;
    --muted-foreground: 215 20.2% 65.1%;
 
    --accent: 217.2 32.6% 17.5%;
    --accent-foreground: 210 40% 98%;
 
    --destructive: 0 62.8% 30.6%;
    --destructive-foreground: 210 40% 98%;
 
    --border: 217.2 32.6% 17.5%;
    --input: 217.2 32.6% 17.5%;
    --ring: 212.7 26.8% 83.9%;
    --chart-1: 220 70% 50%;
    --chart-2: 160 60% 45%;
    --chart-3: 30 80% 55%;
    --chart-4: 280 65% 60%;
    --chart-5: 340 75% 55%;
  }
}
 
@layer base {
  * {
    @apply border-border;
  }
  body {
    @apply bg-background text-foreground;
  }
}

.media-player {
  @apply relative w-full overflow-hidden rounded-lg;
}

.media-player video, 
.media-player audio {
  @apply w-full;
}

.media-controls {
  @apply absolute bottom-0 left-0 right-0 bg-black/50 p-2 transition-opacity;
}

.video-thumb {
  @apply aspect-video object-cover;
}

.audio-thumb {
  @apply aspect-square object-cover;
}

.image-thumb {
  @apply aspect-square object-cover;
}
```

### `frontend\src\lib\logger.js`

```js
// frontend/src/lib/logger.js
class Logger {
    constructor() {
      this.logLevel = process.env.NODE_ENV === 'production' ? 'error' : 'debug';
      this.levels = {
        debug: 0,
        info: 1,
        warn: 2,
        error: 3
      };
    }
  
    shouldLog(level) {
      return this.levels[level] >= this.levels[this.logLevel];
    }
    
    formatMessage(message, ...args) {
      if (args.length === 0) {
        return message;
      }
  
      const timestamp = new Date().toISOString();
      const formattedArgs = args.map(arg => {
        if (arg instanceof Error) {
          return `${arg.message}\n${arg.stack}`;
        } else if (typeof arg === 'object') {
          try {
            return JSON.stringify(arg);
          } catch (e) {
            return String(arg);
          }
        }
        return String(arg);
      });
  
      return `[${timestamp}] ${message} ${formattedArgs.join(' ')}`;
    }
  
    debug(message, ...args) {
      if (this.shouldLog('debug')) {
        console.debug(this.formatMessage(message, ...args));
      }
    }
  
    info(message, ...args) {
      if (this.shouldLog('info')) {
        console.info(this.formatMessage(message, ...args));
      }
    }
  
    warn(message, ...args) {
      if (this.shouldLog('warn')) {
        console.warn(this.formatMessage(message, ...args));
      }
    }
  
    error(message, ...args) {
      if (this.shouldLog('error')) {
        console.error(this.formatMessage(message, ...args));
      }
    }
  
    setLogLevel(level) {
      if (this.levels[level] !== undefined) {
        this.logLevel = level;
      }
    }
  }
  
  export const logger = new Logger();
```

### `frontend\src\lib\utils.js`

```js
import { clsx } from "clsx";
import { twMerge } from "tailwind-merge"

export function cn(...inputs) {
  return twMerge(clsx(inputs));
}
```

### `frontend\src\main.jsx`

```jsx
// frontend/src/main.jsx
import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import { ThemeProvider } from './components/theme-provider';
import './index.css';

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <BrowserRouter>
      <ThemeProvider defaultTheme="dark" storageKey="media-share-theme">
        <App />
      </ThemeProvider>
    </BrowserRouter>
  </React.StrictMode>
);
```

### `frontend\src\pages\Home.jsx`

```jsx
// frontend/src/pages/Home.jsx
import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { MediaCard } from '@/components/MediaCard';
import { useMediaStore } from '@/stores/mediaStore';
import { useUserStore } from '@/stores/userStore';
import { Search, X } from 'lucide-react';
import { logger } from '@/lib/logger';

export function Home() {
  const { mediaItems, loading, error, fetchMedia } = useMediaStore();
  const { user } = useUserStore();
  const [mediaType, setMediaType] = useState('all');
  const [searchTerm, setSearchTerm] = useState('');
  const [tags, setTags] = useState([]);
  const [selectedTag, setSelectedTag] = useState('');
  
  useEffect(() => {
    const loadMedia = async () => {
      try {
        const filters = {};
        if (mediaType !== 'all') {
          filters.type = mediaType;
        }
        if (selectedTag) {
          filters.tag = selectedTag;
        }
        await fetchMedia(filters);
      } catch (err) {
        logger.error('Error loading media in Home component:', err);
      }
    };
    
    loadMedia();
  }, [fetchMedia, mediaType, selectedTag]);
  
  // Load tags
  useEffect(() => {
    const loadTags = async () => {
      try {
        const response = await fetch('/api/tags.php');
        if (!response.ok) throw new Error('Failed to load tags');
        
        const data = await response.json();
        setTags(data);
      } catch (err) {
        logger.error('Error loading tags:', err);
      }
    };
    
    loadTags();
  }, []);
  
  const filteredMedia = mediaItems.filter(item => {
    const matchesSearch = searchTerm.trim() === '' || 
      item.caption.toLowerCase().includes(searchTerm.toLowerCase()) ||
      item.description.toLowerCase().includes(searchTerm.toLowerCase());
    
    return matchesSearch;
  });
  
  const handleChangeMediaType = (value) => {
    setMediaType(value);
  };
  
  const handleTagSelection = (tag) => {
    setSelectedTag(tag === selectedTag ? '' : tag);
  };
  
  const clearFilters = () => {
    setSearchTerm('');
    setSelectedTag('');
    setMediaType('all');
  };
  
  if (!user) {
    return <div>Please select a user to view media</div>;
  }
  
  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold tracking-tight">Media Library</h1>
      </div>
      
      <div className="flex flex-col md:flex-row gap-4">
        <div className="relative flex-1">
          <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input
            type="search"
            placeholder="Search by caption or description..."
            className="pl-8"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        
        {(searchTerm || selectedTag || mediaType !== 'all') && (
          <Button variant="ghost" onClick={clearFilters} className="shrink-0">
            <X className="mr-2 h-4 w-4" />
            Clear filters
          </Button>
        )}
      </div>
      
      <div className="flex flex-wrap gap-2 mb-4">
        {tags.map(tag => (
          <Button
            key={tag}
            variant={selectedTag === tag ? "default" : "outline"}
            size="sm"
            onClick={() => handleTagSelection(tag)}
            className="rounded-full"
          >
            #{tag}
          </Button>
        ))}
      </div>
      
      <Tabs defaultValue="all" value={mediaType} onValueChange={handleChangeMediaType}>
        <TabsList>
          <TabsTrigger value="all">All</TabsTrigger>
          <TabsTrigger value="video">Videos</TabsTrigger>
          <TabsTrigger value="audio">Audio</TabsTrigger>
          <TabsTrigger value="image">Images</TabsTrigger>
        </TabsList>
        
        <TabsContent value="all" className="mt-4">
          <MediaGrid media={filteredMedia} loading={loading} error={error} />
        </TabsContent>
        
        <TabsContent value="video" className="mt-4">
          <MediaGrid 
            media={filteredMedia.filter(m => m.type === 'video')} 
            loading={loading} 
            error={error} 
          />
        </TabsContent>
        
        <TabsContent value="audio" className="mt-4">
          <MediaGrid 
            media={filteredMedia.filter(m => m.type === 'audio')} 
            loading={loading} 
            error={error} 
          />
        </TabsContent>
        
        <TabsContent value="image" className="mt-4">
          <MediaGrid 
            media={filteredMedia.filter(m => m.type === 'image')} 
            loading={loading} 
            error={error} 
          />
        </TabsContent>
      </Tabs>
    </div>
  );
}

function MediaGrid({ media, loading, error }) {
  if (loading) {
    return <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {[1, 2, 3, 4, 5, 6].map((i) => (
        <div key={i} className="rounded-lg border bg-card text-card-foreground shadow-sm animate-pulse">
          <div className="aspect-video w-full bg-muted"></div>
          <div className="p-4 space-y-2">
            <div className="h-4 bg-muted rounded w-3/4"></div>
            <div className="h-3 bg-muted rounded w-1/2"></div>
          </div>
        </div>
      ))}
    </div>;
  }
  
  if (error) {
    return <div className="p-4 border rounded-md bg-destructive/10 text-destructive">
      {error}
    </div>;
  }
  
  if (media.length === 0) {
    return <div className="text-center py-10">
      <p className="text-lg text-muted-foreground">No media found</p>
    </div>;
  }
  
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {media.map((item) => (
        <Link to={`/media/${item.id}`} key={item.id}>
          <MediaCard item={item} />
        </Link>
      ))}
    </div>
  );
}
```

### `frontend\src\pages\MediaDetails.jsx`

```jsx
// frontend/src/pages/MediaDetails.jsx
import React, { useEffect, useRef, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { useMediaStore } from '@/stores/mediaStore';
import { useToast } from '@/components/ui/use-toast';
import { ArrowLeft, Play, Pause, Volume2, VolumeX, DownloadIcon } from 'lucide-react';
import { logger } from '@/lib/logger';

export function MediaDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const { 
    selectedItem, 
    loading, 
    error, 
    fetchMediaById, 
    clearSelectedItem,
    trackMediaEvent
  } = useMediaStore();
  
  const [isPlaying, setIsPlaying] = useState(false);
  const [isMuted, setIsMuted] = useState(false);
  const [duration, setDuration] = useState(0);
  const [currentTime, setCurrentTime] = useState(0);
  const [progress, setProgress] = useState(0);
  
  const mediaRef = useRef(null);
  const analyticsInterval = useRef(null);
  
  // Fetch media details
  useEffect(() => {
    fetchMediaById(id);
    
    return () => {
      clearSelectedItem();
      if (analyticsInterval.current) {
        clearInterval(analyticsInterval.current);
      }
    };
  }, [id, fetchMediaById, clearSelectedItem]);
  
  // Track view event when media loads
  useEffect(() => {
    if (selectedItem) {
      trackMediaEvent(selectedItem.id, 'view');
    }
  }, [selectedItem, trackMediaEvent]);
  
  // Media player event handlers
  const handlePlayPause = () => {
    if (mediaRef.current) {
      if (isPlaying) {
        mediaRef.current.pause();
        trackMediaEvent(selectedItem.id, 'pause', { 
          position: mediaRef.current.currentTime,
          percentage: (mediaRef.current.currentTime / mediaRef.current.duration) * 100
        });
      } else {
        mediaRef.current.play()
          .then(() => {
            trackMediaEvent(selectedItem.id, 'play', { 
              position: mediaRef.current.currentTime,
              percentage: (mediaRef.current.currentTime / mediaRef.current.duration) * 100
            });
          })
          .catch(err => {
            logger.error('Error playing media:', err);
            toast({
              variant: 'destructive',
              title: 'Playback error',
              description: 'Could not play this media file'
            });
          });
      }
    }
  };
  
  const handleMuteToggle = () => {
    if (mediaRef.current) {
      mediaRef.current.muted = !isMuted;
      setIsMuted(!isMuted);
    }
  };
  
  const handleTimeUpdate = () => {
    if (mediaRef.current) {
      const current = mediaRef.current.currentTime;
      const duration = mediaRef.current.duration;
      
      setCurrentTime(current);
      setProgress((current / duration) * 100);
    }
  };
  
  const handleSeek = (e) => {
    if (mediaRef.current && duration) {
      const rect = e.currentTarget.getBoundingClientRect();
      const seekPos = (e.clientX - rect.left) / rect.width;
      const seekTime = duration * seekPos;
      
      mediaRef.current.currentTime = seekTime;
      setCurrentTime(seekTime);
      setProgress((seekTime / duration) * 100);
      
      trackMediaEvent(selectedItem.id, 'seek', { 
        position: seekTime,
        percentage: (seekTime / duration) * 100
      });
    }
  };
  
  const handleDownload = () => {
    if (selectedItem) {
      const link = document.createElement('a');
      link.href = `/backend/uploads/${selectedItem.filename}`;
      link.download = selectedItem.filename;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      trackMediaEvent(selectedItem.id, 'download');
      
      toast({
        title: 'Download started',
        description: `Downloading ${selectedItem.caption}`
      });
    }
  };
  
  // Setup media tracking
  useEffect(() => {
    if (selectedItem && selectedItem.type !== 'image' && isPlaying) {
      // Track progress every 10 seconds
      analyticsInterval.current = setInterval(() => {
        if (mediaRef.current) {
          trackMediaEvent(selectedItem.id, 'progress', {
            position: mediaRef.current.currentTime,
            percentage: (mediaRef.current.currentTime / mediaRef.current.duration) * 100
          });
        }
      }, 10000);
    } 
    
    return () => {
      if (analyticsInterval.current) {
        clearInterval(analyticsInterval.current);
      }
    };
  }, [selectedItem, isPlaying, trackMediaEvent]);
  
  // Format time in MM:SS
  const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
  };
  
  if (loading) {
    return (
      <div className="flex flex-col items-center justify-center h-[60vh]">
        <div className="animate-spin h-8 w-8 border-4 border-primary border-t-transparent rounded-full"></div>
        <p className="mt-4 text-muted-foreground">Loading media...</p>
      </div>
    );
  }
  
  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-[60vh]">
        <p className="text-destructive text-lg font-medium">{error}</p>
        <Button variant="outline" onClick={() => navigate(-1)} className="mt-4">
          Go back
        </Button>
      </div>
    );
  }
  
  if (!selectedItem) return null;
  
  const renderMedia = () => {
    const baseUrl = '/backend/uploads';
    
    if (selectedItem.type === 'video') {
      return (
        <div className="media-player rounded-lg overflow-hidden bg-black">
          <video
            ref={mediaRef}
            src={`${baseUrl}/${selectedItem.filename}`}
            className="w-full max-h-[70vh] object-contain"
            poster={selectedItem.thumbnail ? `${baseUrl}/${selectedItem.thumbnail}` : undefined}
            onClick={handlePlayPause}
            onPlay={() => setIsPlaying(true)}
            onPause={() => setIsPlaying(false)}
            onDurationChange={(e) => setDuration(e.target.duration)}
            onTimeUpdate={handleTimeUpdate}
            onEnded={() => {
              setIsPlaying(false);
              trackMediaEvent(selectedItem.id, 'ended', { 
                percentage: 100,
                position: duration
              });
            }}
          ></video>
          
          <div className="media-controls p-3 bg-black/60">
            <div className="flex items-center gap-2 mb-2">
              <Button 
                variant="ghost" 
                size="icon" 
                className="h-8 w-8 text-white" 
                onClick={handlePlayPause}
              >
                {isPlaying ? <Pause size={16} /> : <Play size={16} />}
              </Button>
              
              <Button 
                variant="ghost" 
                size="icon" 
                className="h-8 w-8 text-white" 
                onClick={handleMuteToggle}
              >
                {isMuted ? <VolumeX size={16} /> : <Volume2 size={16} />}
              </Button>
              
              <div className="text-xs text-white">
                {formatTime(currentTime)} / {formatTime(duration)}
              </div>
            </div>
            
            <div 
              className="h-2 bg-slate-700 rounded-full cursor-pointer" 
              onClick={handleSeek}
            >
              <div 
                className="h-full bg-primary rounded-full" 
                style={{ width: `${progress}%` }}
              ></div>
            </div>
          </div>
        </div>
      );
    }
    
    if (selectedItem.type === 'audio') {
      return (
        <div className="media-player rounded-lg overflow-hidden bg-card p-4 border">
          {selectedItem.thumbnail && (
            <div className="mb-4 flex justify-center">
              <img 
                src={`${baseUrl}/${selectedItem.thumbnail}`} 
                alt={selectedItem.caption} 
                className="w-48 h-48 object-cover rounded-lg" 
              />
            </div>
          )}
          
          <audio
            ref={mediaRef}
            src={`${baseUrl}/${selectedItem.filename}`}
            className="w-full"
            onPlay={() => setIsPlaying(true)}
            onPause={() => setIsPlaying(false)}
            onDurationChange={(e) => setDuration(e.target.duration)}
            onTimeUpdate={handleTimeUpdate}
            onEnded={() => {
              setIsPlaying(false);
              trackMediaEvent(selectedItem.id, 'ended', { 
                percentage: 100,
                position: duration
              });
            }}
          ></audio>
          
          <div className="mt-4 space-y-3">
            <div className="flex items-center gap-2">
              <Button 
                variant="outline" 
                size="icon" 
                className="h-10 w-10" 
                onClick={handlePlayPause}
              >
                {isPlaying ? <Pause size={18} /> : <Play size={18} />}
              </Button>
              
              <Button 
                variant="outline" 
                size="icon" 
                className="h-10 w-10" 
                onClick={handleMuteToggle}
              >
                {isMuted ? <VolumeX size={18} /> : <Volume2 size={18} />}
              </Button>
              
              <div className="text-sm">
                {formatTime(currentTime)} / {formatTime(duration)}
              </div>
            </div>
            
            <Progress value={progress} className="h-2 cursor-pointer" onClick={handleSeek} />
          </div>
        </div>
      );
    }
    
    if (selectedItem.type === 'image') {
      return (
        <div className="flex justify-center">
          <img 
            src={`${baseUrl}/${selectedItem.filename}`} 
            alt={selectedItem.caption} 
            className="max-h-[70vh] object-contain rounded-lg" 
          />
        </div>
      );
    }
    
    return null;
  };
  
  return (
    <div className="space-y-6">
      <div className="flex items-center">
        <Button 
          variant="ghost" 
          size="sm" 
          onClick={() => navigate(-1)} 
          className="mr-2"
        >
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
      </div>
      
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="md:col-span-2 space-y-4">
          {renderMedia()}
          
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold">{selectedItem.caption}</h1>
            
            <Button 
              variant="outline" 
              size="sm" 
              onClick={handleDownload}
              className="flex items-center gap-1"
            >
              <DownloadIcon className="h-4 w-4" />
              Download
            </Button>
          </div>
          
          <p className="text-muted-foreground whitespace-pre-line">
            {selectedItem.description}
          </p>
        </div>
        
        <div className="space-y-4">
          <div className="rounded-lg border bg-card p-4">
            <h2 className="font-semibold mb-3">Media Info</h2>
            
            <div className="space-y-2">
              <div className="flex items-center gap-2">
                <span className="text-sm font-medium">Type:</span>
                <Badge variant="outline" className="capitalize">
                  {selectedItem.type}
                </Badge>
              </div>
              
              <div className="flex items-center gap-2">
                <span className="text-sm font-medium">Format:</span>
                <span className="text-sm text-muted-foreground uppercase">
                  {selectedItem.filename.split('.').pop()}
                </span>
              </div>
              
              {selectedItem.created_at && (
                <div className="flex items-center gap-2">
                  <span className="text-sm font-medium">Added:</span>
                  <span className="text-sm text-muted-foreground">
                    {new Date(selectedItem.created_at).toLocaleDateString()}
                  </span>
                </div>
              )}
            </div>
          </div>
          
          {selectedItem.tags && selectedItem.tags.length > 0 && (
            <div className="rounded-lg border bg-card p-4">
              <h2 className="font-semibold mb-3">Tags</h2>
              <div className="flex flex-wrap gap-2">
                {selectedItem.tags.map(tag => (
                  <Badge key={tag} variant="secondary">
                    #{tag}
                  </Badge>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
```

### `frontend\src\stores\mediaStore.js`

```js
// frontend/src/stores/mediaStore.js
import { create } from 'zustand';
import axios from 'axios';
import { logger } from '../lib/logger';

export const useMediaStore = create((set, get) => ({
  mediaItems: [],
  selectedItem: null,
  loading: false,
  error: null,
  
  fetchMedia: async (filters = {}) => {
    try {
      set({ loading: true, error: null });
      
      const queryParams = new URLSearchParams();
      if (filters.type) queryParams.append('type', filters.type);
      if (filters.tag) queryParams.append('tag', filters.tag);
      
      const response = await axios.get(`/api/media.php?${queryParams.toString()}`);
      set({ mediaItems: response.data, loading: false });
      return response.data;
    } catch (error) {
      logger.error('Error fetching media:', error);
      set({ error: 'Failed to load media', loading: false });
      return [];
    }
  },
  
  fetchMediaById: async (id) => {
    try {
      set({ loading: true, error: null });
      const response = await axios.get(`/api/media.php?id=${id}`);
      
      if (response.data && response.data.length > 0) {
        set({ selectedItem: response.data[0], loading: false });
        return response.data[0];
      } else {
        set({ error: 'Media not found', loading: false });
        return null;
      }
    } catch (error) {
      logger.error('Error fetching media by id:', error);
      set({ error: 'Failed to load media details', loading: false });
      return null;
    }
  },
  
  clearSelectedItem: () => set({ selectedItem: null }),
  
  trackMediaEvent: async (mediaId, event, details = {}) => {
    try {
      const { user } = get();
      if (!user || !mediaId) return;
      
      const payload = {
        media_id: mediaId,
        event_type: event,
        user_name: user,
        ...details
      };
      
      await axios.post('/api/track.php', payload);
    } catch (error) {
      logger.error('Error tracking media event:', error);
    }
  }
}));
```

### `frontend\src\stores\userStore.js`

```js
// frontend/src/stores/userStore.js
import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export const useUserStore = create(
  persist(
    (set) => ({
      user: null,
      setUser: (user) => set({ user }),
      clearUser: () => set({ user: null }),
    }),
    {
      name: 'media-share-user',
    }
  )
);
```

### `frontend\tailwind.config.js`

```js
// frontend/tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ["class"],
    content: [
      './pages/**/*.{js,jsx}',
      './components/**/*.{js,jsx}',
      './app/**/*.{js,jsx}',
      './src/**/*.{js,jsx}',
    ],
    theme: {
    	container: {
    		center: true,
    		padding: '2rem',
    		screens: {
    			'2xl': '1400px'
    		}
    	},
    	extend: {
    		colors: {
    			border: 'hsl(var(--border))',
    			input: 'hsl(var(--input))',
    			ring: 'hsl(var(--ring))',
    			background: 'hsl(var(--background))',
    			foreground: 'hsl(var(--foreground))',
    			primary: {
    				DEFAULT: 'hsl(var(--primary))',
    				foreground: 'hsl(var(--primary-foreground))'
    			},
    			secondary: {
    				DEFAULT: 'hsl(var(--secondary))',
    				foreground: 'hsl(var(--secondary-foreground))'
    			},
    			destructive: {
    				DEFAULT: 'hsl(var(--destructive))',
    				foreground: 'hsl(var(--destructive-foreground))'
    			},
    			muted: {
    				DEFAULT: 'hsl(var(--muted))',
    				foreground: 'hsl(var(--muted-foreground))'
    			},
    			accent: {
    				DEFAULT: 'hsl(var(--accent))',
    				foreground: 'hsl(var(--accent-foreground))'
    			},
    			popover: {
    				DEFAULT: 'hsl(var(--popover))',
    				foreground: 'hsl(var(--popover-foreground))'
    			},
    			card: {
    				DEFAULT: 'hsl(var(--card))',
    				foreground: 'hsl(var(--card-foreground))'
    			},
    			chart: {
    				'1': 'hsl(var(--chart-1))',
    				'2': 'hsl(var(--chart-2))',
    				'3': 'hsl(var(--chart-3))',
    				'4': 'hsl(var(--chart-4))',
    				'5': 'hsl(var(--chart-5))'
    			}
    		},
    		borderRadius: {
    			lg: 'var(--radius)',
    			md: 'calc(var(--radius) - 2px)',
    			sm: 'calc(var(--radius) - 4px)'
    		},
    		keyframes: {
    			'accordion-down': {
    				from: {
    					height: 0
    				},
    				to: {
    					height: 'var(--radix-accordion-content-height)'
    				}
    			},
    			'accordion-up': {
    				from: {
    					height: 'var(--radix-accordion-content-height)'
    				},
    				to: {
    					height: 0
    				}
    			}
    		},
    		animation: {
    			'accordion-down': 'accordion-down 0.2s ease-out',
    			'accordion-up': 'accordion-up 0.2s ease-out'
    		}
    	}
    },
    plugins: [require("tailwindcss-animate")],
  }
```

### `frontend\vite.config.js`

```js
// frontend/vite.config.js
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
  base: '/',
  plugins: [
    react(),
    VitePWA({
      registerType: 'autoUpdate',
      includeAssets: ['favicon.ico', 'robots.txt', 'apple-touch-icon.png'],
      manifest: {
        name: 'Media Share App',
        short_name: 'MediaShare',
        description: 'A simple media sharing application',
        theme_color: '#ffffff',
        icons: [
          {
            src: 'pwa-192x192.png',
            sizes: '192x192',
            type: 'image/png'
          },
          {
            src: 'pwa-512x512.png',
            sizes: '512x512',
            type: 'image/png'
          },
          {
            src: 'pwa-512x512.png',
            sizes: '512x512',
            type: 'image/png',
            purpose: 'any maskable'
          }
        ]
      }
    })
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost/backend/api',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, '')
      }
    }
  }
});
```

### `index.php`

```php
<?php
// index.php
// Check if application is installed
$configFile = __DIR__ . '/backend/config.php';
$installed = false;

if (file_exists($configFile)) {
    // Check if config file contains database settings
    $configContent = file_get_contents($configFile);
    if (strpos($configContent, 'DB_HOST') !== false) {
        $installed = true;
    }
}

// Redirect based on installation status
if (!$installed) {
    // Application not installed, redirect to setup
    header('Location: backend/setup.php');
    exit;
} else {
    // Application installed, redirect to frontend
    header('Location: frontend/dist/index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Share</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .container {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
        }
        h1 {
            margin-top: 0;
            color: #333;
        }
        .buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.25rem;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn-primary {
            background-color: #0d6efd;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Media Share Application</h1>
        
        <?php if ($installed): ?>
            <p>The application is installed and ready to use. Please choose where you want to go:</p>
            <div class="buttons">
                <a href="frontend/dist/index.html" class="btn btn-primary">Frontend</a>
                <a href="backend/admin/index.php" class="btn btn-secondary">Admin Dashboard</a>
            </div>
        <?php else: ?>
            <p>The application is not installed yet. Please run the setup wizard:</p>
            <div class="buttons">
                <a href="backend/setup.php" class="btn btn-primary">Run Setup</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
```
