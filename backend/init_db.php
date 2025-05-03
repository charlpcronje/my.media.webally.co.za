<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// backend/init_db.php
// Database connection parameters
$db_host = 'localhost';
$db_name = 'my_media';
$db_user = 'root';
$db_pass = '';

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