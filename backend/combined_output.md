# Combined Markdown Export

Generated: 2025-04-26T02:25:55.144901


## Index

- `.gitkeep` — ~0 tokens
- `.htaccess` — ~71 tokens
- `config\app.php` — ~26 tokens
- `config\database.php` — ~21 tokens
- `controllers\AdminController.php` — ~117 tokens
- `controllers\AnalyticsController.php` — ~106 tokens
- `controllers\AuthController.php` — ~97 tokens
- `controllers\Controller.php` — ~71 tokens
- `controllers\MediaController.php` — ~140 tokens
- `controllers\RatingController.php` — ~61 tokens
- `database\admin_user.sql` — ~17 tokens
- `database\analytics_table.sql` — ~89 tokens
- `database\init_db.php` — ~134 tokens
- `database\media_table.sql` — ~81 tokens
- `database\ratings_table.sql` — ~78 tokens
- `database\users_table.sql` — ~53 tokens
- `index.php` — ~123 tokens
- `models\Analytics.php` — ~106 tokens
- `models\Media.php` — ~123 tokens
- `models\Rating.php` — ~78 tokens
- `models\User.php` — ~85 tokens
- `uploads\.gitkeep` — ~0 tokens
- `uploads\media\.gitkeep` — ~0 tokens
- `uploads\thumbnails\.gitkeep` — ~0 tokens
- `utils\Auth.php` — ~130 tokens
- `utils\Database.php` — ~83 tokens
- `utils\FileUpload.php` — ~106 tokens
- `utils\Logger.php` — ~70 tokens

**Total tokens: ~2066**

---

### `.gitkeep`

```

```

### `.htaccess`

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# CORS Headers
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization"
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "DENY"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

### `config\app.php`

```php
<?php
// Application configuration
$app_url = 'http://localhost/media/backend';
$jwt_secret = 'fsa8fsd9fasdf6asf7687sadf87';
$upload_path = __DIR__ . '/../uploads/media/';
$thumbnail_path = __DIR__ . '/../uploads/thumbnails/';
```

### `config\database.php`

```php
<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'my_media';
$db_user = 'cp';
$db_pass = '4334.4334';
```

### `controllers\AdminController.php`

```php
<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AdminController extends Controller {
    public function listUsers($db) {
        // Stub: implement list users
        $this->jsonResponse(['users' => []]);
    }
    public function createUser($db, $data) {
        // Stub: implement create user
        $this->jsonResponse(['message' => 'User created']);
    }
    public function updateUser($db, $id, $data) {
        // Stub: implement update user
        $this->jsonResponse(['message' => 'User updated']);
    }
    public function deleteUser($db, $id) {
        // Stub: implement delete user
        $this->jsonResponse(['message' => 'User deleted']);
    }
    public function dashboard($db) {
        // Stub: implement admin dashboard data
        $this->jsonResponse(['stats' => []]);
    }
}
```

### `controllers\AnalyticsController.php`

```php
<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Analytics.php';

class AnalyticsController extends Controller {
    public function trackStart($db, $data) {
        // Stub: implement track playback start
        $this->jsonResponse(['message' => 'Playback start tracked']);
    }
    public function trackEnd($db, $data) {
        // Stub: implement track playback end
        $this->jsonResponse(['message' => 'Playback end tracked']);
    }
    public function trackSkip($db, $data) {
        // Stub: implement track skip
        $this->jsonResponse(['message' => 'Skip tracked']);
    }
    public function getAnalytics($db, $filters = []) {
        // Stub: implement analytics fetch
        $this->jsonResponse(['analytics' => []]);
    }
}
```

### `controllers\AuthController.php`

```php
<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Auth.php';

class AuthController extends Controller {
    public function login($db, $data, $jwt_secret) {
        // Stub: implement login logic
        $this->jsonResponse(['token' => 'demo.jwt.token', 'user' => null]);
    }
    public function logout() {
        Auth::destroySession();
        $this->jsonResponse(['message' => 'Logged out']);
    }
    public function getCurrentUser() {
        // Stub: implement get current user
        $this->jsonResponse(['user' => null]);
    }
    public function verifyRole($role) {
        // Stub: implement role check
        return true;
    }
}
```

### `controllers\Controller.php`

```php
<?php
// Base Controller
class Controller {
    protected function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    protected function errorResponse($message, $status = 400) {
        $this->jsonResponse(['error' => $message], $status);
    }
    protected function validateRequest($fields, $source) {
        foreach ($fields as $field) {
            if (!isset($source[$field])) {
                $this->errorResponse("Missing field: $field", 422);
            }
        }
    }
}
```

### `controllers\MediaController.php`

```php
<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Media.php';

class MediaController extends Controller {
    public function listMedia($db, $filters = []) {
        // Stub: implement list media
        $this->jsonResponse(['media' => []]);
    }
    public function getMedia($db, $id) {
        // Stub: implement get single media
        $this->jsonResponse(['media' => null]);
    }
    public function playMedia($db, $id) {
        // Stub: implement play media
        $this->jsonResponse(['play_url' => null]);
    }
    public function addMedia($db, $data) {
        // Stub: implement add media
        $this->jsonResponse(['message' => 'Media added']);
    }
    public function updateMedia($db, $id, $data) {
        // Stub: implement update media
        $this->jsonResponse(['message' => 'Media updated']);
    }
    public function deleteMedia($db, $id) {
        // Stub: implement delete media
        $this->jsonResponse(['message' => 'Media deleted']);
    }
}
```

### `controllers\RatingController.php`

```php
<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Rating.php';

class RatingController extends Controller {
    public function rateMedia($db, $media_id, $user_id, $rating) {
        // Stub: implement rating
        $this->jsonResponse(['average_rating' => 0]);
    }
    public function getUserRating($db, $media_id, $user_id) {
        // Stub: implement get user rating
        $this->jsonResponse(['rating' => null]);
    }
}
```

### `database\admin_user.sql`

```sql
INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'charl@webally.co.za', SHA2('4334.4334', 256), 'admin');
```

### `database\analytics_table.sql`

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

### `database\init_db.php`

```php
<?php
// Database initialization script
require_once __DIR__ . '/../config/database.php';

function run_sql_file($conn, $file) {
    $sql = file_get_contents($file);
    if ($conn->multi_query($sql)) {
        do {
            // store first result set
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
        echo basename($file) . ": Success\n";
    } else {
        echo basename($file) . ": Error - " . $conn->error . "\n";
    }
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$files = [
    __DIR__ . '/users_table.sql',
    __DIR__ . '/media_table.sql',
    __DIR__ . '/ratings_table.sql',
    __DIR__ . '/analytics_table.sql',
    __DIR__ . '/admin_user.sql',
];

foreach ($files as $file) {
    run_sql_file($conn, $file);
}

$conn->close();
```

### `database\media_table.sql`

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

### `database\ratings_table.sql`

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

### `database\users_table.sql`

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

### `index.php`

```php
<?php
// API Entry Point
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/utils/Database.php';

// Load controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/MediaController.php';
require_once __DIR__ . '/controllers/RatingController.php';
require_once __DIR__ . '/controllers/AnalyticsController.php';
require_once __DIR__ . '/controllers/AdminController.php';

// Basic routing logic (stub, to be expanded)
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$db = new Database($db_host, $db_user, $db_pass, $db_name);

// Example: Route stub
if (strpos($uri, '/api/auth/login') !== false && $method === 'POST') {
    $controller = new AuthController();
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->login($db->getConnection(), $data, $jwt_secret);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
```

### `models\Analytics.php`

```php
<?php
// Analytics model
class Analytics {
    public $id;
    public $media_id;
    public $user_id;
    public $session_id;
    public $device_type;
    public $browser;
    public $os;
    public $start_time;
    public $end_time;
    public $duration;
    public $completed;
    public $skipped;
    public $skip_position;
    public $created_at;
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    public static function recordStart($db, $data) {}
    public static function recordEnd($db, $data) {}
    public static function recordSkip($db, $data) {}
    public static function getAnalytics($db, $filters = []) {}
}
```

### `models\Media.php`

```php
<?php
// Media model
class Media {
    public $id;
    public $title;
    public $description;
    public $type;
    public $file_path;
    public $thumbnail_path;
    public $duration;
    public $tags;
    public $average_rating;
    public $ratings_count;
    public $play_count;
    public $uploaded_by;
    public $created_at;
    public $updated_at;
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    public static function create($db, $data) {}
    public static function get($db, $id) {}
    public static function getAll($db, $filters = []) {}
    public static function update($db, $id, $data) {}
    public static function delete($db, $id) {}
    public static function incrementPlayCount($db, $id) {}
}
```

### `models\Rating.php`

```php
<?php
// Rating model
class Rating {
    public $id;
    public $media_id;
    public $user_id;
    public $rating;
    public $created_at;
    public $updated_at;
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    public static function addOrUpdate($db, $media_id, $user_id, $rating) {}
    public static function getAverage($db, $media_id) {}
    public static function getUserRating($db, $media_id, $user_id) {}
}
```

### `models\User.php`

```php
<?php
// User model
class User {
    public $id;
    public $username;
    public $email;
    public $password;
    public $role;
    public $created_at;
    public $updated_at;
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    public static function create($db, $data) {}
    public static function authenticate($db, $username, $password) {}
    public static function getProfile($db, $id) {}
    public static function getAll($db) {}
}
```

### `uploads\.gitkeep`

```

```

### `uploads\media\.gitkeep`

```

```

### `uploads\thumbnails\.gitkeep`

```

```

### `utils\Auth.php`

```php
<?php
// Authentication utility class
class Auth {
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    public static function generateJWT($user, $secret) {
        $payload = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'exp' => time() + 3600
        ];
        return JWT::encode($payload, $secret, 'HS256');
    }
    public static function validateJWT($token, $secret) {
        try {
            return JWT::decode($token, $secret, ['HS256']);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    public static function destroySession() {
        session_destroy();
    }
}
```

### `utils\Database.php`

```php
<?php
// Database utility class
class Database {
    private $conn;
    public function __construct($host, $user, $pass, $name) {
        $this->conn = new mysqli($host, $user, $pass, $name);
        if ($this->conn->connect_error) {
            $this->logError($this->conn->connect_error);
            die('Database connection failed');
        }
    }
    public function query($sql) {
        return $this->conn->query($sql);
    }
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    public function getConnection() {
        return $this->conn;
    }
    private function logError($error) {
        error_log($error, 3, __DIR__.'/../logs/db_errors.log');
    }
}
```

### `utils\FileUpload.php`

```php
<?php
// File upload utility class
class FileUpload {
    public static function validateFileType($file, $allowedTypes) {
        $fileType = mime_content_type($file['tmp_name']);
        return in_array($fileType, $allowedTypes);
    }
    public static function generateUniqueFilename($originalName) {
        return uniqid() . '_' . basename($originalName);
    }
    public static function upload($file, $destination) {
        $filename = self::generateUniqueFilename($file['name']);
        $target = $destination . $filename;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $filename;
        } else {
            return false;
        }
    }
    public static function generateThumbnail($videoPath, $thumbnailPath) {
        // Stub: Implement thumbnail generation using ffmpeg or similar
        return true;
    }
}
```

### `utils\Logger.php`

```php
<?php
// Logger utility class
class Logger {
    public static function log($message, $level = 'info') {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date][$level] $message\n";
        file_put_contents(__DIR__.'/../logs/app.log', $logMessage, FILE_APPEND);
    }
    public static function error($message) {
        self::log($message, 'error');
    }
    public static function warning($message) {
        self::log($message, 'warning');
    }
    public static function info($message) {
        self::log($message, 'info');
    }
}
```
