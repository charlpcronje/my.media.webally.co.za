// /var/www/html/my.media.webally.co.za/backend/setup.php
<?php
// Setup script for media directories and configuration

// Create media directories
$media_dir = __DIR__ . '/../media';
$thumb_dir = $media_dir . '/thumbnails';

// Create main media directory if it doesn't exist
if (!file_exists($media_dir)) {
    if (mkdir($media_dir, 0755, true)) {
        echo "Created media directory at $media_dir\n";
    } else {
        echo "Failed to create media directory at $media_dir\n";
        exit(1);
    }
}

// Create thumbnails directory if it doesn't exist
if (!file_exists($thumb_dir)) {
    if (mkdir($thumb_dir, 0755, true)) {
        echo "Created thumbnails directory at $thumb_dir\n";
    } else {
        echo "Failed to create thumbnails directory at $thumb_dir\n";
        exit(1);
    }
}

// Set proper permissions
system("chmod -R 755 $media_dir");
system("chown -R www-data:www-data $media_dir");

echo "Media directories created successfully.\n";

// Update config.php if needed
$config_file = __DIR__ . '/config.php';
if (file_exists($config_file)) {
    $config = file_get_contents($config_file);
    
    // Update upload paths if needed
    if (strpos($config, 'UPLOAD_PATH') !== false) {
        $config = preg_replace(
            '/define\s*\(\s*[\'"]UPLOAD_PATH[\'"]\s*,\s*[\'"].*?[\'"]\s*\)/',
            "define('UPLOAD_PATH', __DIR__ . '/../media')",
            $config
        );
        
        $config = preg_replace(
            '/define\s*\(\s*[\'"]THUMBNAIL_PATH[\'"]\s*,\s*[\'"].*?[\'"]\s*\)/',
            "define('THUMBNAIL_PATH', __DIR__ . '/../media/thumbnails')",
            $config
        );
        
        file_put_contents($config_file, $config);
        echo "Updated config paths successfully.\n";
    }
}

echo "Setup complete!\n";